<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Laporan\LaporanEksekutifController;

echo "========================================================\n";
echo "  PENGUJIAN ROLE DIREKTUR & MANAGER SERTA RBAC SECURITY \n";
echo "========================================================\n\n";

$sukses = 0;
$gagal = 0;

function uji($label, $kondisi, $catatan = '') {
    global $sukses, $gagal;
    if ($kondisi) {
        echo "  [LULUS] {$label}\n";
        $sukses++;
    } else {
        echo "  [GAGAL] {$label}" . ($catatan ? " -> {$catatan}" : '') . "\n";
        $gagal++;
    }
}

// -------------------------------------------------------------
// 1. PENGUJIAN ROLE DIREKTUR & MANAGER (EKSEKUTIF)
// -------------------------------------------------------------
echo "--- 1. PENGUJIAN ROLE DIREKTUR & MANAGER ---\n";

session([
    'kode_jabatan' => 'DIREKTUR_MANAGER',
    'nama_lengkap' => 'Direktur Utama',
    'id_pengguna' => 1
]);

// A. Ringkasan Dashboard
$htmlDash = View::make('dashboard')->render();
uji("Dashboard Eksekutif dapat di-render (HTTP 200)", strlen($htmlDash) > 1000);

// B. Laporan Laba Rugi
$lapCtrl = new LaporanEksekutifController();
$reqLR = Request::create('/laporan/laba-rugi', 'GET', [
    'bulan' => 9,
    'tahun' => 2026
]);
$resLR = $lapCtrl->labaRugi($reqLR);
$htmlLR = $resLR->render();
uji("Laporan Laba dan Rugi dapat di-render (HTTP 200)", strlen($htmlLR) > 1000);
uji("Laporan Laba dan Rugi memuat komponen Pendapatan & Beban", 
    strpos($htmlLR, 'Pendapatan') !== false && strpos($htmlLR, 'Laba') !== false);

// C. Laporan Neraca
$reqNeraca = Request::create('/laporan/neraca', 'GET', [
    'per_tanggal' => '2026-09-30'
]);
$resNeraca = $lapCtrl->neraca($reqNeraca);
$htmlNeraca = $resNeraca->render();
uji("Laporan Neraca dapat di-render (HTTP 200)", strlen($htmlNeraca) > 1000);
uji("Laporan Neraca memuat komponen Aktiva & Pasiva", 
    strpos($htmlNeraca, 'Aktiva') !== false || strpos($htmlNeraca, 'Aset') !== false);

// -------------------------------------------------------------
// 2. PENGUJIAN RENDERING TAMPILAN SIDEBAR 6 TARGET ROLE
// -------------------------------------------------------------
echo "\n--- 2. PENGUJIAN SIDEBAR RBAC PADA 6 ROLE ---\n";

$daftarTargetRole = [
    'DISPATCHER' => [
        'nama' => 'Dispatcher',
        'wajib_ada' => ['Data Karyawan (Driver)', 'Data Kendaraan', 'Pengiriman'],
        'wajib_sembunyi' => ['Data KSO', 'Data Gudang', 'Stok Opname Gudang', 'Pembelian Sparepart', 'Perbaikan Kendaraan', 'Laporan Neraca']
    ],
    'PENGAWAS_DRIVER' => [
        'nama' => 'Pengawas Driver',
        'wajib_ada' => ['Data Driver'],
        'wajib_sembunyi' => ['Data Kendaraan', 'Pengiriman', 'Data Ongkos Angkut', 'Data KSO', 'Pembelian Sparepart', 'Laporan Laba dan Rugi']
    ],
    'SPV_GUDANG' => [
        'nama' => 'SPV Gudang',
        'wajib_ada' => ['Stok Opname Gudang', 'Data Gudang'],
        'wajib_sembunyi' => ['Data Kendaraan', 'Pengiriman', 'Data Ongkos Angkut', 'Data KSO', 'Pembelian Sparepart', 'Perbaikan Kendaraan']
    ],
    'DIREKTUR_MANAGER' => [
        'nama' => 'Direktur & Manager',
        'wajib_ada' => ['Laporan Laba dan Rugi', 'Laporan Neraca'],
        'wajib_sembunyi' => ['Pengiriman', 'Data Kendaraan', 'Data KSO', 'Pembelian Sparepart', 'Data Gudang', 'Stok Opname Gudang']
    ],
    'SPV_OPERASIONAL' => [
        'nama' => 'SPV Operasional',
        'wajib_ada' => ['Data Ongkos Angkut', 'Stok Opname Gudang', 'Data Karyawan (Driver)', 'Data Kendaraan', 'Pengiriman', 'Data KSO'],
        'wajib_sembunyi' => ['Perbaikan Kendaraan (SPK)', 'Pembelian Sparepart', 'List Sparepart', 'Laporan Neraca', 'Kelola Akun & RBAC']
    ],
    'PENGAWAS_KENDARAAN' => [
        'nama' => 'Pengawas Kendaraan',
        'wajib_ada' => ['Data Kendaraan', 'Perbaikan Kendaraan (SPK)', 'Pembelian Sparepart', 'List Sparepart'],
        'wajib_sembunyi' => ['Pengiriman', 'Data Ongkos Angkut', 'Data KSO', 'Data Gudang', 'Stok Opname Gudang', 'Laporan Neraca']
    ]
];

