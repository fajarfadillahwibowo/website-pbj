<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Master\JenisAset;
use App\Models\Operasional\Kendaraan;

class JenisAsetController extends Controller
{
    /**
     * Tampilkan daftar master jenis aset armada kendaraan.
     */
    public function index(Request $request)
    {
        $this->pastikanDataDasarTersedia();

        $kataKunci = $request->input('cari');

        $query = JenisAset::withCount('kendaraan');

        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('jenis_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_jenis_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('keterangan', 'like', "%{$kataKunci}%");
            });
        }

        $daftarJenisAset = $query->orderBy('dibuat_pada', 'desc')->get();

        // 4 KPI Statistik Jenis Aset
        $totalJenisAset = JenisAset::count();
        $totalArmadaTerpasang = Kendaraan::count();
        $kategoriTrukBerat = JenisAset::whereIn('kode_jenis_aset', ['KND-TRN', 'KND-TKG', 'KND-TRL'])->count();
        $kategoriTrukSedang = JenisAset::whereNotIn('kode_jenis_aset', ['KND-TRN', 'KND-TKG', 'KND-TRL'])->count();

        return view('master.jenis_aset.index', compact(
            'daftarJenisAset',
            'kataKunci',
            'totalJenisAset',
            'totalArmadaTerpasang',
            'kategoriTrukBerat',
            'kategoriTrukSedang'
        ));
    }

    /**
     * Simpan data jenis aset baru.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'kode_jenis_aset.required' => 'Kode jenis aset wajib diisi.',
            'kode_jenis_aset.unique' => 'Kode jenis aset sudah terdaftar.',
            'jenis_aset.required' => 'Nama kategori jenis aset wajib diisi.',
            'keterangan.required' => 'Keterangan spesifikasi muatan wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_jenis_aset' => 'required|string|max:30|unique:data_jenis_aset,kode_jenis_aset',
            'jenis_aset' => 'required|string|max:100',
            'keterangan' => 'required|string',
        ], $pesanKustom);

        JenisAset::create([
            'kode_jenis_aset' => strtoupper(trim($validated['kode_jenis_aset'])),
            'jenis_aset' => trim($validated['jenis_aset']),
            'keterangan' => trim($validated['keterangan']),
        ]);

        return redirect()->route('master.jenis_aset.index')
            ->with('sukses', "Kategori Jenis Aset {$validated['jenis_aset']} ({$validated['kode_jenis_aset']}) berhasil ditambahkan!");
    }

    /**
     * Ambil data detail jenis aset untuk modal Alpine.js.
     */
    public function ambilDetail($kode_jenis_aset)
    {
        $jenisAset = JenisAset::with(['kendaraan'])->where('kode_jenis_aset', $kode_jenis_aset)->first();

        if (!$jenisAset) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data jenis aset tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $jenisAset
        ]);
    }

    /**
     * Perbarui data jenis aset.
     */
    public function perbarui(Request $request, $kode_jenis_aset)
    {
        $jenisAset = JenisAset::where('kode_jenis_aset', $kode_jenis_aset)->firstOrFail();

        $pesanKustom = [
            'jenis_aset.required' => 'Nama kategori jenis aset wajib diisi.',
            'keterangan.required' => 'Keterangan spesifikasi muatan wajib diisi.',
        ];

        $validated = $request->validate([
            'jenis_aset' => 'required|string|max:100',
            'keterangan' => 'required|string',
        ], $pesanKustom);

        $jenisAset->update([
            'jenis_aset' => trim($validated['jenis_aset']),
            'keterangan' => trim($validated['keterangan']),
            'diperbarui_pada' => now(),
        ]);

        return redirect()->route('master.jenis_aset.index')
            ->with('sukses', "Data kategori {$jenisAset->jenis_aset} ({$jenisAset->kode_jenis_aset}) berhasil diperbarui!");
    }

    /**
     * Hapus data jenis aset dari database.
     */
    public function hapus($kode_jenis_aset)
    {
        $jenisAset = JenisAset::where('kode_jenis_aset', $kode_jenis_aset)->firstOrFail();
        $namaJenis = $jenisAset->jenis_aset;

        // Validasi proteksi relasi: pastikan tidak ada armada kendaraan yang sedang menggunakan jenis aset ini
        $jumlahUnitTerhubung = Kendaraan::where('kode_jenis_aset', $kode_jenis_aset)->count();
        if ($jumlahUnitTerhubung > 0) {
            return redirect()->route('master.jenis_aset.index')
                ->with('error', "Gagal menghapus kategori {$namaJenis}! Terdapat {$jumlahUnitTerhubung} unit armada truk yang masih terhubung ke jenis aset ini di Data Kendaraan.");
        }

        try {
            $jenisAset->delete();

            return redirect()->route('master.jenis_aset.index')
                ->with('sukses', "Kategori jenis aset {$namaJenis} ({$kode_jenis_aset}) berhasil dihapus dari sistem!");
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('master.jenis_aset.index')
                ->with('error', "Gagal menghapus jenis aset {$namaJenis}! Data masih memiliki dependensi di sistem.");
        }
    }

    /**
     * Helper endpoint generator kode jenis aset otomatis (Gap-filling & Acak).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');

        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $panjang = strlen($karakter);
            $kodeUnik = null;
            $percobaan = 0;

            do {
                $acak = '';
                for ($i = 0; $i < 4; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'JNS-' . $acak;
                $sudahAda = DB::table('data_jenis_aset')->where('kode_jenis_aset', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('JNS-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Kode Alfanumerik Acak (Anti-Tebak)'
            ]);
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarJenis = DB::table('data_jenis_aset')
            ->where('kode_jenis_aset', 'like', 'JNS-%')
            ->pluck('kode_jenis_aset');

        $nomorTerpakai = [];
        foreach ($daftarJenis as $kode) {
            if (preg_match('/JNS-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'JNS-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan data jenis aset dasar tersedia.
     */
    private function pastikanDataDasarTersedia(): void
    {
        $defaultJenis = [
            ['kode_jenis_aset' => 'KND-TRN', 'jenis_aset' => 'Truk Tronton Wingbox', 'keterangan' => 'Kapasitas 25 - 30 Ton (500 - 600 Zak Semen)'],
            ['kode_jenis_aset' => 'KND-CDD', 'jenis_aset' => 'Colt Diesel Double (CDD)', 'keterangan' => 'Kapasitas 10 - 15 Ton (200 - 300 Zak Semen)'],
            ['kode_jenis_aset' => 'KND-TKG', 'jenis_aset' => 'Truk Tangki Semen Curah', 'keterangan' => 'Kapasitas 30 - 35 Ton Semen Curah Bulk'],
            ['kode_jenis_aset' => 'KND-TRL', 'jenis_aset' => 'Truk Trailer Gandeng 40ft', 'keterangan' => 'Kapasitas angkut kontainer & semen muatan besar'],
            ['kode_jenis_aset' => 'KND-PKP', 'jenis_aset' => 'Pick Up / Operasional Lapangan', 'keterangan' => 'Armada operasional supervisor & teknisi'],
        ];

        foreach ($defaultJenis as $j) {
            DB::table('data_jenis_aset')->updateOrInsert(
                ['kode_jenis_aset' => $j['kode_jenis_aset']],
                [
                    'jenis_aset' => $j['jenis_aset'],
                    'keterangan' => $j['keterangan'],
                ]
            );
        }
    }
}
