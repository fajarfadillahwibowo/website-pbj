<?php

namespace App\Http\Controllers\Operasional\KSO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Operasional\KSO;
use App\Models\Operasional\OngkosKSO;
use Carbon\Carbon;

class KSOController extends Controller
{
    /**
     * Tampilkan antarmuka manajemen Data KSO & Ongkos Angkut KSO.
     */
    public function index(Request $request)
    {
        $this->pastikanDataAwalTersedia();

        $tabAktif = $request->input('tab', 'kso'); // 'kso' atau 'ongkos'

        // 1. Query Data Mitra KSO
        $cariKso = $request->input('cari_kso');
        $statusKsoFilter = $request->input('status_kso', 'semua');

        $queryKso = KSO::with('daftarOngkosKso');
        if (!empty($cariKso)) {
            $queryKso->where(function ($q) use ($cariKso) {
                $q->where('kode_kso', 'like', "%{$cariKso}%")
                  ->orWhere('nama_kso', 'like', "%{$cariKso}%")
                  ->orWhere('pihak_mitra', 'like', "%{$cariKso}%")
                  ->orWhere('keterangan', 'like', "%{$cariKso}%");
            });
        }
        if ($statusKsoFilter !== 'semua' && !empty($statusKsoFilter)) {
            $queryKso->where('status_kso', $statusKsoFilter);
        }
        $daftarKso = $queryKso->orderBy('diperbarui_pada', 'desc')->get();

        // 2. Query Data Ongkos Angkut KSO
        $cariOa = $request->input('cari_oa');
        $filterKso = $request->input('filter_kso', 'semua');

        $queryOa = OngkosKSO::with('mitraKso');
        if (!empty($cariOa)) {
            $queryOa->where(function ($q) use ($cariOa) {
                $q->where('kode_oa', 'like', "%{$cariOa}%")
                  ->orWhere('nama_oa', 'like', "%{$cariOa}%")
                  ->orWhere('muatan', 'like', "%{$cariOa}%")
                  ->orWhere('kode_kso', 'like', "%{$cariOa}%")
                  ->orWhereHas('mitraKso', function ($qM) use ($cariOa) {
                      $qM->where('nama_kso', 'like', "%{$cariOa}%")
                         ->orWhere('pihak_mitra', 'like', "%{$cariOa}%");
                  });
            });
        }
        if ($filterKso !== 'semua' && !empty($filterKso)) {
            $queryOa->where('kode_kso', $filterKso);
        }
        $daftarOngkosKso = $queryOa->orderBy('diperbarui_pada', 'desc')->get();

        // 3. 4 Kartu KPI Ringkasan KSO
        $semuaKso = KSO::all();
        $totalKso = $semuaKso->count();
        $ksoAktif = $semuaKso->where('status_kso', 'Aktif')->count();
        $totalRuteOa = OngkosKSO::count();
        $totalNilaiKontrak = $semuaKso->where('status_kso', 'Aktif')->sum('nilai_kontrak');

        // Master KSO untuk dropdown
        $pilihanMitraKso = KSO::orderBy('nama_kso', 'asc')->get();

        return view('operasional.kso.index', compact(
            'tabAktif',
            'daftarKso',
            'daftarOngkosKso',
            'cariKso',
            'statusKsoFilter',
            'cariOa',
            'filterKso',
            'totalKso',
            'ksoAktif',
            'totalRuteOa',
            'totalNilaiKontrak',
            'pilihanMitraKso'
        ));
    }

    // =========================================================================
    // SECTION A: CRUD DATA MITRA KSO (data_kso)
    // =========================================================================

