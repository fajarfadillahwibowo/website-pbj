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

        $queryKso = KSO::with('daftarOngkosKso');
        if (!empty($cariKso)) {
            $queryKso->where(function ($q) use ($cariKso) {
                $q->where('kode_kso', 'like', "%{$cariKso}%")
                  ->orWhere('nama_kso', 'like', "%{$cariKso}%");
            });
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
                      $qM->where('nama_kso', 'like', "%{$cariOa}%");
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
        $totalKontrakAda = $semuaKso->whereNotNull('file_kontrak_kso')->count();
        $totalRuteOa = OngkosKSO::count();
        $rataOngkosKso = OngkosKSO::avg('ongkos_angkut') ?? 0;

        // Master KSO untuk dropdown
        $pilihanMitraKso = KSO::orderBy('nama_kso', 'asc')->get();

        return view('operasional.kso.index', compact(
            'tabAktif',
            'daftarKso',
            'daftarOngkosKso',
            'cariKso',
            'cariOa',
            'filterKso',
            'totalKso',
            'totalKontrakAda',
            'totalRuteOa',
            'rataOngkosKso',
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
            'file_kontrak_kso.mimes' => 'File kontrak harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak_kso.max' => 'Ukuran file kontrak maksimal 5MB.',
        ];

        $validated = $request->validate([
            'kode_kso' => 'required|string|max:30|unique:data_kso,kode_kso',
            'nama_kso' => 'required|string|max:100',
            'file_kontrak_kso' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], $pesanKustom);

        $pathFileKontrak = null;
        if ($request->hasFile('file_kontrak_kso')) {
            $pathFileKontrak = $request->file('file_kontrak_kso')->store('kontrak_kso', 'public');
        }

        DB::beginTransaction();
        try {
            $kso = KSO::create([
                'kode_kso' => strtoupper(trim($validated['kode_kso'])),
                'nama_kso' => trim($validated['nama_kso']),
                'file_kontrak_kso' => $pathFileKontrak,
            ]);

            DB::commit();

            return redirect()->route('operasional.kso', ['tab' => 'kso'])
                ->with('sukses', "Data Mitra KSO [{$kso->kode_kso}] {$kso->nama_kso} berhasil disimpan!");
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($pathFileKontrak && Storage::disk('public')->exists($pathFileKontrak)) {
                Storage::disk('public')->delete($pathFileKontrak);
            }
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan Data Mitra KSO: ' . $e->getMessage());
        }
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
            'file_kontrak_kso.mimes' => 'File kontrak harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak_kso.max' => 'Ukuran file kontrak maksimal 5MB.',
        ];

        $validated = $request->validate([
            'nama_kso' => 'required|string|max:100',
            'file_kontrak_kso' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ], $pesanKustom);

        $pathFileLama = $kso->file_kontrak_kso;
        $pathFileKontrak = $pathFileLama;
        $fileBaruDiunggah = false;

        if ($request->hasFile('file_kontrak_kso')) {
            $pathFileKontrak = $request->file('file_kontrak_kso')->store('kontrak_kso', 'public');
            $fileBaruDiunggah = true;
        }

        DB::beginTransaction();
        try {
            $kso->update([
                'nama_kso' => trim($validated['nama_kso']),
                'file_kontrak_kso' => $pathFileKontrak,
                'diperbarui_pada' => now(),
            ]);

            DB::commit();

            if ($fileBaruDiunggah && $pathFileLama && Storage::disk('public')->exists($pathFileLama)) {
                Storage::disk('public')->delete($pathFileLama);
            }

            return redirect()->route('operasional.kso', ['tab' => 'kso'])
                ->with('sukses', "Data Mitra KSO [{$kso->kode_kso}] berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($fileBaruDiunggah && $pathFileKontrak && Storage::disk('public')->exists($pathFileKontrak)) {
                Storage::disk('public')->delete($pathFileKontrak);
            }
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui Data Mitra KSO: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Data Mitra KSO.
     */
    public function hapusKSO($kode_kso)
    {
        $kso = KSO::findOrFail($kode_kso);
        $namaKso = $kso->nama_kso;
        $fileKontrak = $kso->file_kontrak_kso;

        DB::beginTransaction();
        try {
            $kso->delete();
            DB::commit();

            if ($fileKontrak && Storage::disk('public')->exists($fileKontrak)) {
                Storage::disk('public')->delete($fileKontrak);
            }

            return redirect()->route('operasional.kso', ['tab' => 'kso'])
                ->with('sukses', "Data Mitra KSO [{$kode_kso}] {$namaKso} beserta seluruh rute ongkos angkut terkait berhasil dihapus!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('operasional.kso', ['tab' => 'kso'])
                ->with('error', 'Gagal menghapus Data Mitra KSO: ' . $e->getMessage());
        }
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
        if ($request->has('ongkos_angkut')) {
            $raw = (string) $request->input('ongkos_angkut');
            $request->merge(['ongkos_angkut' => preg_replace('/[^0-9]/', '', $raw)]);
        }

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

        DB::beginTransaction();
        try {
            $oa = OngkosKSO::create([
                'kode_oa' => strtoupper(trim($validated['kode_oa'])),
                'kode_kso' => $validated['kode_kso'],
                'nama_oa' => trim($validated['nama_oa']),
                'muatan' => trim($validated['muatan']),
                'ongkos_angkut' => $validated['ongkos_angkut'],
            ]);

            DB::commit();

            return redirect()->route('operasional.kso', ['tab' => 'ongkos'])
                ->with('sukses', "Tarif Ongkos Angkut KSO [{$oa->kode_oa}] {$oa->nama_oa} berhasil disimpan!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan tarif ongkos angkut KSO: ' . $e->getMessage());
        }
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

        if ($request->has('ongkos_angkut')) {
            $raw = (string) $request->input('ongkos_angkut');
            $request->merge(['ongkos_angkut' => preg_replace('/[^0-9]/', '', $raw)]);
        }

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

        DB::beginTransaction();
        try {
            $oa->update([
                'kode_kso' => $validated['kode_kso'],
                'nama_oa' => trim($validated['nama_oa']),
                'muatan' => trim($validated['muatan']),
                'ongkos_angkut' => $validated['ongkos_angkut'],
                'diperbarui_pada' => now(),
            ]);

            DB::commit();

            return redirect()->route('operasional.kso', ['tab' => 'ongkos'])
                ->with('sukses', "Data Tarif Ongkos Angkut [{$oa->kode_oa}] berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui tarif ongkos angkut KSO: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Data Ongkos Angkut KSO.
     */
    public function hapusOngkos($kode_oa)
    {
        $oa = OngkosKSO::findOrFail($kode_oa);
        $namaOa = $oa->nama_oa;

        DB::beginTransaction();
        try {
            $oa->delete();
            DB::commit();

            return redirect()->route('operasional.kso', ['tab' => 'ongkos'])
                ->with('sukses', "Tarif Ongkos Angkut [{$kode_oa}] {$namaOa} berhasil dihapus!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('operasional.kso', ['tab' => 'ongkos'])
                ->with('error', 'Gagal menghapus tarif ongkos angkut KSO: ' . $e->getMessage());
        }
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
                    'file_kontrak_kso' => null,
                    'dibuat_pada' => Carbon::now()->subMonths(2),
                    'diperbarui_pada' => Carbon::now()->subDays(3),
                ],
                [
                    'kode_kso' => 'KSO-002',
                    'nama_kso' => 'KSO Armada Ekspedisi Berkah Bersama',
                    'file_kontrak_kso' => null,
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
