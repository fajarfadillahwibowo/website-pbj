<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Operasional\Pengiriman\OngkosAngkutController;
use App\Http\Controllers\Operasional\KSO\KSOController;
use App\Http\Controllers\Operasional\Gudang\StockOpnameController;
use App\Http\Controllers\Operasional\Armada\KendaraanController;
use App\Http\Controllers\Operasional\Pengiriman\SuratJalanController;
use App\Models\Operasional\OngkosAngkut;
use App\Models\Operasional\KSO;
use App\Models\Operasional\OngkosKSO;

echo "========================================================\n";
echo "  PENGUJIAN MENYELURUH CRUD ROLE: SPV OPERASIONAL       \n";
echo "  5 Menu Sidebar: Ongkos Angkut, Opname Gudang,         \n";
echo "  Data Kendaraan, Pengiriman, dan Data KSO             \n";
echo "========================================================\n\n";

$gagal = 0;
$sukses = 0;

function assertTest($kondisi, $namaUji) {
    global $sukses, $gagal;
    if ($kondisi) {
        echo "  [BERHASIL] {$namaUji}\n";
        $sukses++;
    } else {
        echo "  [GAGAL]    {$namaUji}\n";
        $gagal++;
    }
}

// Set session role SPV Operasional
session(['kode_jabatan' => 'SPV_OPERASIONAL']);

// =========================================================================
// 1. PENGUJIAN CRUD: DATA ONGKOS ANGKUT
// =========================================================================
echo "--- 1. Pengujian CRUD Data Ongkos Angkut ---\n";
$oaController = new OngkosAngkutController();
$kodeOaUji = 'OA-TEST-SPO';

// Bersihkan data uji sebelumnya jika ada
DB::table('data_ongkos_angkut')->where('kode_oa', $kodeOaUji)->delete();

// 1.1 Buat Kode Otomatis OA
$reqBuatOa = Request::create('/operasional/pengiriman/ongkos-angkut/api/buat-kode', 'GET');
$respBuatOa = $oaController->buatKodeOtomatis($reqBuatOa);
$dataBuatOa = json_decode($respBuatOa->getContent(), true);
assertTest($dataBuatOa['status'] === 'sukses' && !empty($dataBuatOa['kode_otomatis']), "Generator Kode Otomatis OA: {$dataBuatOa['kode_otomatis']}");

// 1.2 CREATE Data Ongkos Angkut (dengan sanitasi nominal bertitik)
$kodeGudangContoh = DB::table('list_gudang_so')->value('kode_gudang') ?? 'GDG-PUSAT';
$reqSimpanOa = Request::create('/operasional/pengiriman/ongkos-angkut', 'POST', [
    'kode_oa' => $kodeOaUji,
    'nama_oa' => 'Trayek Khusus Cikarang ➔ Karawang Timur',
    'kode_gudang' => $kodeGudangContoh,
    'kontrak_oa' => 'KTR-OA-2026/09',
    'muatan_oa' => 'Semen Zak 50kg',
    'harga_oa' => '1.250.000', // Format dengan titik ribuan
    'harga_kso' => '1.150.000',
    'harga_kso_khusus' => '1.300.000',
    'wilayah_oa' => 'Karawang',
    'keterangan' => 'Rute prioritas distribusi proyek',
]);
$respSimpanOa = $oaController->simpan($reqSimpanOa);
$oaDibuat = OngkosAngkut::where('kode_oa', $kodeOaUji)->first();
assertTest(
    $oaDibuat !== null &&
    (float)$oaDibuat->harga_oa == 1250000 &&
    (float)$oaDibuat->harga_kso == 1150000,
    "CREATE Data Ongkos Angkut ({$kodeOaUji}) dengan sanitasi Rupiah berhasil"
);

// 1.3 READ Detail OA via JSON API
$respDetailOa = $oaController->ambilDetail($kodeOaUji);
$dataDetailOa = json_decode($respDetailOa->getContent(), true);
assertTest($dataDetailOa['status'] === 'sukses' && $dataDetailOa['data']['kode_oa'] === $kodeOaUji, "READ Detail OA via API berhasil");

