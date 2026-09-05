<?php

namespace App\Http\Controllers\Keuangan\AP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Master\Karyawan;
use App\Helpers\GeneratorKodeOtomatis;
use App\Helpers\FilterKeuanganHelper;
use App\Services\Keuangan\MesinJurnalOtomatis;

class HutangSupplierController extends Controller
{
    /**
     * Tampilkan daftar rilisan uang jalan driver dan kas bon operasional (AP).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterRekening = $request->input('rekening');
        $filterPeriode = $request->input('periode');
        $filterTglMulai = $request->input('tgl_mulai');
        $filterTglSelesai = $request->input('tgl_selesai');

        $query = DB::table('pengeluaran')
            ->where(function ($q) {
                $q->where('pengeluaran.kategori_pengeluaran', 'like', '%Kas Bon%')
                  ->orWhere('pengeluaran.kategori_pengeluaran', 'like', '%BBM%');
            })
            ->leftJoin('data_rekening', 'pengeluaran.id_rekening_sumber', '=', 'data_rekening.id_rekening')
            ->select('pengeluaran.*', 'data_rekening.nama_bank');

        if ($filterRekening !== null && $filterRekening !== '') {
            if ($filterRekening === 'tunai') {
                $query->whereNull('pengeluaran.id_rekening_sumber');
            } else {
                $query->where('pengeluaran.id_rekening_sumber', $filterRekening);
            }
        }

        FilterKeuanganHelper::terapkanFilterTanggal($query, 'pengeluaran.tanggal_pengeluaran', $filterPeriode, $filterTglMulai, $filterTglSelesai);

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('pengeluaran.nomor_pengeluaran', 'like', "%{$kataKunci}%")
                  ->orWhere('pengeluaran.keterangan', 'like', "%{$kataKunci}%");
            });
        }

        $daftarRilisan = $query->orderBy('pengeluaran.id_pengeluaran', 'desc')->get();
        $daftarDriver = Karyawan::where('kategori_karyawan', 'driver')->orderBy('nama_karyawan')->get();
        $daftarRekening = DB::table('data_rekening')->orderBy('nama_bank')->get();

        $totalRilisan = $daftarRilisan->sum('total_nominal');
        $jumlahTransaksi = $daftarRilisan->count();

        $opsiPeriode = FilterKeuanganHelper::opsiPeriode();
        $jumlahFilterAktif = FilterKeuanganHelper::hitungFilterAktif([
            'cari' => $kataKunci,
            'rekening' => $filterRekening,
            'periode' => $filterPeriode,
            'tgl_mulai' => $filterTglMulai,
            'tgl_selesai' => $filterTglSelesai,
        ]);

        return view('keuangan.ap.list_rilisan', compact(
            'daftarRilisan',
            'daftarDriver',
            'daftarRekening',
            'kataKunci',
            'filterRekening',
            'filterPeriode',
            'filterTglMulai',
            'filterTglSelesai',
            'opsiPeriode',
            'jumlahFilterAktif',
            'totalRilisan',
            'jumlahTransaksi'
        ));
    }

    /**
     * Simpan rilisan uang jalan / kas bon supir baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_driver'        => 'required|string|exists:data_karyawan,kode_karyawan',
            'tanggal_rilisan'    => 'required|date',
            'nominal'            => 'required|numeric|min:50000',
            'id_rekening_sumber' => 'nullable|integer|exists:data_rekening,id_rekening',
            'keterangan'         => 'required|string',
        ]);

        $driver = Karyawan::findOrFail($request->kode_driver);
        $nominal = (float) $request->nominal;
        $nomorRilisan = GeneratorKodeOtomatis::buatKodeTransaksi('pengeluaran', 'nomor_pengeluaran', 'RLS-DRV-', $request->tanggal_rilisan);

        $pembuat = auth()->user()->username ?? 'spv_keuangan';

        DB::beginTransaction();
        try {
            if ($request->id_rekening_sumber) {
                DB::table('data_rekening')
                    ->where('id_rekening', $request->id_rekening_sumber)
                    ->decrement('saldo_rekening', $nominal);
            }

            DB::table('pengeluaran')->insert([
                'nomor_pengeluaran'   => $nomorRilisan,
                'tanggal_pengeluaran' => $request->tanggal_rilisan,
                'kategori_pengeluaran'=> 'Uang Jalan / Kas Bon Driver',
                'kode_akun'           => '1107', // Uang Muka / Kas Bon Supir
                'total_nominal'       => $nominal,
                'id_rekening_sumber'  => $request->id_rekening_sumber,
                'keterangan'          => "Rilisan uang jalan untuk driver: {$driver->nama_karyawan} ({$driver->kode_karyawan}) - " . $request->keterangan,
                'status_persetujuan'  => 'disetujui_spv',
                'disetujui_oleh'      => 'spv_keuangan',
                'dibuat_oleh'         => $pembuat,
                'dibuat_pada'         => now(),
            ]);

            // Auto-Journal ke Jurnal Umum Akuntansi (Debit Kas Bon Supir 1107, Kredit Kas/Bank Sumber)
            MesinJurnalOtomatis::jurnalRilisanUangJalan(
                $nomorRilisan,
                $request->tanggal_rilisan,
                $nominal,
                $request->id_rekening_sumber,
                $pembuat,
                "Rilisan uang jalan driver: {$driver->nama_karyawan} ({$driver->kode_karyawan})"
            );

            DB::commit();

            return redirect()->route('keuangan.ap.rilisan')->with('sukses', "Rilisan uang jalan {$nomorRilisan} sebesar Rp " . number_format($nominal, 0, ',', '.') . " untuk {$driver->nama_karyawan} berhasil dicatat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal mencatat rilisan: " . $e->getMessage());
        }
    }
}
