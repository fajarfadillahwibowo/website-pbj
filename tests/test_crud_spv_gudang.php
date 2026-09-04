<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Operasional\Gudang\StokGudangController;
use App\Http\Controllers\Operasional\Gudang\StockOpnameController;
use App\Models\Operasional\Gudang;
use App\Models\Operasional\StockOpname;

echo "========================================================\n";
echo "  PENGUJIAN MENYELURUH CRUD ROLE: SPV GUDANG            \n";
echo "  Modul: Data Gudang & Opname Gudang                   \n";
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

// Persiapan: Ambil kode barang semen yang valid
$kodeBarangSemen = DB::table('data_semen')->value('kode_barang') ?? 'SMN-002';
$kodeGudangUji = 'GDG-TEST-SPV';
$nomorOpnameUji = 'OPN-TEST-SPV';

// Bersihkan data lama jika ada sisa uji sebelumnya
DB::table('opname_gudang')->where('nomor_opname', $nomorOpnameUji)->delete();
DB::table('list_gudang_so')->where('kode_gudang', $kodeGudangUji)->delete();

$stokController = new StokGudangController();
$opnameController = new StockOpnameController();

// ----------------------------------------------------
// BAGIAN 1: SIKLUS CRUD DATA GUDANG
// ----------------------------------------------------
echo "--- 1. Pengujian CRUD Data Fasilitas Gudang ---\n";

// 1.1 Buat Kode Otomatis
$reqKode = Request::create('/operasional/gudang/stok/api/buat-kode', 'GET', ['mode' => 'gap']);
$respKode = $stokController->buatKodeOtomatis($reqKode);
$dataKode = json_decode($respKode->getContent(), true);
assertTest($dataKode['status'] === 'sukses' && !empty($dataKode['kode_otomatis']), "Generator Kode Otomatis Gudang: {$dataKode['kode_otomatis']}");

// 1.2 CREATE Gudang Baru
$reqSimpan = Request::create('/operasional/gudang/stok', 'POST', [
    'kode_gudang' => $kodeGudangUji,
    'nama_gudang' => 'Gudang Uji SPV Gudang Cikarang',
    'jenis_gudang' => 'Distribusi',
    'kode_barang' => $kodeBarangSemen,
    'plant' => 'Plant Cikarang',
    'harga_barang' => '65.000', // Format teks dengan titik rupiah
    'stok_tersedia' => 5000,
    'distrik' => 'Kabupaten Bekasi',
    'sub_distrik' => 'Cikarang Barat',
]);

$respSimpan = $stokController->simpan($reqSimpan);
$gudangDibuat = Gudang::find($kodeGudangUji);
assertTest($gudangDibuat !== null && $gudangDibuat->stok_tersedia === 5000 && (float)$gudangDibuat->harga_barang == 65000, "CREATE Gudang ({$kodeGudangUji}) dengan sanitasi Rupiah berhasil");

// 1.3 READ Detail Gudang via API
$respDetail = $stokController->ambilDetail($kodeGudangUji);
$dataDetail = json_decode($respDetail->getContent(), true);
assertTest($dataDetail['status'] === 'sukses' && $dataDetail['data']['kode_gudang'] === $kodeGudangUji, "READ Detail Gudang via JSON API berhasil");

// 1.4 UPDATE Gudang
$reqUpdate = Request::create('/operasional/gudang/stok/' . $kodeGudangUji, 'PUT', [
    'nama_gudang' => 'Gudang Uji SPV Gudang Cikarang (Updated)',
    'jenis_gudang' => 'Utama',
    'kode_barang' => $kodeBarangSemen,
    'plant' => 'Plant Narogong',
    'harga_barang' => '67000',
    'stok_tersedia' => 6000,
    'distrik' => 'Kabupaten Bogor',
    'sub_distrik' => 'Klapanunggal',
]);

$respUpdate = $stokController->perbarui($reqUpdate, $kodeGudangUji);
$gudangDiupdate = Gudang::find($kodeGudangUji);
assertTest(
    $gudangDiupdate->nama_gudang === 'Gudang Uji SPV Gudang Cikarang (Updated)' &&
    $gudangDiupdate->plant === 'Plant Narogong' &&
    $gudangDiupdate->stok_tersedia === 6000,
    "UPDATE Data Gudang berhasil"
);

