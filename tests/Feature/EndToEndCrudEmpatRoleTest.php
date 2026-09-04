<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Autentikasi\Pengguna;

class EndToEndCrudEmpatRoleTest extends TestCase
{
    /**
     * 1. Uji Navigasi Menu Sidebar dan Hak Akses untuk 4 Role.
     */
    public function test_navigasi_menu_sidebar_dan_izin_empat_role(): void
    {
        $matriksRole = [
            'DISPATCHER' => [
                '/dashboard',
                '/operasional/armada/kendaraan',
                '/operasional/pengiriman/surat-jalan',
                '/operasional/armada/driver',
            ],
            'SPV_GUDANG' => [
                '/dashboard',
                '/operasional/gudang/stok',
                '/operasional/gudang/opname',
            ],
            'SPV_OPERASIONAL' => [
                '/dashboard',
                '/operasional/pengiriman/ongkos-angkut',
                '/operasional/gudang/opname',
                '/operasional/armada/driver',
                '/operasional/armada/kendaraan',
                '/operasional/pengiriman/surat-jalan',
                '/operasional/kso',
            ],
            'PENGAWAS_KENDARAAN' => [
                '/dashboard',
                '/operasional/armada/kendaraan',
                '/operasional/bengkel/perbaikan',
                '/operasional/bengkel/pembelian-sparepart',
                '/operasional/bengkel/sparepart',
            ],
        ];

        foreach ($matriksRole as $kodeRole => $daftarRute) {
            foreach ($daftarRute as $url) {
                $response = $this->withSession(['kode_jabatan' => $kodeRole])
                                 ->get($url);
                $response->assertStatus(200);
            }
        }
    }

    /**
     * 2. Uji End-to-End Siklus Penuh CRUD (Create -> Read -> Update -> Delete) Driver.
     */
    public function test_siklus_crud_driver(): void
    {
        $session = ['kode_jabatan' => 'DISPATCHER'];

        // C - Create
        $kodeKaryawan = 'E2E-DRV-' . rand(100, 999);
        $resCreate = $this->withSession($session)->post('/operasional/armada/driver', [
            'kode_karyawan' => $kodeKaryawan,
            'nama_karyawan' => 'Driver E2E Test',
            'id_jabatan' => 6,
            'no_ktp' => '3201234567890999',
            'no_hp' => '081299998888',
            'alamat' => 'Jl. E2E No. 99',
            'status_karyawan' => 'aktif',
            'tanggal_mulai_kerja' => date('Y-m-d'),
        ]);
        $resCreate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('data_karyawan', ['kode_karyawan' => $kodeKaryawan]);

        // R - Read
        $resRead = $this->withSession($session)->get("/operasional/armada/driver/{$kodeKaryawan}");
        $resRead->assertStatus(200)->assertJson(['status' => 'sukses']);

        // U - Update
        $resUpdate = $this->withSession($session)->put("/operasional/armada/driver/{$kodeKaryawan}", [
            'nama_karyawan' => 'Driver E2E Test Diperbarui',
            'id_jabatan' => 6,
            'no_ktp' => '3201234567890999',
            'no_hp' => '081299997777',
            'alamat' => 'Jl. E2E No. 99 Edit',
            'status_karyawan' => 'aktif',
            'tanggal_mulai_kerja' => date('Y-m-d'),
        ]);
        $resUpdate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('data_karyawan', [
            'kode_karyawan' => $kodeKaryawan,
            'nama_karyawan' => 'Driver E2E Test Diperbarui',
        ]);

        // D - Delete
        $resDelete = $this->withSession($session)->delete("/operasional/armada/driver/{$kodeKaryawan}");
        $resDelete->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('data_karyawan', ['kode_karyawan' => $kodeKaryawan]);
    }

