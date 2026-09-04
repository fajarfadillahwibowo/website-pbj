<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\TokoBangunan;
use App\Models\Master\Customer;
use App\Models\Master\Wilayah;
use App\Helpers\GeneratorKodeOtomatis;
use Illuminate\Support\Facades\DB;

class TokoBangunanController extends Controller
{
    /**
     * Tampilkan daftar master toko bangunan & proyek cabang beserta filter & pencarian.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterWilayah = $request->input('wilayah');
        $filterCustomer = $request->input('customer');
        $filterTipe = $request->input('tipe_lokasi');

        $query = TokoBangunan::with(['customer', 'wilayah']);

        if ($filterWilayah) {
            $query->where('kode_wilayah', $filterWilayah);
        }

        if ($filterCustomer) {
            $query->where('kode_customer', $filterCustomer);
        }

        if ($filterTipe) {
            $query->where('tipe_lokasi', $filterTipe);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_toko_bangunan', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_toko', 'like', "%{$kataKunci}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$kataKunci}%")
                  ->orWhere('no_hp_toko', 'like', "%{$kataKunci}%")
                  ->orWhere('alamat_lengkap', 'like', "%{$kataKunci}%")
                  ->orWhereHas('customer', function ($sub) use ($kataKunci) {
                      $sub->where('nama_pemilik', 'like', "%{$kataKunci}%")
                          ->orWhere('nama_toko_bangunan', 'like', "%{$kataKunci}%");
                  });
            });
        }

        $daftarToko = $query->orderBy('kode_toko', 'asc')->get();
        $daftarWilayah = Wilayah::orderBy('nama_wilayah', 'asc')->get();
        $daftarCustomer = Customer::orderBy('nama_pemilik', 'asc')->get();

        // Metrik KPI
        $totalToko = TokoBangunan::count();
        $totalRetail = TokoBangunan::where('tipe_lokasi', 'toko_retail')->count();
        $totalProyek = TokoBangunan::where('tipe_lokasi', 'proyek_kontraktor')->count();
        $totalCustomerTerhubung = TokoBangunan::distinct('kode_customer')->count('kode_customer');

        // Generator kode otomatis gap-filling
        $kodeOtomatis = GeneratorKodeOtomatis::buatKode('data_toko_bangunan', 'kode_toko', 'TKB-', 3);

        // Opsi Dropdown Kustom
        $opsiWilayah = $daftarWilayah->map(fn($w) => [
            'nilai' => $w->kode_wilayah,
            'label' => $w->nama_wilayah,
            'sub'   => $w->kode_wilayah
        ])->toArray();

        $opsiCustomer = $daftarCustomer->map(fn($c) => [
            'nilai' => $c->kode_customer,
            'label' => $c->nama_pemilik . ' (' . $c->kode_customer . ')',
            'sub'   => 'Plafon: Rp ' . number_format($c->plafon_piutang, 0, ',', '.') . ' | Sisa Piutang: Rp ' . number_format($c->saldo_piutang, 0, ',', '.')
        ])->toArray();

        $opsiTipeLokasi = [
            ['nilai' => 'toko_retail', 'label' => 'Toko Bangunan (Retail Fisik)', 'sub' => 'Outlet penjualan langsung ke end-user'],
            ['nilai' => 'proyek_kontraktor', 'label' => 'Proyek Konstruksi / Kontraktor', 'sub' => 'Titik proyek pembangunan langsung'],
            ['nilai' => 'gudang_transit', 'label' => 'Gudang Transit Pelanggan', 'sub' => 'Lokasi penampungan logistik'],
        ];

        $opsiStatusToko = [
            ['nilai' => 'aktif', 'label' => 'Aktif Beroperasi'],
            ['nilai' => 'non-aktif', 'label' => 'Non-Aktif / Tutup'],
        ];

        return view('master.toko_bangunan.index', compact(
            'daftarToko',
            'daftarWilayah',
            'daftarCustomer',
            'kataKunci',
            'filterWilayah',
            'filterCustomer',
            'filterTipe',
            'totalToko',
            'totalRetail',
            'totalProyek',
            'totalCustomerTerhubung',
            'kodeOtomatis',
            'opsiWilayah',
            'opsiCustomer',
            'opsiTipeLokasi',
            'opsiStatusToko'
        ));
    }

    /**
     * Simpan data toko bangunan / proyek baru.
     */
    public function simpan(Request $request)
    {
        if (!$request->filled('kode_toko')) {
            $request->merge([
                'kode_toko' => GeneratorKodeOtomatis::buatKode('data_toko_bangunan', 'kode_toko', 'TKB-', 3)
            ]);
        }

        $request->validate([
            'kode_toko'          => 'required|string|max:30|unique:data_toko_bangunan,kode_toko',
            'kode_customer'      => 'required|string|exists:data_customer,kode_customer',
            'kode_wilayah'       => 'required|string|exists:data_wilayah,kode_wilayah',
            'nama_toko_bangunan' => 'required|string|max:150',
            'tipe_lokasi'        => 'required|string|in:toko_retail,proyek_kontraktor,gudang_transit',
            'penanggung_jawab'   => 'required|string|max:100',
            'no_hp_toko'         => 'required|string|max:25',
            'alamat_lengkap'     => 'required|string',
            'titik_koordinat'    => 'nullable|string|max:100',
            'status_toko'        => 'required|string|in:aktif,non-aktif',
        ], [
            'kode_toko.unique'     => 'Kode Toko sudah digunakan.',
            'kode_customer.exists' => 'Customer pemilik yang dipilih tidak valid.',
            'kode_wilayah.exists'  => 'Wilayah zonasi yang dipilih tidak valid.',
        ]);

        TokoBangunan::create([
            'kode_toko'          => strtoupper($request->kode_toko),
            'kode_customer'      => $request->kode_customer,
            'kode_wilayah'       => $request->kode_wilayah,
            'nama_toko_bangunan' => $request->nama_toko_bangunan,
            'tipe_lokasi'        => $request->tipe_lokasi,
            'penanggung_jawab'   => $request->penanggung_jawab,
            'no_hp_toko'         => $request->no_hp_toko,
            'alamat_lengkap'     => $request->alamat_lengkap,
            'titik_koordinat'    => $request->titik_koordinat,
            'status_toko'        => $request->status_toko,
        ]);

        return redirect()->route('master.toko_bangunan.index')->with('sukses', "Data Toko / Proyek '{$request->nama_toko_bangunan}' berhasil disimpan.");
    }

