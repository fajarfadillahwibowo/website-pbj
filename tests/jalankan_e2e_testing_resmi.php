<?php

/**
 * =============================================================================
 * PROTOKOL TESTING RESMI: END-TO-END CRUD & NAVIGASI SIDEBAR (4 ROLE UTAMA)
 * =============================================================================
 * Memenuhi Instruksi Tugas:
 * 1. Pengujian Routing & Permission Klik Sidebar untuk:
 *    - Dispatcher
 *    - SPV Gudang
 *    - SPV Operasional
 *    - Pengawas Kendaraan
 * 2. Pengujian Siklus Penuh CRUD Berurutan:
 *    - Create (Validasi & Tersimpan di DB)
 *    - Read   (Render & Detail Sesuai)
 *    - Update (Binding Data & Perubahan Tersimpan)
 *    - Delete (Data Hilang dari DB)
 * 3. Verifikasi Jumlah Record Minimal 3 di Setiap Modul CRUD Sidebar
 * =============================================================================
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

function kirimRequest($method, $uri, $parameters = [], $session = []) {
    global $app;

    $sessionStore = $app->make('session')->driver();
    $sessionStore->setId('e2e-session-' . uniqid());
    $sessionStore->start();
    foreach ($session as $key => $val) {
        $sessionStore->put($key, $val);
    }

    $cookies = [$sessionStore->getName() => $sessionStore->getId()];
    $server = [
        'HTTP_ACCEPT' => 'text/html,application/json,*/*',
        'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
    ];

    $token = $sessionStore->token();
    if ($method !== 'GET') {
        $parameters['_token'] = $token;
    }

    $request = Request::create($uri, $method, $parameters, $cookies, [], $server);
    $request->headers->set('X-CSRF-TOKEN', $token);
    $request->setLaravelSession($sessionStore);

    try {
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request);
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();
        $kernel->terminate($request, $response);

        $sessionStore = $request->session();
        $errorMsg = $sessionStore ? $sessionStore->get('error') : null;
        $suksesMsg = $sessionStore ? $sessionStore->get('sukses') : null;
        $errBag = $sessionStore ? $sessionStore->get('errors') : null;
        $errors = [];
        if ($errBag instanceof \Illuminate\Support\ViewErrorBag) {
            $errors = $errBag->all();
        } elseif (is_array($errBag)) {
            $errors = $errBag;
        }

        return [
            'status' => $statusCode,
            'content' => $content,
            'error' => $errorMsg,
            'sukses' => $suksesMsg,
            'errors' => $errors,
        ];
    } catch (\Throwable $e) {
        return [
            'status' => 500,
            'content' => $e->getMessage(),
            'error' => $e->getMessage(),
            'sukses' => null,
            'errors' => [$e->getMessage()],
        ];
    }
}

$lulusSemua = true;
function cekHasil($judul, $kondisi, $pesanGagal = '') {
    global $lulusSemua;
    if ($kondisi) {
        echo "   [BERHASIL] {$judul}\n";
    } else {
        echo "   [GAGAL]    {$judul} -> {$pesanGagal}\n";
        $lulusSemua = false;
    }
}

echo "========================================================================\n";
echo "           PROTOKOL PENGUJIAN END-TO-END CRUD & RBAC 4 ROLE             \n";
echo "========================================================================\n\n";

// -----------------------------------------------------------------------------
// BAGIAN 1: PENGUJIAN ROUTING & PERMISSION LINK SIDEBAR 4 ROLE
// -----------------------------------------------------------------------------
echo "BAGIAN 1: VERIFIKASI ROUTING & PERMISSION NAVIGASI SIDEBAR\n";
echo "------------------------------------------------------------------------\n";

