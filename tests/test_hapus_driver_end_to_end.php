<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Operasional\Driver;

echo "=== MEMULAI TEST HAPUS DRIVER END-TO-END ===" . PHP_EOL;

// 1. Bersihkan data uji jika ada
DB::table('data_karyawan')->where('kode_karyawan', 'DRV-UJI-001')->delete();

// Dapatkan id_jabatan untuk PENGAWAS_DRIVER
$idJabatan = DB::table('jabatan')->where('kode_jabatan', 'PENGAWAS_DRIVER')->value('id_jabatan') ?? 6;

// 2. Buat driver uji
$driverUji = Driver::create([
    'kode_karyawan' => 'DRV-UJI-001',
    'nama_karyawan' => 'Supir Uji Pengawas',
    'id_jabatan' => $idJabatan,
    'alamat' => 'Jl. Uji Supir No. 99',
    'no_hp' => '081234567890',
    'no_ktp' => '3201012345678901',
    'status_karyawan' => 'aktif',
    'tanggal_mulai_kerja' => '2025-01-01',
]);

echo "[OK] Driver uji berhasil dibuat: " . $driverUji->kode_karyawan . PHP_EOL;

// 3. Test Proteksi RBAC: SPV_OPERASIONAL tidak boleh hapus driver
$sessionStore = $app->make('session.store');
$sessionStore->setId('session-spv-' . uniqid());
$sessionStore->start();
$sessionStore->put('_token', 'token-uji-123');
$sessionStore->put('kode_jabatan', 'SPV_OPERASIONAL');

$requestSpv = Illuminate\Http\Request::create(
    '/operasional/armada/driver/DRV-UJI-001',
    'DELETE',
    ['_token' => 'token-uji-123', 'kode_karyawan' => 'DRV-UJI-001', 'kode_jabatan' => 'SPV_OPERASIONAL']
);
$requestSpv->setLaravelSession($sessionStore);

$responseSpv = $kernel->handle($requestSpv);
echo "Status response SPV: " . $responseSpv->getStatusCode() . PHP_EOL;
$errorMsg = $sessionStore->get('error');
echo "Pesan error SPV: " . $errorMsg . PHP_EOL;

if ($responseSpv->getStatusCode() === 302 && str_contains($errorMsg, 'Akses Ditolak')) {
    echo "[BERHASIL] SPV_OPERASIONAL berhasil diblokir dari aksi hapus driver!" . PHP_EOL;
} else {
    echo "[GAGAL] SPV_OPERASIONAL tidak diblokir dengan benar!" . PHP_EOL;
    exit(1);
}

// 4. Pastikan driver masih ada di database
$cekAda = Driver::where('kode_karyawan', 'DRV-UJI-001')->first();
if (!$cekAda) {
    echo "[GAGAL] Driver hilang sebelum dihapus oleh Pengawas!" . PHP_EOL;
    exit(1);
}

// 5. Test Hapus oleh PENGAWAS_DRIVER
$sessionPengawas = $app->make('session.store');
$sessionPengawas->setId('session-pengawas-' . uniqid());
$sessionPengawas->start();
$sessionPengawas->put('_token', 'token-uji-456');
$sessionPengawas->put('kode_jabatan', 'PENGAWAS_DRIVER');

$requestPengawas = Illuminate\Http\Request::create(
    '/operasional/armada/driver/DRV-UJI-001',
    'DELETE',
    ['_token' => 'token-uji-456', 'kode_karyawan' => 'DRV-UJI-001', 'kode_jabatan' => 'PENGAWAS_DRIVER']
);
$requestPengawas->setLaravelSession($sessionPengawas);

$responsePengawas = $kernel->handle($requestPengawas);
echo "Status response Pengawas: " . $responsePengawas->getStatusCode() . PHP_EOL;
$suksesMsg = $sessionPengawas->get('sukses');
echo "Pesan sukses Pengawas: " . $suksesMsg . PHP_EOL;

if ($responsePengawas->getStatusCode() === 302 && str_contains($suksesMsg, 'berhasil dihapus')) {
    echo "[BERHASIL] Role PENGAWAS_DRIVER sukses menghapus driver!" . PHP_EOL;
} else {
    echo "[GAGAL] Role PENGAWAS_DRIVER gagal menghapus driver!" . PHP_EOL;
    exit(1);
}

// 6. Verifikasi di database: data harus sudah terhapus
$cekTerhapus = Driver::where('kode_karyawan', 'DRV-UJI-001')->first();
if (!$cekTerhapus) {
    echo "[BERHASIL] Data driver DRV-UJI-001 terkonfirmasi terhapus dari tabel data_karyawan!" . PHP_EOL;
} else {
    echo "[GAGAL] Data driver masih ada di tabel data_karyawan!" . PHP_EOL;
    exit(1);
}

// 7. Test Role DISPATCHER juga tetap bisa hapus driver
$driverUji2 = Driver::create([
    'kode_karyawan' => 'DRV-UJI-002',
    'nama_karyawan' => 'Supir Uji Dispatcher',
    'id_jabatan' => $idJabatan,
    'alamat' => 'Jl. Uji Supir 2 No. 88',
    'no_hp' => '081234567891',
    'no_ktp' => '3201012345678902',
    'status_karyawan' => 'aktif',
    'tanggal_mulai_kerja' => '2025-01-01',
]);

$sessionDispatcher = $app->make('session.store');
$sessionDispatcher->setId('session-disp-' . uniqid());
$sessionDispatcher->start();
$sessionDispatcher->put('_token', 'token-uji-789');
$sessionDispatcher->put('kode_jabatan', 'DISPATCHER');

$requestDisp = Illuminate\Http\Request::create(
    '/operasional/armada/driver/DRV-UJI-002',
    'DELETE',
    ['_token' => 'token-uji-789', 'kode_karyawan' => 'DRV-UJI-002', 'kode_jabatan' => 'DISPATCHER']
);
$requestDisp->setLaravelSession($sessionDispatcher);

$responseDisp = $kernel->handle($requestDisp);
$suksesDisp = $sessionDispatcher->get('sukses');
if ($responseDisp->getStatusCode() === 302 && str_contains($suksesDisp, 'berhasil dihapus')) {
    echo "[BERHASIL] Role DISPATCHER juga sukses menghapus driver!" . PHP_EOL;
} else {
    echo "[GAGAL] Role DISPATCHER gagal menghapus driver!" . PHP_EOL;
    exit(1);
}

echo "=== SEMUA TEST HAPUS DRIVER END-TO-END BERHASIL 100% ===" . PHP_EOL;