    /**
     * 3. Uji End-to-End Siklus Penuh CRUD (Create -> Read -> Update -> Delete) Gudang & Stok.
     */
    public function test_siklus_crud_gudang_stok(): void
    {
        $session = ['kode_jabatan' => 'SPV_GUDANG'];
        $firstBarang = DB::table('data_semen')->value('kode_barang') ?? 'SMN-PCC50';
        $kodeGudang = 'E2E-GDG-' . rand(100, 999);

        // C - Create
        $resCreate = $this->withSession($session)->post('/operasional/gudang/stok', [
            'kode_gudang' => $kodeGudang,
            'nama_gudang' => 'Gudang E2E Uji Sistem',
            'jenis_gudang' => 'Utama',
            'kode_barang' => $firstBarang,
            'plant' => 'Plant E2E Testing',
            'harga_barang' => 60000,
            'stok_tersedia' => 1000,
            'distrik' => 'Bekasi',
            'sub_distrik' => 'Cikarang',
        ]);
        $resCreate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('list_gudang_so', ['kode_gudang' => $kodeGudang]);

        // R - Read
        $resRead = $this->withSession($session)->get("/operasional/gudang/stok/{$kodeGudang}");
        $resRead->assertStatus(200)->assertJson(['status' => 'sukses']);

        // U - Update
        $resUpdate = $this->withSession($session)->put("/operasional/gudang/stok/{$kodeGudang}", [
            'nama_gudang' => 'Gudang E2E Uji Sistem Edit',
            'jenis_gudang' => 'Utama',
            'kode_barang' => $firstBarang,
            'plant' => 'Plant E2E Testing Edit',
            'harga_barang' => 61000,
            'stok_tersedia' => 1200,
            'distrik' => 'Bekasi',
            'sub_distrik' => 'Cikarang Selatan',
        ]);
        $resUpdate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('list_gudang_so', [
            'kode_gudang' => $kodeGudang,
            'nama_gudang' => 'Gudang E2E Uji Sistem Edit',
        ]);

        // D - Delete
        $resDelete = $this->withSession($session)->delete("/operasional/gudang/stok/{$kodeGudang}");
        $resDelete->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('list_gudang_so', ['kode_gudang' => $kodeGudang]);
    }

    /**
     * 4. Uji End-to-End Siklus Penuh CRUD (Create -> Read -> Update -> Delete) Ongkos Angkut.
     */
    public function test_siklus_crud_ongkos_angkut(): void
    {
        $session = ['kode_jabatan' => 'SPV_OPERASIONAL'];
        $kodeOA = 'E2E-OA-' . rand(100, 999);

        // C - Create
        $resCreate = $this->withSession($session)->post('/operasional/pengiriman/ongkos-angkut', [
            'kode_oa' => $kodeOA,
            'nama_oa' => 'Trayek E2E Cikarang - Tambun',
            'muatan_oa' => 'Semen Zak 50kg',
            'harga_oa' => 4500,
            'harga_kso' => 4200,
            'harga_kso_khusus' => 4000,
            'wilayah_oa' => 'Jawa Barat',
            'keterangan' => 'Rute E2E Test',
        ]);
        $resCreate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('data_ongkos_angkut', ['kode_oa' => $kodeOA]);

        // R - Read
        $resRead = $this->withSession($session)->get("/operasional/pengiriman/ongkos-angkut/{$kodeOA}");
        $resRead->assertStatus(200)->assertJson(['status' => 'sukses']);

        // U - Update
        $resUpdate = $this->withSession($session)->put("/operasional/pengiriman/ongkos-angkut/{$kodeOA}", [
            'nama_oa' => 'Trayek E2E Cikarang - Tambun Diperbarui',
            'muatan_oa' => 'Semen Zak 50kg',
            'harga_oa' => 4800,
            'harga_kso' => 4400,
            'harga_kso_khusus' => 4200,
            'wilayah_oa' => 'Jawa Barat',
            'keterangan' => 'Rute E2E Test Edit',
        ]);
        $resUpdate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('data_ongkos_angkut', [
            'kode_oa' => $kodeOA,
            'nama_oa' => 'Trayek E2E Cikarang - Tambun Diperbarui',
        ]);

        // D - Delete
        $resDelete = $this->withSession($session)->delete("/operasional/pengiriman/ongkos-angkut/{$kodeOA}");
        $resDelete->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('data_ongkos_angkut', ['kode_oa' => $kodeOA]);
    }

