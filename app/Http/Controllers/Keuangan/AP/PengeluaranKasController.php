<?php

namespace App\Http\Controllers\Keuangan\AP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\PengeluaranKas;
use App\Helpers\GeneratorKodeOtomatis;
use App\Services\Keuangan\MesinJurnalOtomatis;

class PengeluaranKasController extends Controller
{
    /**
     * Tampilkan daftar pengeluaran kas operasional perusahaan (AP).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterKategori = $request->input('kategori');

        $query = DB::table('pengeluaran')
            ->leftJoin('data_kode_akun', 'pengeluaran.kode_akun', '=', 'data_kode_akun.kode_akun')
            ->leftJoin('data_rekening', 'pengeluaran.id_rekening_sumber', '=', 'data_rekening.id_rekening')
            ->select(
                'pengeluaran.*',
                'data_kode_akun.nama_akun',
                'data_rekening.nama_bank',
                'data_rekening.nomor_rekening'
            );

        if ($filterKategori) {
            $query->where('pengeluaran.kategori_pengeluaran', $filterKategori);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('pengeluaran.nomor_pengeluaran', 'like', "%{$kataKunci}%")
                  ->orWhere('pengeluaran.keterangan', 'like', "%{$kataKunci}%")
                  ->orWhere('data_kode_akun.nama_akun', 'like', "%{$kataKunci}%");
            });
        }

        $daftarPengeluaran = $query->orderBy('pengeluaran.id_pengeluaran', 'desc')->get();
        $daftarAkunBeban = DB::table('data_kode_akun')
            ->whereIn('tipe_akun', ['Beban Operasional', 'Harga Pokok Penjualan', 'Beban Lain-Lain'])
            ->orderBy('kode_akun')
            ->get();
        $daftarRekening = DB::table('data_rekening')->orderBy('nama_bank')->get();

        $totalPengeluaran = DB::table('pengeluaran')->sum('total_nominal');
        $totalBBM = DB::table('pengeluaran')->where('kategori_pengeluaran', 'like', '%BBM%')->sum('total_nominal');
        $totalKantor = DB::table('pengeluaran')->where('kategori_pengeluaran', 'like', '%Kantor%')->sum('total_nominal');

        return view('keuangan.ap.pengeluaran_kas', compact(
            'daftarPengeluaran',
            'daftarAkunBeban',
            'daftarRekening',
            'kataKunci',
            'filterKategori',
            'totalPengeluaran',
            'totalBBM',
            'totalKantor'
        ));
    }

    /**
     * Simpan pengeluaran kas operasional baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pengeluaran' => 'required|date',
            'kategori_pengeluaran'=> 'required|string|max:100',
            'kode_akun'           => 'required|string|exists:data_kode_akun,kode_akun',
            'total_nominal'       => 'required|numeric|min:1000',
            'id_rekening_sumber'  => 'nullable|integer|exists:data_rekening,id_rekening',
            'keterangan'          => 'required|string',
        ]);

        $nominal = (float) $request->total_nominal;
        $nomorPengeluaran = GeneratorKodeOtomatis::buatKodeTransaksi('pengeluaran', 'nomor_pengeluaran', 'KAS-OUT-', $request->tanggal_pengeluaran);

        DB::beginTransaction();
        try {
            // Potong saldo rekening sumber jika dipilih
            if ($request->id_rekening_sumber) {
                DB::table('data_rekening')
                    ->where('id_rekening', $request->id_rekening_sumber)
                    ->decrement('saldo_rekening', $nominal);
            }

            DB::table('pengeluaran')->insert([
                'nomor_pengeluaran'   => $nomorPengeluaran,
                'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
                'kategori_pengeluaran'=> $request->kategori_pengeluaran,
                'kode_akun'           => $request->kode_akun,
                'total_nominal'       => $nominal,
                'id_rekening_sumber'  => $request->id_rekening_sumber,
                'keterangan'          => $request->keterangan,
                'status_persetujuan'  => 'disetujui_spv',
                'disetujui_oleh'      => 'spv_keuangan',
                'dibuat_oleh'         => 'staff_ap',
                'dibuat_pada'         => now(),
            ]);

            // Auto-Journal ke Jurnal Umum Akuntansi (Debit Akun Beban, Kredit Kas/Bank Sumber)
            MesinJurnalOtomatis::jurnalPengeluaranKas(
                $nomorPengeluaran,
                $request->tanggal_pengeluaran,
                $request->kode_akun,
                $nominal,
                $request->id_rekening_sumber,
                auth()->user()->username ?? 'staff_ap',
                $request->keterangan
            );

            DB::commit();

            return redirect()->route('keuangan.ap.pengeluaran')->with('sukses', "Pengeluaran {$nomorPengeluaran} sebesar Rp " . number_format($nominal, 0, ',', '.') . " berhasil dicatat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal mencatat pengeluaran kas: " . $e->getMessage());
        }
    }
}
