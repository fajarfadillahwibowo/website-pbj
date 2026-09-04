<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Operasional\Bengkel\SparepartController;
use App\Http\Controllers\Operasional\Bengkel\PembelianSparepartController;
use App\Http\Controllers\Operasional\Bengkel\PerbaikanKendaraanController;
use App\Models\Operasional\Sparepart;
use App\Models\Operasional\PembelianSparepart;
use App\Models\Operasional\PerbaikanKendaraan;

echo "========================================================\n";
echo "  PENGUJIAN MENYELURUH CRUD ROLE: PENGAWAS KENDARAAN    \n";
echo "  3 Menu Sidebar: Perbaikan Kendaraan (SPK),           \n";
echo "  Pembelian Sparepart, dan List Sparepart               \n";
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

// Simulasikan session login Pengawas Kendaraan
session([
    'kode_jabatan' => 'PENGAWAS_KENDARAAN',
    'id_jabatan' => 10,
    'username' => 'pengawas_kendaraan',
    'nama_lengkap' => 'Bambang Supriyanto (Pengawas Kendaraan)'
]);

// =========================================================================
// 1. PENGUJIAN CRUD: MASTER & LIST SPAREPART
// =========================================================================
echo "--- 1. Pengujian CRUD List Sparepart ---\n";
$partController = new SparepartController();
$kodePartUji = 'PRT-TEST-PK';

// Bersihkan sisa data uji jika ada
DB::table('pembelian_sparepart')->where('nomor_faktur_beli', 'FB-TEST-PK')->delete();
DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->delete();

// 1.1 Buat Kode Otomatis Sparepart
$reqBuatPart = Request::create('/operasional/bengkel/sparepart/api/buat-kode', 'GET', ['mode' => 'gap']);
$respBuatPart = $partController->buatKodeOtomatis($reqBuatPart);
$dataBuatPart = json_decode($respBuatPart->getContent(), true);
assertTest($dataBuatPart['status'] === 'sukses' && !empty($dataBuatPart['kode_otomatis']), "Generator Kode Otomatis Sparepart: {$dataBuatPart['kode_otomatis']}");

// 1.2 CREATE Master Sparepart (dengan sanitasi titik Rupiah)
$reqSimpanPart = Request::create('/operasional/bengkel/sparepart', 'POST', [
    'kode_sparepart' => $kodePartUji,
    'nama_sparepart' => 'Kampas Rem Belakang Hino Dutro HD',
    'kategori_part'  => 'Pengereman',
    'stok_part'      => 10,
    'satuan'         => 'Set',
    'harga_satuan'   => '750.000', // Format Rupiah bertitik
]);
$respSimpanPart = $partController->simpan($reqSimpanPart);
$partTersimpan = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->first();
assertTest(
    $partTersimpan && (float)$partTersimpan->harga_satuan == 750000 && $partTersimpan->stok_part == 10,
    "CREATE Sparepart Baru ({$kodePartUji}) dengan sanitasi Rupiah berhasil"
);

// 1.3 READ Detail Sparepart via JSON API
$respDetailPart = $partController->ambilDetail($kodePartUji);
$dataDetailPart = json_decode($respDetailPart->getContent(), true);
assertTest(
    $dataDetailPart['status'] === 'sukses' && $dataDetailPart['data']['kode_sparepart'] === $kodePartUji,
    "READ Detail Sparepart via API berhasil"
);

// 1.4 UPDATE Data Master Sparepart
$reqUpdatePart = Request::create("/operasional/bengkel/sparepart/{$kodePartUji}", 'PUT', [
    'nama_sparepart' => 'Kampas Rem Belakang Hino Dutro HD (Super Duty)',
    'kategori_part'  => 'Pengereman',
    'stok_part'      => 15,
    'satuan'         => 'Set',
    'harga_satuan'   => '850.000',
]);
$respUpdatePart = $partController->perbarui($reqUpdatePart, $kodePartUji);
$partUpdated = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->first();
assertTest(
    $partUpdated && $partUpdated->nama_sparepart === 'Kampas Rem Belakang Hino Dutro HD (Super Duty)' && (float)$partUpdated->harga_satuan == 850000,
    "UPDATE Data Master Sparepart berhasil"
);

// 1.5 Mutasi Stok Masuk (+10)
$reqMutasiMasuk = Request::create("/operasional/bengkel/sparepart/{$kodePartUji}/mutasi", 'POST', [
    'tipe_mutasi' => 'masuk',
    'jumlah'      => 10,
    'keterangan'  => 'Penerimaan restock gudang'
]);
$partController->mutasiStok($reqMutasiMasuk, $kodePartUji);
$stokMasuk = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->value('stok_part');
assertTest($stokMasuk == 25, "Mutasi Stok MASUK (+10 Unit -> {$stokMasuk}) berhasil");

