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

        // 3. Master Customer / Toko Bangunan
        $daftarCustomer = [
            [
                'kode_customer'      => 'CUST-001',
                'kode_wilayah'       => 'WIL-BKS-02',
                'nama_toko_bangunan' => 'TB Maju Jaya Sentosa',
                'nama_pemilik'       => 'H. Sulaeman',
                'alamat'             => 'Jl. Raya Industri No. 45, Cikarang Selatan, Bekasi',
                'no_hp'              => '0812-3456-7890',
                'no_ktp'             => '3216061205780001',
                'foto_ktp'           => null,
                'plafon_piutang'     => 100000000.00,
                'saldo_piutang'      => 35000000.00,
                'saldo_deposit'      => 15000000.00,
            ],
            [
                'kode_customer'      => 'CUST-002',
                'kode_wilayah'       => 'WIL-JKT-01',
                'nama_toko_bangunan' => 'TB Sinar Abadi Makmur',
                'nama_pemilik'       => 'Budi Hartanto',
                'alamat'             => 'Jl. Daan Mogot Km 12 No. 88, Jakarta Barat',
                'no_hp'              => '0813-8877-6655',
                'no_ktp'             => '3173012509820004',
                'foto_ktp'           => null,
                'plafon_piutang'     => 150000000.00,
                'saldo_piutang'      => 62000000.00,
                'saldo_deposit'      => 5000000.00,
            ],
            [
                'kode_customer'      => 'CUST-003',
                'kode_wilayah'       => 'WIL-TGR-04',
                'nama_toko_bangunan' => 'TB Berkah Bangunan',
                'nama_pemilik'       => 'Hj. Siti Rohmah',
                'alamat'             => 'Jl. Raya Serpong No. 102, Tangerang Selatan',
                'no_hp'              => '0857-1122-3344',
                'no_ktp'             => '3674034407850002',
                'foto_ktp'           => null,
                'plafon_piutang'     => 75000000.00,
                'saldo_piutang'      => 0.00,
                'saldo_deposit'      => 25000000.00,
            ],
            [
                'kode_customer'      => 'CUST-004',
                'kode_wilayah'       => 'WIL-BGR-03',
                'nama_toko_bangunan' => 'TB Usaha Bersama Cibinong',
                'nama_pemilik'       => 'Deddy Kurniawan',
                'alamat'             => 'Jl. Raya Mayor Oking No. 15, Cibinong, Bogor',
                'no_hp'              => '0819-9988-7766',
                'no_ktp'             => '3201011508800003',
                'foto_ktp'           => null,
                'plafon_piutang'     => 80000000.00,
                'saldo_piutang'      => 18500000.00,
                'saldo_deposit'      => 0.00,
            ],
            [
                'kode_customer'      => 'CUST-005',
                'kode_wilayah'       => 'WIL-KRW-05',
                'nama_toko_bangunan' => 'TB Sumber Logam Cikampek',
                'nama_pemilik'       => 'Anton Wijaya',
                'alamat'             => 'Jl. Jenderal Sudirman No. 70, Cikampek, Karawang',
                'no_hp'              => '0821-4455-6677',
                'no_ktp'             => '3215010903840005',
                'foto_ktp'           => null,
                'plafon_piutang'     => 120000000.00,
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

        // 4. Master Rekening Bank Perusahaan
        $daftarRekening = [
            [
                'nomor_rekening' => '8800-1234-5678',
                'nama_bank'      => 'BCA (Bank Central Asia)',
                'atas_nama'      => 'PT Pura Balkom Jaya Utama',
                'saldo_rekening' => 450000000.00,
            ],
            [
                'nomor_rekening' => '1300-0987-6543-2',
                'nama_bank'      => 'Bank Mandiri',
                'atas_nama'      => 'PT Pura Balkom Jaya Operasional',
                'saldo_rekening' => 280000000.00,
            ],
            [
                'nomor_rekening' => '0012-01-000999-30-5',
                'nama_bank'      => 'BRI (Bank Rakyat Indonesia)',
                'atas_nama'      => 'PT Pura Balkom Jaya Penerimaan',
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
