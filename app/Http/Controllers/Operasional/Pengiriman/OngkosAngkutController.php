<?php

namespace App\Http\Controllers\Operasional\Pengiriman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\OngkosAngkut;
use App\Models\Operasional\Gudang;
use App\Models\Master\Wilayah;

class OngkosAngkutController extends Controller
{
    /**
     * Tampilkan data daftar master tarif ongkos angkut.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterMuatan = $request->input('muatan', 'semua');
        $filterGudang = $request->input('gudang', 'semua');

        $query = OngkosAngkut::with('gudang');

        // Filter Pencarian Multi-Kolom
        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('kode_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('kontrak_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('muatan_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('wilayah_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_gudang', 'like', "%{$kataKunci}%");
            });
        }

        // Filter Jenis Muatan
        if ($filterMuatan !== 'semua' && !empty($filterMuatan)) {
            $query->where('muatan_oa', $filterMuatan);
        }

        // Filter Gudang Asal
        if ($filterGudang !== 'semua' && !empty($filterGudang)) {
            $query->where('kode_gudang', $filterGudang);
        }

        $daftarOngkosAngkut = $query->orderBy('kode_oa', 'asc')->get();

        // Data Statistik Ringkasan Kartu KPI
        $semuaOA = OngkosAngkut::all();
        $totalRute = $semuaOA->count();
        $rataHargaOa = $semuaOA->avg('harga_oa') ?? 0;
        $rataHargaKso = $semuaOA->avg('harga_kso') ?? 0;
        $rataHargaKsoKhusus = $semuaOA->avg('harga_kso_khusus') ?? 0;

        // Data Master untuk Dropdown Pilihan
        $daftarGudang = Gudang::orderBy('nama_gudang', 'asc')->get();
        $daftarWilayah = Wilayah::orderBy('nama_wilayah', 'asc')->get();

        return view('operasional.pengiriman.ongkos_angkut', compact(
            'daftarOngkosAngkut',
            'kataKunci',
            'filterMuatan',
            'filterGudang',
            'totalRute',
            'rataHargaOa',
            'rataHargaKso',
            'rataHargaKsoKhusus',
            'daftarGudang',
            'daftarWilayah'
        ));
    }

    /**
     * Simpan data ongkos angkut baru ke database.
     */
    public function simpan(Request $request)
    {
        // Bersihkan format rupiah jika terdapat titik/karakter pemisah
        $this->bersihkanInputNominal($request);

        // Isi kode OA otomatis jika kosong
        if (!$request->filled('kode_oa')) {
            $request->merge([
                'kode_oa' => $this->generateKodeOaOtomatis()
            ]);
        }

        $pesanKustom = [
            'kode_oa.required' => 'Kode Ongkos Angkut wajib diisi.',
            'kode_oa.unique' => 'Kode Ongkos Angkut sudah terdaftar dalam sistem.',
            'nama_oa.required' => 'Nama Rute / Trayek OA wajib diisi.',
            'muatan_oa.required' => 'Jenis Muatan OA wajib dipilih / diisi.',
            'harga_oa.required' => 'Harga Ongkos Angkut Standar wajib diisi.',
            'harga_kso.required' => 'Harga KSO Standar wajib diisi.',
            'harga_kso_khusus.required' => 'Harga KSO Khusus wajib diisi.',
            'wilayah_oa.required' => 'Wilayah Cakupan OA wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_oa'          => 'required|string|max:30|unique:data_ongkos_angkut,kode_oa',
            'nama_oa'          => 'required|string|max:150',
            'kode_gudang'      => 'nullable|string|max:30',
            'kontrak_oa'       => 'nullable|string|max:100',
            'muatan_oa'        => 'required|string|max:100',
            'harga_oa'         => 'required|numeric|min:0',
            'harga_kso'        => 'required|numeric|min:0',
            'harga_kso_khusus' => 'required|numeric|min:0',
            'wilayah_oa'       => 'required|string|max:100',
            'keterangan'       => 'nullable|string|max:255',
        ], $pesanKustom);

        OngkosAngkut::create([
            'kode_oa'          => trim($validated['kode_oa']),
            'nama_oa'          => trim($validated['nama_oa']),
            'kode_gudang'      => $validated['kode_gudang'] ?: null,
            'kontrak_oa'       => $validated['kontrak_oa'] ? trim($validated['kontrak_oa']) : null,
            'muatan_oa'        => trim($validated['muatan_oa']),
            'harga_oa'         => $validated['harga_oa'],
            'harga_kso'        => $validated['harga_kso'],
            'harga_kso_khusus' => $validated['harga_kso_khusus'],
            'wilayah_oa'       => trim($validated['wilayah_oa']),
            'keterangan'       => $validated['keterangan'] ? trim($validated['keterangan']) : null,
            'dibuat_pada'      => now(),
            'diperbarui_pada'  => now(),
        ]);

