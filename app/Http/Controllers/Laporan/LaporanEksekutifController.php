<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\KodeAkun;
use App\Models\Keuangan\FakturPenjualan;
use App\Models\Keuangan\Piutang;
use App\Models\Master\Customer;
use Carbon\Carbon;

class LaporanEksekutifController extends Controller
{
    /**
     * Tampilkan Laporan Posisi Keuangan (Neraca).
     */
    public function neraca(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Ambil data akun COA real-time
        $akunCOA = DB::table('data_kode_akun')->get()->keyBy('kode_akun');

        // 1. Aktiva Lancar
        $kasKecil = (float) ($akunCOA['1101']->saldo_berjalan ?? 0.00);
        $bankBCA = (float) ($akunCOA['1102']->saldo_berjalan ?? 0.00);
        $bankMandiri = (float) ($akunCOA['1103']->saldo_berjalan ?? 0.00);
        $bankBRI = (float) ($akunCOA['1104']->saldo_berjalan ?? 0.00);
        $totalKasBank = $kasKecil + $bankBCA + $bankMandiri + $bankBRI;

        $totalPiutangUsaha = (float) ($akunCOA['1105']->saldo_berjalan ?? DB::table('data_customer')->sum('saldo_piutang'));
        $totalPersediaan = (float) ($akunCOA['1106']->saldo_berjalan ?? 0.00);
        $totalUangMukaSupir = (float) ($akunCOA['1107']->saldo_berjalan ?? 0.00);
        $totalAktivaLancar = $totalKasBank + $totalPiutangUsaha + $totalPersediaan + $totalUangMukaSupir;

        // 2. Aktiva Tetap
        $totalNilaiAset = (float) DB::table('data_aset')->sum('harga_aset');
        if ($totalNilaiAset <= 0) {
            $totalNilaiAset = (float) DB::table('data_kode_akun')->whereIn('kode_akun', ['1200', '1201', '1203', '1204', '1206'])->sum('saldo_berjalan');
        }
        $totalAkumulasiPenyusutan = abs((float) DB::table('data_kode_akun')->whereIn('kode_akun', ['1202', '1205', '1207', '1208'])->sum('saldo_berjalan'));
        if ($totalAkumulasiPenyusutan <= 0) {
            $totalAkumulasiPenyusutan = (float) DB::table('riwayat_penyusutan')->sum('beban_penyusutan');
        }
        $totalAktivaTetap = max(0, $totalNilaiAset - $totalAkumulasiPenyusutan);
        $totalAktiva = $totalAktivaLancar + $totalAktivaTetap;

        // 3. Kewajiban (Hutang)
        $totalHutangDagang = abs((float) ($akunCOA['2101']->saldo_berjalan ?? 0.00));
        $totalDepositCustomer = (float) DB::table('data_customer')->sum('saldo_deposit');
        if ($totalDepositCustomer <= 0 && isset($akunCOA['2102'])) {
            $totalDepositCustomer = abs((float) $akunCOA['2102']->saldo_berjalan);
        }
        $totalHutangGaji = abs((float) ($akunCOA['2103']->saldo_berjalan ?? 0.00));
        $totalKewajiban = $totalHutangDagang + $totalDepositCustomer + $totalHutangGaji;

        // 4. Modal & Ekuitas Bersih
        $modalDisetor = abs((float) ($akunCOA['3101']->saldo_berjalan ?? 0.00));
        $totalModal = max(0, $totalAktiva - $totalKewajiban);
        $labaDitahan = max(0, $totalModal - $modalDisetor);

        // Akun COA Kelompok untuk referensi
        $aktivaLancarList = KodeAkun::where('tipe_akun', 'Aktiva Lancar')->get();
        $aktivaTetapList = KodeAkun::where('tipe_akun', 'Aktiva Tetap')->get();
        $kewajibanList = KodeAkun::whereIn('tipe_akun', ['Kewajiban Lancar', 'Kewajiban Jangka Panjang'])->get();
        $modalList = KodeAkun::where('tipe_akun', 'Modal')->get();

        return view('laporan.neraca', compact(
            'bulan',
            'tahun',
            'totalKasBank',
            'totalPiutangUsaha',
            'totalPersediaan',
            'totalUangMukaSupir',
            'totalNilaiAset',
            'totalAkumulasiPenyusutan',
            'totalAktivaLancar',
            'totalAktivaTetap',
            'totalAktiva',
            'totalHutangDagang',
            'totalDepositCustomer',
            'totalHutangGaji',
            'totalKewajiban',
            'modalDisetor',
            'labaDitahan',
            'totalModal',
            'aktivaLancarList',
            'aktivaTetapList',
            'kewajibanList',
            'modalList'
        ));
    }

