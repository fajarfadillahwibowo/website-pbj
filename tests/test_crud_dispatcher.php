<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Operasional\Armada\KendaraanController;
use App\Http\Controllers\Operasional\Armada\DriverController;
use App\Http\Controllers\Operasional\Pengiriman\SuratJalanController;
use App\Models\Operasional\Kendaraan;
use App\Models\Operasional\Driver;
use App\Models\Operasional\SuratJalan;
use App\Models\Keuangan\PembelianSO;
use App\Models\Autentikasi\Jabatan;

echo "========================================================\n";
echo "  PENGUJIAN END-TO-END CRUD ROLE DISPATCHER\n";
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

// Set session sebagai Dispatcher
session([
    'kode_jabatan' => 'DISPATCHER',
    'nama_lengkap' => 'Dispatcher Tester',
    'id_pengguna' => 1
]);

// ---------------------------------------------------------
// 1. PENGUJIAN CRUD MODUL DATA KENDARAAN
// ---------------------------------------------------------
echo "--- 1. MODUL DATA KENDARAAN ---\n";
$kendaraanCtrl = new KendaraanController();

// A. Create / Simpan Kendaraan
$kodeTestKendaraan = 'KND-TEST-' . rand(100, 999);
$platTest = 'BG ' . rand(1000, 9999) . ' XX';

$reqSimpanKnd = Request::create('/operasional/armada/kendaraan', 'POST', [
    'kode_kendaraan' => $kodeTestKendaraan,
    'no_polisi' => $platTest,
    'merek_kendaraan' => 'Hino 500 Test Unit',
    'muatan' => '30 Ton (600 Zak)',
    'tahun_pembuatan' => 2024,
    'status_kendaraan' => 'aktif',
    'nama_pemilik' => 'PT Putra Balkom Jaya',
    'harga_aset' => 850000000,
    'tanggal_pembelian' => '2024-05-15',
    'tanggal_kir' => '2025-05-15',
    'tanggal_pajak' => '2025-05-15',
    'jumlah_unit' => 1
]);

try {
    $respSimpanKnd = $kendaraanCtrl->simpan($reqSimpanKnd);
    if (session('error')) {
        echo "    Debug error Kendaraan simpan: " . session('error') . "\n";
    }
} catch (\Throwable $e) {
    echo "    Exception Kendaraan simpan: " . $e->getMessage() . " at line " . $e->getLine() . "\n";
}
$kendaraanDb = DB::table('data_kendaraan')->where('kode_kendaraan', $kodeTestKendaraan)->first();
$asetDb = DB::table('data_aset')->where('no_polisi', $platTest)->first();

cetakHasil("Create Kendaraan ({$kodeTestKendaraan})", !empty($kendaraanDb) && !empty($asetDb));

// B. Read / Detail Kendaraan
$kodeAsetLookup = $kendaraanDb ? $kendaraanDb->kode_aset : ($asetDb ? $asetDb->kode_aset : $kodeTestKendaraan);
$respDetailKnd = $kendaraanCtrl->ambilDetail($kodeAsetLookup);
$detailData = json_decode($respDetailKnd->getContent(), true);

cetakHasil("Read/Detail Kendaraan ({$kodeAsetLookup})", ($detailData['status'] ?? '') === 'sukses');

// C. Update / Perbarui Kendaraan
$platBaru = 'BG ' . rand(1000, 9999) . ' YY';
$reqUpdateKnd = Request::create('/operasional/armada/kendaraan/' . $kodeAsetLookup, 'PUT', [
    'no_polisi' => $platBaru,
    'merek_kendaraan' => 'Hino 500 Updated Test',
    'muatan' => '32 Ton (640 Zak)',
    'tahun_pembuatan' => 2025,
    'status_kendaraan' => 'rusak',
    'nama_pemilik' => 'PT Putra Balkom Jaya',
    'harga_aset' => 900000000,
    'tanggal_kir' => '2025-08-20',
    'tanggal_pajak' => '2025-08-20',
]);

$respUpdateKnd = $kendaraanCtrl->perbarui($reqUpdateKnd, $kodeAsetLookup);
$kendaraanUpdated = DB::table('data_kendaraan')->where('kode_kendaraan', $kodeTestKendaraan)->first();
$asetUpdated = DB::table('data_aset')->where('kode_aset', $kodeAsetLookup)->first();