    /**
     * Ambil detail lengkap 360 derajat data toko bangunan beserta transaksi (JSON API).
     */
    public function ambilDetail($kode_toko)
    {
        $toko = TokoBangunan::with(['customer', 'wilayah'])->where('kode_toko', $kode_toko)->firstOrFail();

        // Hitung estimasi transaksi terhubung
        $totalPenjualan = DB::table('penjualan')->where('kode_toko', $kode_toko)->sum('total_netto') ?? 0;
        $totalTransaksi = DB::table('penjualan')->where('kode_toko', $kode_toko)->count();
        $riwayatTransaksi = DB::table('penjualan')
            ->where('kode_toko', $kode_toko)
            ->orderBy('tanggal_penjualan', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'status'            => 'sukses',
            'toko'              => $toko,
            'customer'          => $toko->customer,
            'wilayah'           => $toko->wilayah,
            'total_penjualan'   => (float)$totalPenjualan,
            'total_transaksi'   => $totalTransaksi,
            'riwayat_transaksi' => $riwayatTransaksi,
        ]);
    }

    /**
     * Perbarui data toko bangunan.
     */
    public function perbarui(Request $request, $kode_toko)
    {
        $toko = TokoBangunan::where('kode_toko', $kode_toko)->firstOrFail();

        $request->validate([
            'kode_customer'      => 'required|string|exists:data_customer,kode_customer',
            'kode_wilayah'       => 'required|string|exists:data_wilayah,kode_wilayah',
            'nama_toko_bangunan' => 'required|string|max:150',
            'tipe_lokasi'        => 'required|string|in:toko_retail,proyek_kontraktor,gudang_transit',
            'penanggung_jawab'   => 'required|string|max:100',
            'no_hp_toko'         => 'required|string|max:25',
            'alamat_lengkap'     => 'required|string',
            'titik_koordinat'    => 'nullable|string|max:100',
            'status_toko'        => 'required|string|in:aktif,non-aktif',
        ]);

        $toko->update([
            'kode_customer'      => $request->kode_customer,
            'kode_wilayah'       => $request->kode_wilayah,
            'nama_toko_bangunan' => $request->nama_toko_bangunan,
            'tipe_lokasi'        => $request->tipe_lokasi,
            'penanggung_jawab'   => $request->penanggung_jawab,
            'no_hp_toko'         => $request->no_hp_toko,
            'alamat_lengkap'     => $request->alamat_lengkap,
            'titik_koordinat'    => $request->titik_koordinat,
            'status_toko'        => $request->status_toko,
        ]);

        return redirect()->route('master.toko_bangunan.index')->with('sukses', "Data Toko / Proyek '{$toko->nama_toko_bangunan}' berhasil diperbarui.");
    }

    /**
     * Hapus data toko bangunan.
     */
    public function hapus($kode_toko)
    {
        $toko = TokoBangunan::where('kode_toko', $kode_toko)->firstOrFail();
        $nama = $toko->nama_toko_bangunan;
        $toko->delete();

        return redirect()->route('master.toko_bangunan.index')->with('sukses', "Toko / Proyek '{$nama}' berhasil dihapus.");
    }

    /**
     * Hapus banyak data toko bangunan sekaligus (Hapus Massal).
     */
    public function hapusMassal(Request $request)
    {
        $daftarId = $request->input('daftar_id', []);
        if (empty($daftarId) || !is_array($daftarId)) {
            return redirect()->route('master.toko_bangunan.index')->with('gagal', 'Tidak ada toko atau proyek yang dipilih untuk dihapus.');
        }

        $berhasilDihapus = 0;

        DB::beginTransaction();
        try {
            foreach ($daftarId as $kode) {
                $toko = TokoBangunan::where('kode_toko', $kode)->first();
                if ($toko) {
                    $toko->delete();
                    $berhasilDihapus++;
                }
            }
            DB::commit();

            return redirect()->route('master.toko_bangunan.index')->with('sukses', "{$berhasilDihapus} data toko / proyek terpilih berhasil dihapus.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.toko_bangunan.index')->with('gagal', 'Terjadi kesalahan saat menghapus data massal: ' . $th->getMessage());
        }
    }

    /**
     * API Generator Kode Otomatis
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->query('mode', 'gap');
        if ($mode === 'acak') {
            $kode = 'TKB-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            $keterangan = 'Kode acak alfanumerik';
        } else {
            $kode = GeneratorKodeOtomatis::buatKode('data_toko_bangunan', 'kode_toko', 'TKB-', 3);
            $keterangan = 'Nomor urut terkecil yang tersedia (Gap-Filling)';
        }

        return response()->json([
            'status'     => 'sukses',
            'kode'       => $kode,
            'keterangan' => $keterangan,
        ]);
    }
}