    /**
     * Tampilkan Laporan Laba Rugi Komprehensif Eksekutif.
     */
    public function labaRugi(Request $request)
    {
        $bulan = (int)$request->input('bulan', date('n'));
        $tahun = (int)$request->input('tahun', date('Y'));

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = $daftarBulan[$bulan] ?? 'Bulan ' . $bulan;

        // Ambil data akun COA real-time
        $akunCOA = DB::table('data_kode_akun')->get()->keyBy('kode_akun');

        // Real-time Penjualan dari DB untuk periode terpilih
        $queryPenjualan = DB::table('penjualan')
            ->whereYear('tanggal_penjualan', $tahun);
        if ($bulan > 0) {
            $queryPenjualan->whereMonth('tanggal_penjualan', $bulan);
        }
        $realPenjualan = (float) $queryPenjualan->sum('total_netto');
        $realDiskon = (float) $queryPenjualan->sum('diskon');

        $saldoPendapatanCOA = (float) ($akunCOA['4101']->saldo_berjalan ?? 0.00);
        $totalBasisPenjualan = max($realPenjualan, $saldoPendapatanCOA);

        // Komposisi Pendapatan Usaha (Revenue)
        $penjualanSemenZak = $totalBasisPenjualan * 0.70;
        $penjualanSemenCurah = $totalBasisPenjualan * 0.30;
        $pendapatanOngkosAngkut = (float) ($akunCOA['4201']->saldo_berjalan ?? 0.00);
        $potonganPenjualan = $realDiskon;
        $totalPendapatan = max(0, ($penjualanSemenZak + $penjualanSemenCurah + $pendapatanOngkosAngkut) - $potonganPenjualan);

        // Harga Pokok Penjualan (HPP / COGS)
        $querySO = DB::table('pembelian_so')->whereYear('tanggal_so', $tahun);
        if ($bulan > 0) {
            $querySO->whereMonth('tanggal_so', $bulan);
        }
        $totalBeliSO = (float) $querySO->sum('total_harga');
        $saldoHppCOA = (float) ($akunCOA['5101']->saldo_berjalan ?? 0.00);
        $pembelianSemenPabrik = max($totalBeliSO, $saldoHppCOA);
        if ($pembelianSemenPabrik <= 0 && $totalPendapatan > 0) {
            $pembelianSemenPabrik = $totalPendapatan * 0.80;
        }

        $ongkosBongkarMuatPabrik = $pembelianSemenPabrik > 0 ? round($pembelianSemenPabrik * 0.02, 2) : 0.00;
        $biayaKuliPabrik = $pembelianSemenPabrik > 0 ? round($pembelianSemenPabrik * 0.01, 2) : 0.00;
        $totalHpp = $pembelianSemenPabrik + $ongkosBongkarMuatPabrik + $biayaKuliPabrik;
        $labaKotor = $totalPendapatan - $totalHpp;

        // Real-time Pengeluaran Beban dari DB
        $queryPengeluaran = DB::table('pengeluaran')
            ->whereYear('tanggal_pengeluaran', $tahun);
        if ($bulan > 0) {
            $queryPengeluaran->whereMonth('tanggal_pengeluaran', $bulan);
        }

        // 1. Beban Armada & Logistik
        $bebanBBM = (float) (clone $queryPengeluaran)->where('kode_akun', '6101')->sum('total_nominal');
        if ($bebanBBM == 0 && isset($akunCOA['6101'])) {
            $bebanBBM = (float) $akunCOA['6101']->saldo_berjalan;
        }
        $bebanTolPenyeberangan = (float) (clone $queryPengeluaran)->where('kategori_pengeluaran', 'like', '%Tol%')->sum('total_nominal');
        $bebanKirPajakArmada = (float) (clone $queryPengeluaran)->where(function ($q) {
            $q->where('kategori_pengeluaran', 'like', '%Pajak%')
              ->orWhere('kategori_pengeluaran', 'like', '%KIR%');
        })->sum('total_nominal');
        $subtotalBebanLogistik = $bebanBBM + $bebanTolPenyeberangan + $bebanKirPajakArmada;

        // 2. Beban Bengkel & Pemeliharaan
        $bebanServisBengkel = (float) (clone $queryPengeluaran)->where('kode_akun', '6102')->sum('total_nominal');
        if ($bebanServisBengkel == 0 && isset($akunCOA['6102'])) {
            $bebanServisBengkel = (float) $akunCOA['6102']->saldo_berjalan;
        }
        $bebanSparepartBan = (float) (clone $queryPengeluaran)->where('kategori_pengeluaran', 'like', '%Sparepart%')->sum('total_nominal');
        $subtotalBebanBengkel = $bebanServisBengkel + $bebanSparepartBan;

        // 3. Beban Personalia & Supir
        $bebanGajiSupir = (float) (clone $queryPengeluaran)->where('kode_akun', '6103')->sum('total_nominal');
        if ($bebanGajiSupir == 0 && isset($akunCOA['6103'])) {
            $bebanGajiSupir = (float) $akunCOA['6103']->saldo_berjalan;
        }
        $bebanUangJalanSupir = (float) (clone $queryPengeluaran)->where('kategori_pengeluaran', 'like', '%Uang Jalan%')->sum('total_nominal');
        $bebanGajiManajemen = (float) (clone $queryPengeluaran)->where('kategori_pengeluaran', 'like', '%Manajemen%')->sum('total_nominal');
        $subtotalBebanGaji = $bebanGajiSupir + $bebanUangJalanSupir + $bebanGajiManajemen;

        // 4. Beban Kantor & Umum
        $bebanListrikAir = (float) (clone $queryPengeluaran)->where('kode_akun', '6104')->sum('total_nominal');
        if ($bebanListrikAir == 0 && isset($akunCOA['6104'])) {
            $bebanListrikAir = (float) $akunCOA['6104']->saldo_berjalan;
        }
        $bebanAtkKomunikasi = (float) (clone $queryPengeluaran)->where(function ($q) {
            $q->where('kategori_pengeluaran', 'like', '%ATK%')
              ->orWhere('kategori_pengeluaran', 'like', '%Kantor%');
        })->sum('total_nominal');
        $subtotalBebanKantor = $bebanListrikAir + $bebanAtkKomunikasi;

        $totalBebanOperasional = $subtotalBebanLogistik + $subtotalBebanBengkel + $subtotalBebanGaji + $subtotalBebanKantor;
        $labaBersihOperasional = $labaKotor - $totalBebanOperasional;

        // Estimasi Pajak Penghasilan (11%)
        $estimasiPajak = max(0, $labaBersihOperasional * 0.11);
        $labaBersihSetelahPajak = $labaBersihOperasional - $estimasiPajak;

        // Rasio-rasio Finansial Utama (%)
        $marginLabaKotor = $totalPendapatan > 0 ? ($labaKotor / $totalPendapatan) * 100 : 0;
        $marginLabaOperasional = $totalPendapatan > 0 ? ($labaBersihOperasional / $totalPendapatan) * 100 : 0;
        $marginLabaBersih = $totalPendapatan > 0 ? ($labaBersihSetelahPajak / $totalPendapatan) * 100 : 0;
        $rasioBebanOperasional = $totalPendapatan > 0 ? ($totalBebanOperasional / $totalPendapatan) * 100 : 0;

        // Data Tren 6 Bulan Terakhir Berbasis Database
        $trenBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $tglTarget = Carbon::now()->subMonths($i);
            $bTarget = $tglTarget->month;
            $thTarget = $tglTarget->year;
            $namaB = $daftarBulan[$bTarget] ?? 'Bln';
            $singkatB = substr($namaB, 0, 3);

            $rev = (float) DB::table('penjualan')->whereYear('tanggal_penjualan', $thTarget)->whereMonth('tanggal_penjualan', $bTarget)->sum('total_netto');
            if ($rev <= 0 && $bTarget == now()->month) {
                $rev = $totalPendapatan;
            }
            $exp = (float) DB::table('pengeluaran')->whereYear('tanggal_pengeluaran', $thTarget)->whereMonth('tanggal_pengeluaran', $bTarget)->sum('total_nominal');
            if ($exp <= 0 && $bTarget == now()->month) {
                $exp = $totalBebanOperasional;
            }
            $hppEst = $rev * 0.80;
            $netProfit = max(0, $rev - $hppEst - $exp);
            $margin = $rev > 0 ? round(($netProfit / $rev) * 100, 1) : 0;

            $trenBulanan[] = [
                'bulan'       => $singkatB,
                'pendapatan'  => (int) $rev,
                'laba_bersih' => (int) $netProfit,
                'margin'      => $margin,
            ];
        }