cetakHasil("Update Kendaraan ({$platBaru}, status: rusak, harga: 900jt)", 
    $kendaraanUpdated && $kendaraanUpdated->no_polisi === $platBaru && $kendaraanUpdated->status_kendaraan === 'rusak'
);

// D. Delete / Hapus Kendaraan
$respHapusKnd = $kendaraanCtrl->hapus(new Request(), $kodeAsetLookup);
$kendaraanSetelahHapus = DB::table('data_kendaraan')->where('kode_kendaraan', $kodeTestKendaraan)->first();
$asetSetelahHapus = DB::table('data_aset')->where('kode_aset', $kodeAsetLookup)->first();

cetakHasil("Delete Kendaraan ({$kodeTestKendaraan})", empty($kendaraanSetelahHapus) && empty($asetSetelahHapus));


// ---------------------------------------------------------
// 2. PENGUJIAN CRUD MODUL DATA KARYAWAN (DRIVER)
// ---------------------------------------------------------
echo "\n--- 2. MODUL DATA KARYAWAN (DRIVER) ---\n";
$driverCtrl = new DriverController();
$jabatan = Jabatan::first();
$idJabatan = $jabatan ? $jabatan->id_jabatan : 1;

// A. Create / Simpan Driver
$kodeTestDriver = 'DRV-TEST-' . rand(100, 999);
$nikTest = '320101' . rand(1000000000, 9999999999);

$reqSimpanDrv = Request::create('/operasional/armada/driver', 'POST', [
    'kode_karyawan' => $kodeTestDriver,
    'nama_karyawan' => 'Budi Supir Test',
    'id_jabatan' => $idJabatan,
    'no_ktp' => $nikTest,
    'no_hp' => '081234567890',
    'alamat' => 'Jl. Merdeka No. 123 Palembang',
    'status_karyawan' => 'aktif',
    'tanggal_mulai_kerja' => '2024-01-10',
    'tanggal_berhenti' => '2025-01-10',
]);

$respSimpanDrv = $driverCtrl->simpan($reqSimpanDrv);
$driverDb = DB::table('data_karyawan')->where('kode_karyawan', $kodeTestDriver)->first();

cetakHasil("Create Driver ({$kodeTestDriver} - {$nikTest})", !empty($driverDb));

// B. Read / Detail Driver
$respDetailDrv = $driverCtrl->ambilDetail($kodeTestDriver);
$detailDrvData = json_decode($respDetailDrv->getContent(), true);

cetakHasil("Read/Detail Driver ({$kodeTestDriver})", 
    ($detailDrvData['status'] ?? '') === 'sukses' &&
    isset($detailDrvData['data']['nama_karyawan']) &&
    $detailDrvData['data']['tanggal_mulai_kerja'] === '2024-01-10'
);

// C. Update / Perbarui Driver (Ubah nama, status, dan kosongkan tanggal_berhenti)
$reqUpdateDrv = Request::create('/operasional/armada/driver/' . $kodeTestDriver, 'PUT', [
    'nama_karyawan' => 'Budi Supir Perkasa',
    'id_jabatan' => $idJabatan,
    'no_ktp' => $nikTest,
    'no_hp' => '081299998888',
    'alamat' => 'Jl. Veteran No. 45 Palembang',
    'status_karyawan' => 'tetap',
    'tanggal_mulai_kerja' => '2024-01-10',
    'tanggal_berhenti' => null, // Uji pengosongan tanggal_berhenti jika karyawan diangkat tetap
]);

$respUpdateDrv = $driverCtrl->perbarui($reqUpdateDrv, $kodeTestDriver);
$driverUpdated = DB::table('data_karyawan')->where('kode_karyawan', $kodeTestDriver)->first();

cetakHasil("Update Driver ({$kodeTestDriver} -> Tetap, tgl berhenti di-nullkan)",
    $driverUpdated && 
    $driverUpdated->nama_karyawan === 'Budi Supir Perkasa' && 
    $driverUpdated->status_karyawan === 'tetap' && 
    is_null($driverUpdated->tanggal_berhenti)
);

