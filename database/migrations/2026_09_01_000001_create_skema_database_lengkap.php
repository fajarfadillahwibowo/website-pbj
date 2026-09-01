<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi skema lengkap 28 tabel dan view sistem.
     */
    public function up(): void
    {
        $sqlPath = database_path('skema_database.sql');
        if (file_exists($sqlPath)) {
            DB::unprepared(file_get_contents($sqlPath));
        }
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        $tables = [
            'riwayat_login', 'hak_akses_jabatan', 'super_account', 'account', 'modul', 'jabatan',
            'detail_perbaikan', 'surat_perintah_kerja', 'sparepart', 'kendaraan',
            'surat_jalan', 'sales_order', 'ongkos_angkut', 'lokasi_pengiriman',
            'detail_stock_opname', 'stock_opname', 'mutasi_stok', 'gudang_semen',
            'detail_jurnal', 'jurnal_umum', 'kode_akun', 'aset_perusahaan',
            'deposit_customer', 'piutang', 'faktur_penjualan', 'data_customer',
            'data_semen', 'data_karyawan', 'wilayah'
        ];

        foreach ($tables as $table) {
            DB::statement("DROP TABLE IF EXISTS `{$table}`;");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