        return redirect()->route('operasional.pengiriman.ongkos_angkut')
            ->with('sukses', "Data Ongkos Angkut {$validated['nama_oa']} ({$validated['kode_oa']}) berhasil ditambahkan ke sistem!");
    }

    /**
     * Ambil data detail ongkos angkut untuk modal AJAX / Alpine.js.
     */
    public function ambilDetail($kode_oa)
    {
        $oa = OngkosAngkut::with('gudang')->where('kode_oa', $kode_oa)->first();

        if (!$oa) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data Ongkos Angkut tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $oa
        ]);
    }

    /**
     * Perbarui data ongkos angkut yang ada di database.
     */
    public function perbarui(Request $request, $kode_oa)
    {
        $oa = OngkosAngkut::where('kode_oa', $kode_oa)->firstOrFail();

        // Bersihkan format rupiah jika terdapat titik/karakter pemisah
        $this->bersihkanInputNominal($request);

        $pesanKustom = [
            'nama_oa.required' => 'Nama Rute / Trayek OA wajib diisi.',
            'muatan_oa.required' => 'Jenis Muatan OA wajib dipilih / diisi.',
            'harga_oa.required' => 'Harga Ongkos Angkut Standar wajib diisi.',
            'harga_kso.required' => 'Harga KSO Standar wajib diisi.',
            'harga_kso_khusus.required' => 'Harga KSO Khusus wajib diisi.',
            'wilayah_oa.required' => 'Wilayah Cakupan OA wajib diisi.',
        ];

        $validated = $request->validate([
            'nama_oa'          => 'required|string|max:150',
            'kode_gudang'      => 'nullable|string|max:30',
            'kontrak_oa'       => 'nullable|string|max:100',
            'muatan_oa'        => 'required|string|max:100',
            'harga_oa'         => 'required|numeric|min:0',
            'harga_kso'        => 'required|numeric|min:0',
            'harga_kso_khusus' => 'required|numeric|min:0',
            'wilayah_oa'       => 'required|string|max:100',
            'keterangan'       => 'nullable|string|max:255',
        ], $pesanKustom);

        $oa->update([
            'nama_oa'          => trim($validated['nama_oa']),
            'kode_gudang'      => $validated['kode_gudang'] ?: null,
            'kontrak_oa'       => $validated['kontrak_oa'] ? trim($validated['kontrak_oa']) : null,
            'muatan_oa'        => trim($validated['muatan_oa']),
            'harga_oa'         => $validated['harga_oa'],
            'harga_kso'        => $validated['harga_kso'],
            'harga_kso_khusus' => $validated['harga_kso_khusus'],
            'wilayah_oa'       => trim($validated['wilayah_oa']),
            'keterangan'       => $validated['keterangan'] ? trim($validated['keterangan']) : null,
            'diperbarui_pada'  => now(),
        ]);

        return redirect()->route('operasional.pengiriman.ongkos_angkut')
            ->with('sukses', "Data Ongkos Angkut {$oa->nama_oa} ({$oa->kode_oa}) berhasil diperbarui!");
    }

    /**
     * Hapus data ongkos angkut dari database.
     */
    public function hapus($kode_oa)
    {
        $oa = OngkosAngkut::where('kode_oa', $kode_oa)->firstOrFail();
        $namaOa = $oa->nama_oa;

        try {
            $oa->delete();

            return redirect()->route('operasional.pengiriman.ongkos_angkut')
                ->with('sukses', "Data Ongkos Angkut {$namaOa} ({$kode_oa}) berhasil dihapus dari sistem! Nomor slot kode ini siap digunakan kembali.");
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('operasional.pengiriman.ongkos_angkut')
                ->with('error', "Gagal menghapus data ongkos angkut {$namaOa}! Data rute ini masih terikat dengan transaksi surat jalan / operasional lain.");
        }
    }

    /**
     * Generator kode otomatis OA (Gap-filling & Smart Auto-Number).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $kodeOtomatis = $this->generateKodeOaOtomatis();

        return response()->json([
            'status' => 'sukses',
            'kode_otomatis' => $kodeOtomatis,
            'keterangan' => 'Slot Kode Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Helper internal algoritma Gap Filling untuk nomor OA
     */
    private function generateKodeOaOtomatis(): string
    {
        $daftarKode = DB::table('data_ongkos_angkut')
            ->where('kode_oa', 'like', 'OA-%')
            ->pluck('kode_oa');

        $nomorTerpakai = [];
        foreach ($daftarKode as $kode) {
            if (preg_match('/OA-(\d+)/i', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slot = 1;
        while (in_array($slot, $nomorTerpakai)) {
            $slot++;
        }

        return 'OA-' . str_pad($slot, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Helper pembersih input uang dari karakter titik/non-numerik
     */
    private function bersihkanInputNominal(Request $request): void
    {
        foreach (['harga_oa', 'harga_kso', 'harga_kso_khusus'] as $field) {
            if ($request->has($field)) {
                $nilaiMentah = $request->input($field);
                if (is_string($nilaiMentah)) {
                    $bersih = preg_replace('/[^0-9]/', '', $nilaiMentah);
                    $request->merge([$field => $bersih !== '' ? (float) $bersih : 0]);
                }
            }
        }
    }
}
