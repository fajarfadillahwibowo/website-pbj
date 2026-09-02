<?php

namespace App\Http\Controllers\Keuangan\AP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keuangan\PembelianSO;
use App\Models\Master\Customer;
use Illuminate\Support\Facades\DB;

class PembelianSOController extends Controller
{
    /**
     * Tampilkan daftar Pembelian SO ke Pabrik Semen (AP).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterStatus = $request->input('status');

        $query = DB::table('pembelian_so')
            ->leftJoin('data_customer', 'pembelian_so.kode_customer', '=', 'data_customer.kode_customer')
            ->leftJoin('list_gudang_so', 'pembelian_so.kode_gudang', '=', 'list_gudang_so.kode_gudang')
            ->select(
                'pembelian_so.*',
                'data_customer.nama_toko_bangunan',
                'list_gudang_so.nama_gudang',
                'list_gudang_so.plant'
            );

        if ($filterStatus) {
            $query->where('pembelian_so.status_so', $filterStatus);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('pembelian_so.nomor_so', 'like', "%{$kataKunci}%")
                  ->orWhere('data_customer.nama_toko_bangunan', 'like', "%{$kataKunci}%")
                  ->orWhere('list_gudang_so.nama_gudang', 'like', "%{$kataKunci}%");
            });
        }

        $daftarSO = $query->orderBy('pembelian_so.id_so', 'desc')->get();
        $daftarCustomer = DB::table('data_customer')->orderBy('nama_toko_bangunan')->get();
        $daftarGudang = DB::table('list_gudang_so')->orderBy('nama_gudang')->get();

        $totalSO = DB::table('pembelian_so')->count();
        $totalNilaiSO = DB::table('pembelian_so')->sum('total_harga');
        $totalZak = DB::table('pembelian_so')->sum('jumlah_zak');

        return view('keuangan.ap.pembelian_so', compact(
            'daftarSO',
            'daftarCustomer',
            'daftarGudang',
            'kataKunci',
            'filterStatus',
            'totalSO',
            'totalNilaiSO',
            'totalZak'
        ));
    }

    /**
     * Simpan Pembelian SO baru ke pabrik semen.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_customer' => 'required|string|exists:data_customer,kode_customer',
            'kode_gudang'   => 'required|string|exists:list_gudang_so,kode_gudang',
            'tanggal_so'    => 'required|date',
            'jumlah_zak'    => 'required|integer|min:1',
            'harga_satuan'  => 'required|numeric|min:1',
        ]);

        $jumlahZak = (int) $request->jumlah_zak;
        $hargaSatuan = (float) $request->harga_satuan;
        $totalHarga = $jumlahZak * $hargaSatuan;
        $nomorSO = 'SO-PBJ-' . date('Ymd') . '-' . rand(100, 999);

        DB::table('pembelian_so')->insert([
            'nomor_so'      => $nomorSO,
            'tanggal_so'    => $request->tanggal_so,
            'kode_customer' => $request->kode_customer,
            'kode_gudang'   => $request->kode_gudang,
            'jumlah_zak'    => $jumlahZak,
            'harga_satuan'  => $hargaSatuan,
            'total_harga'   => $totalHarga,
            'status_so'     => 'disetujui',
            'dibuat_oleh'   => 'staff_ap',
            'dibuat_pada'   => now(),
        ]);

        return redirect()->route('keuangan.ap.pembelian_so')->with('sukses', "Sales Order {$nomorSO} ({$jumlahZak} Zak - Rp " . number_format($totalHarga, 0, ',', '.') . ") berhasil diterbitkan.");
    }

    /**
     * Generator Nomor SO Otomatis (Daur Ulang Slot vs Acak Tanggal).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        return \App\Helpers\GeneratorKodeOtomatis::responJson('pembelian_so', 'nomor_so', 'PO-', $mode, 3, true);
    }
}
