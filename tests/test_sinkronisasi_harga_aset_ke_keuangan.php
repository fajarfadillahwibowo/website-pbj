<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Operasional\Armada\KendaraanController;
use App\Http\Controllers\Keuangan\Akuntansi\AsetPerusahaanController;
use App\Models\Keuangan\AsetPerusahaan;

echo "========================================================\n";
echo "  TEST INTEGRASI SINKRONISASI HARGA PEROLEHAN ASET      \n";
echo "  OPERASIONAL -> KEUANGAN (AKUNTANSI / ASET)            \n";
echo "========================================================\n\n";

$kendaraanCtrl = new KendaraanController();
$keuanganCtrl = new AsetPerusahaanController();

$kodeAsetTest = 'AST-INT-TEST';
$namaAsetTest = 'Unit Uji Integrasi Harga Perolehan';
$hargaInput = 450000000;

// Bersihkan jika ada data sebelumnya
DB::table('data_aset')->where('kode_aset', $kodeAsetTest)->delete();

// 1. Simpan Aset dari Controller Operasional
$reqSimpan = Request::create('/operasional/armada/kendaraan-aset', 'POST', [
    'kode_aset'         => $kodeAsetTest,
    'kode_jenis_aset'   => 'AST-TRK',
    'nama_aset'         => $namaAsetTest,
    'tanggal_pembelian' => date('Y-m-d'),
    'harga_aset'        => $hargaInput,
    'no_polisi'         => 'B 9999 PBJ',
    'status_aset'       => 'aktif'
]);

$kendaraanCtrl->simpanAset($reqSimpan);

// 2. Verifikasi Data Tersimpan di Database
$row = DB::table('data_aset')->where('kode_aset', $kodeAsetTest)->first();

$simpanSukses = $row !== null;
$hargaAsetSama = $row && (float) $row->harga_aset == (float) $hargaInput;
$hargaPerolehanSama = $row && (float) $row->harga_perolehan == (float) $hargaInput;
$nilaiBukuSama = $row && (float) $row->nilai_buku == (float) $hargaInput;

echo "1. Simpan di Modul Operasional:\n";
echo "   - Data tersimpan di data_aset: " . ($simpanSukses ? "OK\n" : "GAGAL\n");
echo "   - harga_aset tersimpan ({$row->harga_aset}): " . ($hargaAsetSama ? "OK\n" : "GAGAL\n");
echo "   - harga_perolehan tersinkronisasi ({$row->harga_perolehan}): " . ($hargaPerolehanSama ? "OK\n" : "GAGAL\n");
echo "   - nilai_buku tersinkronisasi ({$row->nilai_buku}): " . ($nilaiBukuSama ? "OK\n" : "GAGAL\n");

// 3. Verifikasi Tampilan di View SPV Keuangan (AsetPerusahaanController)
$reqKeuangan = Request::create('/keuangan/akuntansi/aset-perusahaan', 'GET', ['cari' => $kodeAsetTest]);
$viewKeuangan = $keuanganCtrl->index($reqKeuangan);
$htmlKeuangan = $viewKeuangan->render();

$munculDiTabelKeuangan = strpos($htmlKeuangan, $kodeAsetTest) !== false;
$formatRupiahMuncul = strpos($htmlKeuangan, '450.000.000') !== false;

echo "\n2. Verifikasi Keterbacaan di SPV Keuangan:\n";
echo "   - Aset muncul di tabel SPV Keuangan: " . ($munculDiTabelKeuangan ? "OK\n" : "GAGAL\n");
echo "   - Harga Perolehan Rp 450.000.000 tampil di kolom tabel: " . ($formatRupiahMuncul ? "OK\n" : "GAGAL\n");

// 4. Uji Perbarui Aset dari Operasional
$hargaBaru = 520000000;
$reqUpdate = Request::create('/operasional/armada/kendaraan-aset/' . $kodeAsetTest, 'PUT', [
    'kode_jenis_aset'   => 'AST-TRK',
    'nama_aset'         => $namaAsetTest . ' (Update)',
    'tanggal_pembelian' => date('Y-m-d'),
    'harga_aset'        => $hargaBaru,
    'no_polisi'         => 'B 9999 PBJ',
    'status_aset'       => 'aktif'
]);

$kendaraanCtrl->perbaruiAset($reqUpdate, $kodeAsetTest);

$rowUpdated = DB::table('data_aset')->where('kode_aset', $kodeAsetTest)->first();
$updatePerolehanSama = $rowUpdated && (float) $rowUpdated->harga_perolehan == (float) $hargaBaru;
$updateNilaiBukuSama = $rowUpdated && (float) $rowUpdated->nilai_buku == (float) $hargaBaru;

echo "\n3. Uji Pembaruan (Update):\n";
echo "   - harga_perolehan terupdate ({$rowUpdated->harga_perolehan}): " . ($updatePerolehanSama ? "OK\n" : "GAGAL\n");
echo "   - nilai_buku terupdate ({$rowUpdated->nilai_buku}): " . ($updateNilaiBukuSama ? "OK\n" : "GAGAL\n");

// Bersihkan data uji
DB::table('data_aset')->where('kode_aset', $kodeAsetTest)->delete();

if ($simpanSukses && $hargaAsetSama && $hargaPerolehanSama && $nilaiBukuSama && $munculDiTabelKeuangan && $formatRupiahMuncul && $updatePerolehanSama && $updateNilaiBukuSama) {
    echo "\n========================================================\n";
    echo "  HASIL AKHIR: 100% SUKSES SINKRON KE SPV KEUANGAN!     \n";
    echo "========================================================\n";
    exit(0);
} else {
    echo "\n========================================================\n";
    echo "  HASIL AKHIR: TERDAPAT DATA YANG TIDAK SESUAI!        \n";
    echo "========================================================\n";
    exit(1);
}
