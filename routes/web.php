<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Autentikasi\AutentikasiController;
use App\Http\Controllers\Autentikasi\KelolaAkunController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\TokoBangunanController;
use App\Http\Controllers\Master\BarangController;
use App\Http\Controllers\Master\WilayahController;
use App\Http\Controllers\Master\KaryawanController;
use App\Http\Controllers\Master\JenisAsetController;
use App\Http\Controllers\Keuangan\AR\FakturPenjualanController;
use App\Http\Controllers\Keuangan\AR\PiutangController;
use App\Http\Controllers\Keuangan\AR\DepositCustomerController;
use App\Http\Controllers\Keuangan\AP\PembelianSOController;
use App\Http\Controllers\Keuangan\AP\ListSOController;
use App\Http\Controllers\Keuangan\AP\PengeluaranKasController;
use App\Http\Controllers\Keuangan\AP\HutangSupplierController;
use App\Http\Controllers\Keuangan\Akuntansi\KodeAkunController;
use App\Http\Controllers\Keuangan\Akuntansi\JurnalUmumController;
use App\Http\Controllers\Keuangan\Akuntansi\AsetPerusahaanController;
use App\Http\Controllers\Operasional\Gudang\StokGudangController;
use App\Http\Controllers\Operasional\Gudang\StockOpnameController;
use App\Http\Controllers\Operasional\Gudang\PenerimaanBarangController;
use App\Http\Controllers\Operasional\Armada\KendaraanController;
use App\Http\Controllers\Operasional\Armada\DriverController;
use App\Http\Controllers\Operasional\Pengiriman\SalesOrderController;
use App\Http\Controllers\Operasional\Pengiriman\SuratJalanController;
use App\Http\Controllers\Operasional\Pengiriman\OngkosAngkutController;
use App\Http\Controllers\Operasional\Bengkel\PerbaikanKendaraanController;
use App\Http\Controllers\Operasional\Bengkel\PembelianSparepartController;
use App\Http\Controllers\Operasional\Bengkel\SparepartController;
use App\Http\Controllers\Operasional\Monitoring\MonitoringOperasionalController;
use App\Http\Controllers\Operasional\KSO\KSOController;
use App\Http\Controllers\Laporan\LaporanEksekutifController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Akuntansi & Distribusi Semen Terpadu
|--------------------------------------------------------------------------
*/

// Halaman Utama & Autentikasi
Route::get('/', [AutentikasiController::class, 'tampilkanFormLogin'])->name('login');
Route::get('/login', [AutentikasiController::class, 'tampilkanFormLogin'])->name('auth.login');
Route::post('/login', [AutentikasiController::class, 'prosesLogin'])->name('auth.proses_login');
Route::post('/logout', [AutentikasiController::class, 'prosesLogout'])->name('auth.logout');

// Dashboard Utama
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Modul Super Admin / Pengaturan Akun
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/kelola-akun', [KelolaAkunController::class, 'index'])->name('kelola_akun');
    Route::post('/kelola-akun', [KelolaAkunController::class, 'store'])->name('kelola_akun.store');
    Route::post('/kelola-akun/reset-password', [KelolaAkunController::class, 'resetPassword'])->name('kelola_akun.reset_password');
    Route::post('/kelola-akun/toggle-status', [KelolaAkunController::class, 'toggleStatus'])->name('kelola_akun.toggle_status');
});