// D. Delete / Hapus Driver
$respHapusDrv = $driverCtrl->hapus($kodeTestDriver);
$driverSetelahHapus = DB::table('data_karyawan')->where('kode_karyawan', $kodeTestDriver)->first();

cetakHasil("Delete Driver ({$kodeTestDriver})", empty($driverSetelahHapus));


// ---------------------------------------------------------
// 3. PENGUJIAN CRUD MODUL PENGIRIMAN (SURAT JALAN)
// ---------------------------------------------------------
echo "\n--- 3. MODUL PENGIRIMAN (SURAT JALAN) ---\n";
$sjCtrl = new SuratJalanController();

// Siapkan prasyarat data untuk Surat Jalan
$so = PembelianSO::first();
$idSo = $so ? $so->id_so : 1;
$existingKendaraan = Kendaraan::first();
$kodeKndSj = $existingKendaraan ? $existingKendaraan->kode_kendaraan : 'KND-001';
$existingDriver = Driver::first();
$kodeDrvSj = $existingDriver ? $existingDriver->kode_karyawan : 'DRV-001';

// A. Create / Simpan Surat Jalan
$nomorSjTest = 'SJ-TEST-' . rand(1000, 9999);

$reqSimpanSj = Request::create('/operasional/pengiriman/surat-jalan', 'POST', [
    'nomor_surat_jalan' => $nomorSjTest,
    'id_so' => $idSo,
    'kode_kendaraan' => $kodeKndSj,
    'kode_driver' => $kodeDrvSj,
    'tanggal_kirim' => '2026-09-04 10:00:00',
    'status_pengiriman' => 'menunggu',
    'keterangan' => 'Pengujian otomatis Surat Jalan Dispatcher',
]);

$respSimpanSj = $sjCtrl->simpan($reqSimpanSj);
$sjDb = DB::table('pengiriman')->where('nomor_surat_jalan', $nomorSjTest)->first();

cetakHasil("Create Surat Jalan ({$nomorSjTest})", !empty($sjDb));

if ($sjDb) {
    $idPengiriman = $sjDb->id_pengiriman;

    // B. Read / Detail Surat Jalan
    $respDetailSj = $sjCtrl->ambilDetail($idPengiriman);
    $detailSjData = json_decode($respDetailSj->getContent(), true);

    cetakHasil("Read/Detail Surat Jalan (ID: {$idPengiriman})", 
        ($detailSjData['status'] ?? '') === 'sukses' &&
        isset($detailSjData['data']['nomor_surat_jalan'])
    );

    // C. Update / Perbarui Surat Jalan
    $reqUpdateSj = Request::create('/operasional/pengiriman/surat-jalan/' . $idPengiriman, 'PUT', [
        'nomor_surat_jalan' => $nomorSjTest,
        'id_so' => $idSo,
        'kode_kendaraan' => $kodeKndSj,
        'kode_driver' => $kodeDrvSj,
        'tanggal_kirim' => '2026-09-04 14:30:00',
        'status_pengiriman' => 'dalam_perjalanan',
        'keterangan' => 'Status diperbarui: Sedang dalam perjalanan ke lokasi proyek',
    ]);

    $respUpdateSj = $sjCtrl->perbarui($reqUpdateSj, $idPengiriman);
    $sjUpdated = DB::table('pengiriman')->where('id_pengiriman', $idPengiriman)->first();

    cetakHasil("Update Surat Jalan (status: dalam_perjalanan)", 
        $sjUpdated && $sjUpdated->status_pengiriman === 'dalam_perjalanan'
    );

    // D. Delete / Hapus Surat Jalan
    $respHapusSj = $sjCtrl->hapus($idPengiriman);
    $sjSetelahHapus = DB::table('pengiriman')->where('id_pengiriman', $idPengiriman)->first();

    cetakHasil("Delete Surat Jalan (ID: {$idPengiriman})", empty($sjSetelahHapus));
}

echo "\n========================================================\n";
if ($suksesSemua) {
    echo "  HASIL AKHIR: SEMUA FITUR CRUD 3 MODUL 100% BERHASIL!\n";
} else {
    echo "  HASIL AKHIR: ADA PENGUJIAN YANG GAGAL!\n";
}
echo "========================================================\n";