// 1.6 Mutasi Stok Keluar (-5)
$reqMutasiKeluar = Request::create("/operasional/bengkel/sparepart/{$kodePartUji}/mutasi", 'POST', [
    'tipe_mutasi' => 'keluar',
    'jumlah'      => 5,
    'keterangan'  => 'Pengambilan teknisi bengkel'
]);
$partController->mutasiStok($reqMutasiKeluar, $kodePartUji);
$stokKeluar = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->value('stok_part');
assertTest($stokKeluar == 20, "Mutasi Stok KELUAR (-5 Unit -> {$stokKeluar}) berhasil");

// 1.7 Mutasi Stok Atur (50)
$reqMutasiAtur = Request::create("/operasional/bengkel/sparepart/{$kodePartUji}/mutasi", 'POST', [
    'tipe_mutasi' => 'atur',
    'jumlah'      => 50,
    'keterangan'  => 'Hasil audit stok fisik bengkel'
]);
$partController->mutasiStok($reqMutasiAtur, $kodePartUji);
$stokAtur = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->value('stok_part');
assertTest($stokAtur == 50, "Mutasi Stok ATUR LANGSUNG (Set ke 50 Unit) berhasil");

// 1.8 Mutasi Stok Atur ke 0 (Gudang Bengkel Kosong)
$reqMutasiNol = Request::create("/operasional/bengkel/sparepart/{$kodePartUji}/mutasi", 'POST', [
    'tipe_mutasi' => 'atur',
    'jumlah'      => 0,
    'keterangan'  => 'Stok habis total'
]);
$partController->mutasiStok($reqMutasiNol, $kodePartUji);
$stokNol = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->value('stok_part');
assertTest($stokNol == 0, "Mutasi Stok ATUR LANGSUNG ke 0 Unit (Stok Habis) berhasil");


// =========================================================================
// 2. PENGUJIAN CRUD: PEMBELIAN SPAREPART & SINKRONISASI STOK
// =========================================================================
echo "\n--- 2. Pengujian CRUD Pembelian Sparepart ---\n";
$beliController = new PembelianSparepartController();
$nomorFakturUji = 'FB-TEST-PK';

// 2.1 Buat Nomor Faktur Otomatis
$reqBuatFaktur = Request::create('/operasional/bengkel/pembelian-sparepart/api/buat-kode', 'GET', ['mode' => 'gap']);
$respBuatFaktur = $beliController->buatNomorFaktur($reqBuatFaktur);
$dataBuatFaktur = json_decode($respBuatFaktur->getContent(), true);
assertTest($dataBuatFaktur['status'] === 'sukses' && !empty($dataBuatFaktur['kode_otomatis']), "Generator Nomor Faktur Otomatis: {$dataBuatFaktur['kode_otomatis']}");

// 2.2 CREATE Transaksi Pembelian Sparepart (dengan sanitasi Rupiah & tanggal)
// Stok awal saat ini adalah 0, kita beli 12 unit -> stok harus menjadi 12
$reqSimpanBeli = Request::create('/operasional/bengkel/pembelian-sparepart', 'POST', [
    'nomor_faktur_beli' => $nomorFakturUji,
    'kode_sparepart'    => $kodePartUji,
    'tanggal_beli'      => date('Y-m-d'),
    'nama_supplier'     => 'PT Suku Cadang Utama Karawang',
    'jumlah_beli'       => 12,
    'harga_beli'        => '800.000', // Format Rupiah
    'dibuat_oleh'       => 'Bambang Supriyanto (Pengawas Kendaraan)',
]);
$respSimpanBeli = $beliController->simpan($reqSimpanBeli);
$beliTersimpan = DB::table('pembelian_sparepart')->where('nomor_faktur_beli', $nomorFakturUji)->first();
$stokSetelahBeli = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->value('stok_part');
assertTest(
    $beliTersimpan && (float)$beliTersimpan->total_bayar == (12 * 800000) && $stokSetelahBeli == 12,
    "CREATE Pembelian Sparepart ({$nomorFakturUji}) & Sinkronisasi Stok (+12 Unit) berhasil"
);

// 2.3 READ Detail Pembelian via API
$respDetailBeli = $beliController->ambilDetail($beliTersimpan->id_pembelian_part);
$dataDetailBeli = json_decode($respDetailBeli->getContent(), true);
assertTest(
    $dataDetailBeli['status'] === 'sukses' && $dataDetailBeli['data']['nomor_faktur_beli'] === $nomorFakturUji,
    "READ Detail Pembelian via API berhasil"
);

