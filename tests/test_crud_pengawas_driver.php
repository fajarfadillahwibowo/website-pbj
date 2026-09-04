<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Operasional\Armada\DriverController;
use App\Models\Operasional\Driver;
use App\Models\Autentikasi\Jabatan;

echo "========================================================\n";
echo "  PENGUJIAN CRUD ROLE PENGAWAS DRIVER (DATA DRIVER)\n";
echo "========================================================\n\n";

$suksesSemua = true;

function cetakHasil($label, $kondisi, $pesanTambahan = '') {
    global $suksesSemua;
    if ($kondisi) {
        echo "  [LULUS] {$label}\n";
    } else {
        echo "  [GAGAL] {$label}" . ($pesanTambahan ? " -> {$pesanTambahan}" : '') . "\n";
        $suksesSemua = false;
    }
}

// 1. Simulasikan Sesi Pengawas Driver
session([
    'kode_jabatan' => 'PENGAWAS_DRIVER',
    'nama_lengkap' => 'Agus Suryanto (Pengawas Driver)',
    'id_pengguna' => 6
]);

$driverCtrl = new DriverController();
$jabatan = Jabatan::where('kode_jabatan', 'PENGAWAS_DRIVER')->first();
$idJabatan = $jabatan ? $jabatan->id_jabatan : 6;

echo "--- 1. UJI FITUR CREATE (INPUT DATA DRIVER) ---\n";
// Uji validasi NIK 16 digit, nama, nomor HP, tanggal, alamat
$kodeTest = 'DRV-PDR-' . rand(100, 999);
$nikTest = '320112' . rand(1000000000, 9999999999);

$reqSimpan = Request::create('/operasional/armada/driver', 'POST', [
    'kode_karyawan' => $kodeTest,
    'nama_karyawan' => 'Supir Pengawas Test',
    'id_jabatan' => $idJabatan,
    'no_ktp' => $nikTest,
    'no_hp' => '082199887766',
    'alamat' => 'Pool Armada PBJ Km 12 Palembang',
    'status_karyawan' => 'kontrak',
    'tanggal_mulai_kerja' => '2025-01-01',
    'tanggal_berhenti' => '2025-12-31',
]);

try {
    $respSimpan = $driverCtrl->simpan($reqSimpan);
    $driverTersimpan = DB::table('data_karyawan')->where('kode_karyawan', $kodeTest)->first();
    cetakHasil("Create Data Driver Baru ({$kodeTest})", !empty($driverTersimpan));
    if ($driverTersimpan) {
        cetakHasil("Validasi Kolom Kategori Otomatis 'driver'", $driverTersimpan->kategori_karyawan === 'driver');
        cetakHasil("Validasi Pembersihan NIK 16 Digit Murni", strlen($driverTersimpan->no_identitas) === 16);
    }
} catch (\Throwable $e) {
    cetakHasil("Create Data Driver Baru ({$kodeTest})", false, $e->getMessage());
}

echo "\n--- 2. UJI FITUR READ (TAMPIL & DETAIL DRIVER) ---\n";
try {
    // A. Uji Index Query (Tampil & Filter)
    $reqIndex = Request::create('/operasional/armada/driver', 'GET', [
        'cari' => 'Supir Pengawas Test',
        'status' => 'kontrak'
    ]);
    $respIndex = $driverCtrl->index($reqIndex);
    cetakHasil("Read/Index View Tampil Data Driver", $respIndex->getName() === 'operasional.armada.driver');

    // B. Uji API Detail Modal (AJAX)
    $respDetail = $driverCtrl->ambilDetail($kodeTest);
    $dataDetail = json_decode($respDetail->getContent(), true);
    
    cetakHasil("Read/Detail Modal Driver ({$kodeTest})", 
        ($dataDetail['status'] ?? '') === 'sukses' &&
        ($dataDetail['data']['kode_karyawan'] ?? '') === $kodeTest &&
        ($dataDetail['data']['tanggal_mulai_kerja'] ?? '') === '2025-01-01'
    );
} catch (\Throwable $e) {
    cetakHasil("Read Data Driver", false, $e->getMessage());
}

