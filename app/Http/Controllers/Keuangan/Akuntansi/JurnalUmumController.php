<?php

namespace App\Http\Controllers\Keuangan\Akuntansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\JurnalUmum;

class JurnalUmumController extends Controller
{
    /**
     * Tampilkan buku jurnal umum transaksi akuntansi double-entry.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterPosisi = $request->input('posisi');

        $query = DB::table('jurnal_umum')
            ->leftJoin('data_kode_akun', 'jurnal_umum.kode_akun', '=', 'data_kode_akun.kode_akun')
            ->select('jurnal_umum.*', 'data_kode_akun.nama_akun');

        if ($filterPosisi) {
            $query->where('jurnal_umum.posisi', $filterPosisi);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('jurnal_umum.nomor_jurnal', 'like', "%{$kataKunci}%")
                  ->orWhere('jurnal_umum.keterangan', 'like', "%{$kataKunci}%")
                  ->orWhere('data_kode_akun.nama_akun', 'like', "%{$kataKunci}%");
            });
        }

        $daftarJurnal = $query->orderBy('jurnal_umum.id_jurnal', 'desc')->get();
        $daftarAkun = DB::table('data_kode_akun')->orderBy('kode_akun')->get();

        $totalDebit = DB::table('jurnal_umum')->where('posisi', 'Debit')->sum('nominal');
        $totalKredit = DB::table('jurnal_umum')->where('posisi', 'Kredit')->sum('nominal');
        $isBalance = abs($totalDebit - $totalKredit) < 0.01;

        return view('keuangan.akuntansi.jurnal_umum', compact(
            'daftarJurnal',
            'daftarAkun',
            'kataKunci',
            'filterPosisi',
            'totalDebit',
            'totalKredit',
            'isBalance'
        ));
    }

    /**
     * Simpan entri Jurnal Umum Double-Entry baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'kode_akun_debit'   => 'required|string|exists:data_kode_akun,kode_akun',
            'kode_akun_kredit'  => 'required|string|different:kode_akun_debit|exists:data_kode_akun,kode_akun',
            'nominal'           => 'required|numeric|min:1000',
            'keterangan'        => 'required|string|max:255',
        ], [
            'kode_akun_kredit.different' => 'Akun Kredit harus berbeda dengan Akun Debit.',
        ]);

        $nominal = (float) $request->nominal;
        $nomorJurnal = 'JU-' . date('Ymd') . '-' . rand(100, 999);

        DB::beginTransaction();
        try {
            // Sisi Debit
            DB::table('jurnal_umum')->insert([
                'nomor_jurnal'        => $nomorJurnal,
                'tanggal_transaksi'   => $request->tanggal_transaksi,
                'kode_akun'           => $request->kode_akun_debit,
                'posisi'              => 'Debit',
                'nominal'             => $nominal,
                'keterangan'          => $request->keterangan,
                'referensi_transaksi' => $request->referensi ?? 'MANUAL-ADJ',
                'dibuat_oleh'         => 'spv_keuangan',
                'dibuat_pada'         => now(),
            ]);

            // Sisi Kredit
            DB::table('jurnal_umum')->insert([
                'nomor_jurnal'        => $nomorJurnal,
                'tanggal_transaksi'   => $request->tanggal_transaksi,
                'kode_akun'           => $request->kode_akun_kredit,
                'posisi'              => 'Kredit',
                'nominal'             => $nominal,
                'keterangan'          => $request->keterangan,
                'referensi_transaksi' => $request->referensi ?? 'MANUAL-ADJ',
                'dibuat_oleh'         => 'spv_keuangan',
                'dibuat_pada'         => now(),
            ]);

            DB::commit();

            return redirect()->route('keuangan.akuntansi.jurnal')->with('sukses', "Entri Jurnal {$nomorJurnal} (Double-Entry Rp " . number_format($nominal, 0, ',', '.') . ") berhasil dicatat secara seimbang.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal mencatat jurnal: " . $e->getMessage());
        }
    }
}
