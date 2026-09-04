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

        // Filter Pencarian Multi-Kolom (Termasuk atribut Gudang dari SPV Gudang)
        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('kode_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('kontrak_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('muatan_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('wilayah_oa', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_gudang', 'like', "%{$kataKunci}%")
                  ->orWhereHas('gudang', function ($qG) use ($kataKunci) {
                      $qG->where('nama_gudang', 'like', "%{$kataKunci}%")
                         ->orWhere('plant', 'like', "%{$kataKunci}%")
                         ->orWhere('distrik', 'like', "%{$kataKunci}%")
                         ->orWhere('jenis_gudang', 'like', "%{$kataKunci}%");
                  });
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

        // Data Master untuk Dropdown Pilihan (Sinkron dengan SPV Gudang)
        $daftarGudang = Gudang::with('barang')->orderBy('kode_gudang', 'asc')->get();
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

        // Sanitasi kode_gudang jika bernilai 'semua', empty string, atau 'null' agar menjadi null valid
        if ($request->has('kode_gudang')) {
            $valGudang = trim((string) $request->input('kode_gudang'));
            if ($valGudang === '' || strtolower($valGudang) === 'semua' || strtolower($valGudang) === 'null') {
                $request->merge(['kode_gudang' => null]);
            }
        }

        // Default muatan dan wilayah jika tidak terisi
        if (!$request->filled('muatan_oa')) {
            $request->merge(['muatan_oa' => 'Semen Zak 50kg']);
        }
        if (!$request->filled('wilayah_oa')) {
            $wilayahDefault = Wilayah::value('nama_wilayah') ?? 'Wilayah Distribusi';
            $request->merge(['wilayah_oa' => $wilayahDefault]);
        }

        $pesanKustom = [
            'kode_oa.required' => 'Kode Ongkos Angkut wajib diisi.',
            'kode_oa.unique' => 'Kode Ongkos Angkut sudah terdaftar dalam sistem.',
            'nama_oa.required' => 'Nama Rute / Trayek OA wajib diisi.',
            'kode_gudang.exists' => 'Fasilitas gudang yang dipilih tidak valid atau belum terdaftar pada Master Gudang (SPV Gudang).',
            'muatan_oa.required' => 'Jenis Muatan OA wajib dipilih / diisi.',
            'harga_oa.required' => 'Harga Ongkos Angkut Standar wajib diisi.',
            'harga_kso.required' => 'Harga KSO Standar wajib diisi.',
            'harga_kso_khusus.required' => 'Harga KSO Khusus wajib diisi.',
            'wilayah_oa.required' => 'Wilayah Cakupan OA wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_oa'          => 'required|string|max:30|unique:data_ongkos_angkut,kode_oa',
            'nama_oa'          => 'required|string|max:150',
            'kode_gudang'      => 'nullable|string|max:30|exists:list_gudang_so,kode_gudang',
            'kontrak_oa'       => 'nullable|string|max:100',
            'muatan_oa'        => 'required|string|max:100',
            'harga_oa'         => 'required|numeric|min:0',
            'harga_kso'        => 'required|numeric|min:0',
            'harga_kso_khusus' => 'required|numeric|min:0',
            'wilayah_oa'       => 'required|string|max:100',
            'keterangan'       => 'nullable|string|max:255',
        ], $pesanKustom);

        DB::beginTransaction();
        try {
            $oa = OngkosAngkut::create([
                'kode_oa'          => trim($validated['kode_oa']),
                'nama_oa'          => trim($validated['nama_oa']),
                'kode_gudang'      => ($validated['kode_gudang'] ?? null) ?: null,
                'kontrak_oa'       => !empty($validated['kontrak_oa']) ? trim($validated['kontrak_oa']) : null,
                'muatan_oa'        => trim($validated['muatan_oa']),
                'harga_oa'         => $validated['harga_oa'] ?? 0,
                'harga_kso'        => $validated['harga_kso'] ?? 0,
                'harga_kso_khusus' => $validated['harga_kso_khusus'] ?? 0,
                'wilayah_oa'       => trim($validated['wilayah_oa']),
                'keterangan'       => !empty($validated['keterangan']) ? trim($validated['keterangan']) : null,
            ]);

            DB::commit();

            return redirect()->route('operasional.pengiriman.ongkos_angkut')
                ->with('sukses', "Data Ongkos Angkut {$validated['nama_oa']} ({$validated['kode_oa']}) berhasil ditambahkan ke sistem!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan data ongkos angkut: ' . $e->getMessage());
        }
    }

    /**
     * Ambil data detail ongkos angkut untuk modal AJAX / Alpine.js.
     */
    public function ambilDetail($kode_oa)
    {
        $oa = OngkosAngkut::with(['gudang.barang'])->where('kode_oa', $kode_oa)->first();

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

        // Sanitasi kode_gudang jika bernilai 'semua', empty string, atau 'null' agar menjadi null valid
        if ($request->has('kode_gudang')) {
            $valGudang = trim((string) $request->input('kode_gudang'));
            if ($valGudang === '' || strtolower($valGudang) === 'semua' || strtolower($valGudang) === 'null') {
                $request->merge(['kode_gudang' => null]);
            }
        }

        // Default muatan dan wilayah jika tidak terisi
        if (!$request->filled('muatan_oa')) {
            $request->merge(['muatan_oa' => $oa->muatan_oa ?: 'Semen Zak 50kg']);
        }
        if (!$request->filled('wilayah_oa')) {
            $request->merge(['wilayah_oa' => $oa->wilayah_oa ?: (Wilayah::value('nama_wilayah') ?? 'Wilayah Distribusi')]);
        }

        $pesanKustom = [
            'nama_oa.required' => 'Nama Rute / Trayek OA wajib diisi.',
            'kode_gudang.exists' => 'Fasilitas gudang yang dipilih tidak valid atau belum terdaftar pada Master Gudang (SPV Gudang).',
            'muatan_oa.required' => 'Jenis Muatan OA wajib dipilih / diisi.',
            'harga_oa.required' => 'Harga Ongkos Angkut Standar wajib diisi.',
            'harga_kso.required' => 'Harga KSO Standar wajib diisi.',
            'harga_kso_khusus.required' => 'Harga KSO Khusus wajib diisi.',
            'wilayah_oa.required' => 'Wilayah Cakupan OA wajib diisi.',
        ];

        $validated = $request->validate([
            'nama_oa'          => 'required|string|max:150',
            'kode_gudang'      => 'nullable|string|max:30|exists:list_gudang_so,kode_gudang',
            'kontrak_oa'       => 'nullable|string|max:100',
            'muatan_oa'        => 'required|string|max:100',
            'harga_oa'         => 'required|numeric|min:0',
            'harga_kso'        => 'required|numeric|min:0',
            'harga_kso_khusus' => 'required|numeric|min:0',
            'wilayah_oa'       => 'required|string|max:100',
            'keterangan'       => 'nullable|string|max:255',
        ], $pesanKustom);

        DB::beginTransaction();
        try {
            $oa->update([
                'nama_oa'          => trim($validated['nama_oa']),
                'kode_gudang'      => ($validated['kode_gudang'] ?? null) ?: null,
                'kontrak_oa'       => !empty($validated['kontrak_oa']) ? trim($validated['kontrak_oa']) : null,
                'muatan_oa'        => trim($validated['muatan_oa']),
                'harga_oa'         => $validated['harga_oa'] ?? 0,
                'harga_kso'        => $validated['harga_kso'] ?? 0,
                'harga_kso_khusus' => $validated['harga_kso_khusus'] ?? 0,
                'wilayah_oa'       => trim($validated['wilayah_oa']),
                'keterangan'       => !empty($validated['keterangan']) ? trim($validated['keterangan']) : null,
                'diperbarui_pada'  => now(),
            ]);

            DB::commit();

            return redirect()->route('operasional.pengiriman.ongkos_angkut')
                ->with('sukses', "Data Ongkos Angkut {$oa->nama_oa} ({$oa->kode_oa}) berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui data ongkos angkut: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data ongkos angkut dari database.
     */
    public function hapus($kode_oa)
    {
        $oa = OngkosAngkut::where('kode_oa', $kode_oa)->firstOrFail();
        $namaOa = $oa->nama_oa;

        DB::beginTransaction();
        try {
            $oa->delete();
            DB::commit();

            return redirect()->route('operasional.pengiriman.ongkos_angkut')
                ->with('sukses', "Data Ongkos Angkut {$namaOa} ({$kode_oa}) berhasil dihapus dari sistem! Nomor slot kode ini siap digunakan kembali.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('operasional.pengiriman.ongkos_angkut')
                ->with('error', "Gagal menghapus data ongkos angkut {$namaOa}: " . $e->getMessage());
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
                } elseif (is_numeric($nilaiMentah)) {
                    $request->merge([$field => (float) $nilaiMentah]);
                } else {
                    $request->merge([$field => 0]);
                }
            } else {
                $request->merge([$field => 0]);
            }
        }
    }
}
