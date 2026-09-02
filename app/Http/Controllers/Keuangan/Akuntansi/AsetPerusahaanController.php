<?php

namespace App\Http\Controllers\Keuangan\Akuntansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\AsetPerusahaan;
use App\Helpers\GeneratorKodeOtomatis;

class AsetPerusahaanController extends Controller
{
    /**
     * Tampilkan daftar aset tetap perusahaan (Truk, Gudang, Peralatan).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterJenis = $request->input('jenis');

        $query = DB::table('data_aset')
            ->leftJoin('data_jenis_aset', 'data_aset.kode_jenis_aset', '=', 'data_jenis_aset.kode_jenis_aset')
            ->select('data_aset.*', 'data_jenis_aset.jenis_aset');

        if ($filterJenis) {
            $query->where('data_aset.kode_jenis_aset', $filterJenis);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('data_aset.nama_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('data_aset.kode_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('data_aset.no_polisi', 'like', "%{$kataKunci}%");
            });
        }

        $daftarAset = $query->orderBy('data_aset.kode_aset', 'asc')->get();
        $daftarJenis = DB::table('data_jenis_aset')->orderBy('jenis_aset')->get();

        $totalNilaiAset = DB::table('data_aset')->sum('harga_aset');
        $totalAset = DB::table('data_aset')->count();
        $totalTruk = DB::table('data_aset')->where('kode_jenis_aset', 'AST-TRK')->count();

        // Generator kode aset otomatis
        $kodeOtomatis = GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3);

        return view('keuangan.akuntansi.aset_perusahaan', compact(
            'daftarAset',
            'daftarJenis',
            'kataKunci',
            'filterJenis',
            'totalNilaiAset',
            'totalAset',
            'totalTruk',
            'kodeOtomatis'
        ));
    }

    /**
     * Simpan aset tetap baru.
     */
    public function store(Request $request)
    {
        // Isi kode otomatis jika kosong
        if (!$request->filled('kode_aset')) {
            $request->merge([
                'kode_aset' => GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3)
            ]);
        }

        $request->validate([
            'kode_aset'         => 'required|string|max:30|unique:data_aset,kode_aset',
            'kode_jenis_aset'   => 'required|string|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset'         => 'required|string|max:100',
            'tanggal_pembelian' => 'required|date',
            'harga_aset'        => 'required|numeric|min:0',
        ]);

        DB::table('data_aset')->insert([
            'kode_aset'         => strtoupper($request->kode_aset),
            'kode_jenis_aset'   => $request->kode_jenis_aset,
            'nama_aset'         => $request->nama_aset,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'harga_aset'        => $request->harga_aset,
            'no_polisi'         => $request->no_polisi ?? '-',
            'merek_aset'        => $request->merek_aset ?? '-',
            'jenis_kendaraan'   => $request->jenis_kendaraan ?? '-',
            'muatan'            => $request->muatan ?? '-',
            'status_aset'       => 'aktif',
            'nama_pemilik'      => 'PT Pura Balkom Jaya Utama',
            'dibuat_pada'       => now(),
        ]);

        return redirect()->route('keuangan.akuntansi.aset')->with('sukses', "Aset {$request->nama_aset} berhasil didaftarkan.");
    }

    /**
     * Generator Kode Aset Otomatis (Daur Ulang Slot vs Acak).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        return \App\Helpers\GeneratorKodeOtomatis::responJson('data_aset', 'kode_aset', 'AST-', $mode, 3, false);
    }
}
