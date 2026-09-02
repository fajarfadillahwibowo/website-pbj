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

        return redirect()->route('operasional.gudang.stok')
            ->with('sukses', "Gudang {$gudang->nama_gudang} ({$gudang->kode_gudang}) berhasil didaftarkan!");
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

        return redirect()->route('operasional.gudang.stok')
            ->with('sukses', "Data Gudang {$gudang->nama_gudang} berhasil diperbarui!");
    }

    /**
     * Penyesuaian stok fisik cepat (Mutasi Masuk / Keluar / Set Stok).
     */
    public function mutasiStok(Request $request, $kode_gudang)
    {
        $gudang = Gudang::findOrFail($kode_gudang);

        $validated = $request->validate([
            'tipe_mutasi' => 'required|in:masuk,keluar,atur',
            'jumlah_zak' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $stokLama = $gudang->stok_tersedia;
        $jumlah = $validated['jumlah_zak'];

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

        $gudang->update([
            'stok_tersedia' => $stokBaru,
            'diperbarui_pada' => now(),
        ]);

        return redirect()->route('operasional.gudang.stok')->with('sukses', $pesan);
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

        $gudang->delete();

        return redirect()->route('operasional.gudang.stok')
            ->with('sukses', "Gudang {$namaGudang} ({$kode_gudang}) berhasil dihapus dari sistem!");
    }

    /**
     * Generator kode otomatis untuk Kode Gudang.
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
                $kandidat = 'GDG-' . $acak;
                $sudahAda = DB::table('list_gudang_so')->where('kode_gudang', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('GDG-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Acak Anti-Tebak'
            ]);
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

        $kodeBaru = 'GDG-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
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
