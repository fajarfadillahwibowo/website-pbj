<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('data_kode_akun')) {
            return;
        }

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
            ['kode_akun' => '1200', 'nama_akun' => 'Tanah & Lahan', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '1201', 'nama_akun' => 'Kendaraan Truk & Tronton', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'saldo_awal' => 1850000000.00, 'saldo_berjalan' => 1850000000.00],
            ['kode_akun' => '1202', 'nama_akun' => 'Akumulasi Penyusutan Kendaraan', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'saldo_awal' => 220000000.00, 'saldo_berjalan' => 220000000.00],
            ['kode_akun' => '1203', 'nama_akun' => 'Peralatan & Fasilitas Gudang', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'saldo_awal' => 85000000.00, 'saldo_berjalan' => 85000000.00],
            ['kode_akun' => '1204', 'nama_akun' => 'Bangunan & Gedung Kantor', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '1205', 'nama_akun' => 'Akumulasi Penyusutan Bangunan', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '1206', 'nama_akun' => 'Peralatan & Inventaris Kantor', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '1207', 'nama_akun' => 'Akumulasi Penyusutan Alat Kantor', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '1208', 'nama_akun' => 'Akumulasi Penyusutan Fasilitas Gudang', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Aset Tetap', 'saldo_normal' => 'Kredit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],

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
            ['kode_akun' => '6105', 'nama_akun' => 'Beban Penyusutan Kendaraan Truk', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Operasional Truk', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '6106', 'nama_akun' => 'Beban Penyusutan Bangunan & Gedung', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Operasional', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '6107', 'nama_akun' => 'Beban Penyusutan Fasilitas Gudang', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Operasional', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
            ['kode_akun' => '6108', 'nama_akun' => 'Beban Penyusutan Peralatan Kantor', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Operasional', 'saldo_normal' => 'Debit', 'saldo_awal' => 0.00, 'saldo_berjalan' => 0.00],
        ];

        foreach ($daftarAkun as $akun) {
            $ada = DB::table('data_kode_akun')->where('kode_akun', $akun['kode_akun'])->exists();
            if (!$ada) {
                DB::table('data_kode_akun')->insert(array_merge($akun, [
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]));
            }
        }

        // Pastikan juga kategori jenis aset standar tersedia di data_jenis_aset
        if (Schema::hasTable('data_jenis_aset')) {
            $jenisAsetStandar = [
                ['kode_jenis_aset' => 'AST-TRK', 'jenis_aset' => 'Armada Truk & Tronton', 'keterangan' => 'Truk armada ekspedisi pengangkut semen (umur 8 tahun, tarif 12.5%)'],
                ['kode_jenis_aset' => 'AST-GDG', 'jenis_aset' => 'Peralatan & Fasilitas Gudang', 'keterangan' => 'Forklift, konveyor, dan timbangan semen gudang (umur 8 tahun, tarif 12.5%)'],
                ['kode_jenis_aset' => 'AST-OFC', 'jenis_aset' => 'Peralatan & Inventaris Kantor', 'keterangan' => 'Komputer, printer, server, AC, dan inventaris kantor (umur 4 tahun, tarif 25%)'],
                ['kode_jenis_aset' => 'AST-TNH', 'jenis_aset' => 'Tanah & Bangunan Properti', 'keterangan' => 'Tanah kavling, bangunan gedung kantor, gudang semen, pos satpam, dan fasilitas properti'],
                ['kode_jenis_aset' => 'AST-BDG', 'jenis_aset' => 'Bangunan & Gedung', 'keterangan' => 'Gedung kantor, mess staf, dan pos operasional (umur 20 tahun, tarif 5%)'],
            ];

            foreach ($jenisAsetStandar as $jenis) {
                $adaJenis = DB::table('data_jenis_aset')->where('kode_jenis_aset', $jenis['kode_jenis_aset'])->exists();
                if (!$adaJenis) {
                    DB::table('data_jenis_aset')->insert(array_merge($jenis, [
                        'dibuat_pada' => now(),
                        'diperbarui_pada' => now(),
                    ]));
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data akun tidak dihapus demi menjaga integritas relasi foreign key
    }
};