// 1.5 MUTASI STOK
// A. Masuk (+500 zak)
$reqMutasiMasuk = Request::create('/operasional/gudang/stok/' . $kodeGudangUji . '/mutasi', 'POST', [
    'tipe_mutasi' => 'masuk',
    'jumlah_zak' => 500,
    'keterangan' => 'Penerimaan pabrik',
]);
$stokController->mutasiStok($reqMutasiMasuk, $kodeGudangUji);
$gudangMutasi1 = Gudang::find($kodeGudangUji);
assertTest($gudangMutasi1->stok_tersedia === 6500, "Mutasi Stok MASUK (+500) -> Stok sekarang: {$gudangMutasi1->stok_tersedia} Zak");

// B. Keluar (-300 zak)
$reqMutasiKeluar = Request::create('/operasional/gudang/stok/' . $kodeGudangUji . '/mutasi', 'POST', [
    'tipe_mutasi' => 'keluar',
    'jumlah_zak' => 300,
    'keterangan' => 'Distribusi toko',
]);
$stokController->mutasiStok($reqMutasiKeluar, $kodeGudangUji);
$gudangMutasi2 = Gudang::find($kodeGudangUji);
assertTest($gudangMutasi2->stok_tersedia === 6200, "Mutasi Stok KELUAR (-300) -> Stok sekarang: {$gudangMutasi2->stok_tersedia} Zak");

// C. Atur Langsung (Set to 2500 zak)
$reqMutasiAtur = Request::create('/operasional/gudang/stok/' . $kodeGudangUji . '/mutasi', 'POST', [
    'tipe_mutasi' => 'atur',
    'jumlah_zak' => 2500,
    'keterangan' => 'Penyesuaian stok opname',
]);
$stokController->mutasiStok($reqMutasiAtur, $kodeGudangUji);
$gudangMutasi3 = Gudang::find($kodeGudangUji);
assertTest($gudangMutasi3->stok_tersedia === 2500, "Mutasi Stok ATUR LANGSUNG (2.500) -> Stok sekarang: {$gudangMutasi3->stok_tersedia} Zak");

// D. Atur Langsung ke 0 Zak (Gudang kosong)
$reqMutasiNol = Request::create('/operasional/gudang/stok/' . $kodeGudangUji . '/mutasi', 'POST', [
    'tipe_mutasi' => 'atur',
    'jumlah_zak' => 0,
    'keterangan' => 'Gudang dikosongkan sementara',
]);
$stokController->mutasiStok($reqMutasiNol, $kodeGudangUji);
$gudangMutasi4 = Gudang::find($kodeGudangUji);
assertTest($gudangMutasi4->stok_tersedia === 0, "Mutasi Stok ATUR KE 0 ZAK -> Stok sekarang: {$gudangMutasi4->stok_tersedia} Zak");

// Kembalikan stok ke 2500 untuk pengujian opname
$gudangMutasi4->update(['stok_tersedia' => 2500]);

echo "\n--- 2. Pengujian CRUD Stok Opname Fisik Gudang ---\n";

// 2.1 Buat Nomor Opname Otomatis
$reqNoOpn = Request::create('/operasional/gudang/opname/api/buat-kode', 'GET', ['mode' => 'gap']);
$respNoOpn = $opnameController->buatNomorOpname($reqNoOpn);
$dataNoOpn = json_decode($respNoOpn->getContent(), true);
assertTest($dataNoOpn['status'] === 'sukses' && !empty($dataNoOpn['kode_otomatis']), "Generator Nomor Opname Otomatis: {$dataNoOpn['kode_otomatis']}");

// 2.2 CREATE Catatan Opname (Status Draft)
$reqOpnameSimpan = Request::create('/operasional/gudang/opname', 'POST', [
    'nomor_opname' => $nomorOpnameUji,
    'kode_gudang' => $kodeGudangUji,
    'tanggal_opname' => date('Y-m-d'),
    'stok_sistem' => 2500,
    'stok_fisik' => 2485,
    'keterangan_selisih' => 'Ditemukan 15 zak semen robek saat bongkar muat armada.',
    'status_konfirmasi' => 'draft',
    'petugas_opname' => 'Ahmad Fauzi (SPV Gudang)',
]);
$respOpnSimpan = $opnameController->simpan($reqOpnameSimpan);
$opnameDibuat = StockOpname::where('nomor_opname', $nomorOpnameUji)->first();
assertTest(
    $opnameDibuat !== null &&
    $opnameDibuat->selisih === -15 &&
    $opnameDibuat->status_konfirmasi === 'draft',
    "CREATE Opname Draft ({$nomorOpnameUji}) dengan perhitungan selisih otomatis (-15 Zak) berhasil"
);

