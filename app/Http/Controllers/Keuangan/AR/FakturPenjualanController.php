<?php

namespace App\Http\Controllers\Keuangan\AR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\FakturPenjualan;
use App\Models\Keuangan\Piutang;
use App\Models\Keuangan\DepositCustomer;
use App\Models\Master\Customer;
use App\Models\Master\TokoBangunan;
use App\Models\Master\Barang;

class FakturPenjualanController extends Controller
{
    /**
     * Tampilkan daftar transaksi Faktur Penjualan Semen (AR).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterStatus = $request->input('status');
        $filterMetode = $request->input('metode');

        $query = FakturPenjualan::with(['customer', 'tokoBangunan']);

        if ($filterStatus) {
            $query->where('status_pembayaran', $filterStatus);
        }

        if ($filterMetode) {
            $query->where('metode_pembayaran', $filterMetode);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_faktur', 'like', "%{$kataKunci}%")
                  ->orWhereHas('customer', function ($c) use ($kataKunci) {
                      $c->where('nama_pemilik', 'like', "%{$kataKunci}%")
                        ->orWhere('nama_toko_bangunan', 'like', "%{$kataKunci}%");
                  });
            });
        }

        $daftarFaktur = $query->orderBy('id_penjualan', 'desc')->get();
        $daftarCustomer = Customer::orderBy('nama_pemilik')->get();
        $daftarToko = TokoBangunan::with('customer')->where('status_toko', 'aktif')->orderBy('nama_toko_bangunan')->get();
        $daftarBarang = Barang::orderBy('nama_barang')->get();

        // Opsi Dropdown Toko Bangunan
        $opsiToko = $daftarToko->map(fn($t) => [
            'nilai' => $t->kode_toko,
            'label' => $t->nama_toko_bangunan . ' (' . ($t->customer->nama_pemilik ?? '-') . ')',
            'sub'   => 'Customer Induk: ' . ($t->customer->nama_pemilik ?? '-') . ' | PIC: ' . $t->penanggung_jawab
        ])->toArray();

        // Statistik Penjualan
        $totalPenjualan = FakturPenjualan::sum('total_netto');
        $totalLunas = FakturPenjualan::where('status_pembayaran', 'Lunas')->sum('total_netto');
        $totalPiutang = FakturPenjualan::sum('sisa_piutang');
        $totalFaktur = FakturPenjualan::count();

        return view('keuangan.ar.faktur_penjualan', compact(
            'daftarFaktur',
            'daftarCustomer',
            'daftarToko',
            'opsiToko',
            'daftarBarang',
            'kataKunci',
            'filterStatus',
            'filterMetode',
            'totalPenjualan',
            'totalLunas',
            'totalPiutang',
            'totalFaktur'
        ));
    }

    /**
     * Simpan Faktur Penjualan Baru dan mutasikan saldo piutang/deposit terkait.
     */
    public function store(Request $request)
    {
        // Jika kode_toko dipilih, otomatis ambil kode_customer dari toko tersebut
        if ($request->filled('kode_toko')) {
            $toko = TokoBangunan::where('kode_toko', $request->kode_toko)->first();
            if ($toko) {
                $request->merge(['kode_customer' => $toko->kode_customer]);
            }
        }

        $request->validate([
            'kode_customer'     => 'required|string|exists:data_customer,kode_customer',
            'kode_toko'         => 'nullable|string|exists:data_toko_bangunan,kode_toko',
            'tanggal_penjualan' => 'required|date',
            'metode_pembayaran' => 'required|string|in:Tunai,Transfer,Kredit / Piutang,Potong Deposit',
            'total_bruto'       => 'required|numeric|min:1',
            'diskon'            => 'nullable|numeric|min:0',
        ], [
            'kode_customer.exists' => 'Customer toko bangunan tidak ditemukan.',
            'total_bruto.min'      => 'Total nominal penjualan minimal Rp 1.',
        ]);

        $customer = Customer::findOrFail($request->kode_customer);
        $totalBruto = (float) $request->total_bruto;
        $diskon = (float) ($request->diskon ?? 0);
        $totalNetto = max(0, $totalBruto - $diskon);

        $metode = $request->metode_pembayaran;
        $nomorFaktur = 'INV-' . date('Ymd') . '-' . sprintf('%03d', rand(100, 999));

        DB::beginTransaction();
        try {
            $jumlahDibayar = 0.00;
            $sisaPiutang = 0.00;
            $statusPembayaran = 'Belum Lunas';
            $jatuhTempo = null;

            if ($metode === 'Kredit / Piutang') {
                // Periksa batas plafon piutang
                if (($customer->saldo_piutang + $totalNetto) > $customer->plafon_piutang) {
                    $sisaPlafon = max(0, $customer->plafon_piutang - $customer->saldo_piutang);
                    return redirect()->back()->withInput()->with('gagal', "Penjualan melebihi Plafon Kredit customer '{$customer->nama_pemilik}'. Sisa plafon tersedia: Rp " . number_format($sisaPlafon, 0, ',', '.'));
                }

                $sisaPiutang = $totalNetto;
                $jumlahDibayar = 0.00;
                $statusPembayaran = 'Belum Lunas';
                $jatuhTempo = $request->jatuh_tempo ?? date('Y-m-d', strtotime('+30 days'));

                // Update saldo piutang customer
                $customer->increment('saldo_piutang', $totalNetto);
            } elseif ($metode === 'Potong Deposit') {
                // Periksa saldo deposit customer
                if ($customer->saldo_deposit < $totalNetto) {
                    return redirect()->back()->withInput()->with('gagal', "Saldo deposit customer '{$customer->nama_pemilik}' tidak mencukupi (Tersedia: Rp " . number_format($customer->saldo_deposit, 0, ',', '.') . ").");
                }

                $jumlahDibayar = $totalNetto;
                $sisaPiutang = 0.00;
                $statusPembayaran = 'Lunas';

                // Potong saldo deposit customer
                $customer->decrement('saldo_deposit', $totalNetto);

                // Catat mutasi deposit
                DepositCustomer::create([
                    'nomor_bukti_deposit' => 'DEP-OUT-' . date('Ymd') . '-' . rand(100, 999),
                    'kode_customer'       => $customer->kode_customer,
                    'tanggal_deposit'     => $request->tanggal_penjualan,
                    'tipe_mutasi'         => 'Keluar / Terpakai',
                    'jumlah_nominal'      => $totalNetto,
                    'saldo_akhir_deposit' => $customer->fresh()->saldo_deposit,
                    'keterangan'          => "Potong deposit untuk Faktur {$nomorFaktur}",
                    'dibuat_oleh'         => 'staff_ar',
                ]);
            } else {
                // Tunai atau Transfer Bank
                $jumlahDibayar = $totalNetto;
                $sisaPiutang = 0.00;
                $statusPembayaran = 'Lunas';
            }

            // Buat Faktur Penjualan
            $faktur = FakturPenjualan::create([
                'nomor_faktur'       => $nomorFaktur,
                'tanggal_penjualan'  => $request->tanggal_penjualan,
                'kode_customer'      => $customer->kode_customer,
                'kode_toko'          => $request->kode_toko ?? null,
                'metode_pembayaran'  => $metode,
                'total_bruto'        => $totalBruto,
                'diskon'             => $diskon,
                'total_netto'        => $totalNetto,
                'jumlah_dibayar'     => $jumlahDibayar,
                'sisa_piutang'       => $sisaPiutang,
                'status_pembayaran'  => $statusPembayaran,
                'jatuh_tempo'        => $jatuhTempo,
                'id_rekening'        => $metode === 'Transfer' ? 1 : null,
                'status_persetujuan' => 'disetujui',
                'dibuat_oleh'        => 'staff_ar',
            ]);

            // Jika kredit, catat ke list_piutang
            if ($metode === 'Kredit / Piutang') {
                Piutang::create([
                    'id_penjualan'        => $faktur->id_penjualan,
                    'kode_customer'       => $customer->kode_customer,
                    'jumlah_piutang'      => $totalNetto,
                    'sisa_piutang'        => $totalNetto,
                    'tanggal_terbit'      => $request->tanggal_penjualan,
                    'tanggal_jatuh_tempo' => $jatuhTempo,
                    'status_piutang'      => 'belum_lunas',
                ]);
            }

            DB::commit();

            return redirect()->route('keuangan.ar.faktur')->with('sukses', "Faktur Penjualan {$nomorFaktur} ({$metode}) senilai Rp " . number_format($totalNetto, 0, ',', '.') . " berhasil diterbitkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('gagal', "Gagal menerbitkan faktur: " . $e->getMessage());
        }
    }
}
