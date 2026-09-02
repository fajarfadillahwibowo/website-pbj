<?php

namespace App\Http\Controllers\Operasional\Bengkel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\PembelianSparepart;
use App\Models\Operasional\Sparepart;
use Carbon\Carbon;

class PembelianSparepartController extends Controller
{
    /**
     * Tampilkan riwayat dan formulir pembelian sparepart bengkel armada.
     */
    public function index(Request $request)
    {
        $this->pastikanDataAwalTersedia();

        $kataKunci = $request->input('cari');
        $filterPart = $request->input('kode_sparepart', 'semua');

        $query = PembelianSparepart::with('sparepart');

        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_faktur_beli', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_supplier', 'like', "%{$kataKunci}%")
                  ->orWhere('dibuat_oleh', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_sparepart', 'like', "%{$kataKunci}%")
                  ->orWhereHas('sparepart', function ($qP) use ($kataKunci) {
                      $qP->where('nama_sparepart', 'like', "%{$kataKunci}%")
                         ->orWhere('kategori_part', 'like', "%{$kataKunci}%");
                  });
            });
        }

        if ($filterPart !== 'semua' && !empty($filterPart)) {
            $query->where('kode_sparepart', $filterPart);
        }

        $daftarPembelian = $query->orderBy('tanggal_beli', 'desc')->orderBy('dibuat_pada', 'desc')->get();

        // 4 Kartu KPI Ringkasan Pembelian Sparepart
        $semuaBeli = PembelianSparepart::all();
        $totalFaktur = $semuaBeli->count();
        $totalUnitDibeli = $semuaBeli->sum('jumlah_beli');
        $awalBulanIni = Carbon::now()->startOfMonth();
        $pengeluaranBulanIni = $semuaBeli->filter(fn($b) => $b->tanggal_beli && $b->tanggal_beli >= $awalBulanIni)->sum('total_bayar');
        $totalAkumulasiBelanja = $semuaBeli->sum('total_bayar');

        // Master Sparepart untuk Dropdown Form
        $daftarSparepart = Sparepart::orderBy('nama_sparepart', 'asc')->get();

        return view('operasional.bengkel.pembelian_sparepart', compact(
            'daftarPembelian',
            'kataKunci',
            'filterPart',
            'totalFaktur',
            'totalUnitDibeli',
            'pengeluaranBulanIni',
            'totalAkumulasiBelanja',
            'daftarSparepart'
        ));
    }

    /**
     * Simpan data transaksi pembelian sparepart baru.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'nomor_faktur_beli.required' => 'Nomor faktur pembelian wajib diisi.',
            'nomor_faktur_beli.unique' => 'Nomor faktur pembelian sudah terdaftar.',
            'kode_sparepart.required' => 'Sparepart wajib dipilih.',
            'kode_sparepart.exists' => 'Sparepart tidak valid.',
            'tanggal_beli.required' => 'Tanggal pembelian wajib diisi.',
            'nama_supplier.required' => 'Nama toko / supplier wajib diisi.',
            'jumlah_beli.required' => 'Jumlah kuantitas beli wajib diisi.',
            'harga_beli.required' => 'Harga beli satuan wajib diisi.',
            'dibuat_oleh.required' => 'Nama pengawas/pencatat wajib diisi.',
        ];

        $validated = $request->validate([
            'nomor_faktur_beli' => 'required|string|max:50|unique:pembelian_sparepart,nomor_faktur_beli',
            'kode_sparepart' => 'required|string|max:30|exists:list_sparepart,kode_sparepart',
            'tanggal_beli' => 'required|date',
            'nama_supplier' => 'required|string|max:100',
            'jumlah_beli' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'dibuat_oleh' => 'required|string|max:50',
        ], $pesanKustom);

        $totalBayar = (int) $validated['jumlah_beli'] * (float) $validated['harga_beli'];

        $pembelian = PembelianSparepart::create([
            'nomor_faktur_beli' => strtoupper(trim($validated['nomor_faktur_beli'])),
            'kode_sparepart' => $validated['kode_sparepart'],
            'tanggal_beli' => $validated['tanggal_beli'],
            'nama_supplier' => trim($validated['nama_supplier']),
            'jumlah_beli' => $validated['jumlah_beli'],
            'harga_beli' => $validated['harga_beli'],
            'total_bayar' => $totalBayar,
            'dibuat_oleh' => trim($validated['dibuat_oleh']),
        ]);

        // Otomatis tambah stok fisik di master sparepart
        $part = Sparepart::find($validated['kode_sparepart']);
        if ($part) {
            $part->increment('stok_part', $validated['jumlah_beli']);
            $part->update(['harga_satuan' => $validated['harga_beli']]); // Perbarui harga standar ke harga beli terbaru
        }

        return redirect()->route('operasional.bengkel.pembelian_sparepart')
            ->with('sukses', "Faktur Pembelian [{$pembelian->nomor_faktur_beli}] berhasil disimpan! Stok sparepart otomatis bertambah +{$pembelian->jumlah_beli}.");
    }

    /**
     * Ambil Detail Pembelian Sparepart (JSON).
     */
    public function ambilDetail($id_pembelian_part)
    {
        $pembelian = PembelianSparepart::with('sparepart')->find($id_pembelian_part);

        if (!$pembelian) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data faktur pembelian tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $pembelian
        ]);
    }

    /**
     * Perbarui data faktur pembelian sparepart.
     */
    public function perbarui(Request $request, $id_pembelian_part)
    {
        $pembelian = PembelianSparepart::findOrFail($id_pembelian_part);

        $pesanKustom = [
            'kode_sparepart.required' => 'Sparepart wajib dipilih.',
            'kode_sparepart.exists' => 'Sparepart tidak valid.',
            'tanggal_beli.required' => 'Tanggal pembelian wajib diisi.',
            'nama_supplier.required' => 'Nama toko / supplier wajib diisi.',
            'jumlah_beli.required' => 'Jumlah kuantitas beli wajib diisi.',
            'harga_beli.required' => 'Harga beli satuan wajib diisi.',
            'dibuat_oleh.required' => 'Nama pengawas wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_sparepart' => 'required|string|max:30|exists:list_sparepart,kode_sparepart',
            'tanggal_beli' => 'required|date',
            'nama_supplier' => 'required|string|max:100',
            'jumlah_beli' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric|min:0',
            'dibuat_oleh' => 'required|string|max:50',
        ], $pesanKustom);

        $selisihJumlah = (int) $validated['jumlah_beli'] - (int) $pembelian->jumlah_beli;
        $totalBayar = (int) $validated['jumlah_beli'] * (float) $validated['harga_beli'];

        // Jika sparepart sama, sesuaikan stok selisih
        if ($pembelian->kode_sparepart === $validated['kode_sparepart']) {
            if ($selisihJumlah != 0) {
                Sparepart::where('kode_sparepart', $pembelian->kode_sparepart)->increment('stok_part', $selisihJumlah);
            }
        } else {
            // Jika sparepart diubah, kembalikan stok lama & tambahkan stok baru
            Sparepart::where('kode_sparepart', $pembelian->kode_sparepart)->decrement('stok_part', $pembelian->jumlah_beli);
            Sparepart::where('kode_sparepart', $validated['kode_sparepart'])->increment('stok_part', $validated['jumlah_beli']);
        }

        $pembelian->update([
            'kode_sparepart' => $validated['kode_sparepart'],
            'tanggal_beli' => $validated['tanggal_beli'],
            'nama_supplier' => trim($validated['nama_supplier']),
            'jumlah_beli' => $validated['jumlah_beli'],
            'harga_beli' => $validated['harga_beli'],
            'total_bayar' => $totalBayar,
            'dibuat_oleh' => trim($validated['dibuat_oleh']),
        ]);

        return redirect()->route('operasional.bengkel.pembelian_sparepart')
            ->with('sukses', "Faktur Pembelian [{$pembelian->nomor_faktur_beli}] berhasil diperbarui!");
    }

    /**
     * Hapus data transaksi pembelian sparepart.
     */
    public function hapus($id_pembelian_part)
    {
        $pembelian = PembelianSparepart::findOrFail($id_pembelian_part);
        $nomor = $pembelian->nomor_faktur_beli;

        // Kurangi stok di master sparepart
        Sparepart::where('kode_sparepart', $pembelian->kode_sparepart)->decrement('stok_part', $pembelian->jumlah_beli);

        $pembelian->delete();

        return redirect()->route('operasional.bengkel.pembelian_sparepart')
            ->with('sukses', "Faktur Pembelian [{$nomor}] berhasil dihapus! Stok sparepart telah disesuaikan.");
    }

    /**
     * Generator Nomor Faktur Pembelian Otomatis.
     */
    public function buatNomorFaktur(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        $formatTanggal = date('Ymd');

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
                $kandidat = 'FB-SP-' . $formatTanggal . '-' . $acak;
                $sudahAda = DB::table('pembelian_sparepart')->where('nomor_faktur_beli', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('FB-SP-' . $formatTanggal . '-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Tanggal & Acak Anti-Tebak'
            ]);
        }

        // Mode GAP FILLING
        $daftarFaktur = DB::table('pembelian_sparepart')
            ->where('nomor_faktur_beli', 'like', 'FB-SP-%')
            ->pluck('nomor_faktur_beli');

        $nomorTerpakai = [];
        foreach ($daftarFaktur as $kode) {
            if (preg_match('/FB-SP-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'FB-SP-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan data awal transaksi pembelian sparepart tersedia.
     */
    private function pastikanDataAwalTersedia(): void
    {
        $jumlahBeli = DB::table('pembelian_sparepart')->count();
        if ($jumlahBeli === 0) {
            $partOli = DB::table('list_sparepart')->value('kode_sparepart') ?? 'PRT-001';

            DB::table('pembelian_sparepart')->insert([
                [
                    'id_pembelian_part' => 1,
                    'nomor_faktur_beli' => 'FB-SP-001',
                    'kode_sparepart' => $partOli,
                    'tanggal_beli' => Carbon::now()->subDays(5)->format('Y-m-d'),
                    'nama_supplier' => 'Distributor Pelumas Pertamina Karawang',
                    'jumlah_beli' => 5,
                    'harga_beli' => 5200000,
                    'total_bayar' => 26000000,
                    'dibuat_oleh' => 'Bambang Supriyanto (Pengawas Kendaraan)',
                    'dibuat_pada' => Carbon::now()->subDays(5),
                ],
                [
                    'id_pembelian_part' => 2,
                    'nomor_faktur_beli' => 'FB-SP-002',
                    'kode_sparepart' => 'PRT-002',
                    'tanggal_beli' => Carbon::now()->subDays(2)->format('Y-m-d'),
                    'nama_supplier' => 'Toko Ban Armada Jaya Sentosa Cikarang',
                    'jumlah_beli' => 4,
                    'harga_beli' => 3450000,
                    'total_bayar' => 13800000,
                    'dibuat_oleh' => 'Bambang Supriyanto (Pengawas Kendaraan)',
                    'dibuat_pada' => Carbon::now()->subDays(2),
                ],
            ]);
        }
    }
}
