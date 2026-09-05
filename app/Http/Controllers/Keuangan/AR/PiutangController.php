<?php

namespace App\Http\Controllers\Keuangan\AR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\Piutang;
use App\Models\Keuangan\FakturPenjualan;
use App\Models\Master\Customer;
use App\Helpers\FilterKeuanganHelper;
use App\Services\Keuangan\MesinJurnalOtomatis;
use Carbon\Carbon;

class PiutangController extends Controller
{
    /**
     * Tampilkan daftar monitoring piutang toko bangunan (AR).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterStatus = $request->input('status');
        $filterPeriode = $request->input('periode');
        $filterTglMulai = $request->input('tgl_mulai');
        $filterTglSelesai = $request->input('tgl_selesai');

        $query = Piutang::with(['customer', 'penjualan']);

        if ($filterStatus) {
            $query->where('status_piutang', $filterStatus);
        }

        // Filter Jatuh Tempo / Periode
        if ($filterPeriode === 'overdue') {
            $query->whereDate('tanggal_jatuh_tempo', '<', Carbon::today())
                  ->where('sisa_piutang', '>', 0);
        } elseif ($filterPeriode === '30_hari') {
            $query->whereBetween('tanggal_jatuh_tempo', [Carbon::today(), Carbon::today()->addDays(30)]);
        } elseif ($filterPeriode === 'bulan_ini') {
            $query->whereMonth('tanggal_jatuh_tempo', Carbon::now()->month)
                  ->whereYear('tanggal_jatuh_tempo', Carbon::now()->year);
        } elseif ($filterPeriode === 'kustom') {
            if (!empty($filterTglMulai) && !empty($filterTglSelesai)) {
                $query->whereDate('tanggal_jatuh_tempo', '>=', $filterTglMulai)
                      ->whereDate('tanggal_jatuh_tempo', '<=', $filterTglSelesai);
            } elseif (!empty($filterTglMulai)) {
                $query->whereDate('tanggal_jatuh_tempo', '>=', $filterTglMulai);
            } elseif (!empty($filterTglSelesai)) {
                $query->whereDate('tanggal_jatuh_tempo', '<=', $filterTglSelesai);
            }
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->whereHas('customer', function ($c) use ($kataKunci) {
                    $c->where('nama_toko_bangunan', 'like', "%{$kataKunci}%")
                      ->orWhere('kode_customer', 'like', "%{$kataKunci}%");
                })->orWhereHas('penjualan', function ($p) use ($kataKunci) {
                    $p->where('nomor_faktur', 'like', "%{$kataKunci}%");
                });
            });
        }

        $daftarPiutang = $query->orderBy('id_piutang', 'desc')->get();

        $opsiPeriodePiutang = [
            ['nilai' => '', 'label' => '-- Semua Jatuh Tempo --'],
            ['nilai' => 'overdue', 'label' => 'Lewat Jatuh Tempo (Overdue)'],
            ['nilai' => 'bulan_ini', 'label' => 'Jatuh Tempo Bulan Ini'],
            ['nilai' => '30_hari', 'label' => '30 Hari ke Depan'],
            ['nilai' => 'kustom', 'label' => 'Rentang Kustom'],
        ];

        $jumlahFilterAktif = FilterKeuanganHelper::hitungFilterAktif([
            'cari'        => $kataKunci,
            'status'      => $filterStatus,
            'periode'     => $filterPeriode,
            'tgl_mulai'   => $filterTglMulai,
            'tgl_selesai' => $filterTglSelesai,
        ]);

        $totalPiutang = Piutang::sum('jumlah_piutang');
        $totalSisa = Piutang::sum('sisa_piutang');
        $totalTerbayar = max(0, $totalPiutang - $totalSisa);
        $totalCustomerPiutang = Piutang::where('sisa_piutang', '>', 0)->distinct('kode_customer')->count('kode_customer');

        return view('keuangan.ar.list_piutang', compact(
            'daftarPiutang',
            'kataKunci',
            'filterStatus',
            'filterPeriode',
            'filterTglMulai',
            'filterTglSelesai',
            'opsiPeriodePiutang',
            'jumlahFilterAktif',
            'totalPiutang',
            'totalSisa',
            'totalTerbayar',
            'totalCustomerPiutang'
        ));
    }

    /**
     * Proses pelunasan atau cicilan pembayaran piutang toko.
     */
    public function bayar(Request $request, $id_piutang)
    {
        $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
        ], [
            'jumlah_bayar.min' => 'Nominal pembayaran minimal Rp 1.',
        ]);

        $piutang = Piutang::with(['customer', 'penjualan'])->findOrFail($id_piutang);
        $jumlahBayar = (float) $request->jumlah_bayar;

        if ($jumlahBayar > $piutang->sisa_piutang) {
            return redirect()->back()->with('gagal', "Nominal pembayaran (Rp " . number_format($jumlahBayar, 0, ',', '.') . ") melebihi sisa piutang (Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . ").");
        }

        DB::beginTransaction();
        try {
            $sisaBaru = max(0, $piutang->sisa_piutang - $jumlahBayar);
            $statusBaru = $sisaBaru == 0 ? 'lunas' : 'sebagian';

            $piutang->update([
                'sisa_piutang'   => $sisaBaru,
                'status_piutang' => $statusBaru,
            ]);

            // Potong saldo piutang di data_customer
            $piutang->customer->decrement('saldo_piutang', $jumlahBayar);

            // Update faktur penjualan terkait
            if ($piutang->penjualan) {
                $faktur = $piutang->penjualan;
                $fakturSisaBaru = max(0, $faktur->sisa_piutang - $jumlahBayar);
                $faktur->update([
                    'jumlah_dibayar'    => $faktur->jumlah_dibayar + $jumlahBayar,
                    'sisa_piutang'      => $fakturSisaBaru,
                    'status_pembayaran' => $fakturSisaBaru == 0 ? 'Lunas' : 'Belum Lunas',
                ]);
            }

            // Auto-Journal ke Jurnal Umum Akuntansi (Debit Kas/Bank, Kredit Piutang)
            $nomorFaktur = $piutang->penjualan ? $piutang->penjualan->nomor_faktur : "PIUTANG-{$piutang->id_piutang}";
            MesinJurnalOtomatis::jurnalPelunasanPiutang(
                $nomorFaktur,
                date('Y-m-d'),
                $jumlahBayar,
                null,
                auth()->user()->username ?? 'spv_keuangan',
                "Penerimaan Pelunasan/Cicilan Piutang {$nomorFaktur} - {$piutang->customer->nama_pemilik}"
            );

            DB::commit();

            return redirect()->route('keuangan.ar.piutang')->with('sukses', "Pembayaran piutang sebesar Rp " . number_format($jumlahBayar, 0, ',', '.') . " untuk {$piutang->customer->nama_toko_bangunan} berhasil dicatat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal memproses pembayaran piutang: " . $e->getMessage());
        }
    }
}
