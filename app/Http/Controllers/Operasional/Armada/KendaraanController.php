<?php

namespace App\Http\Controllers\Operasional\Armada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\Kendaraan;
use App\Models\Master\JenisAset;
use App\Helpers\GeneratorKodeOtomatis;
use Carbon\Carbon;

class KendaraanController extends Controller
{
    /**
     * Tampilkan halaman data master kendaraan truk armada ekspedisi & data jenis aset (Tab Terpadu).
     */
    public function index(Request $request)
    {
        // Pastikan master jenis aset memiliki data dasar
        $this->pastikanJenisAsetTersedia();

        $tabAktif = $request->input('tab', 'kendaraan');
        $kataKunci = $request->input('cari');
        $statusFilter = $request->input('status', 'semua');
        $jenisFilter = $request->input('jenis', 'semua');

        // Query Data Kendaraan
        $queryKendaraan = Kendaraan::with('jenisAset');

        if (!empty($kataKunci) && $tabAktif === 'kendaraan') {
            $queryKendaraan->where(function ($q) use ($kataKunci) {
                $q->where('nama_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('no_polisi', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('merek_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('no_mesin', 'like', "%{$kataKunci}%")
                  ->orWhere('no_rangka', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_pemilik', 'like', "%{$kataKunci}%")
                  ->orWhere('muatan', 'like', "%{$kataKunci}%");
            });
        }

        if ($statusFilter !== 'semua' && !empty($statusFilter)) {
            $queryKendaraan->where('status_aset', $statusFilter);
        }

        if ($jenisFilter !== 'semua' && !empty($jenisFilter)) {
            $queryKendaraan->where('kode_jenis_aset', $jenisFilter);
        }

        $daftarKendaraan = $queryKendaraan->orderBy('dibuat_pada', 'desc')->get();

        // 4 KPI Statistik Kendaraan
        $semuaKendaraan = Kendaraan::all();
        $totalKendaraan = $semuaKendaraan->count();
        $kendaraanAktif = $semuaKendaraan->where('status_aset', 'aktif')->count();
        $kendaraanServis = $semuaKendaraan->whereIn('status_aset', ['dalam_perbaikan', 'rusak'])->count();

        // Hitung kendaraan dengan KIR/Pajak mendekati jatuh tempo (<= 30 hari atau lewat)
        $batasPeringatan = Carbon::today()->addDays(30);
        $kendaraanPerhatianPajakKir = $semuaKendaraan->filter(function ($k) use ($batasPeringatan) {
            $kirPerhatian = $k->tanggal_kir && Carbon::parse($k->tanggal_kir)->lte($batasPeringatan);
            $pajakPerhatian = $k->tanggal_pajak && Carbon::parse($k->tanggal_pajak)->lte($batasPeringatan);
            return $kirPerhatian || $pajakPerhatian;
        })->count();

        // Query Data Jenis Aset (Kategori)
        $queryJenis = JenisAset::withCount('kendaraan');
        if (!empty($kataKunci) && $tabAktif === 'jenis_aset') {
            $queryJenis->where(function ($q) use ($kataKunci) {
                $q->where('jenis_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_jenis_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('keterangan', 'like', "%{$kataKunci}%");
            });
        }
        $daftarJenisAset = $queryJenis->orderBy('dibuat_pada', 'desc')->get();
        $totalJenisAset = JenisAset::count();

        // Statistik Aset Perusahaan (persis seperti di sidebar Aset Perusahaan SPV Keuangan)
        $totalNilaiAset = DB::table('data_aset')->sum('harga_aset');
        $totalAset = DB::table('data_aset')->count();
        $totalTrukAktif = DB::table('data_aset')->whereNotNull('no_polisi')->where('no_polisi', '!=', '-')->count();

        // Generator kode aset otomatis
        $kodeAsetOtomatis = GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3);

        // Query Data Aset Perusahaan untuk Tab Jenis Aset
        $queryAsetPerusahaan = DB::table('data_aset')
            ->leftJoin('data_jenis_aset', 'data_aset.kode_jenis_aset', '=', 'data_jenis_aset.kode_jenis_aset')
            ->select('data_aset.*', 'data_jenis_aset.jenis_aset');

        if (!empty($kataKunci) && $tabAktif === 'jenis_aset') {
            $queryAsetPerusahaan->where(function ($q) use ($kataKunci) {
                $q->where('data_aset.nama_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('data_aset.kode_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('data_aset.no_polisi', 'like', "%{$kataKunci}%");
            });
        }

        if ($jenisFilter !== 'semua' && !empty($jenisFilter) && $tabAktif === 'jenis_aset') {
            $queryAsetPerusahaan->where('data_aset.kode_jenis_aset', $jenisFilter);
        }

        $daftarAsetPerusahaan = $queryAsetPerusahaan->orderBy('data_aset.kode_aset', 'asc')->get();
        $daftarSemuaJenis = DB::table('data_jenis_aset')->orderBy('jenis_aset')->get();

        return view('operasional.armada.kendaraan', compact(
            'tabAktif',
            'daftarKendaraan',
            'daftarJenisAset',
            'daftarAsetPerusahaan',
            'daftarSemuaJenis',
            'kataKunci',
            'statusFilter',
            'jenisFilter',
            'totalKendaraan',
            'kendaraanAktif',
            'kendaraanServis',
            'kendaraanPerhatianPajakKir',
            'totalJenisAset',
            'totalNilaiAset',
            'totalAset',
            'totalTrukAktif',
            'kodeAsetOtomatis'
        ));
    }

    /**
     * Simpan data kendaraan armada baru ke database.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'kode_aset.required' => 'Kode aset kendaraan wajib diisi.',
            'kode_aset.unique' => 'Kode aset sudah terdaftar di database.',
            'kode_jenis_aset.required' => 'Jenis aset wajib dipilih.',
            'kode_jenis_aset.exists' => 'Jenis aset tidak valid.',
            'nama_aset.required' => 'Nama model aset truk wajib diisi.',
            'no_polisi.required' => 'Nomor plat polisi wajib diisi.',
            'tanggal_pembelian.required' => 'Tanggal pembelian armada wajib diisi.',
            'harga_aset.required' => 'Harga perolehan aset wajib diisi.',
            'harga_aset.numeric' => 'Harga aset harus berupa angka.',
            'no_mesin.required' => 'Nomor mesin kendaraan wajib diisi.',
            'no_rangka.required' => 'Nomor rangka kendaraan wajib diisi.',
            'merek_aset.required' => 'Merek aset truk wajib diisi.',
            'muatan.required' => 'Kapasitas muatan wajib diisi.',
            'tahun_pembuatan.required' => 'Tahun pembuatan wajib diisi.',
            'status_aset.required' => 'Status operasional armada wajib dipilih.',
            'nama_pemilik.required' => 'Nama pemilik armada wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_aset' => 'required|string|max:30|unique:data_aset,kode_aset',
            'kode_jenis_aset' => 'required|string|max:30|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset' => 'required|string|max:100',
            'no_polisi' => 'required|string|max:20',
            'tanggal_pembelian' => 'required|date',
            'harga_aset' => 'required|numeric|min:0',
            'no_mesin' => 'required|string|max:50',
            'no_rangka' => 'required|string|max:50',
            'merek_aset' => 'required|string|max:50',
            'muatan' => 'required|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'required|integer|min:1990|max:2099',
            'tanggal_kir' => 'nullable|date',
            'tanggal_pajak' => 'nullable|date',
            'status_aset' => 'required|in:aktif,rusak,dalam_perbaikan,dijual,non-aktif',
            'nama_pemilik' => 'required|string|max:100',
        ], $pesanKustom);

        Kendaraan::create([
            'kode_aset' => trim($validated['kode_aset']),
            'kode_jenis_aset' => $validated['kode_jenis_aset'],
            'nama_aset' => trim($validated['nama_aset']),
            'no_polisi' => strtoupper(trim($validated['no_polisi'])),
            'tanggal_pembelian' => $validated['tanggal_pembelian'],
            'harga_aset' => $validated['harga_aset'],
            'no_mesin' => strtoupper(trim($validated['no_mesin'])),
            'no_rangka' => strtoupper(trim($validated['no_rangka'])),
            'merek_aset' => trim($validated['merek_aset']),
            'muatan' => trim($validated['muatan']),
            'jenis_kendaraan' => trim($validated['jenis_kendaraan'] ?? $validated['nama_aset']),
            'tahun_pembuatan' => $validated['tahun_pembuatan'],
            'tanggal_kir' => $validated['tanggal_kir'] ?? null,
            'tanggal_pajak' => $validated['tanggal_pajak'] ?? null,
            'status_aset' => $validated['status_aset'],
            'nama_pemilik' => trim($validated['nama_pemilik']),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
            ->with('sukses', "Data armada {$validated['nama_aset']} ({$validated['no_polisi']}) berhasil ditambahkan ke database!");
    }

    /**
     * Ambil data detail kendaraan dalam format JSON untuk modal Alpine.js.
     */
    public function ambilDetail($kode_aset)
    {
        $kendaraan = Kendaraan::with('jenisAset')->where('kode_aset', $kode_aset)->first();

        if (!$kendaraan) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data kendaraan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $kendaraan
        ]);
    }

    /**
     * Perbarui data kendaraan armada di database.
     */
    public function perbarui(Request $request, $kode_aset)
    {
        $kendaraan = Kendaraan::where('kode_aset', $kode_aset)->firstOrFail();

        $pesanKustom = [
            'kode_jenis_aset.required' => 'Jenis aset wajib dipilih.',
            'kode_jenis_aset.exists' => 'Jenis aset tidak valid.',
            'nama_aset.required' => 'Nama model aset truk wajib diisi.',
            'no_polisi.required' => 'Nomor plat polisi wajib diisi.',
            'tanggal_pembelian.required' => 'Tanggal pembelian armada wajib diisi.',
            'harga_aset.required' => 'Harga perolehan aset wajib diisi.',
            'harga_aset.numeric' => 'Harga aset harus berupa angka.',
            'no_mesin.required' => 'Nomor mesin kendaraan wajib diisi.',
            'no_rangka.required' => 'Nomor rangka kendaraan wajib diisi.',
            'merek_aset.required' => 'Merek aset truk wajib diisi.',
            'muatan.required' => 'Kapasitas muatan wajib diisi.',
            'tahun_pembuatan.required' => 'Tahun pembuatan wajib diisi.',
            'status_aset.required' => 'Status operasional armada wajib dipilih.',
            'nama_pemilik.required' => 'Nama pemilik armada wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_jenis_aset' => 'required|string|max:30|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset' => 'required|string|max:100',
            'no_polisi' => 'required|string|max:20',
            'tanggal_pembelian' => 'required|date',
            'harga_aset' => 'required|numeric|min:0',
            'no_mesin' => 'required|string|max:50',
            'no_rangka' => 'required|string|max:50',
            'merek_aset' => 'required|string|max:50',
            'muatan' => 'required|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'required|integer|min:1990|max:2099',
            'tanggal_kir' => 'nullable|date',
            'tanggal_pajak' => 'nullable|date',
            'status_aset' => 'required|in:aktif,rusak,dalam_perbaikan,dijual,non-aktif',
            'nama_pemilik' => 'required|string|max:100',
        ], $pesanKustom);

        $kendaraan->update([
            'kode_jenis_aset' => $validated['kode_jenis_aset'],
            'nama_aset' => trim($validated['nama_aset']),
            'no_polisi' => strtoupper(trim($validated['no_polisi'])),
            'tanggal_pembelian' => $validated['tanggal_pembelian'],
            'harga_aset' => $validated['harga_aset'],
            'no_mesin' => strtoupper(trim($validated['no_mesin'])),
            'no_rangka' => strtoupper(trim($validated['no_rangka'])),
            'merek_aset' => trim($validated['merek_aset']),
            'muatan' => trim($validated['muatan']),
            'jenis_kendaraan' => trim($validated['jenis_kendaraan'] ?? $validated['nama_aset']),
            'tahun_pembuatan' => $validated['tahun_pembuatan'],
            'tanggal_kir' => $validated['tanggal_kir'] ?? null,
            'tanggal_pajak' => $validated['tanggal_pajak'] ?? null,
            'status_aset' => $validated['status_aset'],
            'nama_pemilik' => trim($validated['nama_pemilik']),
            'diperbarui_pada' => now(),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
            ->with('sukses', "Data kendaraan {$kendaraan->nama_aset} ({$kendaraan->no_polisi}) berhasil diperbarui!");
    }

    /**
     * Hapus data kendaraan dari database.
     */
    public function hapus($kode_aset)
    {
        $kendaraan = Kendaraan::where('kode_aset', $kode_aset)->firstOrFail();
        $namaAset = $kendaraan->nama_aset;
        $noPolisi = $kendaraan->no_polisi;

        try {
            $kendaraan->delete();

            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('sukses', "Data armada {$namaAset} ({$noPolisi}) berhasil dihapus dari database! Slot kode {$kode_aset} kini siap didaur ulang.");
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('error', "Gagal menghapus kendaraan {$namaAset}! Data armada ini masih terikat dengan dokumen Surat Jalan, Pengiriman, atau SPK Servis Bengkel.");
        }
    }

    /**
     * Helper endpoint generator kode kendaraan otomatis (Gap-filling & Acak).
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
                $kandidat = 'TRK-' . $acak;
                $sudahAda = DB::table('data_aset')->where('kode_aset', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('TRK-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Kode Alfanumerik Acak (Anti-Tebak)'
            ]);
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarAset = DB::table('data_aset')
            ->where('kode_aset', 'like', 'TRK-%')
            ->pluck('kode_aset');

        $nomorTerpakai = [];
        foreach ($daftarAset as $kode) {
            if (preg_match('/TRK-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'TRK-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    // =========================================================================
    // SUB-FITUR: CRUD DATA JENIS ASET DI DALAM DATA KENDARAAN
    // =========================================================================

    /**
     * Simpan data jenis aset baru.
     */
    public function simpanJenisAset(Request $request)
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

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Kategori Jenis Aset {$validated['jenis_aset']} ({$validated['kode_jenis_aset']}) berhasil ditambahkan!");
    }

    /**
     * Ambil data detail jenis aset untuk modal Alpine.js.
     */
    public function ambilDetailJenisAset($kode_jenis_aset)
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
    public function perbaruiJenisAset(Request $request, $kode_jenis_aset)
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

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Data kategori {$jenisAset->jenis_aset} ({$jenisAset->kode_jenis_aset}) berhasil diperbarui!");
    }