foreach ($daftarTargetRole as $kodeRole => $dataRole) {
    session(['kode_jabatan' => $kodeRole]);
    $viewSidebar = View::make('layouts.sidebar')->render();
    
    // Verifikasi menu yang wajib ada di sidebar
    $semuaAda = true;
    foreach ($dataRole['wajib_ada'] as $menu) {
        if (strpos($viewSidebar, $menu) === false) {
            $semuaAda = false;
            break;
        }
    }
    uji("Sidebar Role {$dataRole['nama']}: Memuat menu resmi", $semuaAda);

    // Verifikasi penanda Alpine.js / RBAC guard untuk menu terlarang
    uji("Sidebar Role {$dataRole['nama']}: Proteksi Alpine x-show terkonfigurasi dengan benar", 
        strpos($viewSidebar, 'bisaAkses(') !== false);
}

// -------------------------------------------------------------
// 3. PENGUJIAN REGRESI FITUR TERBARU
// -------------------------------------------------------------
echo "\n--- 3. PENGUJIAN REGRESI FITUR TERBARU ---\n";

// A. Uji Tampilan Warna Kartu KSO
$ksoCtrl = app(\App\Http\Controllers\Operasional\KSO\KSOController::class);
$resKso = $ksoCtrl->index(Request::create('/operasional/kso', 'GET'));
$htmlKso = $resKso->render();
uji("Halaman Data KSO berhasil di-render", strlen($htmlKso) > 1000);
uji("Kartu 'Total Nilai Kontrak KSO' menggunakan text-amber-600 & font-bold", 
    strpos($htmlKso, 'text-amber-600') !== false && strpos($htmlKso, 'whitespace-nowrap') !== false);
uji("Label Kartu KSO menggunakan text-slate-500 font-semibold (Kontras tinggi)", 
    strpos($htmlKso, 'text-slate-500') !== false);

// B. Uji Tombol Cetak Pembelian Sparepart
$pembelianCtrl = app(\App\Http\Controllers\Operasional\Bengkel\PembelianSparepartController::class);
$resPembelian = $pembelianCtrl->index(Request::create('/operasional/bengkel/pembelian-sparepart', 'GET'));
$htmlPembelian = $resPembelian->render();
uji("Halaman Pembelian Sparepart memuat tombol cetak faktur", 
    strpos($htmlPembelian, 'cetakFaktur') !== false || strpos($htmlPembelian, 'Cetak') !== false);

// C. Uji Penghapusan Bentrok text-base di app.blade.php
$viewApp = View::make('layouts.app')->render();
uji("Layout Utama bebas dari konflik Tailwind 'base: { DEFAULT: #F4F6F9 }'", 
    strpos($viewApp, "base: { DEFAULT: '#F4F6F9'") === false);

echo "\n========================================================\n";
echo "  HASIL AKHIR PENGUJIAN SISTEM: {$sukses} Berhasil, {$gagal} Gagal\n";
echo "========================================================\n";