$matriksMenu = [
    'DISPATCHER' => [
        'Dashboard'           => '/dashboard',
        'Data Kendaraan'      => '/operasional/armada/kendaraan',
        'Pengiriman (SJ)'     => '/operasional/pengiriman/surat-jalan',
        'Data Supir (Driver)' => '/operasional/armada/driver',
    ],
    'SPV_GUDANG' => [
        'Dashboard'           => '/dashboard',
        'Data Gudang (Stok)'  => '/operasional/gudang/stok',
        'Stok Opname Gudang'  => '/operasional/gudang/opname',
    ],
    'SPV_OPERASIONAL' => [
        'Dashboard'           => '/dashboard',
        'Data Ongkos Angkut'  => '/operasional/pengiriman/ongkos-angkut',
        'Stok Opname Gudang'  => '/operasional/gudang/opname',
        'Data Supir (Driver)' => '/operasional/armada/driver',
        'Data Kendaraan'      => '/operasional/armada/kendaraan',
        'Pengiriman (SJ)'     => '/operasional/pengiriman/surat-jalan',
        'Data KSO'            => '/operasional/kso',
    ],
    'PENGAWAS_KENDARAAN' => [
        'Dashboard'           => '/dashboard',
        'Data Kendaraan'      => '/operasional/armada/kendaraan',
        'Perbaikan SPK'       => '/operasional/bengkel/perbaikan',
        'Pembelian Sparepart' => '/operasional/bengkel/pembelian-sparepart',
        'List Sparepart'      => '/operasional/bengkel/sparepart',
    ],
];

foreach ($matriksMenu as $role => $daftarMenu) {
    echo "\n[Role: {$role}]\n";
    foreach ($daftarMenu as $namaMenu => $url) {
        $res = kirimRequest('GET', $url, [], ['kode_jabatan' => $role]);
        cekHasil("Menu '{$namaMenu}' ({$url})", $res['status'] === 200, "HTTP Status: {$res['status']}");
    }
}

// -----------------------------------------------------------------------------
// BAGIAN 2: PROTOKOL PENGUJIAN END-TO-END CRUD BERURUTAN (C -> R -> U -> D)
// -----------------------------------------------------------------------------
echo "\n\nBAGIAN 2: PROTOKOL PENGUJIAN END-TO-END CRUD BERURUTAN\n";
echo "------------------------------------------------------------------------\n";

// --- 1. CRUD DRIVER (Role: DISPATCHER) ---
echo "\n1. Siklus CRUD Driver (Role: DISPATCHER)\n";
$kodeDrv = 'TST-DRV-' . rand(1000, 9999);
$resC = kirimRequest('POST', '/operasional/armada/driver', [
    'kode_karyawan' => $kodeDrv,
    'nama_karyawan' => 'Supir E2E Test',
    'id_jabatan' => 6,
    'no_ktp' => '3201234567890123',
    'no_hp' => '081234567899',
    'alamat' => 'Jl. Pengujian E2E No. 10',
    'status_karyawan' => 'aktif',
    'tanggal_mulai_kerja' => date('Y-m-d'),
], ['kode_jabatan' => 'DISPATCHER']);
$adaDiDb = DB::table('data_karyawan')->where('kode_karyawan', $kodeDrv)->exists();
cekHasil("CREATE: Tambah driver {$kodeDrv} ke database", $resC['status'] === 302 && $adaDiDb);

$resR = kirimRequest('GET', "/operasional/armada/driver/{$kodeDrv}", [], ['kode_jabatan' => 'DISPATCHER']);
$dataJson = json_decode($resR['content'], true);
cekHasil("READ: Ambil data detail driver {$kodeDrv}", $resR['status'] === 200 && ($dataJson['status'] ?? '') === 'sukses');

