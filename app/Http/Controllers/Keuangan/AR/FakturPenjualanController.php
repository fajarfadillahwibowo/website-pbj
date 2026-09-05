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
use App\Helpers\GeneratorKodeOtomatis;
use App\Helpers\FilterKeuanganHelper;
use App\Services\Keuangan\MesinJurnalOtomatis;

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
        $filterPeriode = $request->input('periode');
        $filterTglMulai = $request->input('tgl_mulai');
        $filterTglSelesai = $request->input('tgl_selesai');

        $query = FakturPenjualan::with(['customer', 'tokoBangunan', 'barang']);

        if ($filterStatus) {
            $query->where('status_pembayaran', $filterStatus);
        }

        if ($filterMetode) {
            if ($filterMetode === 'Kredit' || $filterMetode === 'Kredit / Piutang') {
                $query->whereIn('metode_pembayaran', ['Kredit', 'Kredit / Piutang']);
            } elseif ($filterMetode === 'Deposit' || $filterMetode === 'Potong Deposit') {
                $query->whereIn('metode_pembayaran', ['Deposit', 'Potong Deposit']);
            } else {
                $query->where('metode_pembayaran', $filterMetode);
            }
        }

        // Terapkan filter periode tanggal terpadu
        FilterKeuanganHelper::terapkanFilterTanggal($query, 'tanggal_penjualan', $filterPeriode, $filterTglMulai, $filterTglSelesai);

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_faktur', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_barang', 'like', "%{$kataKunci}%")
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

        // Opsi Periode Standar
        $opsiPeriode = FilterKeuanganHelper::opsiPeriode();
        $jumlahFilterAktif = FilterKeuanganHelper::hitungFilterAktif([
            'cari'        => $kataKunci,
            'status'      => $filterStatus,
            'metode'      => $filterMetode,
            'periode'     => $filterPeriode,
            'tgl_mulai'   => $filterTglMulai,
            'tgl_selesai' => $filterTglSelesai,
        ]);

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
            'filterPeriode',
            'filterTglMulai',
            'filterTglSelesai',
            'opsiPeriode',
            'jumlahFilterAktif',
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
        // Jika kode_toko diisi, sinkronkan kode_customer pemilik
        if ($request->filled('kode_toko')) {
            $toko = TokoBangunan::where('kode_toko', $request->kode_toko)->first();
            if ($toko) {
                $request->merge(['kode_customer' => $toko->kode_customer]);
            }
        }

        $request->validate([
            'kode_customer'     => 'required|string|exists:data_customer,kode_customer',
            'kode_toko'         => 'nullable|string|exists:data_toko_bangunan,kode_toko',
            'kode_barang'       => 'required|string|exists:data_semen,kode_barang',
            'jumlah_zak'        => 'required|numeric|min:1',
            'harga_satuan'      => 'required|numeric|min:1',
            'tanggal_penjualan' => 'required|date',
            'metode_pembayaran' => 'required|string|in:Tunai,Transfer,Kredit,Kredit / Piutang,Deposit,Potong Deposit',
            'total_bruto'       => 'required|numeric|min:1',
            'diskon'            => 'nullable|numeric|min:0',
        ], [
            'kode_customer.exists' => 'Customer toko bangunan tidak ditemukan.',
            'kode_barang.exists'   => 'Produk semen tidak ditemukan dalam master data.',
            'jumlah_zak.min'       => 'Kuantitas semen minimal 1 zak / satuan.',
            'harga_satuan.min'     => 'Harga satuan semen minimal Rp 1.',
            'total_bruto.min'      => 'Total nominal penjualan minimal Rp 1.',
        ]);

        $customer = Customer::findOrFail($request->kode_customer);
        $barang = Barang::find($request->kode_barang);
        $namaBarang = $barang ? $barang->nama_barang : 'Semen Portland (PCC)';
        $satuanBarang = $barang ? ($barang->satuan_barang ?? 'Zak') : 'Zak';
        $jumlahZak = (int) $request->jumlah_zak;
        $hargaSatuan = (float) $request->harga_satuan;

        $totalBruto = (float) $request->total_bruto;
        $diskon = (float) ($request->diskon ?? 0);
        $totalNetto = max(0, $totalBruto - $diskon);

        $metode = $request->metode_pembayaran;
        if ($metode === 'Kredit') {
            $metode = 'Kredit / Piutang';
        } elseif ($metode === 'Deposit') {
            $metode = 'Potong Deposit';
        }
        $nomorFaktur = GeneratorKodeOtomatis::buatKodeTransaksi('penjualan', 'nomor_faktur', 'INV-', $request->tanggal_penjualan);
        $pembuat = auth()->user()->username ?? 'spv_keuangan';

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
                    'nomor_bukti_deposit' => GeneratorKodeOtomatis::buatKodeTransaksi('list_deposit', 'nomor_bukti_deposit', 'DEP-OUT-', $request->tanggal_penjualan),
                    'kode_customer'       => $customer->kode_customer,
                    'tanggal_deposit'     => $request->tanggal_penjualan,
                    'tipe_mutasi'         => 'Keluar / Terpakai',
                    'jumlah_nominal'      => $totalNetto,
                    'saldo_akhir_deposit' => $customer->fresh()->saldo_deposit,
                    'keterangan'          => "Potong deposit untuk Faktur {$nomorFaktur}",
                    'dibuat_oleh'         => $pembuat,
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
                'kode_barang'        => $request->kode_barang,
                'nama_barang'        => $namaBarang,
                'satuan_barang'      => $satuanBarang,
                'jumlah_zak'         => $jumlahZak,
                'harga_satuan'       => $hargaSatuan,
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
                'dibuat_oleh'        => $pembuat,
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

            // Auto-Journal ke Jurnal Umum Akuntansi (Double-Entry Seimbang)
            $nomorJurnal = MesinJurnalOtomatis::jurnalFakturPenjualan(
                $nomorFaktur,
                $request->tanggal_penjualan,
                $metode,
                $totalNetto,
                $metode === 'Transfer' ? 1 : null,
                $pembuat,
                "Faktur Penjualan {$nomorFaktur} - {$customer->nama_pemilik}"
            );

            DB::commit();

            return redirect()->route('keuangan.ar.faktur')->with('sukses', "Faktur Penjualan {$nomorFaktur} ({$metode}) senilai Rp " . number_format($totalNetto, 0, ',', '.') . " berhasil diterbitkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('gagal', "Gagal menerbitkan faktur: " . $e->getMessage());
        }
    }

    /**
     * Cetak dokumen Faktur Penjualan / Invoice resmi PT Putra Balkom Jaya.
     */
    public function cetak($nomor_faktur)
    {
        $faktur = FakturPenjualan::with(['customer', 'tokoBangunan', 'barang'])
            ->where('nomor_faktur', $nomor_faktur)
            ->firstOrFail();

        return view('keuangan.ar.cetak_faktur', compact('faktur'));
    }
}