// Modul Master Data
Route::prefix('master')->name('master.')->group(function () {
    // Customer (Entitas Pemilik & Finansial)
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/api/buat-kode', [CustomerController::class, 'buatKodeOtomatis'])->name('customer.buat_kode');
    Route::get('/customer/{kode_customer}/detail', [CustomerController::class, 'ambilDetail'])->name('customer.detail');
    Route::put('/customer/{kode_customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{kode_customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    // Toko Bangunan & Proyek Cabang (1:N Customer)
    Route::get('/toko-bangunan', [TokoBangunanController::class, 'index'])->name('toko_bangunan.index');
    Route::post('/toko-bangunan', [TokoBangunanController::class, 'simpan'])->name('toko_bangunan.simpan');
    Route::get('/toko-bangunan/api/buat-kode', [TokoBangunanController::class, 'buatKodeOtomatis'])->name('toko_bangunan.buat_kode');
    Route::get('/toko-bangunan/{kode_toko}/detail', [TokoBangunanController::class, 'ambilDetail'])->name('toko_bangunan.detail');
    Route::put('/toko-bangunan/{kode_toko}', [TokoBangunanController::class, 'perbarui'])->name('toko_bangunan.perbarui');
    Route::delete('/toko-bangunan/{kode_toko}', [TokoBangunanController::class, 'hapus'])->name('toko_bangunan.hapus');

    // Barang / Semen
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{kode_barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{kode_barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Wilayah
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');
    Route::put('/wilayah/{kode_wilayah}', [WilayahController::class, 'update'])->name('wilayah.update');
    Route::delete('/wilayah/{kode_wilayah}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');

    // Karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::put('/karyawan/{kode_karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{kode_karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
    
    // CRUD Master Jenis Aset / Kategori Kendaraan
    Route::get('/jenis-aset', [JenisAsetController::class, 'index'])->name('jenis_aset.index');
    Route::post('/jenis-aset', [JenisAsetController::class, 'simpan'])->name('jenis_aset.simpan');
    Route::get('/jenis-aset/api/buat-kode', [JenisAsetController::class, 'buatKodeOtomatis'])->name('jenis_aset.buat_kode');
    Route::get('/jenis-aset/{kode_jenis_aset}', [JenisAsetController::class, 'ambilDetail'])->name('jenis_aset.detail');
    Route::put('/jenis-aset/{kode_jenis_aset}', [JenisAsetController::class, 'perbarui'])->name('jenis_aset.perbarui');
    Route::delete('/jenis-aset/{kode_jenis_aset}', [JenisAsetController::class, 'hapus'])->name('jenis_aset.hapus');
});

// Modul Keuangan (AR, AP, Akuntansi)
Route::prefix('keuangan')->name('keuangan.')->group(function () {
    // Account Receivable (Piutang)
    Route::prefix('ar')->name('ar.')->group(function () {
        Route::get('/faktur-penjualan', [FakturPenjualanController::class, 'index'])->name('faktur');
        Route::post('/faktur-penjualan', [FakturPenjualanController::class, 'store'])->name('faktur.store');
        Route::get('/list-piutang', [PiutangController::class, 'index'])->name('piutang');
        Route::post('/list-piutang/{id_piutang}/bayar', [PiutangController::class, 'bayar'])->name('piutang.bayar');
        Route::get('/deposit-customer', [DepositCustomerController::class, 'index'])->name('deposit');
        Route::post('/deposit-customer/topup', [DepositCustomerController::class, 'topUp'])->name('deposit.topup');
    });

    // Account Payable (Hutang)
    Route::prefix('ap')->name('ap.')->group(function () {
        Route::get('/pembelian-so', [PembelianSOController::class, 'index'])->name('pembelian_so');
        Route::post('/pembelian-so', [PembelianSOController::class, 'store'])->name('pembelian_so.store');
        Route::get('/list-so', [ListSOController::class, 'index'])->name('list_so');
        Route::get('/pengeluaran-kas', [PengeluaranKasController::class, 'index'])->name('pengeluaran');
        Route::post('/pengeluaran-kas', [PengeluaranKasController::class, 'store'])->name('pengeluaran.store');
        Route::get('/list-rilisan', [HutangSupplierController::class, 'index'])->name('rilisan');
        Route::post('/list-rilisan', [HutangSupplierController::class, 'store'])->name('rilisan.store');
    });

    // Akuntansi & Buku Besar
    Route::prefix('akuntansi')->name('akuntansi.')->group(function () {
        Route::get('/kode-akun', [KodeAkunController::class, 'index'])->name('kode_akun');
        Route::post('/kode-akun', [KodeAkunController::class, 'store'])->name('kode_akun.store');
        Route::put('/kode-akun/{kode_akun}', [KodeAkunController::class, 'update'])->name('kode_akun.update');
        Route::delete('/kode-akun/{kode_akun}', [KodeAkunController::class, 'destroy'])->name('kode_akun.destroy');
        Route::get('/jurnal-umum', [JurnalUmumController::class, 'index'])->name('jurnal');
        Route::post('/jurnal-umum', [JurnalUmumController::class, 'store'])->name('jurnal.store');
        Route::get('/aset-perusahaan', [AsetPerusahaanController::class, 'index'])->name('aset');
        Route::post('/aset-perusahaan', [AsetPerusahaanController::class, 'store'])->name('aset.store');
    });
});

// Modul Operasional (Gudang, Armada, Pengiriman, Bengkel, Monitoring)
Route::prefix('operasional')->name('operasional.')->group(function () {
    // Gudang & Stok (SPV Gudang)
    Route::prefix('gudang')->name('gudang.')->group(function () {
        // CRUD Data Gudang & Stok Semen
        Route::get('/stok', [StokGudangController::class, 'index'])->name('stok');
        Route::post('/stok', [StokGudangController::class, 'simpan'])->name('stok.simpan');
        Route::get('/stok/api/buat-kode', [StokGudangController::class, 'buatKodeOtomatis'])->name('stok.buat_kode');
        Route::get('/stok/{kode_gudang}', [StokGudangController::class, 'ambilDetail'])->name('stok.detail');
        Route::put('/stok/{kode_gudang}', [StokGudangController::class, 'perbarui'])->name('stok.perbarui');
        Route::post('/stok/{kode_gudang}/mutasi', [StokGudangController::class, 'mutasiStok'])->name('stok.mutasi');
        Route::delete('/stok/{kode_gudang}', [StokGudangController::class, 'hapus'])->name('stok.hapus');

        // CRUD Stok Opname Fisik Gudang
        Route::get('/opname', [StockOpnameController::class, 'index'])->name('opname');
        Route::post('/opname', [StockOpnameController::class, 'simpan'])->name('opname.simpan');
        Route::get('/opname/api/buat-kode', [StockOpnameController::class, 'buatNomorOpname'])->name('opname.buat_kode');
        Route::get('/opname/{id_opname}', [StockOpnameController::class, 'ambilDetail'])->name('opname.detail');
        Route::put('/opname/{id_opname}', [StockOpnameController::class, 'perbarui'])->name('opname.perbarui');
        Route::patch('/opname/{id_opname}/konfirmasi', [StockOpnameController::class, 'konfirmasiSPV'])->name('opname.konfirmasi');
        Route::delete('/opname/{id_opname}', [StockOpnameController::class, 'hapus'])->name('opname.hapus');

        Route::get('/penerimaan-barang', [PenerimaanBarangController::class, 'index'])->name('penerimaan');
    });

    // Armada & Driver
    Route::prefix('armada')->name('armada.')->group(function () {
        // CRUD Data Kendaraan / Armada Truk
        Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan');
        Route::post('/kendaraan', [KendaraanController::class, 'simpan'])->name('kendaraan.simpan');
        Route::get('/kendaraan/api/buat-kode', [KendaraanController::class, 'buatKodeOtomatis'])->name('kendaraan.buat_kode');
        Route::get('/kendaraan/{kode_aset}', [KendaraanController::class, 'ambilDetail'])->name('kendaraan.detail');
        Route::put('/kendaraan/{kode_aset}', [KendaraanController::class, 'perbarui'])->name('kendaraan.perbarui');
        Route::delete('/kendaraan/{kode_aset}', [KendaraanController::class, 'hapus'])->name('kendaraan.hapus');

        // Sub-Fitur Data Jenis Aset di dalam Data Kendaraan
        Route::post('/kendaraan-jenis-aset', [KendaraanController::class, 'simpanJenisAset'])->name('kendaraan.jenis_aset.simpan');
        Route::get('/kendaraan-jenis-aset/api/buat-kode', [KendaraanController::class, 'buatKodeJenisAsetOtomatis'])->name('kendaraan.jenis_aset.buat_kode');
        Route::get('/kendaraan-jenis-aset/{kode_jenis_aset}', [KendaraanController::class, 'ambilDetailJenisAset'])->name('kendaraan.jenis_aset.detail');
        Route::put('/kendaraan-jenis-aset/{kode_jenis_aset}', [KendaraanController::class, 'perbaruiJenisAset'])->name('kendaraan.jenis_aset.perbarui');
        Route::delete('/kendaraan-jenis-aset/{kode_jenis_aset}', [KendaraanController::class, 'hapusJenisAset'])->name('kendaraan.jenis_aset.hapus');
        
        // CRUD Data Driver
        Route::get('/driver', [DriverController::class, 'index'])->name('driver');
        Route::post('/driver', [DriverController::class, 'simpan'])->name('driver.simpan');
        Route::get('/driver/api/buat-kode', [DriverController::class, 'buatKodeOtomatis'])->name('driver.buat_kode');
        Route::get('/driver/{kode_karyawan}', [DriverController::class, 'ambilDetail'])->name('driver.detail');
        Route::put('/driver/{kode_karyawan}', [DriverController::class, 'perbarui'])->name('driver.perbarui');
        Route::delete('/driver/{kode_karyawan}', [DriverController::class, 'hapus'])->name('driver.hapus');
    });

    // Pengiriman & Dispatcher
    Route::prefix('pengiriman')->name('pengiriman.')->group(function () {
        Route::get('/sales-order', [SalesOrderController::class, 'index'])->name('so');
        
        // CRUD Surat Jalan Pengiriman
        Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat_jalan');
        Route::post('/surat-jalan', [SuratJalanController::class, 'simpan'])->name('surat_jalan.simpan');
        Route::get('/surat-jalan/api/buat-kode', [SuratJalanController::class, 'buatKodeOtomatis'])->name('surat_jalan.buat_kode');
        Route::get('/surat-jalan/{id_pengiriman}', [SuratJalanController::class, 'ambilDetail'])->name('surat_jalan.detail');
        Route::put('/surat-jalan/{id_pengiriman}', [SuratJalanController::class, 'perbarui'])->name('surat_jalan.perbarui');
        Route::patch('/surat-jalan/{id_pengiriman}/status', [SuratJalanController::class, 'perbaruiStatus'])->name('surat_jalan.perbarui_status');
        Route::delete('/surat-jalan/{id_pengiriman}', [SuratJalanController::class, 'hapus'])->name('surat_jalan.hapus');

        // CRUD Data Master Ongkos Angkut
        Route::get('/ongkos-angkut', [OngkosAngkutController::class, 'index'])->name('ongkos_angkut');
        Route::post('/ongkos-angkut', [OngkosAngkutController::class, 'simpan'])->name('ongkos_angkut.simpan');
        Route::get('/ongkos-angkut/api/buat-kode', [OngkosAngkutController::class, 'buatKodeOtomatis'])->name('ongkos_angkut.buat_kode');
        Route::get('/ongkos-angkut/{kode_oa}', [OngkosAngkutController::class, 'ambilDetail'])->name('ongkos_angkut.detail');
        Route::put('/ongkos-angkut/{kode_oa}', [OngkosAngkutController::class, 'perbarui'])->name('ongkos_angkut.perbarui');
        Route::delete('/ongkos-angkut/{kode_oa}', [OngkosAngkutController::class, 'hapus'])->name('ongkos_angkut.hapus');
    });

    // Bengkel & Pemeliharaan (Pengawas Kendaraan)
    Route::prefix('bengkel')->name('bengkel.')->group(function () {
        // 1. CRUD Perbaikan Kendaraan (SPK)
        Route::get('/perbaikan', [PerbaikanKendaraanController::class, 'index'])->name('perbaikan');
        Route::post('/perbaikan', [PerbaikanKendaraanController::class, 'simpan'])->name('perbaikan.simpan');
        Route::get('/perbaikan/api/buat-kode', [PerbaikanKendaraanController::class, 'buatNomorSPK'])->name('perbaikan.buat_kode');
        Route::get('/perbaikan/{id_perbaikan}', [PerbaikanKendaraanController::class, 'ambilDetail'])->name('perbaikan.detail');
        Route::put('/perbaikan/{id_perbaikan}', [PerbaikanKendaraanController::class, 'perbarui'])->name('perbaikan.perbarui');
        Route::patch('/perbaikan/{id_perbaikan}/status', [PerbaikanKendaraanController::class, 'perbaruiStatus'])->name('perbaikan.perbarui_status');
        Route::delete('/perbaikan/{id_perbaikan}', [PerbaikanKendaraanController::class, 'hapus'])->name('perbaikan.hapus');

        // 2. CRUD Pembelian & Pengadaan Sparepart
        Route::get('/pembelian-sparepart', [PembelianSparepartController::class, 'index'])->name('pembelian_sparepart');
        Route::post('/pembelian-sparepart', [PembelianSparepartController::class, 'simpan'])->name('pembelian_sparepart.simpan');
        Route::get('/pembelian-sparepart/api/buat-kode', [PembelianSparepartController::class, 'buatNomorFaktur'])->name('pembelian_sparepart.buat_kode');
        Route::get('/pembelian-sparepart/{id_pembelian_part}', [PembelianSparepartController::class, 'ambilDetail'])->name('pembelian_sparepart.detail');
        Route::put('/pembelian-sparepart/{id_pembelian_part}', [PembelianSparepartController::class, 'perbarui'])->name('pembelian_sparepart.perbarui');
        Route::delete('/pembelian-sparepart/{id_pembelian_part}', [PembelianSparepartController::class, 'hapus'])->name('pembelian_sparepart.hapus');

        // 3. CRUD Master & Stok Sparepart
        Route::get('/sparepart', [SparepartController::class, 'index'])->name('sparepart');
        Route::post('/sparepart', [SparepartController::class, 'simpan'])->name('sparepart.simpan');
        Route::get('/sparepart/api/buat-kode', [SparepartController::class, 'buatKodeOtomatis'])->name('sparepart.buat_kode');
        Route::get('/sparepart/{kode_sparepart}', [SparepartController::class, 'ambilDetail'])->name('sparepart.detail');
        Route::put('/sparepart/{kode_sparepart}', [SparepartController::class, 'perbarui'])->name('sparepart.perbarui');
        Route::post('/sparepart/{kode_sparepart}/mutasi', [SparepartController::class, 'mutasiStok'])->name('sparepart.mutasi');
        Route::delete('/sparepart/{kode_sparepart}', [SparepartController::class, 'hapus'])->name('sparepart.hapus');
    });

    // Monitoring Operasional
    Route::get('/monitoring', [MonitoringOperasionalController::class, 'index'])->name('monitoring');

    // CRUD Data KSO (Kerja Sama Operasional) & Ongkos Angkut KSO
    Route::prefix('kso')->name('kso.')->group(function () {
        Route::post('/', [KSOController::class, 'simpanKSO'])->name('simpan');
        Route::get('/api/buat-kode', [KSOController::class, 'buatKodeKSO'])->name('buat_kode');
        Route::get('/{kode_kso}', [KSOController::class, 'ambilDetailKSO'])->name('detail');
        Route::put('/{kode_kso}', [KSOController::class, 'perbaruiKSO'])->name('perbarui');
        Route::delete('/{kode_kso}', [KSOController::class, 'hapusKSO'])->name('hapus');

        // Sub-fitur Ongkos Angkut KSO
        Route::post('/ongkos/simpan', [KSOController::class, 'simpanOngkos'])->name('ongkos.simpan');
        Route::get('/ongkos/api/buat-kode', [KSOController::class, 'buatKodeOA'])->name('ongkos.buat_kode');
        Route::get('/ongkos/{kode_oa}', [KSOController::class, 'ambilDetailOngkos'])->name('ongkos.detail');
        Route::put('/ongkos/{kode_oa}', [KSOController::class, 'perbaruiOngkos'])->name('ongkos.perbarui');
        Route::delete('/ongkos/{kode_oa}', [KSOController::class, 'hapusOngkos'])->name('ongkos.hapus');
    });
    Route::get('/kso', [KSOController::class, 'index'])->name('kso');
});

// Modul Laporan Eksekutif
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/neraca', [LaporanEksekutifController::class, 'neraca'])->name('neraca');
    Route::get('/laba-rugi', [LaporanEksekutifController::class, 'labaRugi'])->name('laba_rugi');
    Route::get('/arus-kas', [LaporanEksekutifController::class, 'arusKas'])->name('arus_kas');
});