$resU = kirimRequest('PUT', "/operasional/armada/driver/{$kodeDrv}", [
    'nama_karyawan' => 'Supir E2E Test Diperbarui',
    'id_jabatan' => 6,
    'no_ktp' => '3201234567890123',
    'no_hp' => '081234567888',
    'alamat' => 'Jl. Pengujian E2E No. 10 Revisi',
    'status_karyawan' => 'aktif',
    'tanggal_mulai_kerja' => date('Y-m-d'),
], ['kode_jabatan' => 'DISPATCHER']);
$namaUpdate = DB::table('data_karyawan')->where('kode_karyawan', $kodeDrv)->value('nama_karyawan');
cekHasil("UPDATE: Ubah nama driver menjadi '{$namaUpdate}'", $resU['status'] === 302 && $namaUpdate === 'Supir E2E Test Diperbarui');

$resD = kirimRequest('DELETE', "/operasional/armada/driver/{$kodeDrv}", [], ['kode_jabatan' => 'DISPATCHER']);
$masihAda = DB::table('data_karyawan')->where('kode_karyawan', $kodeDrv)->exists();
cekHasil("DELETE: Hapus driver {$kodeDrv} dari database", $resD['status'] === 302 && !$masihAda);


// --- 2. CRUD DATA GUDANG (Role: SPV_GUDANG) ---
echo "\n2. Siklus CRUD Data Gudang (Role: SPV_GUDANG)\n";
$kodeGdg = 'TST-GDG-' . rand(1000, 9999);
$firstSemen = DB::table('data_semen')->value('kode_barang') ?? 'SMN-PCC50';
$resC = kirimRequest('POST', '/operasional/gudang/stok', [
    'kode_gudang' => $kodeGdg,
    'nama_gudang' => 'Gudang Uji E2E',
    'jenis_gudang' => 'Utama',
    'kode_barang' => $firstSemen,
    'plant' => 'Plant E2E Testing',
    'harga_barang' => 60000,
    'stok_tersedia' => 1000,
    'distrik' => 'Bekasi',
    'sub_distrik' => 'Cikarang',
], ['kode_jabatan' => 'SPV_GUDANG']);
$adaDiDb = DB::table('list_gudang_so')->where('kode_gudang', $kodeGdg)->exists();
cekHasil("CREATE: Tambah gudang {$kodeGdg} ke database", $resC['status'] === 302 && $adaDiDb);

$resR = kirimRequest('GET', "/operasional/gudang/stok/{$kodeGdg}", [], ['kode_jabatan' => 'SPV_GUDANG']);
$dataJson = json_decode($resR['content'], true);
cekHasil("READ: Ambil data detail gudang {$kodeGdg}", $resR['status'] === 200 && ($dataJson['status'] ?? '') === 'sukses');

$resU = kirimRequest('PUT', "/operasional/gudang/stok/{$kodeGdg}", [
    'nama_gudang' => 'Gudang Uji E2E Diperbarui',
    'jenis_gudang' => 'Utama',
    'kode_barang' => $firstSemen,
    'plant' => 'Plant E2E Testing',
    'harga_barang' => 62000,
    'stok_tersedia' => 1200,
    'distrik' => 'Bekasi',
    'sub_distrik' => 'Cikarang Selatan',
], ['kode_jabatan' => 'SPV_GUDANG']);
$namaUpdate = DB::table('list_gudang_so')->where('kode_gudang', $kodeGdg)->value('nama_gudang');
cekHasil("UPDATE: Ubah nama gudang menjadi '{$namaUpdate}'", $resU['status'] === 302 && $namaUpdate === 'Gudang Uji E2E Diperbarui');

$resD = kirimRequest('DELETE', "/operasional/gudang/stok/{$kodeGdg}", [], ['kode_jabatan' => 'SPV_GUDANG']);
$masihAda = DB::table('list_gudang_so')->where('kode_gudang', $kodeGdg)->exists();
cekHasil("DELETE: Hapus gudang {$kodeGdg} dari database", $resD['status'] === 302 && !$masihAda);


