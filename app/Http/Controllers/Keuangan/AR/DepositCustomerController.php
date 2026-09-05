<?php

namespace App\Http\Controllers\Keuangan\AR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\DepositCustomer;
use App\Models\Master\Customer;
use App\Helpers\GeneratorKodeOtomatis;
use App\Helpers\FilterKeuanganHelper;
use App\Services\Keuangan\MesinJurnalOtomatis;

class DepositCustomerController extends Controller
{
    /**
     * Tampilkan saldo deposit pelanggan dan riwayat mutasi setoran (AR).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterTipe = $request->input('tipe');
        $filterPeriode = $request->input('periode');
        $filterTglMulai = $request->input('tgl_mulai');
        $filterTglSelesai = $request->input('tgl_selesai');

        $query = DepositCustomer::with('customer');

        if ($filterTipe) {
            $query->where('tipe_mutasi', $filterTipe);
        }

        // Terapkan filter tanggal terpadu
        FilterKeuanganHelper::terapkanFilterTanggal($query, 'tanggal_transaksi', $filterPeriode, $filterTglMulai, $filterTglSelesai);

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
        $daftarRekening = DB::table('data_rekening')->get();

        $opsiCustomerDeposit = $daftarCustomer->map(function ($c) {
            return [
                'nilai' => $c->kode_customer,
                'label' => "{$c->nama_toko_bangunan} ({$c->nama_pemilik})",
                'sublabel' => "Saldo: Rp " . number_format($c->saldo_deposit, 0, ',', '.')
            ];
        })->toArray();

        $opsiRekeningDeposit = $daftarRekening->map(function ($r) {
            return [
                'nilai' => $r->id_rekening,
                'label' => "{$r->nama_bank} - {$r->nomor_rekening} ({$r->atas_nama})",
                'sublabel' => "Saldo: Rp " . number_format($r->saldo_rekening, 0, ',', '.')
            ];
        })->toArray();

        $opsiPeriode = FilterKeuanganHelper::opsiPeriode();
        $jumlahFilterAktif = FilterKeuanganHelper::hitungFilterAktif([
            'cari'        => $kataKunci,
            'tipe'        => $filterTipe,
            'periode'     => $filterPeriode,
            'tgl_mulai'   => $filterTglMulai,
            'tgl_selesai' => $filterTglSelesai,
        ]);

        $totalDepositAktif = Customer::sum('saldo_deposit');
        $totalMasuk = DepositCustomer::where('tipe_mutasi', 'Masuk')->sum('jumlah_nominal');
        $totalTerpakai = DepositCustomer::where('tipe_mutasi', 'Keluar / Terpakai')->sum('jumlah_nominal');
        $totalMitraDeposit = Customer::where('saldo_deposit', '>', 0)->count();

        return view('keuangan.ar.deposit_customer', compact(
            'daftarMutasi',
            'daftarCustomer',
            'daftarRekening',
            'opsiCustomerDeposit',
            'opsiRekeningDeposit',
            'kataKunci',
            'filterTipe',
            'filterPeriode',
            'filterTglMulai',
            'filterTglSelesai',
            'opsiPeriode',
            'jumlahFilterAktif',
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
            'kode_customer'       => 'required|string|exists:data_customer,kode_customer',
            'tanggal_deposit'     => 'required|date',
            'jumlah_nominal'      => 'required|numeric|min:100000',
            'id_rekening_tujuan'  => 'nullable|integer|exists:data_rekening,id_rekening',
            'keterangan'          => 'nullable|string',
        ], [
            'kode_customer.exists' => 'Customer toko bangunan tidak ditemukan.',
            'jumlah_nominal.min'   => 'Nominal top-up deposit minimal Rp 100.000.',
        ]);

        $customer = Customer::findOrFail($request->kode_customer);
        $nominal = (float) $request->jumlah_nominal;
        $nomorBukti = GeneratorKodeOtomatis::buatKodeTransaksi('list_deposit', 'nomor_bukti_deposit', 'DEP-IN-', $request->tanggal_deposit);
        $pembuat = auth()->user()->username ?? 'spv_keuangan';

        DB::beginTransaction();
        try {
            // 1. Tambah saldo deposit customer
            $customer->increment('saldo_deposit', $nominal);

            // 2. Tambah saldo rekening bank tujuan jika dipilih
            if ($request->id_rekening_tujuan) {
                DB::table('data_rekening')
                    ->where('id_rekening', $request->id_rekening_tujuan)
                    ->increment('saldo_rekening', $nominal);
            }

            // 3. Catat riwayat mutasi deposit
            DepositCustomer::create([
                'nomor_bukti_deposit' => $nomorBukti,
                'kode_customer'       => $customer->kode_customer,
                'tanggal_deposit'     => $request->tanggal_deposit,
                'tipe_mutasi'         => 'Masuk',
                'jumlah_nominal'      => $nominal,
                'saldo_akhir_deposit' => $customer->fresh()->saldo_deposit,
                'keterangan'          => $request->keterangan ?? "Top-up deposit via transfer bank",
                'dibuat_oleh'         => $pembuat,
            ]);

            // 4. Auto-Journal ke Jurnal Umum Akuntansi (Debit Kas/Bank, Kredit Titipan Deposit 2102)
            MesinJurnalOtomatis::jurnalTopUpDeposit(
                $nomorBukti,
                $request->tanggal_deposit,
                $nominal,
                $request->id_rekening_tujuan,
                $pembuat,
                "Penerimaan Top-up Deposit {$nomorBukti} - {$customer->nama_toko_bangunan}"
            );

            DB::commit();

            return redirect()->route('keuangan.ar.deposit')->with('sukses', "Top-up deposit Rp " . number_format($nominal, 0, ',', '.') . " untuk {$customer->nama_toko_bangunan} berhasil disimpan dan dijurnal otomatis.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal memproses top-up deposit: " . $e->getMessage());
        }
    }
}
