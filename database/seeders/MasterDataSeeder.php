<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Jalankan seeder master data: Wilayah, Customer, Semen/Barang, Gudang SO, dan Rekening Bank.
     */
    public function run(): void
    {
        // 1. Master Wilayah
        $daftarWilayah = [
            ['kode_wilayah' => 'WIL-JKT-01', 'nama_wilayah' => 'DKI Jakarta & Sekitarnya'],
            ['kode_wilayah' => 'WIL-BKS-02', 'nama_wilayah' => 'Bekasi & Cikarang'],
            ['kode_wilayah' => 'WIL-BGR-03', 'nama_wilayah' => 'Bogor & Depok'],
            ['kode_wilayah' => 'WIL-TGR-04', 'nama_wilayah' => 'Tangerang Raya & Banten'],
            ['kode_wilayah' => 'WIL-KRW-05', 'nama_wilayah' => 'Karawang & Purwakarta'],
            ['kode_wilayah' => 'WIL-BDG-06', 'nama_wilayah' => 'Bandung Raya'],
        ];

        foreach ($daftarWilayah as $wilayah) {
            DB::table('data_wilayah')->updateOrInsert(
                ['kode_wilayah' => $wilayah['kode_wilayah']],
                $wilayah
            );
        }

        // 2. Master Data Semen / Barang
        $daftarSemen = [
            [
                'kode_barang'        => 'SMN-TNS-40',
                'nama_barang'        => 'Semen Tonasa PCC 40 Kg',
                'jenis_barang'       => 'Zak',
                'satuan_barang'      => 'Zak',
                'harga_pokok'        => 58000.00,
                'harga_jual_standar' => 64500.00,
            ],
            [
                'kode_barang'        => 'SMN-TNS-50',
                'nama_barang'        => 'Semen Tonasa PCC 50 Kg',
                'jenis_barang'       => 'Zak',
                'satuan_barang'      => 'Zak',
                'harga_pokok'        => 71000.00,
                'harga_jual_standar' => 79000.00,
            ],
            [
                'kode_barang'        => 'SMN-GRS-40',
                'nama_barang'        => 'Semen Gresik PCC 40 Kg',
                'jenis_barang'       => 'Zak',
                'satuan_barang'      => 'Zak',
                'harga_pokok'        => 59500.00,
                'harga_jual_standar' => 66000.00,
            ],
            [
                'kode_barang'        => 'SMN-DNX-40',
                'nama_barang'        => 'Dynamix Serbaguna 40 Kg',
                'jenis_barang'       => 'Zak',
                'satuan_barang'      => 'Zak',
                'harga_pokok'        => 57500.00,
                'harga_jual_standar' => 63500.00,
            ],
            [
                'kode_barang'        => 'SMN-PDG-50',
                'nama_barang'        => 'Semen Padang Type I 50 Kg',
                'jenis_barang'       => 'Zak',
                'satuan_barang'      => 'Zak',
                'harga_pokok'        => 73000.00,
                'harga_jual_standar' => 81500.00,
            ],
            [
                'kode_barang'        => 'SMN-CRH-OPC',
                'nama_barang'        => 'Semen Curah OPC Type I (Tonase)',
                'jenis_barang'       => 'Curah',
                'satuan_barang'      => 'Ton',
                'harga_pokok'        => 1150000.00,
                'harga_jual_standar' => 1280000.00,
            ],
        ];

        foreach ($daftarSemen as $semen) {
            DB::table('data_semen')->updateOrInsert(
                ['kode_barang' => $semen['kode_barang']],
                $semen
            );
        }

        // 3. Master Customer (Entitas Pemilik Finansial)
        $daftarCustomer = [
            [
                'kode_customer'      => 'CUST-001',
                'kode_wilayah'       => 'WIL-BKS-02',
                'nama_toko_bangunan' => 'Grup TB Maju Jaya (H. Sulaeman)',
                'nama_pemilik'       => 'H. Sulaeman',
                'alamat'             => 'Jl. Raya Industri No. 45, Cikarang Selatan, Bekasi',
                'no_hp'              => '0812-3456-7890',
                'no_ktp'             => '3216061205780001',
                'foto_ktp'           => null,
                'plafon_piutang'     => 150000000.00,
                'saldo_piutang'      => 35000000.00,
                'saldo_deposit'      => 15000000.00,
            ],
            [
                'kode_customer'      => 'CUST-002',
                'kode_wilayah'       => 'WIL-JKT-01',
                'nama_toko_bangunan' => 'PT Sinar Abadi Makmur (Budi Hartanto)',
                'nama_pemilik'       => 'Budi Hartanto',
                'alamat'             => 'Jl. Daan Mogot Km 12 No. 88, Jakarta Barat',
                'no_hp'              => '0813-8877-6655',
                'no_ktp'             => '3173012509820004',
                'foto_ktp'           => null,
                'plafon_piutang'     => 250000000.00,
                'saldo_piutang'      => 62000000.00,
                'saldo_deposit'      => 5000000.00,
            ],
            [
                'kode_customer'      => 'CUST-003',
                'kode_wilayah'       => 'WIL-TGR-04',
                'nama_toko_bangunan' => 'TB Berkah Bersama (Hj. Siti Rohmah)',
                'nama_pemilik'       => 'Hj. Siti Rohmah',
                'alamat'             => 'Jl. Raya Serpong No. 102, Tangerang Selatan',
                'no_hp'              => '0857-1122-3344',
                'no_ktp'             => '3674034407850002',
                'foto_ktp'           => null,
                'plafon_piutang'     => 100000000.00,
                'saldo_piutang'      => 0.00,
                'saldo_deposit'      => 25000000.00,
            ],
            [
                'kode_customer'      => 'CUST-004',
                'kode_wilayah'       => 'WIL-BGR-03',
                'nama_toko_bangunan' => 'CV Usaha Bersama Konstruksi',
                'nama_pemilik'       => 'Deddy Kurniawan',
                'alamat'             => 'Jl. Raya Mayor Oking No. 15, Cibinong, Bogor',
                'no_hp'              => '0819-9988-7766',
                'no_ktp'             => '3201011508800003',
                'foto_ktp'           => null,
                'plafon_piutang'     => 120000000.00,
                'saldo_piutang'      => 18500000.00,
                'saldo_deposit'      => 0.00,
            ],
            [
                'kode_customer'      => 'CUST-005',
                'kode_wilayah'       => 'WIL-KRW-05',
                'nama_toko_bangunan' => 'TB Sumber Logam Karawang (Anton Wijaya)',
                'nama_pemilik'       => 'Anton Wijaya',
                'alamat'             => 'Jl. Jenderal Sudirman No. 70, Cikampek, Karawang',
                'no_hp'              => '0821-4455-6677',
                'no_ktp'             => '3215010903840005',
                'foto_ktp'           => null,
                'plafon_piutang'     => 180000000.00,
                'saldo_piutang'      => 45000000.00,
                'saldo_deposit'      => 10000000.00,
            ],
        ];

        foreach ($daftarCustomer as $customer) {
            DB::table('data_customer')->updateOrInsert(
                ['kode_customer' => $customer['kode_customer']],
                $customer
            );
        }

        // 3.1 Master Toko Bangunan & Proyek (Relasi 1 Customer : Many Toko/Proyek)
        $daftarToko = [
            [
                'kode_toko'          => 'TKB-001',
                'kode_customer'      => 'CUST-001',
                'kode_wilayah'       => 'WIL-BKS-02',
                'nama_toko_bangunan' => 'TB Maju Jaya Sentosa Cikarang',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Ahmad Fauzi (Kepala Toko)',
                'no_hp_toko'         => '0812-3456-7891',
                'alamat_lengkap'     => 'Jl. Raya Industri No. 45, Cikarang Selatan, Bekasi',
                'titik_koordinat'    => '-6.3245, 107.1654',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-002',
                'kode_customer'      => 'CUST-001',
                'kode_wilayah'       => 'WIL-BKS-02',
                'nama_toko_bangunan' => 'TB Maju Jaya 2 Cibitung',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Rian Pratama',
                'no_hp_toko'         => '0812-3456-7892',
                'alamat_lengkap'     => 'Jl. Fatahillah No. 18, Cibitung, Bekasi',
                'titik_koordinat'    => '-6.2654, 107.1021',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-003',
                'kode_customer'      => 'CUST-001',
                'kode_wilayah'       => 'WIL-BKS-02',
                'nama_toko_bangunan' => 'Proyek Ruko Grand Cikarang City',
                'tipe_lokasi'        => 'proyek_kontraktor',
                'penanggung_jawab'   => 'Ir. Hendra (Site Manager)',
                'no_hp_toko'         => '0812-3456-7899',
                'alamat_lengkap'     => 'Kavling Blok C-12, Grand Cikarang City, Karangraharja',
                'titik_koordinat'    => '-6.2541, 107.1789',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-004',
                'kode_customer'      => 'CUST-002',
                'kode_wilayah'       => 'WIL-JKT-01',
                'nama_toko_bangunan' => 'TB Sinar Abadi Daan Mogot',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Herman Susanto',
                'no_hp_toko'         => '0813-8877-6651',
                'alamat_lengkap'     => 'Jl. Daan Mogot Km 12 No. 88, Kalideres, Jakarta Barat',
                'titik_koordinat'    => '-6.1524, 106.7021',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-005',
                'kode_customer'      => 'CUST-002',
                'kode_wilayah'       => 'WIL-JKT-01',
                'nama_toko_bangunan' => 'TB Sinar Abadi 2 Cengkareng',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Yulianti (SPV Kasir)',
                'no_hp_toko'         => '0813-8877-6652',
                'alamat_lengkap'     => 'Jl. Kamal Raya No. 40, Cengkareng, Jakarta Barat',
                'titik_koordinat'    => '-6.1345, 106.7231',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-006',
                'kode_customer'      => 'CUST-002',
                'kode_wilayah'       => 'WIL-JKT-01',
                'nama_toko_bangunan' => 'Proyek Apartemen Aeropolis Commercial',
                'tipe_lokasi'        => 'proyek_kontraktor',
                'penanggung_jawab'   => 'Wawan Setiawan (Logistik Proyek)',
                'no_hp_toko'         => '0813-8877-6699',
                'alamat_lengkap'     => 'Jl. Marsekal Suryadarma No. 1, Tangerang / Perbatasan Jakarta',
                'titik_koordinat'    => '-6.1412, 106.6543',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-007',
                'kode_customer'      => 'CUST-003',
                'kode_wilayah'       => 'WIL-TGR-04',
                'nama_toko_bangunan' => 'TB Berkah Bangunan Serpong',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Fajar Nugraha',
                'no_hp_toko'         => '0857-1122-3341',
                'alamat_lengkap'     => 'Jl. Raya Serpong No. 102, Pondok Jagung, Tangerang Selatan',
                'titik_koordinat'    => '-6.2543, 106.6621',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-008',
                'kode_customer'      => 'CUST-003',
                'kode_wilayah'       => 'WIL-TGR-04',
                'nama_toko_bangunan' => 'TB Berkah Bangunan BSD City',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Lutfi Hakim',
                'no_hp_toko'         => '0857-1122-3342',
                'alamat_lengkap'     => 'Ruko Tol Boulevard Blok B-10, BSD City, Tangerang Selatan',
                'titik_koordinat'    => '-6.2912, 106.6789',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-009',
                'kode_customer'      => 'CUST-004',
                'kode_wilayah'       => 'WIL-BGR-03',
                'nama_toko_bangunan' => 'TB Usaha Bersama Mayor Oking',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Danang Tri',
                'no_hp_toko'         => '0819-9988-7761',
                'alamat_lengkap'     => 'Jl. Raya Mayor Oking No. 15, Cibinong, Bogor',
                'titik_koordinat'    => '-6.4821, 106.8521',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-010',
                'kode_customer'      => 'CUST-004',
                'kode_wilayah'       => 'WIL-BGR-03',
                'nama_toko_bangunan' => 'Proyek Klaster Sentul Hills',
                'tipe_lokasi'        => 'proyek_kontraktor',
                'penanggung_jawab'   => 'Bambang Sudiro',
                'no_hp_toko'         => '0819-9988-7799',
                'alamat_lengkap'     => 'Kawasan Sentul Nirwana Blok D-5, Babakan Madang, Bogor',
                'titik_koordinat'    => '-6.5512, 106.8834',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-011',
                'kode_customer'      => 'CUST-005',
                'kode_wilayah'       => 'WIL-KRW-05',
                'nama_toko_bangunan' => 'TB Sumber Logam Cikampek Utama',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Kurniawan Eko',
                'no_hp_toko'         => '0821-4455-6671',
                'alamat_lengkap'     => 'Jl. Jenderal Sudirman No. 70, Cikampek, Karawang',
                'titik_koordinat'    => '-6.4021, 107.4521',
                'status_toko'        => 'aktif',
            ],
            [
                'kode_toko'          => 'TKB-012',
                'kode_customer'      => 'CUST-005',
                'kode_wilayah'       => 'WIL-KRW-05',
                'nama_toko_bangunan' => 'TB Sumber Logam Karawang Barat',
                'tipe_lokasi'        => 'toko_retail',
                'penanggung_jawab'   => 'Surya Kencana',
                'no_hp_toko'         => '0821-4455-6672',
                'alamat_lengkap'     => 'Jl. Akses Tol Karawang Barat No. 22, Telukjambe, Karawang',
                'titik_koordinat'    => '-6.3123, 107.2845',
                'status_toko'        => 'aktif',
            ],
        ];

        foreach ($daftarToko as $toko) {
            DB::table('data_toko_bangunan')->updateOrInsert(
                ['kode_toko' => $toko['kode_toko']],
                $toko
            );
        }

        // 4. Master Rekening Bank Perusahaan
        $daftarRekening = [
            [
                'nomor_rekening' => '8800-1234-5678',
                'nama_bank'      => 'BCA (Bank Central Asia)',
                'atas_nama'      => 'PT Putra Balkom Jaya Utama',
                'saldo_rekening' => 450000000.00,
            ],
            [
                'nomor_rekening' => '1300-0987-6543-2',
                'nama_bank'      => 'Bank Mandiri',
                'atas_nama'      => 'PT Putra Balkom Jaya Operasional',
                'saldo_rekening' => 280000000.00,
            ],
            [
                'nomor_rekening' => '0012-01-000999-30-5',
                'nama_bank'      => 'BRI (Bank Rakyat Indonesia)',
                'atas_nama'      => 'PT Putra Balkom Jaya Penerimaan',
                'saldo_rekening' => 175000000.00,
            ],
        ];

        foreach ($daftarRekening as $rekening) {
            DB::table('data_rekening')->updateOrInsert(
                ['nomor_rekening' => $rekening['nomor_rekening']],
                $rekening
            );
        }

        // 5. Master List Gudang SO
        $daftarGudang = [
            [
                'kode_gudang'   => 'GDG-CKR-01',
                'nama_gudang'   => 'Gudang Penyangga Cikarang',
                'jenis_gudang'  => 'Gudang Distribusi',
                'kode_barang'   => 'SMN-TNS-40',
                'plant'         => 'Plant Narogong / Cikarang',
                'harga_barang'  => 58000.00,
                'stok_tersedia' => 4500,
                'distrik'       => 'Cikarang',
                'sub_distrik'   => 'Bekasi',
            ],
            [
                'kode_gudang'   => 'GDG-MRK-02',
                'nama_gudang'   => 'Gudang Transit Merak Banten',
                'jenis_gudang'  => 'Gudang Pelabuhan',
                'kode_barang'   => 'SMN-GRS-40',
                'plant'         => 'Plant Ciwandan Merak',
                'harga_barang'  => 59500.00,
                'stok_tersedia' => 3200,
                'distrik'       => 'Cilegon',
                'sub_distrik'   => 'Merak',
            ],
            [
                'kode_gudang'   => 'GDG-CKP-03',
                'nama_gudang'   => 'Gudang Hub Cikampek',
                'jenis_gudang'  => 'Gudang Distribusi',
                'kode_barang'   => 'SMN-DNX-40',
                'plant'         => 'Plant Karawang',
                'harga_barang'  => 57500.00,
                'stok_tersedia' => 2800,
                'distrik'       => 'Cikampek',
                'sub_distrik'   => 'Karawang',
            ],
        ];

        foreach ($daftarGudang as $gudang) {
            DB::table('list_gudang_so')->updateOrInsert(
                ['kode_gudang' => $gudang['kode_gudang']],
                $gudang
            );
        }
    }
}
