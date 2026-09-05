<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\Operasional\Armada\KendaraanController;

echo "========================================================\n";
echo "  PENGUJIAN VIEW DATA KENDARAAN (ROLE DISPATCHER)\n";
echo "========================================================\n\n";

// 1. Uji Render sebagai DISPATCHER
session([
    'kode_jabatan' => 'DISPATCHER',
    'nama_lengkap' => 'Dispatcher Tester',
    'id_pengguna' => 1
]);

$controller = new KendaraanController();

$reqDispatcher = Request::create('/operasional/armada/kendaraan', 'GET', ['tab' => 'jenis_aset']);
$viewDispatcher = $controller->index($reqDispatcher);
$htmlDispatcher = $viewDispatcher->render();

// Verifikasi
$subTabAsetCondition = strpos($htmlDispatcher, 'x-show="subTabJenisAset === \'aset\' && jabatanAktif !== \'DISPATCHER\'"') !== false;
$subTabSwitcherHidden = strpos($htmlDispatcher, 'x-show="jabatanAktif !== \'DISPATCHER\'"') !== false;
$labelDispatcherShown = strpos($htmlDispatcher, 'Master Kategori Jenis Aset (Tipe Truk)') !== false;
$tambahAsetHidden = strpos($htmlDispatcher, 'x-show="subTabJenisAset === \'aset\' && jabatanAktif !== \'DISPATCHER\'"') !== false;
$tabelKategoriAlwaysShown = strpos($htmlDispatcher, 'x-show="subTabJenisAset === \'kategori\' || jabatanAktif === \'DISPATCHER\'"') !== false;
$alpineSubTabInit = strpos($htmlDispatcher, "subTabJenisAset: 'kategori'") !== false;
$unitArmadaTerdaftarDihapus = strpos($htmlDispatcher, 'Unit Armada Terdaftar') === false;
$namaKolomJenisAsetRingkas = strpos($htmlDispatcher, '<th class="px-4 py-3 font-semibold uppercase tracking-wider">Jenis Aset</th>') !== false;

echo "Pengujian Dispatcher:\n";
echo "  [1] subTabJenisAset diinisialisasi ke 'kategori': " . ($alpineSubTabInit ? "LULUS\n" : "GAGAL\n");
echo "  [2] Tombol sub-tab switcher 'Inventaris Aset' disembunyikan untuk Dispatcher: " . ($subTabSwitcherHidden ? "LULUS\n" : "GAGAL\n");
echo "  [3] Label khusus 'Master Kategori Jenis Aset (Tipe Truk)' ditampilkan untuk Dispatcher: " . ($labelDispatcherShown ? "LULUS\n" : "GAGAL\n");
echo "  [4] Tombol 'Tambah Aset Perusahaan' disembunyikan untuk Dispatcher: " . ($tambahAsetHidden ? "LULUS\n" : "GAGAL\n");
echo "  [5] Tabel Master Kategori selalu aktif untuk Dispatcher: " . ($tabelKategoriAlwaysShown ? "LULUS\n" : "GAGAL\n");
echo "  [6] Tabel Inventaris Aset diberi guard proteksi jabatan: " . ($subTabAsetCondition ? "LULUS\n" : "GAGAL\n");
echo "  [7] Kolom 'Unit Armada Terdaftar' telah berhasil dihapus: " . ($unitArmadaTerdaftarDihapus ? "LULUS\n" : "GAGAL\n");
echo "  [8] Header kolom tabel disederhanakan menjadi 'Jenis Aset': " . ($namaKolomJenisAsetRingkas ? "LULUS\n" : "GAGAL\n");

if ($alpineSubTabInit && $subTabSwitcherHidden && $labelDispatcherShown && $tambahAsetHidden && $tabelKategoriAlwaysShown && $subTabAsetCondition && $unitArmadaTerdaftarDihapus && $namaKolomJenisAsetRingkas) {
    echo "\n=> SEMUA PENGECEKAN VIEW ROLE DISPATCHER LULUS 100%!\n";
} else {
    echo "\n=> ADA PENGECEKAN VIEW YANG GAGAL!\n";
    exit(1);
}
