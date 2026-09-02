<?php

namespace App\Http\Controllers\Keuangan\AR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\Piutang;
use App\Models\Keuangan\FakturPenjualan;
use App\Models\Master\Customer;
use App\Helpers\GeneratorKodeOtomatis;
use Carbon\Carbon;

class PiutangController extends Controller
{
    /**
     * Tampilkan daftar monitoring piutang toko bangunan (AR).
     */
    public function index(Request $request)
    {
        $this->pastikanDataAwalTersedia();

        $kataKunci = $request->input('cari');
        $filterStatus = $request->input('status');
        $filterCustomer = $request->input('customer');

        $query = Piutang::with(['customer', 'penjualan']);

        if ($filterStatus) {
            if ($filterStatus === 'terlambat') {
                $query->where('status_piutang', '!=', 'lunas')
                      ->where('sisa_piutang', '>', 0)
                      ->where('tanggal_jatuh_tempo', '<', Carbon::today()->format('Y-m-d'));
            } else {
                $query->where('status_piutang', $filterStatus);
            }
        }

        if ($filterCustomer) {
            $query->where('kode_customer', $filterCustomer);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->whereHas('customer', function ($c) use ($kataKunci) {
                    $c->where('nama_toko_bangunan', 'like', "%{$kataKunci}%")
                      ->orWhere('kode_customer', 'like', "%{$kataKunci}%")
                      ->orWhere('nama_pemilik', 'like', "%{$kataKunci}%");
                })->orWhereHas('penjualan', function ($p) use ($kataKunci) {
                    $p->where('nomor_faktur', 'like', "%{$kataKunci}%");
                });
            });
        }

        $daftarPiutang = $query->orderBy('tanggal_jatuh_tempo', 'asc')
                               ->orderBy('id_piutang', 'desc')
                               ->get();

        // Data pendukung form & filter
        $daftarCustomer = Customer::orderBy('nama_toko_bangunan', 'asc')->get();

        // 5 Ringkasan Statistik Piutang
        $totalPiutang = Piutang::sum('jumlah_piutang');
        $totalSisa = Piutang::sum('sisa_piutang');
        $totalTerbayar = max(0, $totalPiutang - $totalSisa);
        $totalCustomerPiutang = Piutang::where('sisa_piutang', '>', 0)->distinct('kode_customer')->count('kode_customer');
        $totalTerlambat = Piutang::where('status_piutang', '!=', 'lunas')
            ->where('sisa_piutang', '>', 0)
            ->where('tanggal_jatuh_tempo', '<', Carbon::today()->format('Y-m-d'))
            ->sum('sisa_piutang');

        return view('keuangan.ar.list_piutang', compact(
            'daftarPiutang',
            'daftarCustomer',
            'kataKunci',
            'filterStatus',
            'filterCustomer',
            'totalPiutang',
            'totalSisa',
            'totalTerbayar',
            'totalCustomerPiutang',
            'totalTerlambat'
        ));
    }

    /**
     * Simpan data catatan piutang baru (Create).
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'kode_customer'       => 'required|exists:data_customer,kode_customer',
            'nomor_faktur'        => 'required|string|max:50',
            'tanggal_terbit'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_terbit',
            'jumlah_piutang'      => 'required|numeric|min:1000',
        ], [
            'kode_customer.required'       => 'Customer Toko Bangunan wajib dipilih.',
            'nomor_faktur.required'        => 'Nomor faktur / tagihan wajib diisi.',
            'tanggal_terbit.required'      => 'Tanggal penerbitan wajib diisi.',
            'tanggal_jatuh_tempo.required' => 'Tanggal jatuh tempo wajib diisi.',
            'tanggal_jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo harus sama atau setelah tanggal terbit.',
            'jumlah_piutang.required'      => 'Jumlah nominal piutang wajib diisi.',
            'jumlah_piutang.min'           => 'Nominal piutang minimal Rp 1.000.',
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($request->kode_customer);
            $jumlahPiutang = (float) $request->jumlah_piutang;
            $nomorFaktur = trim($request->nomor_faktur);

            // Buat Faktur Penjualan Terkait
            $faktur = FakturPenjualan::create([
                'nomor_faktur'       => $nomorFaktur,
                'tanggal_penjualan'  => $request->tanggal_terbit,
                'kode_customer'      => $customer->kode_customer,
                'metode_pembayaran'  => 'Kredit / Piutang',
                'total_bruto'        => $jumlahPiutang,
                'diskon'             => 0,
                'total_netto'        => $jumlahPiutang,
                'jumlah_dibayar'     => 0,
                'sisa_piutang'       => $jumlahPiutang,
                'status_pembayaran'  => 'Belum Lunas',
                'jatuh_tempo'        => $request->tanggal_jatuh_tempo,
                'status_persetujuan' => 'disetujui',
                'dibuat_oleh'        => 'spv_keuangan',
            ]);

            // Buat Record Piutang
            $piutang = Piutang::create([
                'id_penjualan'        => $faktur->id_penjualan,
                'kode_customer'       => $customer->kode_customer,
                'jumlah_piutang'      => $jumlahPiutang,
                'sisa_piutang'        => $jumlahPiutang,
                'tanggal_terbit'      => $request->tanggal_terbit,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status_piutang'      => 'belum_lunas',
            ]);

            // Tambah Saldo Piutang Customer
            $customer->increment('saldo_piutang', $jumlahPiutang);

            DB::commit();

            return redirect()->route('keuangan.ar.piutang')->with('sukses', "Catatan piutang {$nomorFaktur} untuk {$customer->nama_toko_bangunan} senilai Rp " . number_format($jumlahPiutang, 0, ',', '.') . " berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('gagal', "Gagal menyimpan data piutang: " . $e->getMessage());
        }
    }

    /**
     * Ambil data detail piutang untuk Modal JSON (Read).
     */
    public function ambilDetail(Request $request, $id_piutang)
    {
        $piutang = Piutang::with(['customer', 'penjualan'])->findOrFail($id_piutang);

        return response()->json([
            'status'  => 'sukses',
            'piutang' => [
                'id_piutang'             => $piutang->id_piutang,
                'id_penjualan'           => $piutang->id_penjualan,
                'nomor_faktur'           => $piutang->penjualan->nomor_faktur ?? "ID-{$piutang->id_penjualan}",
                'kode_customer'          => $piutang->kode_customer,
                'nama_toko_bangunan'     => $piutang->customer->nama_toko_bangunan ?? '-',
                'nama_pemilik'           => $piutang->customer->nama_pemilik ?? '-',
                'no_hp'                  => $piutang->customer->no_hp ?? '-',
                'alamat'                 => $piutang->customer->alamat ?? '-',
                'plafon_piutang_rupiah'  => 'Rp ' . number_format($piutang->customer->plafon_piutang ?? 0, 0, ',', '.'),
                'saldo_piutang_customer' => 'Rp ' . number_format($piutang->customer->saldo_piutang ?? 0, 0, ',', '.'),
                'jumlah_piutang'         => (float) $piutang->jumlah_piutang,
                'jumlah_piutang_rupiah'  => $piutang->jumlah_piutang_rupiah,
                'sisa_piutang'           => (float) $piutang->sisa_piutang,
                'sisa_piutang_rupiah'    => $piutang->sisa_piutang_rupiah,
                'jumlah_terbayar'        => (float) $piutang->jumlah_terbayar,
                'jumlah_terbayar_rupiah' => $piutang->jumlah_terbayar_rupiah,
                'persentase_terbayar'    => $piutang->persentase_terbayar,
                'tanggal_terbit'         => $piutang->tanggal_terbit ? $piutang->tanggal_terbit->format('Y-m-d') : '',
                'tanggal_terbit_format'  => $piutang->tanggal_terbit ? $piutang->tanggal_terbit->format('d/m/Y') : '-',
                'tanggal_jatuh_tempo'    => $piutang->tanggal_jatuh_tempo ? $piutang->tanggal_jatuh_tempo->format('Y-m-d') : '',
                'tanggal_jatuh_tempo_format' => $piutang->tanggal_jatuh_tempo ? $piutang->tanggal_jatuh_tempo->format('d/m/Y') : '-',
                'status_piutang'         => $piutang->status_piutang,
                'status_aging'           => $piutang->status_aging,
                'terakhir_diedit_relatif'=> $piutang->terakhir_diedit_relatif,
                'terakhir_diedit_waktu'  => $piutang->terakhir_diedit_waktu,
            ]
        ]);
    }

    /**
     * Perbarui informasi data piutang (Update).
     */
    public function perbarui(Request $request, $id_piutang)
    {
        $request->validate([
            'tanggal_jatuh_tempo' => 'required|date',
            'status_piutang'      => 'required|in:belum_lunas,sebagian,lunas,macet',
            'jumlah_piutang'      => 'required|numeric|min:1000',
        ]);

        $piutang = Piutang::with(['customer', 'penjualan'])->findOrFail($id_piutang);

        DB::beginTransaction();
        try {
            $jumlahBaru = (float) $request->jumlah_piutang;
            $selisihJumlah = $jumlahBaru - (float) $piutang->jumlah_piutang;
            $sisaBaru = max(0, (float) $piutang->sisa_piutang + $selisihJumlah);

            $statusBaru = $request->status_piutang;
            if ($statusBaru === 'lunas') {
                $sisaBaru = 0;
            }

            $piutang->update([
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status_piutang'      => $statusBaru,
                'jumlah_piutang'      => $jumlahBaru,
                'sisa_piutang'        => $sisaBaru,
            ]);

            // Sinkronkan ke faktur penjualan jika ada
            if ($piutang->penjualan) {
                $piutang->penjualan->update([
                    'total_netto'        => $jumlahBaru,
                    'sisa_piutang'       => $sisaBaru,
                    'jatuh_tempo'        => $request->tanggal_jatuh_tempo,
                    'status_pembayaran'  => $statusBaru === 'lunas' ? 'Lunas' : 'Belum Lunas',
                ]);
            }

            // Sesuaikan saldo piutang customer jika ada selisih
            if ($selisihJumlah != 0 && $piutang->customer) {
                $piutang->customer->increment('saldo_piutang', $selisihJumlah);
            }

            DB::commit();

            return redirect()->route('keuangan.ar.piutang')->with('sukses', "Data piutang {$piutang->penjualan->nomor_faktur} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal memperbarui piutang: " . $e->getMessage());
        }
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

        if ($jumlahBayar > (float) $piutang->sisa_piutang) {
            return redirect()->back()->with('gagal', "Nominal pembayaran (Rp " . number_format($jumlahBayar, 0, ',', '.') . ") melebihi sisa piutang (Rp " . number_format($piutang->sisa_piutang, 0, ',', '.') . ").");
        }

        DB::beginTransaction();
        try {
            $sisaBaru = max(0, (float) $piutang->sisa_piutang - $jumlahBayar);
            $statusBaru = $sisaBaru == 0 ? 'lunas' : 'sebagian';

            $piutang->update([
                'sisa_piutang'   => $sisaBaru,
                'status_piutang' => $statusBaru,
            ]);

            // Potong saldo piutang di data_customer
            if ($piutang->customer) {
                $piutang->customer->decrement('saldo_piutang', $jumlahBayar);
            }

            // Update faktur penjualan terkait
            if ($piutang->penjualan) {
                $faktur = $piutang->penjualan;
                $fakturSisaBaru = max(0, (float) $faktur->sisa_piutang - $jumlahBayar);
                $faktur->update([
                    'jumlah_dibayar'    => (float) $faktur->jumlah_dibayar + $jumlahBayar,
                    'sisa_piutang'      => $fakturSisaBaru,
                    'status_pembayaran' => $fakturSisaBaru == 0 ? 'Lunas' : 'Belum Lunas',
                ]);
            }

            DB::commit();

            return redirect()->route('keuangan.ar.piutang')->with('sukses', "Pembayaran piutang sebesar Rp " . number_format($jumlahBayar, 0, ',', '.') . " untuk {$piutang->customer->nama_toko_bangunan} berhasil dicatat.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal memproses pembayaran piutang: " . $e->getMessage());
        }
    }

    /**
     * Hapus catatan piutang (Delete).
     */
    public function hapus(Request $request, $id_piutang)
    {
        $piutang = Piutang::with(['customer', 'penjualan'])->findOrFail($id_piutang);

        DB::beginTransaction();
        try {
            $nomorFaktur = $piutang->penjualan->nomor_faktur ?? "ID-{$piutang->id_penjualan}";
            $sisaHapus = (float) $piutang->sisa_piutang;

            // Potong saldo piutang customer sesuai sisa yang dihapus
            if ($sisaHapus > 0 && $piutang->customer) {
                $piutang->customer->decrement('saldo_piutang', $sisaHapus);
            }

            // Hapus piutang
            $piutang->delete();

            DB::commit();

            return redirect()->route('keuangan.ar.piutang')->with('sukses', "Catatan piutang {$nomorFaktur} berhasil dihapus dari sistem.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('gagal', "Gagal menghapus catatan piutang: " . $e->getMessage());
        }
    }

    /**
     * Generator Nomor Faktur / Piutang Otomatis (Daur Ulang Slot vs Acak Tanggal).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        return GeneratorKodeOtomatis::responJson('penjualan', 'nomor_faktur', 'INV-', $mode, 3, true);
    }

    /**
     * Pastikan data awal sampel piutang tersedia jika database kosong.
     */
    private function pastikanDataAwalTersedia(): void
    {
        $jumlahPiutang = DB::table('list_piutang')->count();
        if ($jumlahPiutang === 0) {
            $customer1 = DB::table('data_customer')->where('kode_customer', 'CUST-001')->first() 
                         ?? DB::table('data_customer')->first();
            $customer2 = DB::table('data_customer')->where('kode_customer', 'CUST-002')->first() 
                         ?? DB::table('data_customer')->skip(1)->first();

            if ($customer1) {
                // Faktur 1 (Belum Lunas - tempo 7 hari lagi)
                $faktur1Id = DB::table('penjualan')->insertGetId([
                    'nomor_faktur'       => 'INV-20260901-001',
                    'tanggal_penjualan'  => Carbon::now()->subDays(10)->format('Y-m-d'),
                    'kode_customer'      => $customer1->kode_customer,
                    'metode_pembayaran'  => 'Kredit / Piutang',
                    'total_bruto'        => 32000000.00,
                    'diskon'             => 0.00,
                    'total_netto'        => 32000000.00,
                    'jumlah_dibayar'     => 12000000.00,
                    'sisa_piutang'       => 20000000.00,
                    'status_pembayaran'  => 'Belum Lunas',
                    'jatuh_tempo'        => Carbon::now()->addDays(7)->format('Y-m-d'),
                    'id_rekening'        => null,
                    'status_persetujuan' => 'disetujui',
                    'dibuat_oleh'        => 'spv_keuangan',
                    'dibuat_pada'        => Carbon::now()->subDays(10),
                    'diperbarui_pada'    => Carbon::now()->subDays(2),
                ]);

                DB::table('list_piutang')->insert([
                    'id_penjualan'        => $faktur1Id,
                    'kode_customer'       => $customer1->kode_customer,
                    'jumlah_piutang'      => 32000000.00,
                    'sisa_piutang'        => 20000000.00,
                    'tanggal_terbit'      => Carbon::now()->subDays(10)->format('Y-m-d'),
                    'tanggal_jatuh_tempo' => Carbon::now()->addDays(7)->format('Y-m-d'),
                    'status_piutang'      => 'sebagian',
                    'dibuat_pada'         => Carbon::now()->subDays(10),
                    'diperbarui_pada'     => Carbon::now()->subDays(2),
                ]);

                DB::table('data_customer')->where('kode_customer', $customer1->kode_customer)
                    ->update(['saldo_piutang' => 20000000.00]);
            }

            if ($customer2) {
                // Faktur 2 (Terlambat Jatuh Tempo)
                $faktur2Id = DB::table('penjualan')->insertGetId([
                    'nomor_faktur'       => 'INV-20260815-002',
                    'tanggal_penjualan'  => Carbon::now()->subDays(25)->format('Y-m-d'),
                    'kode_customer'      => $customer2->kode_customer,
                    'metode_pembayaran'  => 'Kredit / Piutang',
                    'total_bruto'        => 18500000.00,
                    'diskon'             => 0.00,
                    'total_netto'        => 18500000.00,
                    'jumlah_dibayar'     => 0.00,
                    'sisa_piutang'       => 18500000.00,
                    'status_pembayaran'  => 'Belum Lunas',
                    'jatuh_tempo'        => Carbon::now()->subDays(4)->format('Y-m-d'),
                    'id_rekening'        => null,
                    'status_persetujuan' => 'disetujui',
                    'dibuat_oleh'        => 'spv_keuangan',
                    'dibuat_pada'        => Carbon::now()->subDays(25),
                    'diperbarui_pada'    => Carbon::now()->subDays(4),
                ]);

                DB::table('list_piutang')->insert([
                    'id_penjualan'        => $faktur2Id,
                    'kode_customer'       => $customer2->kode_customer,
                    'jumlah_piutang'      => 18500000.00,
                    'sisa_piutang'        => 18500000.00,
                    'tanggal_terbit'      => Carbon::now()->subDays(25)->format('Y-m-d'),
                    'tanggal_jatuh_tempo' => Carbon::now()->subDays(4)->format('Y-m-d'),
                    'status_piutang'      => 'belum_lunas',
                    'dibuat_pada'         => Carbon::now()->subDays(25),
                    'diperbarui_pada'     => Carbon::now()->subDays(4),
                ]);

                DB::table('data_customer')->where('kode_customer', $customer2->kode_customer)
                    ->update(['saldo_piutang' => 18500000.00]);
            }
        }
    }
}