// --- 3. CRUD ONGKOS ANGKUT (Role: SPV_OPERASIONAL) ---
echo "\n3. Siklus CRUD Ongkos Angkut (Role: SPV_OPERASIONAL)\n";
$kodeOA = 'TST-OA-' . rand(1000, 9999);
$resC = kirimRequest('POST', '/operasional/pengiriman/ongkos-angkut', [
    'kode_oa' => $kodeOA,
    'nama_oa' => 'Rute E2E Cikarang - Cibarusah',
    'muatan_oa' => 'Semen Zak 50kg',
    'harga_oa' => 4500,
    'harga_kso' => 4200,
    'harga_kso_khusus' => 4000,
    'wilayah_oa' => 'Jawa Barat',
    'keterangan' => 'Rute Uji E2E',
], ['kode_jabatan' => 'SPV_OPERASIONAL']);
$adaDiDb = DB::table('data_ongkos_angkut')->where('kode_oa', $kodeOA)->exists();
cekHasil("CREATE: Tambah rute OA {$kodeOA} ke database", $resC['status'] === 302 && $adaDiDb);

$resR = kirimRequest('GET', "/operasional/pengiriman/ongkos-angkut/{$kodeOA}", [], ['kode_jabatan' => 'SPV_OPERASIONAL']);
$dataJson = json_decode($resR['content'], true);
cekHasil("READ: Ambil data detail rute OA {$kodeOA}", $resR['status'] === 200 && ($dataJson['status'] ?? '') === 'sukses');

$resU = kirimRequest('PUT', "/operasional/pengiriman/ongkos-angkut/{$kodeOA}", [
    'nama_oa' => 'Rute E2E Cikarang - Cibarusah Diperbarui',
    'muatan_oa' => 'Semen Zak 50kg',
    'harga_oa' => 4900,
    'harga_kso' => 4500,
    'harga_kso_khusus' => 4300,
    'wilayah_oa' => 'Jawa Barat',
    'keterangan' => 'Rute Uji E2E Diperbarui',
], ['kode_jabatan' => 'SPV_OPERASIONAL']);
$namaUpdate = DB::table('data_ongkos_angkut')->where('kode_oa', $kodeOA)->value('nama_oa');
cekHasil("UPDATE: Ubah nama rute OA menjadi '{$namaUpdate}'", $resU['status'] === 302 && $namaUpdate === 'Rute E2E Cikarang - Cibarusah Diperbarui');

$resD = kirimRequest('DELETE', "/operasional/pengiriman/ongkos-angkut/{$kodeOA}", [], ['kode_jabatan' => 'SPV_OPERASIONAL']);
$masihAda = DB::table('data_ongkos_angkut')->where('kode_oa', $kodeOA)->exists();
cekHasil("DELETE: Hapus rute OA {$kodeOA} dari database", $resD['status'] === 302 && !$masihAda);


// --- 4. CRUD SPAREPART BENGKEL (Role: PENGAWAS_KENDARAAN) ---
echo "\n4. Siklus CRUD Sparepart Bengkel (Role: PENGAWAS_KENDARAAN)\n";
$kodePart = 'TST-PRT-' . rand(1000, 9999);
$resC = kirimRequest('POST', '/operasional/bengkel/sparepart', [
    'kode_sparepart' => $kodePart,
    'nama_sparepart' => 'Bearing Roda Depan Hino E2E',
    'kategori_part' => 'Bearing',
    'stok_part' => 10,
    'satuan' => 'Pcs',
    'harga_satuan' => 175000,
], ['kode_jabatan' => 'PENGAWAS_KENDARAAN']);
$adaDiDb = DB::table('list_sparepart')->where('kode_sparepart', $kodePart)->exists();
cekHasil("CREATE: Tambah sparepart {$kodePart} ke database", $resC['status'] === 302 && $adaDiDb);

$resR = kirimRequest('GET', "/operasional/bengkel/sparepart/{$kodePart}", [], ['kode_jabatan' => 'PENGAWAS_KENDARAAN']);
$dataJson = json_decode($resR['content'], true);
cekHasil("READ: Ambil data detail sparepart {$kodePart}", $resR['status'] === 200 && ($dataJson['status'] ?? '') === 'sukses');