// 2.4 UPDATE Pembelian Sparepart (Tambah jumlah beli menjadi 15 unit -> selisih +3 -> stok jadi 15)
$reqUpdateBeli = Request::create("/operasional/bengkel/pembelian-sparepart/{$beliTersimpan->id_pembelian_part}", 'PUT', [
    'kode_sparepart' => $kodePartUji,
    'tanggal_beli'   => date('Y-m-d'),
    'nama_supplier'  => 'PT Suku Cadang Utama Karawang (Pusat)',
    'jumlah_beli'    => 15,
    'harga_beli'     => '800.000',
    'dibuat_oleh'    => 'Bambang Supriyanto (Pengawas Kendaraan)',
]);
$respUpdateBeli = $beliController->perbarui($reqUpdateBeli, $beliTersimpan->id_pembelian_part);
$stokSetelahUpdateBeli = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->value('stok_part');
assertTest(
    $stokSetelahUpdateBeli == 15,
    "UPDATE Pembelian & Penyesuaian Selisih Stok (Menjadi {$stokSetelahUpdateBeli} Unit) berhasil"
);

// 2.5 Proteksi Integritas: Coba hapus sparepart yang memiliki relasi pembelian (harus tertolak aman)
$respHapusPartGagal = $partController->hapus($kodePartUji);
$partMasihAda = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->exists();
$pesanErrorPart = session('error');
assertTest(
    $partMasihAda && !empty($pesanErrorPart),
    "Proteksi FK: Hapus Sparepart saat memiliki transaksi pembelian tertolak aman ('{$pesanErrorPart}')"
);

// 2.6 DELETE Faktur Pembelian (Stok 15 dikurangi 15 unit pembelian -> stok kembali ke 0)
$respHapusBeli = $beliController->hapus($beliTersimpan->id_pembelian_part);
$beliTerhapus = !DB::table('pembelian_sparepart')->where('nomor_faktur_beli', $nomorFakturUji)->exists();
$stokSetelahHapusBeli = DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->value('stok_part');
assertTest(
    $beliTerhapus && $stokSetelahHapusBeli == 0,
    "DELETE Faktur Pembelian & Pemulihan Stok Riil (-15 Unit -> {$stokSetelahHapusBeli}) berhasil"
);

// 2.7 DELETE Master Sparepart setelah relasi bersih
$respHapusPart = $partController->hapus($kodePartUji);
$partTerhapus = !DB::table('list_sparepart')->where('kode_sparepart', $kodePartUji)->exists();
assertTest($partTerhapus, "DELETE Master Sparepart ({$kodePartUji}) setelah relasi bersih berhasil");


// =========================================================================
// 3. PENGUJIAN CRUD: PERBAIKAN KENDARAAN (SPK SERVIS)
// =========================================================================
echo "\n--- 3. Pengujian CRUD Perbaikan Kendaraan (SPK) ---\n";
$spkController = new PerbaikanKendaraanController();
$nomorSpkUji = 'SPK-TEST-PK';

// Bersihkan data uji SPK sebelumnya jika ada
DB::table('perbaikan_kendaraan')->where('nomor_spk_perbaikan', $nomorSpkUji)->delete();

// Ambil kendaraan uji yang ada di database
$kodeKendaraanUji = DB::table('data_kendaraan')->value('kode_kendaraan') ?? 'KND-001';

// 3.1 Buat Nomor SPK Otomatis
$reqBuatSpk = Request::create('/operasional/bengkel/perbaikan/api/buat-kode', 'GET', ['mode' => 'gap']);
$respBuatSpk = $spkController->buatNomorSPK($reqBuatSpk);
$dataBuatSpk = json_decode($respBuatSpk->getContent(), true);
assertTest($dataBuatSpk['status'] === 'sukses' && !empty($dataBuatSpk['kode_otomatis']), "Generator Nomor SPK Otomatis: {$dataBuatSpk['kode_otomatis']}");

