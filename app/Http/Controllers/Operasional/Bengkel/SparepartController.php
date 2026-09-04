<?php

namespace App\Http\Controllers\Operasional\Bengkel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\Sparepart;
use Carbon\Carbon;

class SparepartController extends Controller
{
    /**
     * Tampilkan katalog & inventaris stok sparepart bengkel armada.
     */
    public function index(Request $request)
    {
        $this->pastikanDataAwalTersedia();

        $kataKunci = $request->input('cari');
        $kategoriFilter = $request->input('kategori', 'semua');

        $query = Sparepart::query();

        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('kode_sparepart', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_sparepart', 'like', "%{$kataKunci}%")
                  ->orWhere('kategori_part', 'like', "%{$kataKunci}%")
                  ->orWhere('satuan', 'like', "%{$kataKunci}%");
            });
        }

        if ($kategoriFilter !== 'semua' && !empty($kategoriFilter)) {
            $query->where('kategori_part', $kategoriFilter);
        }

        $daftarSparepart = $query->orderBy('diperbarui_pada', 'desc')->get();

        // 4 Kartu KPI Ringkasan Sparepart
        $semuaPart = Sparepart::all();
        $totalJenisPart = $semuaPart->count();
        $totalKuantitasFisik = $semuaPart->sum('stok_part');
        $partStokMenipis = $semuaPart->filter(fn($p) => $p->stok_part <= 5)->count();
        $totalValuasiPersediaan = $semuaPart->sum(fn($p) => $p->stok_part * $p->harga_satuan);

        // Kategori Part Unik untuk Filter
        $daftarKategori = Sparepart::select('kategori_part')->distinct()->pluck('kategori_part');

        return view('operasional.bengkel.sparepart', compact(
            'daftarSparepart',
            'kataKunci',
            'kategoriFilter',
            'totalJenisPart',
            'totalKuantitasFisik',
            'partStokMenipis',
            'totalValuasiPersediaan',
            'daftarKategori'
        ));
    }

    /**
     * Simpan data master sparepart baru.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'kode_sparepart.required' => 'Kode sparepart wajib diisi.',
            'kode_sparepart.unique' => 'Kode sparepart sudah terdaftar.',
            'nama_sparepart.required' => 'Nama sparepart wajib diisi.',
            'kategori_part.required' => 'Kategori sparepart wajib dipilih.',
            'stok_part.required' => 'Kuantitas stok awal wajib diisi.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga_satuan.required' => 'Harga beli satuan wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_sparepart' => 'required|string|max:30|unique:list_sparepart,kode_sparepart',
            'nama_sparepart' => 'required|string|max:100',
            'kategori_part' => 'required|string|max:50',
            'stok_part' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'harga_satuan' => 'required|numeric|min:0',
        ], $pesanKustom);

        $part = Sparepart::create([
            'kode_sparepart' => strtoupper(trim($validated['kode_sparepart'])),
            'nama_sparepart' => trim($validated['nama_sparepart']),
            'kategori_part' => trim($validated['kategori_part']),
            'stok_part' => $validated['stok_part'],
            'satuan' => trim($validated['satuan']),
            'harga_satuan' => $validated['harga_satuan'],
        ]);

        return redirect()->route('operasional.bengkel.sparepart')
            ->with('sukses', "Sparepart [{$part->kode_sparepart}] {$part->nama_sparepart} berhasil ditambahkan!");
    }

    /**
     * Ambil Detail Sparepart (JSON).
     */
    public function ambilDetail($kode_sparepart)
    {
        $part = Sparepart::where('kode_sparepart', $kode_sparepart)->first();

        if (!$part) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data sparepart tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $part
        ]);
    }

    /**
     * Perbarui data master sparepart.
     */
    public function perbarui(Request $request, $kode_sparepart)
    {
        $part = Sparepart::findOrFail($kode_sparepart);

        $pesanKustom = [
            'nama_sparepart.required' => 'Nama sparepart wajib diisi.',
            'kategori_part.required' => 'Kategori sparepart wajib dipilih.',
            'stok_part.required' => 'Kuantitas stok wajib diisi.',
            'satuan.required' => 'Satuan barang wajib diisi.',
            'harga_satuan.required' => 'Harga beli satuan wajib diisi.',
        ];

        $validated = $request->validate([
            'nama_sparepart' => 'required|string|max:100',
            'kategori_part' => 'required|string|max:50',
            'stok_part' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'harga_satuan' => 'required|numeric|min:0',
        ], $pesanKustom);

        $part->update([
            'nama_sparepart' => trim($validated['nama_sparepart']),
            'kategori_part' => trim($validated['kategori_part']),
            'stok_part' => $validated['stok_part'],
            'satuan' => trim($validated['satuan']),
            'harga_satuan' => $validated['harga_satuan'],
        ]);

        return redirect()->route('operasional.bengkel.sparepart')
            ->with('sukses', "Data sparepart [{$part->kode_sparepart}] berhasil diperbarui!");
    }

    /**
     * Penyesuaian / Mutasi Stok Cepat (Stok Masuk / Stok Keluar / Set Fisik).
     */
    public function mutasiStok(Request $request, $kode_sparepart)
    {
        $part = Sparepart::findOrFail($kode_sparepart);

        $validated = $request->validate([
            'tipe_mutasi' => 'required|in:masuk,keluar,atur',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $jumlah = (int) $validated['jumlah'];
        $stokLama = $part->stok_part;

        if ($validated['tipe_mutasi'] === 'masuk') {
            $stokBaru = $stokLama + $jumlah;
            $pesan = "Penambahan stok masuk +{$jumlah} {$part->satuan} berhasil diproses.";
        } elseif ($validated['tipe_mutasi'] === 'keluar') {
            if ($stokLama < $jumlah) {
                return redirect()->route('operasional.bengkel.sparepart')
                    ->with('error', "Gagal! Stok tersedia ({$stokLama}) tidak mencukupi untuk pengurangan {$jumlah} {$part->satuan}.");
            }
            $stokBaru = $stokLama - $jumlah;
            $pesan = "Pengurangan stok keluar -{$jumlah} {$part->satuan} berhasil diproses.";
        } else {
            $stokBaru = $jumlah;
            $pesan = "Kuantitas fisik sparepart diatur menjadi {$jumlah} {$part->satuan}.";
        }

        $part->update([
            'stok_part' => $stokBaru,
        ]);

        return redirect()->route('operasional.bengkel.sparepart')
            ->with('sukses', "{$pesan} (Stok sekarang: {$stokBaru} {$part->satuan})");
    }

    /**
     * Hapus data sparepart.
     */
    public function hapus($kode_sparepart)
    {
        $part = Sparepart::findOrFail($kode_sparepart);
        $nama = $part->nama_sparepart;

        $part->delete();

        return redirect()->route('operasional.bengkel.sparepart')
            ->with('sukses', "Sparepart [{$kode_sparepart}] {$nama} berhasil dihapus!");
    }

    /**
     * Hapus banyak data sparepart sekaligus (Hapus Massal).
     */
    public function hapusMassal(Request $request)
    {
        $daftarId = $request->input('daftar_id', []);
        if (empty($daftarId) || !is_array($daftarId)) {
            return redirect()->route('operasional.bengkel.sparepart')->with('error', 'Tidak ada data suku cadang / sparepart yang dipilih untuk dihapus.');
        }

        $berhasilDihapus = 0;

        DB::beginTransaction();
        try {
            foreach ($daftarId as $kode) {
                $part = Sparepart::find($kode);
                if ($part) {
                    $part->delete();
                    $berhasilDihapus++;
                }
            }
            DB::commit();

            return redirect()->route('operasional.bengkel.sparepart')->with('sukses', "{$berhasilDihapus} data sparepart terpilih berhasil dihapus.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('operasional.bengkel.sparepart')->with('error', 'Terjadi kesalahan saat menghapus data massal: ' . $th->getMessage());
        }
    }

    /**
     * Generator Kode Sparepart Otomatis.
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
                for ($i = 0; $i < 3; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'PRT-' . $acak;
                $sudahAda = DB::table('list_sparepart')->where('kode_sparepart', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('PRT-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Acak Anti-Tebak'
            ]);
        }

        // Mode GAP FILLING
        $daftarPart = DB::table('list_sparepart')
            ->where('kode_sparepart', 'like', 'PRT-%')
            ->pluck('kode_sparepart');

        $nomorTerpakai = [];
        foreach ($daftarPart as $kode) {
            if (preg_match('/PRT-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'PRT-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan data awal sparepart tersedia.
     */
    private function pastikanDataAwalTersedia(): void
    {
        $jumlahPart = DB::table('list_sparepart')->count();
        if ($jumlahPart === 0) {
            DB::table('list_sparepart')->insert([
                [
                    'kode_sparepart' => 'PRT-001',
                    'nama_sparepart' => 'Oli Mesin Diesel Meditran SX 15W-40',
                    'kategori_part' => 'Pelumas & Oli',
                    'stok_part' => 24,
                    'satuan' => 'Drum (200L)',
                    'harga_satuan' => 5200000,
                    'dibuat_pada' => Carbon::now()->subMonths(2),
                    'diperbarui_pada' => Carbon::now()->subDays(2),
                ],
                [
                    'kode_sparepart' => 'PRT-002',
                    'nama_sparepart' => 'Ban Luar Gajah Tunggal 10.00R20 16PR (Tronton)',
                    'kategori_part' => 'Ban & Roda',
                    'stok_part' => 4,
                    'satuan' => 'Pcs',
                    'harga_satuan' => 3450000,
                    'dibuat_pada' => Carbon::now()->subMonths(2),
                    'diperbarui_pada' => Carbon::now()->subHours(6),
                ],
                [
                    'kode_sparepart' => 'PRT-003',
                    'nama_sparepart' => 'Brake Shoe / Kampas Rem Depan Hino FM260JD',
                    'kategori_part' => 'Pengereman',
                    'stok_part' => 12,
                    'satuan' => 'Set',
                    'harga_satuan' => 850000,
                    'dibuat_pada' => Carbon::now()->subMonths(1),
                    'diperbarui_pada' => Carbon::now()->subDays(5),
                ],
                [
                    'kode_sparepart' => 'PRT-004',
                    'nama_sparepart' => 'Filter Oli Hino 500 & Canter Asli OEM',
                    'kategori_part' => 'Filter',
                    'stok_part' => 30,
                    'satuan' => 'Pcs',
                    'harga_satuan' => 145000,
                    'dibuat_pada' => Carbon::now()->subMonths(1),
                    'diperbarui_pada' => Carbon::now()->subHours(1),
                ],
                [
                    'kode_sparepart' => 'PRT-005',
                    'nama_sparepart' => 'Aki Truk GS Astra N150 Heavy Duty 12V 150Ah',
                    'kategori_part' => 'Elektrikal & Aki',
                    'stok_part' => 3,
                    'satuan' => 'Unit',
                    'harga_satuan' => 2400000,
                    'dibuat_pada' => Carbon::now()->subMonths(1),
                    'diperbarui_pada' => Carbon::now()->subDays(1),
                ],
            ]);
        }
    }
}
