<?php

namespace App\Http\Controllers\Keuangan\AP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Master\Karyawan;

class HutangSupplierController extends Controller
{
    /**
     * Tampilkan daftar rilisan uang jalan driver dan kas bon operasional (AP).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');

        $query = DB::table('pengeluaran')
            ->where('kategori_pengeluaran', 'like', '%Kas Bon%')
            ->orWhere('kategori_pengeluaran', 'like', '%BBM%')
            ->leftJoin('data_rekening', 'pengeluaran.id_rekening_sumber', '=', 'data_rekening.id_rekening')
            ->select('pengeluaran.*', 'data_rekening.nama_bank');

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_pengeluaran', 'like', "%{$kataKunci}%")
                  ->orWhere('keterangan', 'like', "%{$kataKunci}%");
            });
        }

        $daftarRilisan = $query->orderBy('id_pengeluaran', 'desc')->get();
        $daftarDriver = Karyawan::where('kategori_karyawan', 'driver')->orderBy('nama_karyawan')->get();
        $daftarRekening = DB::table('data_rekening')->orderBy('nama_bank')->get();

        $totalRilisan = $daftarRilisan->sum('total_nominal');
        $jumlahTransaksi = $daftarRilisan->count();

        return view('keuangan.ap.list_rilisan', compact(
            'daftarRilisan',
            'daftarDriver',
            'daftarRekening',
            'kataKunci',
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
        $nomorRilisan = 'RLS-DRV-' . date('Ymd') . '-' . rand(100, 999);

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
                'dibuat_oleh'         => 'staff_ap',
                'dibuat_pada'         => now(),
            ]);

            DB::commit();

            return redirect()->route('keuangan.ap.rilisan')->with('sukses', "Rilisan uang jalan {$nomorRilisan} sebesar Rp " . number_format($nominal, 0, ',', '.') . " untuk {$driver->nama_karyawan} berhasil dicatat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal mencatat rilisan: " . $e->getMessage());
        }
    }

    /**
     * Generator Nomor Rilisan Otomatis (Daur Ulang Slot vs Acak Tanggal).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        return \App\Helpers\GeneratorKodeOtomatis::responJson('pengeluaran', 'nomor_pengeluaran', 'RLS-', $mode, 3, true);
    }
}