// 3.2 CREATE SPK Perbaikan Baru (dengan sanitasi Rupiah biaya jasa & part)
$reqSimpanSpk = Request::create('/operasional/bengkel/perbaikan', 'POST', [
    'nomor_spk_perbaikan' => $nomorSpkUji,
    'kode_kendaraan'      => $kodeKendaraanUji,
    'tanggal_masuk'       => date('Y-m-d'),
    'tanggal_selesai'     => null,
    'keluhan_kerusakan'   => 'Kampas rem aus dan minyak rem berkurang drastis pada jalur Cikarang.',
    'tindakan_perbaikan'  => 'Ganti kampas rem set dan kuras minyak rem DOT 4.',
    'biaya_jasa'          => '350.000', // Format Rupiah
    'biaya_sparepart'     => '850.000', // Format Rupiah
    'bengkel_pelaksana'   => 'Bengkel Internal PBJ Karawang',
    'status_perbaikan'    => 'Dalam Proses',
    'pengawas_kendaraan'  => 'Bambang Supriyanto (Pengawas Kendaraan)',
]);
$respSimpanSpk = $spkController->simpan($reqSimpanSpk);
$spkTersimpan = DB::table('perbaikan_kendaraan')->where('nomor_spk_perbaikan', $nomorSpkUji)->first();
assertTest(
    $spkTersimpan && (float)$spkTersimpan->total_biaya == 1200000 && $spkTersimpan->status_perbaikan === 'Dalam Proses',
    "CREATE SPK Perbaikan ({$nomorSpkUji}) dengan sanitasi Rupiah total Rp 1.200.000 berhasil"
);

// 3.3 READ Detail SPK Perbaikan via API (Verifikasi tanggal YYYY-MM-DD murni tanpa ISO time)
$respDetailSpk = $spkController->ambilDetail($spkTersimpan->id_perbaikan);
$dataDetailSpk = json_decode($respDetailSpk->getContent(), true);
$tglMasukJson = $dataDetailSpk['data']['tanggal_masuk'] ?? '';
assertTest(
    $dataDetailSpk['status'] === 'sukses' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglMasukJson),
    "READ Detail SPK via API dengan format tanggal presisi Y-m-d ({$tglMasukJson}) berhasil"
);

// 3.4 UPDATE Data SPK Perbaikan
$reqUpdateSpk = Request::create("/operasional/bengkel/perbaikan/{$spkTersimpan->id_perbaikan}", 'PUT', [
    'kode_kendaraan'     => $kodeKendaraanUji,
    'tanggal_masuk'      => date('Y-m-d'),
    'tanggal_selesai'    => date('Y-m-d'),
    'keluhan_kerusakan'  => 'Kampas rem aus dan minyak rem bocor halus.',
    'tindakan_perbaikan' => 'Ganti kampas rem set, ganti selang rem, dan kuras minyak rem.',
    'biaya_jasa'         => '400.000',
    'biaya_sparepart'    => '950.000',
    'bengkel_pelaksana'  => 'Bengkel Internal PBJ Karawang',
    'status_perbaikan'   => 'Dalam Proses',
    'pengawas_kendaraan' => 'Bambang Supriyanto (Pengawas Kendaraan)',
]);
$respUpdateSpk = $spkController->perbarui($reqUpdateSpk, $spkTersimpan->id_perbaikan);
$spkUpdated = DB::table('perbaikan_kendaraan')->where('nomor_spk_perbaikan', $nomorSpkUji)->first();
assertTest(
    $spkUpdated && (float)$spkUpdated->total_biaya == 1350000 && $spkUpdated->biaya_jasa == 400000,
    "UPDATE Data SPK Perbaikan berhasil"
);

// 3.5 UPDATE Status Cepat SPK (Dalam Proses -> Selesai)
$reqStatusSpk = Request::create("/operasional/bengkel/perbaikan/{$spkTersimpan->id_perbaikan}/status", 'PATCH', [
    'status_perbaikan' => 'Selesai'
]);
$respStatusSpk = $spkController->perbaruiStatus($reqStatusSpk, $spkTersimpan->id_perbaikan);
$spkSelesai = DB::table('perbaikan_kendaraan')->where('nomor_spk_perbaikan', $nomorSpkUji)->first();
assertTest(
    $spkSelesai && $spkSelesai->status_perbaikan === 'Selesai' && !empty($spkSelesai->tanggal_selesai),
    "UPDATE Status Cepat SPK ('Selesai' dengan tanggal_selesai terisi otomatis) berhasil"
);

// 3.6 DELETE SPK Perbaikan
$respHapusSpk = $spkController->hapus($spkTersimpan->id_perbaikan);
$spkTerhapus = !DB::table('perbaikan_kendaraan')->where('nomor_spk_perbaikan', $nomorSpkUji)->exists();
assertTest($spkTerhapus, "DELETE Data SPK Perbaikan ({$nomorSpkUji}) berhasil");

echo "\n========================================================\n";
echo "  HASIL AKHIR PENGUJIAN: {$sukses} Berhasil, {$gagal} Gagal\n";
echo "========================================================\n";

if ($gagal > 0) {
    exit(1);
}
exit(0);