        return view('laporan.laba_rugi', compact(
            'bulan',
            'tahun',
            'namaBulan',
            'daftarBulan',
            'penjualanSemenZak',
            'penjualanSemenCurah',
            'pendapatanOngkosAngkut',
            'potonganPenjualan',
            'totalPendapatan',
            'pembelianSemenPabrik',
            'ongkosBongkarMuatPabrik',
            'biayaKuliPabrik',
            'totalHpp',
            'labaKotor',
            'bebanBBM',
            'bebanTolPenyeberangan',
            'bebanKirPajakArmada',
            'subtotalBebanLogistik',
            'bebanServisBengkel',
            'bebanSparepartBan',
            'subtotalBebanBengkel',
            'bebanGajiSupir',
            'bebanUangJalanSupir',
            'bebanGajiManajemen',
            'subtotalBebanGaji',
            'bebanListrikAir',
            'bebanAtkKomunikasi',
            'subtotalBebanKantor',
            'totalBebanOperasional',
            'labaBersihOperasional',
            'estimasiPajak',
            'labaBersihSetelahPajak',
            'marginLabaKotor',
            'marginLabaOperasional',
            'marginLabaBersih',
            'rasioBebanOperasional',
            'trenBulanan'
        ));
    }

    /**
     * Tampilkan Laporan Arus Kas (Cash Flow).
     */
    public function arusKas(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $penerimaanCustomer = (float) DB::table('penjualan')->where('status_pembayaran', 'Lunas')->sum('total_netto')
            + (float) DB::table('list_deposit')->where('tipe_mutasi', 'Masuk')->sum('jumlah_nominal');

        $pengeluaranOperasional = (float) DB::table('pengeluaran')->sum('total_nominal')
            + (float) DB::table('pembelian_so')->sum('total_harga');

        $arusKasOperasi = $penerimaanCustomer - $pengeluaranOperasional;

        $akunCOA = DB::table('data_kode_akun')->get()->keyBy('kode_akun');
        $kasKecil = (float) ($akunCOA['1101']->saldo_berjalan ?? 0.00);
        $bankBCA = (float) ($akunCOA['1102']->saldo_berjalan ?? 0.00);
        $bankMandiri = (float) ($akunCOA['1103']->saldo_berjalan ?? 0.00);
        $bankBRI = (float) ($akunCOA['1104']->saldo_berjalan ?? 0.00);
        $saldoAkhirKas = $kasKecil + $bankBCA + $bankMandiri + $bankBRI;
        $saldoAwalKas = max(0, $saldoAkhirKas - $arusKasOperasi);

        return view('laporan.arus_kas', compact(
            'bulan',
            'tahun',
            'penerimaanCustomer',
            'pengeluaranOperasional',
            'arusKasOperasi',
            'saldoAwalKas',
            'saldoAkhirKas'
        ));
    }
}