// 1.4 UPDATE Data Ongkos Angkut
$reqUpdateOa = Request::create('/operasional/pengiriman/ongkos-angkut/' . $kodeOaUji, 'PUT', [
    'nama_oa' => 'Trayek Khusus Cikarang ➔ Karawang Timur (Revisi Tarif)',
    'kode_gudang' => $kodeGudangContoh,
    'kontrak_oa' => 'KTR-OA-2026/09-REV',
    'muatan_oa' => 'Semen Zak 50kg',
    'harga_oa' => '1300000',
    'harga_kso' => '1200000',
    'harga_kso_khusus' => '1350000',
    'wilayah_oa' => 'Karawang Barat',
    'keterangan' => 'Penyesuaian kenaikan tarif solar',
]);
$oaController->perbarui($reqUpdateOa, $kodeOaUji);
$oaDiupdate = OngkosAngkut::where('kode_oa', $kodeOaUji)->first();
assertTest(
    $oaDiupdate->nama_oa === 'Trayek Khusus Cikarang ➔ Karawang Timur (Revisi Tarif)' &&
    (float)$oaDiupdate->harga_oa == 1300000,
    "UPDATE Data Ongkos Angkut berhasil"
);

// 1.5 DELETE Data Ongkos Angkut
$oaController->hapus($kodeOaUji);
$oaTerhapus = OngkosAngkut::where('kode_oa', $kodeOaUji)->first();
assertTest($oaTerhapus === null, "DELETE Data Ongkos Angkut berhasil");

// =========================================================================
// 2. PENGUJIAN CRUD: DATA KSO & ONGKOS ANGKUT KSO
// =========================================================================
echo "\n--- 2. Pengujian CRUD Data KSO & Tarif Ongkos KSO ---\n";
$ksoController = new KSOController();
$kodeKsoUji = 'KSO-TEST-SPO';
$kodeOaKsoUji = 'OAK-TEST-SPO';

// Bersihkan data uji sebelumnya jika ada
DB::table('ongkos_kso')->where('kode_oa', $kodeOaKsoUji)->delete();
DB::table('data_kso')->where('kode_kso', $kodeKsoUji)->delete();

// 2.1 Buat Kode KSO Otomatis
$reqBuatKso = Request::create('/operasional/kso/api/buat-kode', 'GET', ['mode' => 'gap']);
$respBuatKso = $ksoController->buatKodeKSO($reqBuatKso);
$dataBuatKso = json_decode($respBuatKso->getContent(), true);
assertTest($dataBuatKso['status'] === 'sukses' && !empty($dataBuatKso['kode_otomatis']), "Generator Kode Otomatis KSO: {$dataBuatKso['kode_otomatis']}");

// 2.2 CREATE Mitra KSO Baru
$reqSimpanKso = Request::create('/operasional/kso', 'POST', [
    'kode_kso' => $kodeKsoUji,
    'nama_kso' => 'KSO Mitra Armada Ekspedisi Prima',
]);
$ksoController->simpanKSO($reqSimpanKso);
$ksoDibuat = KSO::where('kode_kso', $kodeKsoUji)->first();
assertTest(
    $ksoDibuat !== null &&
    $ksoDibuat->nama_kso === 'KSO Mitra Armada Ekspedisi Prima',
    "CREATE Data Mitra KSO ({$kodeKsoUji}) sesuai class diagram berhasil"
);

// 2.3 READ Detail KSO via JSON API
$respDetailKso = $ksoController->ambilDetailKSO($kodeKsoUji);
$dataDetailKso = json_decode($respDetailKso->getContent(), true);
assertTest(
    $dataDetailKso['status'] === 'sukses' &&
    $dataDetailKso['data']['kode_kso'] === $kodeKsoUji &&
    $dataDetailKso['data']['nama_kso'] === 'KSO Mitra Armada Ekspedisi Prima',
    "READ Detail KSO via API (Kode: {$dataDetailKso['data']['kode_kso']}) berhasil"
);

// 2.4 UPDATE Mitra KSO
$reqUpdateKso = Request::create('/operasional/kso/' . $kodeKsoUji, 'PUT', [
    'nama_kso' => 'KSO Mitra Armada Ekspedisi Prima (Amandemen 1)',
]);
$ksoController->perbaruiKSO($reqUpdateKso, $kodeKsoUji);
$ksoDiupdate = KSO::where('kode_kso', $kodeKsoUji)->first();
assertTest(
    $ksoDiupdate->nama_kso === 'KSO Mitra Armada Ekspedisi Prima (Amandemen 1)',
    "UPDATE Data Mitra KSO berhasil"
);

// 2.5 Buat Kode OA KSO Otomatis
$reqBuatOaKso = Request::create('/operasional/kso/ongkos/api/buat-kode', 'GET', ['mode' => 'gap']);
$respBuatOaKso = $ksoController->buatKodeOA($reqBuatOaKso);
$dataBuatOaKso = json_decode($respBuatOaKso->getContent(), true);
assertTest($dataBuatOaKso['status'] === 'sukses' && !empty($dataBuatOaKso['kode_otomatis']), "Generator Kode Otomatis OA KSO: {$dataBuatOaKso['kode_otomatis']}");