    /**
     * 5. Uji End-to-End Siklus Penuh CRUD (Create -> Read -> Update -> Delete) Sparepart Bengkel.
     */
    public function test_siklus_crud_sparepart_bengkel(): void
    {
        $session = ['kode_jabatan' => 'PENGAWAS_KENDARAAN'];
        $kodePart = 'E2E-PRT-' . rand(100, 999);

        // C - Create
        $resCreate = $this->withSession($session)->post('/operasional/bengkel/sparepart', [
            'kode_sparepart' => $kodePart,
            'nama_sparepart' => 'Kampas Kopling Hino 500 E2E',
            'kategori_part' => 'Kopling',
            'stok_part' => 15,
            'satuan' => 'Set',
            'harga_satuan' => 850000,
        ]);
        $resCreate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('list_sparepart', ['kode_sparepart' => $kodePart]);

        // R - Read
        $resRead = $this->withSession($session)->get("/operasional/bengkel/sparepart/{$kodePart}");
        $resRead->assertStatus(200)->assertJson(['status' => 'sukses']);

        // U - Update
        $resUpdate = $this->withSession($session)->put("/operasional/bengkel/sparepart/{$kodePart}", [
            'nama_sparepart' => 'Kampas Kopling Hino 500 E2E Edit',
            'kategori_part' => 'Kopling',
            'stok_part' => 20,
            'satuan' => 'Set',
            'harga_satuan' => 875000,
        ]);
        $resUpdate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('list_sparepart', [
            'kode_sparepart' => $kodePart,
            'nama_sparepart' => 'Kampas Kopling Hino 500 E2E Edit',
        ]);

        // D - Delete
        $resDelete = $this->withSession($session)->delete("/operasional/bengkel/sparepart/{$kodePart}");
        $resDelete->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('list_sparepart', ['kode_sparepart' => $kodePart]);
    }

    /**
     * 6. Verifikasi Ketersediaan Minimal 3 Data Lengkap di Seluruh Tabel Sidebar 4 Role.
     */
    public function test_ketersediaan_minimal_tiga_data_lengkap_seluruh_sidebar(): void
    {
        $syaratMinimalTiga = [
            'list_gudang_so' => 'Data Gudang (SPV Gudang)',
            'opname_gudang' => 'Stok Opname Gudang (SPV Gudang & SPV Ops)',
            'data_kendaraan' => 'Data Kendaraan (Dispatcher & Pengawas Kendaraan)',
            'pengiriman' => 'Pengiriman Surat Jalan (Dispatcher & SPV Ops)',
            'data_ongkos_angkut' => 'Data Ongkos Angkut (SPV Operasional)',
            'data_kso' => 'Data KSO Mitra (SPV Operasional)',
            'list_sparepart' => 'List Sparepart (Pengawas Kendaraan)',
            'pembelian_sparepart' => 'Pembelian Sparepart (Pengawas Kendaraan)',
            'perbaikan_kendaraan' => 'Perbaikan Kendaraan SPK (Pengawas Kendaraan)',
        ];

        foreach ($syaratMinimalTiga as $tabel => $namaModul) {
            $jumlah = DB::table($tabel)->count();
            $this->assertGreaterThanOrEqual(3, $jumlah, "Tabel {$tabel} ({$namaModul}) harus memiliki minimal 3 data lengkap, saat ini ada {$jumlah}.");
        }

        // Khusus driver
        $jumlahDriver = DB::table('data_karyawan')->where('kategori_karyawan', 'driver')->count();
        $this->assertGreaterThanOrEqual(3, $jumlahDriver, "Data Driver harus memiliki minimal 3 pengemudi aktif.");
    }
}