    /**
     * Simpan Data Mitra KSO baru.
     */
    public function simpanKSO(Request $request)
    {
        $pesanKustom = [
            'kode_kso.required' => 'Kode KSO wajib diisi.',
            'kode_kso.unique' => 'Kode KSO sudah terdaftar.',
            'nama_kso.required' => 'Nama KSO wajib diisi.',
            'pihak_mitra.required' => 'Nama pihak mitra KSO wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai kontrak wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai kontrak wajib diisi.',
            'status_kso.required' => 'Status KSO wajib dipilih.',
            'file_kontrak_kso.mimes' => 'File kontrak harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak_kso.max' => 'Ukuran file kontrak maksimal 5MB.',
        ];

        $validated = $request->validate([
            'kode_kso' => 'required|string|max:30|unique:data_kso,kode_kso',
            'nama_kso' => 'required|string|max:100',
            'pihak_mitra' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'nilai_kontrak' => 'nullable|numeric|min:0',
            'status_kso' => 'required|in:Aktif,Selesai,Ditangguhkan',
            'keterangan' => 'nullable|string',
            'file_kontrak_kso' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], $pesanKustom);

        $pathFileKontrak = null;
        if ($request->hasFile('file_kontrak_kso')) {
            $pathFileKontrak = $request->file('file_kontrak_kso')->store('kontrak_kso', 'public');
        }

        $kso = KSO::create([
            'kode_kso' => strtoupper(trim($validated['kode_kso'])),
            'nama_kso' => trim($validated['nama_kso']),
            'pihak_mitra' => trim($validated['pihak_mitra']),
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'nilai_kontrak' => $validated['nilai_kontrak'] ?? 0,
            'status_kso' => $validated['status_kso'],
            'keterangan' => $validated['keterangan'] ? trim($validated['keterangan']) : null,
            'file_kontrak_kso' => $pathFileKontrak,
        ]);

        return redirect()->route('operasional.kso', ['tab' => 'kso'])
            ->with('sukses', "Data Mitra KSO [{$kso->kode_kso}] {$kso->nama_kso} berhasil disimpan!");
    }