// 2.6 CREATE Tarif Ongkos Angkut KSO
$reqSimpanOaKso = Request::create('/operasional/kso/ongkos/simpan', 'POST', [
    'kode_oa' => $kodeOaKsoUji,
    'kode_kso' => $kodeKsoUji,
    'nama_oa' => 'Plant Narogong ➔ Gudang Distribusi Subang',
    'muatan' => 'Tronton 30 Ton (600 Zak)',
    'ongkos_angkut' => '2.100.000', // Format ribuan bertitik
]);
$ksoController->simpanOngkos($reqSimpanOaKso);
$oaKsoDibuat = OngkosKSO::where('kode_oa', $kodeOaKsoUji)->first();
assertTest(
    $oaKsoDibuat !== null &&
    (float)$oaKsoDibuat->ongkos_angkut == 2100000 &&
    $oaKsoDibuat->kode_kso === $kodeKsoUji,
    "CREATE Tarif Ongkos Angkut KSO ({$kodeOaKsoUji}) berhasil"
);

// 2.7 READ Detail Ongkos Angkut KSO via API
$respDetailOaKso = $ksoController->ambilDetailOngkos($kodeOaKsoUji);
$dataDetailOaKso = json_decode($respDetailOaKso->getContent(), true);
assertTest($dataDetailOaKso['status'] === 'sukses' && $dataDetailOaKso['data']['kode_oa'] === $kodeOaKsoUji, "READ Detail Tarif OA KSO via API berhasil");

// 2.8 UPDATE Tarif Ongkos Angkut KSO
$reqUpdateOaKso = Request::create('/operasional/kso/ongkos/' . $kodeOaKsoUji, 'PUT', [
    'kode_kso' => $kodeKsoUji,
    'nama_oa' => 'Plant Narogong ➔ Gudang Distribusi Subang (Revisi Tarif)',
    'muatan' => 'Tronton 30 Ton (600 Zak)',
    'ongkos_angkut' => '2250000',
]);
$ksoController->perbaruiOngkos($reqUpdateOaKso, $kodeOaKsoUji);
$oaKsoDiupdate = OngkosKSO::where('kode_oa', $kodeOaKsoUji)->first();
assertTest(
    $oaKsoDiupdate->nama_oa === 'Plant Narogong ➔ Gudang Distribusi Subang (Revisi Tarif)' &&
    (float)$oaKsoDiupdate->ongkos_angkut == 2250000,
    "UPDATE Tarif Ongkos Angkut KSO berhasil"
);

// 2.9 DELETE Tarif OA KSO & Mitra KSO
$ksoController->hapusOngkos($kodeOaKsoUji);
$oaKsoTerhapus = OngkosKSO::where('kode_oa', $kodeOaKsoUji)->first();
assertTest($oaKsoTerhapus === null, "DELETE Tarif Ongkos Angkut KSO berhasil");

$ksoController->hapusKSO($kodeKsoUji);
$ksoTerhapus = KSO::where('kode_kso', $kodeKsoUji)->first();
assertTest($ksoTerhapus === null, "DELETE Data Mitra KSO berhasil");

// =========================================================================
// 3. PENGUJIAN INTEGRASI CONTROLLER: OPNAME, KENDARAAN, & PENGIRIMAN
// =========================================================================
echo "\n--- 3. Verifikasi Controller Opname Gudang, Data Kendaraan & Pengiriman ---\n";

// 3.1 Opname Gudang Controller
$opnameController = new StockOpnameController();
$respOpnameIndex = $opnameController->index(Request::create('/operasional/gudang/opname', 'GET'));
assertTest($respOpnameIndex !== null, "Controller Opname Gudang (StockOpnameController) dapat diakses");

// 3.2 Data Kendaraan Controller
$kendaraanController = new KendaraanController();
$respKendaraanIndex = $kendaraanController->index(Request::create('/operasional/armada/kendaraan', 'GET'));
assertTest($respKendaraanIndex !== null, "Controller Data Kendaraan (KendaraanController) dapat diakses");

// 3.3 Pengiriman / Surat Jalan Controller
$sjController = new SuratJalanController();
$respSjIndex = $sjController->index(Request::create('/operasional/pengiriman/surat-jalan', 'GET'));
assertTest($respSjIndex !== null, "Controller Pengiriman (SuratJalanController) dapat diakses");

echo "\n========================================================\n";
echo "  HASIL AKHIR PENGUJIAN: {$sukses} Berhasil, {$gagal} Gagal\n";
echo "========================================================\n";

if ($gagal > 0) {
    exit(1);
}
exit(0);