$resU = kirimRequest('PUT', "/operasional/bengkel/sparepart/{$kodePart}", [
    'nama_sparepart' => 'Bearing Roda Depan Hino E2E Diperbarui',
    'kategori_part' => 'Bearing',
    'stok_part' => 15,
    'satuan' => 'Pcs',
    'harga_satuan' => 180000,
], ['kode_jabatan' => 'PENGAWAS_KENDARAAN']);
$namaUpdate = DB::table('list_sparepart')->where('kode_sparepart', $kodePart)->value('nama_sparepart');
cekHasil("UPDATE: Ubah nama sparepart menjadi '{$namaUpdate}'", $resU['status'] === 302 && $namaUpdate === 'Bearing Roda Depan Hino E2E Diperbarui');

$resD = kirimRequest('DELETE', "/operasional/bengkel/sparepart/{$kodePart}", [], ['kode_jabatan' => 'PENGAWAS_KENDARAAN']);
$masihAda = DB::table('list_sparepart')->where('kode_sparepart', $kodePart)->exists();
cekHasil("DELETE: Hapus sparepart {$kodePart} dari database", $resD['status'] === 302 && !$masihAda);


// -----------------------------------------------------------------------------
// BAGIAN 3: VERIFIKASI DATA BARU LENGKAP MINIMAL 3 RECORD PER MODUL
// -----------------------------------------------------------------------------
echo "\n\nBAGIAN 3: VERIFIKASI MINIMAL 3 DATA LENGKAP DI SEMUA CRUD SIDEBAR\n";
echo "------------------------------------------------------------------------\n";

$daftarTabelVerifikasi = [
    'data_kendaraan'      => ['label' => 'Data Kendaraan (Armada Truk)', 'role' => 'Dispatcher & Pengawas Kendaraan'],
    'pengiriman'          => ['label' => 'Pengiriman & Surat Jalan (SJ)', 'role' => 'Dispatcher & SPV Operasional'],
    'data_karyawan_drv'   => ['label' => 'Data Supir (Driver)', 'role' => 'Dispatcher & SPV Operasional (RO)'],
    'list_gudang_so'      => ['label' => 'Data Gudang & Stok Semen', 'role' => 'SPV Gudang'],
    'opname_gudang'       => ['label' => 'Stok Opname Fisik Gudang', 'role' => 'SPV Gudang & SPV Operasional'],
    'data_ongkos_angkut'  => ['label' => 'Data Master Ongkos Angkut (OA)', 'role' => 'SPV Operasional'],
    'data_kso'            => ['label' => 'Data Kerjasama Operasional (KSO)', 'role' => 'SPV Operasional'],
    'list_sparepart'      => ['label' => 'Data Master & Stok Sparepart', 'role' => 'Pengawas Kendaraan'],
    'pembelian_sparepart' => ['label' => 'Data Pembelian Sparepart', 'role' => 'Pengawas Kendaraan'],
    'perbaikan_kendaraan' => ['label' => 'Surat Perintah Kerja (SPK Servis)', 'role' => 'Pengawas Kendaraan'],
];

foreach ($daftarTabelVerifikasi as $tabelKey => $info) {
    if ($tabelKey === 'data_karyawan_drv') {
        $jumlah = DB::table('data_karyawan')->where('kategori_karyawan', 'driver')->count();
    } else {
        $jumlah = DB::table($tabelKey)->count();
    }

    cekHasil(
        "Modul: {$info['label']} [Role: {$info['role']}] -> {$jumlah} Record",
        $jumlah >= 3,
        "Jumlah saat ini {$jumlah} (kurang dari 3)"
    );
}

echo "\n========================================================================\n";
if ($lulusSemua) {
    echo "  STATUS: 100% SUKSES — SEMUA PENGUJIAN LULUS TANPA ERROR!\n";
} else {
    echo "  STATUS: TERDAPAT PENGUJIAN YANG GAGAL!\n";
}
echo "========================================================================\n";
