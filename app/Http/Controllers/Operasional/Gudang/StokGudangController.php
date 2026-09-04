<?php

namespace App\Http\Controllers\Operasional\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\Gudang;
use App\Models\Master\Barang;
use Carbon\Carbon;

class StokGudangController extends Controller
{
    /**
     * Tampilkan data master fasilitas gudang dan stok persediaan semen.
     */
    public function index(Request $request)
    {
        $this->pastikanDataAwalTersedia();

        $kataKunci = $request->input('cari');
        $jenisFilter = $request->input('jenis', 'semua');

        $query = Gudang::with('barang');

        // Filter pencarian multi-kolom
        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('kode_gudang', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_gudang', 'like', "%{$kataKunci}%")
                  ->orWhere('plant', 'like', "%{$kataKunci}%")
                  ->orWhere('distrik', 'like', "%{$kataKunci}%")
                  ->orWhere('sub_distrik', 'like', "%{$kataKunci}%")
                  ->orWhereHas('barang', function ($qB) use ($kataKunci) {
                      $qB->where('nama_barang', 'like', "%{$kataKunci}%")
                         ->orWhere('jenis_barang', 'like', "%{$kataKunci}%")
                         ->orWhere('kode_barang', 'like', "%{$kataKunci}%");
                  });
            });
        }

        // Filter tipe / jenis gudang
        if ($jenisFilter !== 'semua' && !empty($jenisFilter)) {
            $query->where('jenis_gudang', $jenisFilter);
        }

        $daftarGudang = $query->orderBy('kode_gudang', 'asc')->get();

        // 4 KPI Statistik Persediaan Gudang
        $semuaGudang = Gudang::all();
        $totalGudang = $semuaGudang->count();
        $totalStokZak = $semuaGudang->sum('stok_tersedia');
        $stokKritis = $semuaGudang->where('stok_tersedia', '<=', 1000)->count();
        
        $totalValuasiStok = 0;
        foreach ($semuaGudang as $g) {
            $totalValuasiStok += ($g->harga_barang * $g->stok_tersedia);
        }

        // Master Barang Semen untuk Dropdown Form
        $daftarBarang = Barang::orderBy('nama_barang', 'asc')->get();

        return view('operasional.gudang.stok', compact(
            'daftarGudang',
            'kataKunci',
            'jenisFilter',
            'totalGudang',
            'totalStokZak',
            'stokKritis',
            'totalValuasiStok',
            'daftarBarang'
        ));
    }

    /**
     * Simpan data gudang baru ke database.
     */
    public function simpan(Request $request)
    {
        // 1. Auto-generate kode_gudang jika belum terisi dari client
        if (!$request->filled('kode_gudang')) {
            $request->merge(['kode_gudang' => $this->generateKodeGudang('gap')]);
        } else {
            $request->merge(['kode_gudang' => strtoupper(trim((string) $request->input('kode_gudang')))]);
        }

        // 2. Sanitasi harga_barang (buang simbol Rp, titik, dsb.)
        if ($request->has('harga_barang')) {
            $raw = (string) $request->input('harga_barang');
            $request->merge(['harga_barang' => preg_replace('/[^0-9]/', '', $raw)]);
        }

        // 3. Fallback nilai default cerdas untuk mencegah kegagalan validasi
        if (!$request->filled('harga_barang')) {
            $request->merge(['harga_barang' => 0]);
        }

        if (!$request->filled('stok_tersedia')) {
            $request->merge(['stok_tersedia' => 0]);
        }

        if (!$request->filled('jenis_gudang')) {
            $request->merge(['jenis_gudang' => 'Utama']);
        }

        if (!$request->filled('kode_barang')) {
            $defaultSemen = DB::table('data_semen')->value('kode_barang');
            if ($defaultSemen) {
                $request->merge(['kode_barang' => $defaultSemen]);
            }
        }

        $pesanKustom = [
            'kode_gudang.required' => 'Kode gudang wajib diisi.',
            'kode_gudang.unique' => 'Kode gudang sudah terdaftar.',
            'nama_gudang.required' => 'Nama gudang wajib diisi.',
            'jenis_gudang.required' => 'Jenis fasilitas gudang wajib dipilih.',
            'kode_barang.required' => 'Komoditas semen yang disimpan wajib dipilih.',
            'kode_barang.exists' => 'Data semen tidak valid.',
            'plant.required' => 'Lokasi plant pabrik wajib diisi.',
            'harga_barang.required' => 'Harga standar semen wajib diisi.',
            'stok_tersedia.required' => 'Kuantitas stok awal wajib diisi.',
            'distrik.required' => 'Distrik / Kabupaten wajib diisi.',
            'sub_distrik.required' => 'Sub-distrik / Kecamatan wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_gudang' => 'required|string|max:30|unique:list_gudang_so,kode_gudang',
            'nama_gudang' => 'required|string|max:100',
            'jenis_gudang' => 'required|string|max:50',
            'kode_barang' => 'required|string|max:30|exists:data_semen,kode_barang',
            'plant' => 'required|string|max:50',
            'harga_barang' => 'required|numeric|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'distrik' => 'required|string|max:50',
            'sub_distrik' => 'required|string|max:50',
        ], $pesanKustom);

        DB::beginTransaction();
        try {
            $gudang = Gudang::create([
                'kode_gudang' => strtoupper(trim($validated['kode_gudang'])),
                'nama_gudang' => trim($validated['nama_gudang']),
                'jenis_gudang' => trim($validated['jenis_gudang']),
                'kode_barang' => $validated['kode_barang'],
                'plant' => trim($validated['plant']),
                'harga_barang' => $validated['harga_barang'],
                'stok_tersedia' => $validated['stok_tersedia'],
                'distrik' => trim($validated['distrik']),
                'sub_distrik' => trim($validated['sub_distrik']),
            ]);

            DB::commit();

            return redirect()->route('operasional.gudang.stok')
                ->with('sukses', "Gudang {$gudang->nama_gudang} ({$gudang->kode_gudang}) berhasil didaftarkan!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mendaftarkan fasilitas gudang: ' . $e->getMessage());
        }
    }

    /**
     * Ambil data detail gudang (JSON) untuk modal edit dan mutasi.
     */
    public function ambilDetail($kode_gudang)
    {
        $gudang = Gudang::with('barang')->find($kode_gudang);

        if (!$gudang) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data fasilitas gudang tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $gudang
        ]);
    }

    /**
     * Perbarui data fasilitas gudang.
     */
    public function perbarui(Request $request, $kode_gudang)
    {
        $gudang = Gudang::findOrFail($kode_gudang);

        if ($request->has('harga_barang')) {
            $raw = (string) $request->input('harga_barang');
            $request->merge(['harga_barang' => preg_replace('/[^0-9]/', '', $raw)]);
        }

        if (!$request->filled('harga_barang')) {
            $request->merge(['harga_barang' => $gudang->harga_barang ?? 0]);
        }

        if (!$request->filled('stok_tersedia')) {
            $request->merge(['stok_tersedia' => $gudang->stok_tersedia ?? 0]);
        }

        if (!$request->filled('jenis_gudang')) {
            $request->merge(['jenis_gudang' => $gudang->jenis_gudang ?? 'Utama']);
        }

        if (!$request->filled('kode_barang')) {
            $request->merge(['kode_barang' => $gudang->kode_barang]);
        }

        $pesanKustom = [
            'nama_gudang.required' => 'Nama gudang wajib diisi.',
            'jenis_gudang.required' => 'Jenis fasilitas gudang wajib dipilih.',
            'kode_barang.required' => 'Komoditas semen wajib dipilih.',
            'kode_barang.exists' => 'Data semen tidak valid.',
            'plant.required' => 'Lokasi plant pabrik wajib diisi.',
            'harga_barang.required' => 'Harga standar semen wajib diisi.',
            'stok_tersedia.required' => 'Kuantitas stok wajib diisi.',
            'distrik.required' => 'Distrik / Kabupaten wajib diisi.',
            'sub_distrik.required' => 'Sub-distrik / Kecamatan wajib diisi.',
        ];

        $validated = $request->validate([
            'nama_gudang' => 'required|string|max:100',
            'jenis_gudang' => 'required|string|max:50',
            'kode_barang' => 'required|string|max:30|exists:data_semen,kode_barang',
            'plant' => 'required|string|max:50',
            'harga_barang' => 'required|numeric|min:0',
            'stok_tersedia' => 'required|integer|min:0',
            'distrik' => 'required|string|max:50',
            'sub_distrik' => 'required|string|max:50',
        ], $pesanKustom);

        DB::beginTransaction();
        try {
            $gudang->update([
                'nama_gudang' => trim($validated['nama_gudang']),
                'jenis_gudang' => trim($validated['jenis_gudang']),
                'kode_barang' => $validated['kode_barang'],
                'plant' => trim($validated['plant']),
                'harga_barang' => $validated['harga_barang'],
                'stok_tersedia' => $validated['stok_tersedia'],
                'distrik' => trim($validated['distrik']),
                'sub_distrik' => trim($validated['sub_distrik']),
                'diperbarui_pada' => now(),
            ]);

            DB::commit();

            return redirect()->route('operasional.gudang.stok')
                ->with('sukses', "Data Gudang {$gudang->nama_gudang} berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui fasilitas gudang: ' . $e->getMessage());
        }
    }

    /**
     * Penyesuaian stok fisik cepat (Mutasi Masuk / Keluar / Set Stok).
     */
    public function mutasiStok(Request $request, $kode_gudang)
    {
        $gudang = Gudang::findOrFail($kode_gudang);

        $validated = $request->validate([
            'tipe_mutasi' => 'required|in:masuk,keluar,atur',
            'jumlah_zak' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'tipe_mutasi.required' => 'Tipe aksi mutasi wajib dipilih.',
            'jumlah_zak.required' => 'Jumlah kuantitas zak wajib diisi.',
            'jumlah_zak.integer' => 'Jumlah kuantitas zak harus berupa bilangan bulat.',
            'jumlah_zak.min' => 'Jumlah kuantitas zak tidak boleh negatif.',
        ]);

        $stokLama = $gudang->stok_tersedia;
        $jumlah = (int) $validated['jumlah_zak'];

        if (in_array($validated['tipe_mutasi'], ['masuk', 'keluar']) && $jumlah < 1) {
            return redirect()->back()->with('error', 'Jumlah kuantitas zak untuk mutasi masuk atau keluar minimal 1 zak.');
        }

        if ($validated['tipe_mutasi'] === 'masuk') {
            $stokBaru = $stokLama + $jumlah;
            $pesan = "Stok masuk sebanyak {$jumlah} zak berhasil ditambahkan ke Gudang {$gudang->nama_gudang}. (Total: {$stokBaru} Zak)";
        } elseif ($validated['tipe_mutasi'] === 'keluar') {
            if ($stokLama < $jumlah) {
                return redirect()->back()->with('error', "Stok di gudang tidak mencukupi! Kuantitas saat ini: {$stokLama} Zak.");
            }
            $stokBaru = $stokLama - $jumlah;
            $pesan = "Pengeluaran stok sebanyak {$jumlah} zak dari Gudang {$gudang->nama_gudang} berhasil dicatat. (Sisa: {$stokBaru} Zak)";
        } else {
            $stokBaru = $jumlah;
            $pesan = "Stok fisik Gudang {$gudang->nama_gudang} berhasil disesuaikan menjadi {$stokBaru} Zak.";
        }

        DB::beginTransaction();
        try {
            $gudang->update([
                'stok_tersedia' => $stokBaru,
                'diperbarui_pada' => now(),
            ]);

            DB::commit();

            return redirect()->route('operasional.gudang.stok')->with('sukses', $pesan);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses mutasi stok: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data fasilitas gudang dari database.
     */
    public function hapus($kode_gudang)
    {
        $gudang = Gudang::findOrFail($kode_gudang);
        $namaGudang = $gudang->nama_gudang;

        // Proteksi jika masih digunakan di Sales Order
        $digunakanSO = DB::table('pembelian_so')->where('kode_gudang', $kode_gudang)->exists();
        if ($digunakanSO) {
            return redirect()->route('operasional.gudang.stok')
                ->with('error', "Gagal menghapus! Gudang {$namaGudang} masih terhubung dengan transaksi Sales Order.");
        }

        // Proteksi jika masih memiliki riwayat di Stock Opname
        $digunakanOpname = DB::table('opname_gudang')->where('kode_gudang', $kode_gudang)->exists();
        if ($digunakanOpname) {
            return redirect()->route('operasional.gudang.stok')
                ->with('error', "Gagal menghapus! Gudang {$namaGudang} masih memiliki riwayat catatan Stock Opname fisik.");
        }

        DB::beginTransaction();
        try {
            $gudang->delete();
            DB::commit();

            return redirect()->route('operasional.gudang.stok')
                ->with('sukses', "Gudang {$namaGudang} ({$kode_gudang}) berhasil dihapus dari sistem!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('operasional.gudang.stok')
                ->with('error', 'Gagal menghapus fasilitas gudang: ' . $e->getMessage());
        }
    }

    /**
     * Hapus banyak fasilitas gudang sekaligus (Hapus Massal).
     */
    public function hapusMassal(Request $request)
    {
        $daftarId = $request->input('daftar_id', []);
        if (empty($daftarId) || !is_array($daftarId)) {
            return redirect()->route('operasional.gudang.stok')->with('error', 'Tidak ada fasilitas gudang yang dipilih untuk dihapus.');
        }

        $berhasilDihapus = 0;
        $gagalDihapus = 0;

        DB::beginTransaction();
        try {
            foreach ($daftarId as $kode) {
                $gudang = Gudang::find($kode);
                if ($gudang) {
                    $digunakanSO = DB::table('pembelian_so')->where('kode_gudang', $kode)->exists();
                    if ($digunakanSO) {
                        $gagalDihapus++;
                        continue;
                    }
                    $gudang->delete();
                    $berhasilDihapus++;
                }
            }
            DB::commit();

            if ($gagalDihapus > 0) {
                return redirect()->route('operasional.gudang.stok')->with('sukses', "{$berhasilDihapus} gudang berhasil dihapus. {$gagalDihapus} gudang dilewati karena terhubung dengan transaksi Sales Order.");
            }

            return redirect()->route('operasional.gudang.stok')->with('sukses', "{$berhasilDihapus} data fasilitas gudang terpilih berhasil dihapus.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('operasional.gudang.stok')->with('error', 'Terjadi kesalahan saat menghapus data massal: ' . $th->getMessage());
        }
    }

    /**
     * Generator kode otomatis untuk Kode Gudang.
     */
    public function generateKodeGudang(string $mode = 'gap'): string
    {
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
                $kandidat = 'GDG-' . $acak;
                $sudahAda = DB::table('list_gudang_so')->where('kode_gudang', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return $kodeUnik ?? ('GDG-' . strtoupper(bin2hex(random_bytes(2))));
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarGudang = DB::table('list_gudang_so')
            ->where('kode_gudang', 'like', 'GDG-%')
            ->pluck('kode_gudang');

        $nomorTerpakai = [];
        foreach ($daftarGudang as $kode) {
            if (preg_match('/GDG-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        return 'GDG-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generator kode otomatis untuk Kode Gudang (JSON endpoint).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        $kodeBaru = $this->generateKodeGudang($mode);

        return response()->json([
            'status' => 'sukses',
            'mode' => $mode,
            'kode_otomatis' => $kodeBaru,
            'keterangan' => $mode === 'acak' ? 'Format Acak Anti-Tebak' : 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan data gudang awal tersedia.
     */
    private function pastikanDataAwalTersedia(): void
    {
        $jumlahGudang = DB::table('list_gudang_so')->count();
        if ($jumlahGudang === 0) {
            $kodeBarangSemen = DB::table('data_semen')->value('kode_barang') ?? 'SMN-002';

            DB::table('list_gudang_so')->insert([
                [
                    'kode_gudang' => 'GDG-PUSAT',
                    'nama_gudang' => 'Gudang Utama Pabrik Cikarang',
                    'jenis_gudang' => 'Utama',
                    'kode_barang' => $kodeBarangSemen,
                    'plant' => 'Plant Cikarang',
                    'harga_barang' => 64000.00,
                    'stok_tersedia' => 35000,
                    'distrik' => 'Kabupaten Bekasi',
                    'sub_distrik' => 'Cikarang Pusat',
                    'dibuat_pada' => Carbon::now()->subDays(5),
                    'diperbarui_pada' => Carbon::now()->subDays(1),
                ],
                [
                    'kode_gudang' => 'GDG-KRW-01',
                    'nama_gudang' => 'Gudang Distribusi Karawang',
                    'jenis_gudang' => 'Distribusi',
                    'kode_barang' => $kodeBarangSemen,
                    'plant' => 'Plant Karawang',
                    'harga_barang' => 64000.00,
                    'stok_tersedia' => 12500,
                    'distrik' => 'Kabupaten Karawang',
                    'sub_distrik' => 'Klari',
                    'dibuat_pada' => Carbon::now()->subDays(3),
                    'diperbarui_pada' => Carbon::now()->subHours(4),
                ],
                [
                    'kode_gudang' => 'GDG-BKS-02',
                    'nama_gudang' => 'Gudang Buffer Tambun',
                    'jenis_gudang' => 'Buffer',
                    'kode_barang' => $kodeBarangSemen,
                    'plant' => 'Plant Cikarang',
                    'harga_barang' => 64000.00,
                    'stok_tersedia' => 850,
                    'distrik' => 'Kabupaten Bekasi',
                    'sub_distrik' => 'Tambun Selatan',
                    'dibuat_pada' => Carbon::now()->subDays(2),
                    'diperbarui_pada' => Carbon::now()->subHours(1),
                ],
            ]);
        }
    }
}