    /**
     * Ambil Detail Mitra KSO (JSON).
     */
    public function ambilDetailKSO($kode_kso)
    {
        $kso = KSO::with('daftarOngkosKso')->where('kode_kso', $kode_kso)->first();

        if (!$kso) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data KSO tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $kso
        ]);
    }

    /**
     * Perbarui Data Mitra KSO.
     */
    public function perbaruiKSO(Request $request, $kode_kso)
    {
        $kso = KSO::findOrFail($kode_kso);

        $pesanKustom = [
            'nama_kso.required' => 'Nama KSO wajib diisi.',
            'pihak_mitra.required' => 'Nama pihak mitra KSO wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai kontrak wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai kontrak wajib diisi.',
            'status_kso.required' => 'Status KSO wajib dipilih.',
            'file_kontrak_kso.mimes' => 'File kontrak harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak_kso.max' => 'Ukuran file kontrak maksimal 5MB.',
        ];

        $validated = $request->validate([
            'nama_kso' => 'required|string|max:100',
            'pihak_mitra' => 'required|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'nilai_kontrak' => 'nullable|numeric|min:0',
            'status_kso' => 'required|in:Aktif,Selesai,Ditangguhkan',
            'keterangan' => 'nullable|string',
            'file_kontrak_kso' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], $pesanKustom);

        $pathFileKontrak = $kso->file_kontrak_kso;
        if ($request->hasFile('file_kontrak_kso')) {
            if ($kso->file_kontrak_kso && Storage::disk('public')->exists($kso->file_kontrak_kso)) {
                Storage::disk('public')->delete($kso->file_kontrak_kso);
            }
            $pathFileKontrak = $request->file('file_kontrak_kso')->store('kontrak_kso', 'public');
        }

        $kso->update([
            'nama_kso' => trim($validated['nama_kso']),
            'pihak_mitra' => trim($validated['pihak_mitra']),
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'nilai_kontrak' => $validated['nilai_kontrak'] ?? 0,
            'status_kso' => $validated['status_kso'],
            'keterangan' => $validated['keterangan'] ? trim($validated['keterangan']) : null,
            'file_kontrak_kso' => $pathFileKontrak,
        ]);

        return redirect()->route('operasional.kso', ['tab' => 'kso'])
            ->with('sukses', "Data Mitra KSO [{$kso->kode_kso}] berhasil diperbarui!");
    }

    /**
     * Hapus Data Mitra KSO.
     */
    public function hapusKSO($kode_kso)
    {
        $kso = KSO::findOrFail($kode_kso);
        $namaKso = $kso->nama_kso;

        if ($kso->file_kontrak_kso && Storage::disk('public')->exists($kso->file_kontrak_kso)) {
            Storage::disk('public')->delete($kso->file_kontrak_kso);
        }

        $kso->delete();

        return redirect()->route('operasional.kso', ['tab' => 'kso'])
            ->with('sukses', "Data Mitra KSO [{$kode_kso}] {$namaKso} beserta seluruh rute ongkos angkut terkait berhasil dihapus!");
    }

    /**
     * Generator Kode KSO Otomatis (Daur Ulang Slot vs Format Tanggal/Acak).
     */
    public function buatKodeKSO(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        $tahun = date('Y');

        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $panjang = strlen($karakter);
            $kodeUnik = null;
            $percobaan = 0;

            do {
                $acak = '';
                for ($i = 0; $i < 3; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'KSO-' . $tahun . '-' . $acak;
                $sudahAda = DB::table('data_kso')->where('kode_kso', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('KSO-' . $tahun . '-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Tahun & Acak Anti-Tebak'
            ]);
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarKso = DB::table('data_kso')
            ->where('kode_kso', 'like', 'KSO-%')
            ->pluck('kode_kso');

        $nomorTerpakai = [];
        foreach ($daftarKso as $kode) {
            if (preg_match('/KSO-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'KSO-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    // =========================================================================
    // SECTION B: CRUD ONGKOS ANGKUT KSO (ongkos_kso)
    // =========================================================================

    /**
     * Simpan Data Rute Ongkos Angkut KSO baru.
     */
    public function simpanOngkos(Request $request)
    {
        $pesanKustom = [
            'kode_oa.required' => 'Kode OA wajib diisi.',
            'kode_oa.unique' => 'Kode OA sudah terdaftar.',
            'kode_kso.required' => 'Mitra KSO wajib dipilih.',
            'kode_kso.exists' => 'Mitra KSO tidak valid.',
            'nama_oa.required' => 'Nama rute trayek OA wajib diisi.',
            'muatan.required' => 'Kapasitas muatan armada wajib diisi.',
            'ongkos_angkut.required' => 'Tarif ongkos angkut wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_oa' => 'required|string|max:30|unique:ongkos_kso,kode_oa',
            'kode_kso' => 'required|string|max:30|exists:data_kso,kode_kso',
            'nama_oa' => 'required|string|max:100',
            'muatan' => 'required|string|max:50',
            'ongkos_angkut' => 'required|numeric|min:0',
        ], $pesanKustom);

        $oa = OngkosKSO::create([
            'kode_oa' => strtoupper(trim($validated['kode_oa'])),
            'kode_kso' => $validated['kode_kso'],
            'nama_oa' => trim($validated['nama_oa']),
            'muatan' => trim($validated['muatan']),
            'ongkos_angkut' => $validated['ongkos_angkut'],
        ]);

        return redirect()->route('operasional.kso', ['tab' => 'ongkos'])
            ->with('sukses', "Tarif Ongkos Angkut KSO [{$oa->kode_oa}] {$oa->nama_oa} berhasil disimpan!");
    }

    /**
     * Ambil Detail Ongkos Angkut KSO (JSON).
     */
    public function ambilDetailOngkos($kode_oa)
    {
        $oa = OngkosKSO::with('mitraKso')->where('kode_oa', $kode_oa)->first();

        if (!$oa) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data Ongkos Angkut KSO tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $oa
        ]);
    }

    /**
     * Perbarui Data Rute Ongkos Angkut KSO.
     */
    public function perbaruiOngkos(Request $request, $kode_oa)
    {
        $oa = OngkosKSO::findOrFail($kode_oa);

        $pesanKustom = [
            'kode_kso.required' => 'Mitra KSO wajib dipilih.',
            'kode_kso.exists' => 'Mitra KSO tidak valid.',
            'nama_oa.required' => 'Nama rute trayek OA wajib diisi.',
            'muatan.required' => 'Kapasitas muatan armada wajib diisi.',
            'ongkos_angkut.required' => 'Tarif ongkos angkut wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_kso' => 'required|string|max:30|exists:data_kso,kode_kso',
            'nama_oa' => 'required|string|max:100',
            'muatan' => 'required|string|max:50',
            'ongkos_angkut' => 'required|numeric|min:0',
        ], $pesanKustom);

        $oa->update([
            'kode_kso' => $validated['kode_kso'],
            'nama_oa' => trim($validated['nama_oa']),
            'muatan' => trim($validated['muatan']),
            'ongkos_angkut' => $validated['ongkos_angkut'],
        ]);

        return redirect()->route('operasional.kso', ['tab' => 'ongkos'])
            ->with('sukses', "Data Tarif Ongkos Angkut [{$oa->kode_oa}] berhasil diperbarui!");
    }

    /**
     * Hapus Data Ongkos Angkut KSO.
     */
    public function hapusOngkos($kode_oa)
    {
        $oa = OngkosKSO::findOrFail($kode_oa);
        $namaOa = $oa->nama_oa;

        $oa->delete();

        return redirect()->route('operasional.kso', ['tab' => 'ongkos'])
            ->with('sukses', "Tarif Ongkos Angkut [{$kode_oa}] {$namaOa} berhasil dihapus!");
    }

    /**
     * Generator Kode OA Otomatis (Daur Ulang Slot vs Format Acak).
     */
    public function buatKodeOA(Request $request)
    {
        $mode = $request->input('mode', 'gap');

        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $panjang = strlen($karakter);
            $kodeUnik = null;
            $percobaan = 0;

            do {
                $acak = '';
                for ($i = 0; $i < 3; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'OAK-' . $acak;
                $sudahAda = DB::table('ongkos_kso')->where('kode_oa', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('OAK-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Acak Anti-Tebak'
            ]);
        }

        // Mode GAP FILLING
        $daftarOa = DB::table('ongkos_kso')
            ->where('kode_oa', 'like', 'OAK-%')
            ->pluck('kode_oa');

        $nomorTerpakai = [];
        foreach ($daftarOa as $kode) {
            if (preg_match('/OAK-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'OAK-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan data KSO dan Ongkos KSO awal tersedia.
     */
    private function pastikanDataAwalTersedia(): void
    {
        $jumlahKso = DB::table('data_kso')->count();
        if ($jumlahKso === 0) {
            DB::table('data_kso')->insert([
                [
                    'kode_kso' => 'KSO-001',
                    'nama_kso' => 'KSO Angkutan Semen Mitra Logistik Sentosa',
                    'pihak_mitra' => 'PT Mitra Logistik Sentosa',
                    'status_kso' => 'Aktif',
                    'tanggal_mulai' => '2026-01-01',
                    'tanggal_selesai' => '2026-12-31',
                    'nilai_kontrak' => 850000000,
                    'keterangan' => 'Kerja sama sewa armada tronton dan distribusi semen sak area Jawa Barat & Banten.',
                    'dibuat_pada' => Carbon::now()->subMonths(2),
                    'diperbarui_pada' => Carbon::now()->subDays(3),
                ],
                [
                    'kode_kso' => 'KSO-002',
                    'nama_kso' => 'KSO Armada Ekspedisi Berkah Bersama',
                    'pihak_mitra' => 'CV Berkah Bersama Trans',
                    'status_kso' => 'Aktif',
                    'tanggal_mulai' => '2026-02-15',
                    'tanggal_selesai' => '2027-02-14',
                    'nilai_kontrak' => 450000000,
                    'keterangan' => 'KSO pengadaan truk colt diesel double untuk pengiriman toko bangunan Jabodetabek.',
                    'dibuat_pada' => Carbon::now()->subMonths(1),
                    'diperbarui_pada' => Carbon::now()->subHours(5),
                ],
            ]);
        }

        $jumlahOa = DB::table('ongkos_kso')->count();
        if ($jumlahOa === 0) {
            DB::table('ongkos_kso')->insert([
                [
                    'kode_oa' => 'OAK-001',
                    'kode_kso' => 'KSO-001',
                    'nama_oa' => 'Plant Cikarang ➔ Hub Distribusi Karawang',
                    'muatan' => 'Tronton 30 Ton (600 Zak)',
                    'ongkos_angkut' => 1850000,
                    'dibuat_pada' => Carbon::now()->subMonths(2),
                    'diperbarui_pada' => Carbon::now()->subDays(2),
                ],
                [
                    'kode_oa' => 'OAK-002',
                    'kode_kso' => 'KSO-001',
                    'nama_oa' => 'Plant Narogong ➔ Hub Bekasi Barat',
                    'muatan' => 'Tronton 30 Ton (600 Zak)',
                    'ongkos_angkut' => 1450000,
                    'dibuat_pada' => Carbon::now()->subMonths(2),
                    'diperbarui_pada' => Carbon::now()->subHours(12),
                ],
                [
                    'kode_oa' => 'OAK-003',
                    'kode_kso' => 'KSO-002',
                    'nama_oa' => 'Gudang Karawang ➔ Retail Toko Bangunan Cikampek',
                    'muatan' => 'CDD 8 Ton (160 Zak)',
                    'ongkos_angkut' => 650000,
                    'dibuat_pada' => Carbon::now()->subMonths(1),
                    'diperbarui_pada' => Carbon::now()->subHours(2),
                ],
            ]);
        }
    }
}
