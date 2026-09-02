<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Autentikasi\AutentikasiController;
use App\Http\Controllers\Autentikasi\KelolaAkunController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\BarangController;
use App\Http\Controllers\Master\WilayahController;
use App\Http\Controllers\Master\KaryawanController;
use App\Http\Controllers\Keuangan\AR\FakturPenjualanController;
use App\Http\Controllers\Keuangan\AR\PiutangController;
use App\Http\Controllers\Keuangan\AR\DepositCustomerController;
use App\Http\Controllers\Keuangan\AP\PembelianSOController;
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
use App\Http\Controllers\Operasional\Monitoring\KSOController;
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
    // Customer
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer/{kode_customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{kode_customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');

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
    // Gudang & Stok
    Route::prefix('gudang')->name('gudang.')->group(function () {
        Route::get('/stok', [StokGudangController::class, 'index'])->name('stok');
        Route::get('/opname', [StockOpnameController::class, 'index'])->name('opname');
        Route::get('/penerimaan-barang', [PenerimaanBarangController::class, 'index'])->name('penerimaan');
    });

    // Armada & Driver
    Route::prefix('armada')->name('armada.')->group(function () {
        Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan');
        Route::get('/driver', [DriverController::class, 'index'])->name('driver');
    });

    // Pengiriman & Dispatcher
    Route::prefix('pengiriman')->name('pengiriman.')->group(function () {
        Route::get('/sales-order', [SalesOrderController::class, 'index'])->name('so');
        Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat_jalan');
        Route::get('/ongkos-angkut', [OngkosAngkutController::class, 'index'])->name('ongkos_angkut');
    });

    // Bengkel & Pemeliharaan (Pengawas Kendaraan)
    Route::prefix('bengkel')->name('bengkel.')->group(function () {
        Route::get('/perbaikan', [PerbaikanKendaraanController::class, 'index'])->name('perbaikan');
        Route::get('/pembelian-sparepart', [PembelianSparepartController::class, 'index'])->name('pembelian_sparepart');
        Route::get('/sparepart', [SparepartController::class, 'index'])->name('sparepart');
    });

    // Monitoring & KSO
    Route::get('/monitoring', [MonitoringOperasionalController::class, 'index'])->name('monitoring');
    Route::get('/kso', [KSOController::class, 'index'])->name('kso');
});

// Modul Laporan Eksekutif
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/neraca', [LaporanEksekutifController::class, 'neraca'])->name('neraca');
    Route::get('/laba-rugi', [LaporanEksekutifController::class, 'labaRugi'])->name('laba_rugi');
    Route::get('/arus-kas', [LaporanEksekutifController::class, 'arusKas'])->name('arus_kas');
});