// 2.3 READ Detail Opname via API
$respOpnDetail = $opnameController->ambilDetail($opnameDibuat->id_opname);
$dataOpnDetail = json_decode($respOpnDetail->getContent(), true);
assertTest(
    $dataOpnDetail['status'] === 'sukses' &&
    $dataOpnDetail['data']['nomor_opname'] === $nomorOpnameUji &&
    $dataOpnDetail['data']['tanggal_opname'] === date('Y-m-d'),
    "READ Detail Opname via API (Format tanggal presisi Y-m-d: {$dataOpnDetail['data']['tanggal_opname']}) berhasil"
);

// 2.4 UPDATE Opname
$reqOpnUpdate = Request::create('/operasional/gudang/opname/' . $opnameDibuat->id_opname, 'PUT', [
    'kode_gudang' => $kodeGudangUji,
    'tanggal_opname' => date('Y-m-d'),
    'stok_sistem' => 2500,
    'stok_fisik' => 2490, // Revisi selisih menjadi -10 zak
    'keterangan_selisih' => 'Revisi perhitungan fisik: 5 zak berhasil diselamatkan, selisih riil -10 zak.',
    'status_konfirmasi' => 'draft',
    'petugas_opname' => 'Ahmad Fauzi (SPV Gudang)',
]);
$opnameController->perbarui($reqOpnUpdate, $opnameDibuat->id_opname);
$opnameDiupdate = StockOpname::find($opnameDibuat->id_opname);
assertTest(
    $opnameDiupdate->stok_fisik === 2490 &&
    $opnameDiupdate->selisih === -10,
    "UPDATE Data Opname (Kuantitas fisik dan selisih otomatis terkalibrasi) berhasil"
);

// 2.5 PROTEKSI RELASI: Coba hapus Gudang saat masih punya Opname
echo "\n--- 3. Pengujian Integritas Relasional & Sinkronisasi ---\n";
$respHapusGudangTertahan = $stokController->hapus($kodeGudangUji);
$gudangMasihAda = Gudang::find($kodeGudangUji);
assertTest(
    $gudangMasihAda !== null,
    "PROTEKSI HAPUS: Gudang tidak bisa dihapus sembarangan saat memiliki riwayat Stock Opname"
);

// 2.6 KONFIRMASI SPV GUDANG & SINKRONISASI STOK
$reqKonfirmasi = Request::create('/operasional/gudang/opname/' . $opnameDibuat->id_opname . '/konfirmasi', 'PATCH');
$opnameController->konfirmasiSPV($reqKonfirmasi, $opnameDibuat->id_opname);

$opnameDikonfirmasi = StockOpname::find($opnameDibuat->id_opname);
$gudangTersinkron = Gudang::find($kodeGudangUji);

assertTest(
    $opnameDikonfirmasi->status_konfirmasi === 'dikonfirmasi_spv' &&
    $gudangTersinkron->stok_tersedia === 2490,
    "KONFIRMASI SPV & SINKRONISASI: Status opname 'dikonfirmasi_spv' dan stok riil gudang sinkron menjadi 2.490 Zak"
);

// 2.7 DELETE Catatan Opname
$respOpnHapus = $opnameController->hapus($opnameDibuat->id_opname);
$opnameTerhapus = StockOpname::where('nomor_opname', $nomorOpnameUji)->first();
assertTest($opnameTerhapus === null, "DELETE Catatan Stock Opname berhasil");

// 2.8 DELETE Fasilitas Gudang setelah relasi bersih
$respGudangHapus = $stokController->hapus($kodeGudangUji);
$gudangTerhapus = Gudang::find($kodeGudangUji);
assertTest($gudangTerhapus === null, "DELETE Fasilitas Gudang berhasil setelah riwayat opname bersih");

echo "\n========================================================\n";
echo "  HASIL AKHIR PENGUJIAN: {$sukses} Berhasil, {$gagal} Gagal\n";
echo "========================================================\n";

if ($gagal > 0) {
    exit(1);
}
exit(0);