echo "\n--- 3. UJI FITUR UPDATE (EDIT & PERBARUI DRIVER) ---\n";
try {
    // Ubah nama, status menjadi tetap, dan kosongkan tanggal_berhenti
    $reqUpdate = Request::create('/operasional/armada/driver/' . $kodeTest, 'PUT', [
        'nama_karyawan' => 'Supir Pengawas Test (Diperbarui)',
        'id_jabatan' => $idJabatan,
        'no_ktp' => $nikTest,
        'no_hp' => '082199887700',
        'alamat' => 'Pool Armada PBJ Km 14 Palembang Baru',
        'status_karyawan' => 'tetap',
        'tanggal_mulai_kerja' => '2025-01-01',
        'tanggal_berhenti' => null, // Supir diangkat tetap -> tanggal berakhir di-nullkan
    ]);

    $respUpdate = $driverCtrl->perbarui($reqUpdate, $kodeTest);
    $driverDiperbarui = DB::table('data_karyawan')->where('kode_karyawan', $kodeTest)->first();

    cetakHasil("Update Nama Driver & Alamat", 
        $driverDiperbarui && 
        $driverDiperbarui->nama_karyawan === 'Supir Pengawas Test (Diperbarui)' &&
        $driverDiperbarui->alamat === 'Pool Armada PBJ Km 14 Palembang Baru'
    );

    cetakHasil("Update Status Karyawan ke 'tetap' & Nullifikasi Tanggal Berhenti", 
        $driverDiperbarui && 
        $driverDiperbarui->status_karyawan === 'tetap' &&
        is_null($driverDiperbarui->tanggal_berhenti)
    );
} catch (\Throwable $e) {
    cetakHasil("Update Data Driver", false, $e->getMessage());
}

echo "\n--- 4. UJI FITUR DELETE (HAPUS DATA DRIVER) ---\n";
try {
    $respHapus = $driverCtrl->hapus($kodeTest);
    $driverSetelahHapus = DB::table('data_karyawan')->where('kode_karyawan', $kodeTest)->first();

    cetakHasil("Delete Data Driver ({$kodeTest})", empty($driverSetelahHapus));
} catch (\Throwable $e) {
    cetakHasil("Delete Data Driver", false, $e->getMessage());
}

echo "\n--- 5. UJI ENDPOINT GENERATOR KODE OTOMATIS ---\n";
try {
    $reqKodeGap = Request::create('/operasional/armada/driver/api/buat-kode', 'GET', ['mode' => 'gap']);
    $respKodeGap = $driverCtrl->buatKodeOtomatis($reqKodeGap);
    $dataKodeGap = json_decode($respKodeGap->getContent(), true);

    cetakHasil("Generator Kode Otomatis Mode Gap-Filling (DRV-XXX)", 
        ($dataKodeGap['status'] ?? '') === 'sukses' &&
        str_starts_with($dataKodeGap['kode_otomatis'], 'DRV-')
    );

    $reqKodeAcak = Request::create('/operasional/armada/driver/api/buat-kode', 'GET', ['mode' => 'acak']);
    $respKodeAcak = $driverCtrl->buatKodeOtomatis($reqKodeAcak);
    $dataKodeAcak = json_decode($respKodeAcak->getContent(), true);

    cetakHasil("Generator Kode Otomatis Mode Acak (DRV-XXXX)", 
        ($dataKodeAcak['status'] ?? '') === 'sukses' &&
        str_starts_with($dataKodeAcak['kode_otomatis'], 'DRV-')
    );
} catch (\Throwable $e) {
    cetakHasil("Generator Kode Otomatis", false, $e->getMessage());
}

echo "\n========================================================\n";
if ($suksesSemua) {
    echo "  HASIL AKHIR: CRUD ROLE PENGAWAS DRIVER 100% SUKSES!\n";
} else {
    echo "  HASIL AKHIR: ADA PENGUJIAN YANG GAGAL!\n";
}
echo "========================================================\n";
