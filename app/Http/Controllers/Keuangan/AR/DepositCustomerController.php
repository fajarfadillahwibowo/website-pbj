<?php

namespace App\Http\Controllers\Keuangan\AR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\DepositCustomer;
use App\Models\Master\Customer;

class DepositCustomerController extends Controller
{
    /**
     * Tampilkan saldo deposit pelanggan dan riwayat mutasi setoran (AR).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterTipe = $request->input('tipe');

        $query = DepositCustomer::with('customer');

        if ($filterTipe) {
            $query->where('tipe_mutasi', $filterTipe);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_bukti_deposit', 'like', "%{$kataKunci}%")
                  ->orWhereHas('customer', function ($c) use ($kataKunci) {
                      $c->where('nama_toko_bangunan', 'like', "%{$kataKunci}%")
                        ->orWhere('kode_customer', 'like', "%{$kataKunci}%");
                  });
            });
        }

        $daftarMutasi = $query->orderBy('id_deposit', 'desc')->get();
        $daftarCustomer = Customer::orderBy('nama_toko_bangunan')->get();

        $totalDepositAktif = Customer::sum('saldo_deposit');
        $totalMasuk = DepositCustomer::where('tipe_mutasi', 'Masuk')->sum('jumlah_nominal');
        $totalTerpakai = DepositCustomer::where('tipe_mutasi', 'Keluar / Terpakai')->sum('jumlah_nominal');
        $totalMitraDeposit = Customer::where('saldo_deposit', '>', 0)->count();

        return view('keuangan.ar.deposit_customer', compact(
            'daftarMutasi',
            'daftarCustomer',
            'kataKunci',
            'filterTipe',
            'totalDepositAktif',
            'totalMasuk',
            'totalTerpakai',
            'totalMitraDeposit'
        ));
    }

    /**
     * Top-up saldo deposit customer baru.
     */
    public function topUp(Request $request)
    {
        $request->validate([
            'kode_customer'   => 'required|string|exists:data_customer,kode_customer',
            'tanggal_deposit' => 'required|date',
            'jumlah_nominal'  => 'required|numeric|min:100000',
            'keterangan'      => 'nullable|string',
        ], [
            'kode_customer.exists' => 'Customer toko bangunan tidak ditemukan.',
            'jumlah_nominal.min'   => 'Nominal top-up deposit minimal Rp 100.000.',
        ]);

        $customer = Customer::findOrFail($request->kode_customer);
        $nominal = (float) $request->jumlah_nominal;
        $nomorBukti = 'DEP-IN-' . date('Ymd') . '-' . rand(100, 999);

        DB::beginTransaction();
        try {
            $customer->increment('saldo_deposit', $nominal);

            DepositCustomer::create([
                'nomor_bukti_deposit' => $nomorBukti,
                'kode_customer'       => $customer->kode_customer,
                'tanggal_deposit'     => $request->tanggal_deposit,
                'tipe_mutasi'         => 'Masuk',
                'jumlah_nominal'      => $nominal,
                'saldo_akhir_deposit' => $customer->fresh()->saldo_deposit,
                'keterangan'          => $request->keterangan ?? "Top-up deposit via transfer bank",
                'dibuat_oleh'         => 'staff_ar',
            ]);

            DB::commit();

            return redirect()->route('keuangan.ar.deposit')->with('sukses', "Top-up deposit Rp " . number_format($nominal, 0, ',', '.') . " untuk {$customer->nama_toko_bangunan} berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal memproses top-up deposit: " . $e->getMessage());
        }
    }
}