    /**
     * Hapus data jenis aset.
     */
    public function hapusJenisAset($kode_jenis_aset)
    {
        $jenisAset = JenisAset::where('kode_jenis_aset', $kode_jenis_aset)->firstOrFail();
        $namaJenis = $jenisAset->jenis_aset;

        $jumlahUnitTerhubung = Kendaraan::where('kode_jenis_aset', $kode_jenis_aset)->count();
        if ($jumlahUnitTerhubung > 0) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('error', "Gagal menghapus kategori {$namaJenis}! Terdapat {$jumlahUnitTerhubung} unit armada truk yang masih terhubung ke jenis aset ini.");
        }

        try {
            $jenisAset->delete();

            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('sukses', "Kategori jenis aset {$namaJenis} ({$kode_jenis_aset}) berhasil dihapus dari sistem!");
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('error', "Gagal menghapus jenis aset {$namaJenis}! Data masih memiliki dependensi di sistem.");
        }
    }

    /**
     * Generator kode otomatis untuk jenis aset.
     */
    public function buatKodeJenisAsetOtomatis(Request $request)
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
     * Simpan data aset perusahaan baru dari sub-fitur di Data Kendaraan.
     */
    public function simpanAset(Request $request)
    {
        if (!$request->filled('kode_aset')) {
            $request->merge([
                'kode_aset' => GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3)
            ]);
        }

        $pesanKustom = [
            'kode_aset.required' => 'Kode aset wajib diisi.',
            'kode_aset.unique' => 'Kode aset sudah terdaftar.',
            'kode_jenis_aset.required' => 'Jenis aset wajib dipilih.',
            'kode_jenis_aset.exists' => 'Jenis aset tidak valid.',
            'nama_aset.required' => 'Nama aset wajib diisi.',
            'tanggal_pembelian.required' => 'Tanggal pembelian wajib diisi.',
            'harga_aset.required' => 'Harga perolehan aset wajib diisi.',
            'harga_aset.numeric' => 'Harga aset harus berupa angka.',
        ];

        $validated = $request->validate([
            'kode_aset'         => 'required|string|max:30|unique:data_aset,kode_aset',
            'kode_jenis_aset'   => 'required|string|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset'         => 'required|string|max:100',
            'no_polisi'         => 'nullable|string|max:20',
            'tanggal_pembelian' => 'required|date',
            'harga_aset'        => 'required|numeric|min:0',
        ], $pesanKustom);

        DB::table('data_aset')->insert([
            'kode_aset'         => strtoupper(trim($validated['kode_aset'])),
            'kode_jenis_aset'   => $validated['kode_jenis_aset'],
            'nama_aset'         => trim($validated['nama_aset']),
            'tanggal_pembelian' => $validated['tanggal_pembelian'],
            'harga_aset'        => $validated['harga_aset'],
            'no_polisi'         => !empty($validated['no_polisi']) ? strtoupper(trim($validated['no_polisi'])) : '-',
            'merek_aset'        => $request->merek_aset ?? '-',
            'jenis_kendaraan'   => $request->jenis_kendaraan ?? '-',
            'muatan'            => $request->muatan ?? '-',
            'status_aset'       => $request->status_aset ?? 'aktif',
            'nama_pemilik'      => $request->nama_pemilik ?? 'PT Putra Balkom Jaya',
            'dibuat_pada'       => now(),
            'diperbarui_pada'   => now(),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Aset {$validated['nama_aset']} ({$validated['kode_aset']}) berhasil didaftarkan ke inventaris aset perusahaan!");
    }

    /**
     * Ambil detail data aset untuk modal Alpine.js di Data Kendaraan.
     */
    public function ambilDetailAset($kode_aset)
    {
        $aset = DB::table('data_aset')
            ->leftJoin('data_jenis_aset', 'data_aset.kode_jenis_aset', '=', 'data_jenis_aset.kode_jenis_aset')
            ->select('data_aset.*', 'data_jenis_aset.jenis_aset')
            ->where('data_aset.kode_aset', $kode_aset)
            ->first();

        if (!$aset) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data aset tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $aset
        ]);
    }

    /**
     * Perbarui data aset perusahaan di Data Kendaraan.
     */
    public function perbaruiAset(Request $request, $kode_aset)
    {
        $pesanKustom = [
            'kode_jenis_aset.required' => 'Jenis aset wajib dipilih.',
            'nama_aset.required' => 'Nama aset wajib diisi.',
            'tanggal_pembelian.required' => 'Tanggal pembelian wajib diisi.',
            'harga_aset.required' => 'Harga perolehan aset wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_jenis_aset'   => 'required|string|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset'         => 'required|string|max:100',
            'no_polisi'         => 'nullable|string|max:20',
            'tanggal_pembelian' => 'required|date',
            'harga_aset'        => 'required|numeric|min:0',
            'status_aset'       => 'nullable|string|in:aktif,non-aktif,dalam_perbaikan,rusak,dijual',
        ], $pesanKustom);

        DB::table('data_aset')->where('kode_aset', $kode_aset)->update([
            'kode_jenis_aset'   => $validated['kode_jenis_aset'],
            'nama_aset'         => trim($validated['nama_aset']),
            'tanggal_pembelian' => $validated['tanggal_pembelian'],
            'harga_aset'        => $validated['harga_aset'],
            'no_polisi'         => !empty($validated['no_polisi']) ? strtoupper(trim($validated['no_polisi'])) : '-',
            'status_aset'       => $validated['status_aset'] ?? 'aktif',
            'diperbarui_pada'   => now(),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Data aset {$validated['nama_aset']} ({$kode_aset}) berhasil diperbarui!");
    }

    /**
     * Hapus data aset perusahaan di Data Kendaraan.
     */
    public function hapusAset($kode_aset)
    {
        $aset = DB::table('data_aset')->where('kode_aset', $kode_aset)->first();
        if (!$aset) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('error', 'Data aset tidak ditemukan.');
        }

        DB::table('data_aset')->where('kode_aset', $kode_aset)->delete();

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Aset {$aset->nama_aset} ({$kode_aset}) berhasil dihapus!");
    }

    /**
     * Generator kode otomatis untuk aset perusahaan.
     */
    public function buatKodeAsetOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $acak = '';
            for ($i = 0; $i < 4; $i++) {
                $acak .= $karakter[random_int(0, strlen($karakter) - 1)];
            }
            return response()->json([
                'status' => 'sukses',
                'kode_otomatis' => 'AST-' . $acak,
                'keterangan' => 'Kode Acak Anti-Tebak'
            ]);
        }

        $kodeBaru = GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3);
        return response()->json([
            'status' => 'sukses',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia'
        ]);
    }

    /**
     * Helper inisialisasi data jenis aset dan data awal jika kosong.
     */
    private function pastikanJenisAsetTersedia(): void
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

        // Jika data_aset masih kosong, tambahkan sample data awal
        $jumlahAset = DB::table('data_aset')->count();
        if ($jumlahAset === 0) {
            $contohUnit = [
                [
                    'kode_aset' => 'TRK-001',
                    'kode_jenis_aset' => 'KND-TRN',
                    'nama_aset' => 'Hino 500 Tronton Wingbox FL 260',
                    'tanggal_pembelian' => '2023-03-15',
                    'harga_aset' => 1250000000.00,
                    'no_polisi' => 'B 9283 TDF',
                    'no_mesin' => 'J08E-UG-12948',
                    'no_rangka' => 'MJECFL8J7N-039182',
                    'merek_aset' => 'Hino',
                    'muatan' => '25 Ton (500 Zak)',
                    'jenis_kendaraan' => 'Tronton Wingbox',
                    'tahun_pembuatan' => 2023,
                    'tanggal_kir' => Carbon::today()->addMonths(4)->format('Y-m-d'),
                    'tanggal_pajak' => Carbon::today()->addMonths(7)->format('Y-m-d'),
                    'status_aset' => 'aktif',
                    'nama_pemilik' => 'PT Putra Balkom Jaya',
                ],
                [
                    'kode_aset' => 'TRK-002',
                    'kode_jenis_aset' => 'KND-CDD',
                    'nama_aset' => 'Mitsubishi Canter FE 74 HD',
                    'tanggal_pembelian' => '2022-07-20',
                    'harga_aset' => 540000000.00,
                    'no_polisi' => 'B 8411 UQ',
                    'no_mesin' => '4D34-T9921',
                    'no_rangka' => 'MHMFE74HD-082194',
                    'merek_aset' => 'Mitsubishi Fuso',
                    'muatan' => '15 Ton (300 Zak)',
                    'jenis_kendaraan' => 'Colt Diesel Double',
                    'tahun_pembuatan' => 2022,
                    'tanggal_kir' => Carbon::today()->addDays(15)->format('Y-m-d'),
                    'tanggal_pajak' => Carbon::today()->addMonths(2)->format('Y-m-d'),
                    'status_aset' => 'aktif',
                    'nama_pemilik' => 'PT Putra Balkom Jaya',
                ],
                [
                    'kode_aset' => 'TRK-003',
                    'kode_jenis_aset' => 'KND-TKG',
                    'nama_aset' => 'Isuzu GIGA FVM 240 Tangki Curah',
                    'tanggal_pembelian' => '2021-11-10',
                    'harga_aset' => 1400000000.00,
                    'no_polisi' => 'B 9042 KPB',
                    'no_mesin' => '6HK1-TCN-84920',
                    'no_rangka' => 'MP3FVM34N-028491',
                    'merek_aset' => 'Isuzu',
                    'muatan' => '32 Ton Semen Curah',
                    'jenis_kendaraan' => 'Tangki Curah Bulk',
                    'tahun_pembuatan' => 2021,
                    'tanggal_kir' => Carbon::today()->subDays(5)->format('Y-m-d'),
                    'tanggal_pajak' => Carbon::today()->addMonths(5)->format('Y-m-d'),
                    'status_aset' => 'dalam_perbaikan',
                    'nama_pemilik' => 'PT Putra Balkom Jaya',
                ],
            ];

            foreach ($contohUnit as $unit) {
                DB::table('data_aset')->insert(array_merge($unit, [
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]));
            }
        }
    }
}
