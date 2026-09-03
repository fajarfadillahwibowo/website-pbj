<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeuanganSeeder extends Seeder
{
    /**
     * Jalankan seeder keuangan: Bagan Akun (COA), Data Jenis Aset & Aset, Transaksi Penjualan Awal, Piutang, dan Deposit.
     */
    public function run(): void
    {
        // 1. Bagan Akun Standar (COA - Chart of Accounts)
        $daftarAkun = [
            // AKTIVA LANCAR
            ['kode_akun' => '1101', 'nama_akun' => 'Kas Operasional Kantor', 'tipe_akun' => 'Aktiva Lancar', 'kelompok_akun' => 'Kas & Setara Kas', 'saldo_normal' => 'Debit', 'saldo_awal' => 25000000.00, 'saldo_berjalan' => 25000000.00],
            ['kode_akun' => '1102', 'nama_akun' => 'Bank BCA - 880012345678', 'tipe_akun' => 'Aktiva Lancar', 'kelompok_akun' => 'Kas & Setara Kas', 'saldo_normal' => 'Debit', 'saldo_awal' => 450000000.00, 'saldo_berjalan' => 450000000.00],
            ['kode_akun' => '1103', 'nama_akun' => 'Bank Mandiri - 1300098765432', 'tipe_akun' => 'Aktiva Lancar', 'kelompok_akun' => 'Kas & Setara Kas', 'saldo_normal' => 'Debit', 'saldo_awal' => 280000000.00, 'saldo_berjalan' => 280000000.00],
            ['kode_akun' => '1104', 'nama_akun' => 'Bank BRI - 001201000999305', 'tipe_akun' => 'Aktiva Lancar', 'kelompok_akun' => 'Kas & Setara Kas', 'saldo_normal' => 'Debit', 'saldo_awal' => 175000000.00, 'saldo_berjalan' => 175000000.00],
            ['kode_akun' => '1105', 'nama_akun' => 'Piutang Dagang (Customer)', 'tipe_akun' => 'Aktiva Lancar', 'kelompok_akun' => 'Piutang Usaha', 'saldo_normal' => 'Debit', 'saldo_awal' => 160500000.00, 'saldo_berjalan' => 160500000.00],
            ['kode_akun' => '1106', 'nama_akun' => 'Persediaan Semen Zak & Curah', 'tipe_akun' => 'Aktiva Lancar', 'kelompok_akun' => 'Persediaan', 'saldo_normal' => 'Debit', 'saldo_awal' => 625000000.00, 'saldo_berjalan' => 625000000.00],
            ['kode_akun' => '1107', 'nama_akun' => 'Uang Muka / Kas Bon Supir', 'tipe_akun' => 'Aktiva Lancar', 'kelompok_akun' => 'Uang Muka', 'saldo_normal' => 'Debit', 'saldo_awal' => 18000000.00, 'saldo_berjalan' => 18000000.00],

            // AKTIVA TETAP
            ['kode_akun' => '1201', 'nama_akun' => 'Kendaraan Truk & Tronton', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'saldo_awal' => 1850000000.00, 'saldo_berjalan' => 1850000000.00],
            ['kode_akun' => '1202', 'nama_akun' => 'Akumulasi Penyusutan Kendaraan', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'saldo_awal' => 220000000.00, 'saldo_berjalan' => 220000000.00],
            ['kode_akun' => '1203', 'nama_akun' => 'Peralatan & Fasilitas Gudang', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'saldo_awal' => 85000000.00, 'saldo_berjalan' => 85000000.00],

            // KEWAJIBAN LANCAR
            ['kode_akun' => '2101', 'nama_akun' => 'Hutang Dagang (Pabrik Semen)', 'tipe_akun' => 'Kewajiban Lancar', 'kelompok_akun' => 'Hutang Usaha', 'saldo_normal' => 'Kredit', 'saldo_awal' => 320000000.00, 'saldo_berjalan' => 320000000.00],
            ['kode_akun' => '2102', 'nama_akun' => 'Titipan Saldo Deposit Customer', 'tipe_akun' => 'Kewajiban Lancar', 'kelompok_akun' => 'Hutang Lainnya', 'saldo_normal' => 'Kredit', 'saldo_awal' => 55000000.00, 'saldo_berjalan' => 55000000.00],
            ['kode_akun' => '2103', 'nama_akun' => 'Hutang Gaji & Operasional', 'tipe_akun' => 'Kewajiban Lancar', 'kelompok_akun' => 'Biaya Akrual', 'saldo_normal' => 'Kredit', 'saldo_awal' => 42000000.00, 'saldo_berjalan' => 42000000.00],

            // MODAL
            ['kode_akun' => '3101', 'nama_akun' => 'Modal Disetor Pemilik', 'tipe_akun' => 'Modal', 'kelompok_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'saldo_awal' => 2500000000.00, 'saldo_berjalan' => 2500000000.00],
            ['kode_akun' => '3201', 'nama_akun' => 'Laba Ditahan', 'tipe_akun' => 'Modal', 'kelompok_akun' => 'Ekuitas', 'saldo_normal' => 'Kredit', 'saldo_awal' => 451500000.00, 'saldo_berjalan' => 451500000.00],

            // PENDAPATAN & HPP
            ['kode_akun' => '4101', 'nama_akun' => 'Pendapatan Penjualan Semen', 'tipe_akun' => 'Pendapatan', 'kelompok_akun' => 'Penjualan', 'saldo_normal' => 'Kredit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 850000000.00],
            ['kode_akun' => '4201', 'nama_akun' => 'Pendapatan Jasa Ongkos Angkut', 'tipe_akun' => 'Pendapatan', 'kelompok_akun' => 'Pendapatan Jasa', 'saldo_normal' => 'Kredit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 78000000.00],
            ['kode_akun' => '5101', 'nama_akun' => 'Harga Pokok Penjualan (HPP)', 'tipe_akun' => 'Harga Pokok Penjualan', 'kelompok_akun' => 'HPP', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 745000000.00],

            // BEBAN OPERASIONAL
            ['kode_akun' => '6101', 'nama_akun' => 'Beban BBM & Tol Armada', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Operasional Truk', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 38500000.00],
            ['kode_akun' => '6102', 'nama_akun' => 'Beban Servis & Sparepart Truk', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Operasional Truk', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 14200000.00],
            ['kode_akun' => '6103', 'nama_akun' => 'Beban Gaji Karyawan & Supir', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Beban Umum & Admin', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 52000000.00],
            ['kode_akun' => '6104', 'nama_akun' => 'Beban Listrik, Air & Kantor', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Beban Umum & Admin', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 6800000.00],
        ];

        foreach ($daftarAkun as $akun) {
            DB::table('data_kode_akun')->updateOrInsert(
                ['kode_akun' => $akun['kode_akun']],
                $akun
            );
        }

        // 2. Data Jenis Aset & Data Aset Perusahaan
        $daftarJenisAset = [
            ['kode_jenis_aset' => 'AST-TRK', 'jenis_aset' => 'Armada Truk & Tronton', 'keterangan' => 'Kendaraan pengiriman semen zak dan curah'],
            ['kode_jenis_aset' => 'AST-GDG', 'jenis_aset' => 'Peralatan & Fasilitas Gudang', 'keterangan' => 'Forklift, palet besi, dan conveyor'],
            ['kode_jenis_aset' => 'AST-OFC', 'jenis_aset' => 'Inventaris Kantor', 'keterangan' => 'Komputer, printer, dan server lokal'],
        ];

        foreach ($daftarJenisAset as $ja) {
            DB::table('data_jenis_aset')->updateOrInsert(
                ['kode_jenis_aset' => $ja['kode_jenis_aset']],
                $ja
            );
        }

        $daftarAset = [
            [
                'kode_aset'         => 'AST-001',
                'kode_jenis_aset'   => 'AST-TRK',
                'nama_aset'         => 'Hino Dutro 130 HD (Colt Diesel)',
                'tanggal_pembelian' => '2023-03-15',
                'harga_aset'        => 385000000.00,
                'no_polisi'         => 'B 9123 PBJ',
                'no_mesin'          => 'W04D-TN12345',
                'no_rangka'         => 'MHKRD1234567890',
                'merek_aset'        => 'Hino',
                'muatan'            => '200 Zak (8 Ton)',
                'jenis_kendaraan'   => 'Colt Diesel Double',
                'tahun_pembuatan'   => 2023,
                'tanggal_kir'       => '2026-11-20',
                'tanggal_pajak'     => '2026-10-15',
                'status_aset'       => 'aktif',
                'nama_pemilik'      => 'PT Putra Balkom Jaya',
            ],
            [
                'kode_aset'         => 'AST-002',
                'kode_jenis_aset'   => 'AST-TRK',
                'nama_aset'         => 'Mitsubishi Fuso Fighter (Tronton 10 Roda)',
                'tanggal_pembelian' => '2022-07-10',
                'harga_aset'        => 720000000.00,
                'no_polisi'         => 'B 9555 PBJ',
                'no_mesin'          => '6M60-12890',
                'no_rangka'         => 'MHKFM1234567899',
                'merek_aset'        => 'Mitsubishi Fuso',
                'muatan'            => '600 Zak (24 Ton)',
                'jenis_kendaraan'   => 'Tronton',
                'tahun_pembuatan'   => 2022,
                'tanggal_kir'       => '2026-09-30',
                'tanggal_pajak'     => '2026-08-10',
                'status_aset'       => 'aktif',
                'nama_pemilik'      => 'PT Putra Balkom Jaya',
            ],
            [
                'kode_aset'         => 'AST-003',
                'kode_jenis_aset'   => 'AST-GDG',
                'nama_aset'         => 'Forklift Toyota 3.5 Ton Diesel',
                'tanggal_pembelian' => '2024-01-20',
                'harga_aset'        => 145000000.00,
                'no_polisi'         => '-',
                'no_mesin'          => '1DZ-III-9988',
                'no_rangka'         => 'TYTFL350011',
                'merek_aset'        => 'Toyota Forklift',
                'muatan'            => '3.5 Ton',
                'jenis_kendaraan'   => 'Forklift Gudang',
                'tahun_pembuatan'   => 2023,
                'tanggal_kir'       => null,
                'tanggal_pajak'     => null,
                'status_aset'       => 'aktif',
                'nama_pemilik'      => 'PT Putra Balkom Jaya',
            ],
        ];

        foreach ($daftarAset as $aset) {
            DB::table('data_aset')->updateOrInsert(
                ['kode_aset' => $aset['kode_aset']],
                $aset
            );
        }

        // 3. Data Transaksi Penjualan & Piutang Awal
        $penjualan1 = [
            'id_penjualan'       => 1,
            'nomor_faktur'       => 'INV-20260901-001',
            'tanggal_penjualan'  => '2026-09-01',
            'kode_customer'      => 'CUST-001',
            'metode_pembayaran'  => 'Kredit / Piutang',
            'total_bruto'        => 35000000.00,
            'diskon'             => 0.00,
            'total_netto'        => 35000000.00,
            'jumlah_dibayar'     => 0.00,
            'sisa_piutang'       => 35000000.00,
            'status_pembayaran'  => 'Belum Lunas',
            'jatuh_tempo'        => '2026-09-30',
            'id_rekening'        => null,
            'status_persetujuan' => 'disetujui',
            'dibuat_oleh'        => 'staff_ar',
        ];
        DB::table('penjualan')->updateOrInsert(['id_penjualan' => 1], $penjualan1);

        $piutang1 = [
            'id_piutang'          => 1,
            'id_penjualan'        => 1,
            'kode_customer'       => 'CUST-001',
            'jumlah_piutang'      => 35000000.00,
            'sisa_piutang'        => 35000000.00,
            'tanggal_terbit'      => '2026-09-01',
            'tanggal_jatuh_tempo' => '2026-09-30',
            'status_piutang'      => 'belum_lunas',
        ];
        DB::table('list_piutang')->updateOrInsert(['id_piutang' => 1], $piutang1);

        $penjualan2 = [
            'id_penjualan'       => 2,
            'nomor_faktur'       => 'INV-20260901-002',
            'tanggal_penjualan'  => '2026-09-01',
            'kode_customer'      => 'CUST-002',
            'metode_pembayaran'  => 'Kredit / Piutang',
            'total_bruto'        => 62000000.00,
            'diskon'             => 0.00,
            'total_netto'        => 62000000.00,
            'jumlah_dibayar'     => 0.00,
            'sisa_piutang'       => 62000000.00,
            'status_pembayaran'  => 'Belum Lunas',
            'jatuh_tempo'        => '2026-09-25',
            'id_rekening'        => null,
            'status_persetujuan' => 'disetujui',
            'dibuat_oleh'        => 'staff_ar',
        ];
        DB::table('penjualan')->updateOrInsert(['id_penjualan' => 2], $penjualan2);

        $piutang2 = [
            'id_piutang'          => 2,
            'id_penjualan'        => 2,
            'kode_customer'       => 'CUST-002',
            'jumlah_piutang'      => 62000000.00,
            'sisa_piutang'        => 62000000.00,
            'tanggal_terbit'      => '2026-09-01',
            'tanggal_jatuh_tempo' => '2026-09-25',
            'status_piutang'      => 'belum_lunas',
        ];
        DB::table('list_piutang')->updateOrInsert(['id_piutang' => 2], $piutang2);

        // 4. Riwayat Mutasi Deposit Customer Awal
        $daftarDeposit = [
            [
                'id_deposit'          => 1,
                'nomor_bukti_deposit' => 'DEP-20260901-001',
                'kode_customer'       => 'CUST-001',
                'tanggal_deposit'     => '2026-09-01',
                'tipe_mutasi'         => 'Masuk',
                'jumlah_nominal'      => 15000000.00,
                'saldo_akhir_deposit' => 15000000.00,
                'keterangan'          => 'Setoran deposit awal via transfer BCA',
                'dibuat_oleh'         => 'staff_ar',
            ],
            [
                'id_deposit'          => 2,
                'nomor_bukti_deposit' => 'DEP-20260901-002',
                'kode_customer'       => 'CUST-003',
                'tanggal_deposit'     => '2026-09-01',
                'tipe_mutasi'         => 'Masuk',
                'jumlah_nominal'      => 25000000.00,
                'saldo_akhir_deposit' => 25000000.00,
                'keterangan'          => 'Top-up saldo deposit via transfer Mandiri',
                'dibuat_oleh'         => 'staff_ar',
            ],
        ];

        foreach ($daftarDeposit as $dep) {
            DB::table('list_deposit')->updateOrInsert(['id_deposit' => $dep['id_deposit']], $dep);
        }

        // 5. Data Pengeluaran Kas Operasional Awal
        $daftarPengeluaran = [
            [
                'id_pengeluaran'      => 1,
                'nomor_pengeluaran'   => 'KAS-OUT-20260901-001',
                'tanggal_pengeluaran' => '2026-09-01',
                'kategori_pengeluaran'=> 'BBM & Tol Armada',
                'kode_akun'           => '6101',
                'total_nominal'       => 4500000.00,
                'id_rekening_sumber'  => 1,
                'keterangan'          => 'Pengisian Solar B35 & E-Toll 3 armada truk rute Cikarang-Merak',
                'status_persetujuan'  => 'disetujui_spv',
                'disetujui_oleh'      => 'spv_keuangan',
                'dibuat_oleh'         => 'staff_ap',
            ],
            [
                'id_pengeluaran'      => 2,
                'nomor_pengeluaran'   => 'KAS-OUT-20260901-002',
                'tanggal_pengeluaran' => '2026-09-01',
                'kategori_pengeluaran'=> 'Operasional Kantor',
                'kode_akun'           => '6104',
                'total_nominal'       => 1200000.00,
                'id_rekening_sumber'  => 1,
                'keterangan'          => 'Pembelian perlengkapan ATK cetak invoice dan formulir surat jalan',
                'status_persetujuan'  => 'disetujui_spv',
                'disetujui_oleh'      => 'spv_keuangan',
                'dibuat_oleh'         => 'staff_ap',
            ],
        ];

        foreach ($daftarPengeluaran as $pengeluaran) {
            DB::table('pengeluaran')->updateOrInsert(['id_pengeluaran' => $pengeluaran['id_pengeluaran']], $pengeluaran);
        }
    }
}
