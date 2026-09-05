<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Cek apakah foreign key fk_kirim_driver sudah ada, jika ya drop dulu untuk modifikasi aturan delete
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND TABLE_NAME = 'pengiriman'
              AND CONSTRAINT_NAME = 'fk_kirim_driver'
        ");

        if (!empty($fkExists)) {
            DB::statement("ALTER TABLE `pengiriman` DROP FOREIGN KEY `fk_kirim_driver`");
        }

        // 2. Ubah kolom kode_driver di pengiriman menjadi NULLABLE agar tidak melanggar integritas jika supir dihapus
        DB::statement("ALTER TABLE `pengiriman` MODIFY COLUMN `kode_driver` VARCHAR(30) NULL DEFAULT NULL");

        // 3. Tambahkan kembali FK dengan aturan ON DELETE SET NULL ON UPDATE CASCADE
        DB::statement("
            ALTER TABLE `pengiriman`
            ADD CONSTRAINT `fk_kirim_driver`
            FOREIGN KEY (`kode_driver`) REFERENCES `data_karyawan` (`kode_karyawan`)
            ON DELETE SET NULL ON UPDATE CASCADE
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.REFERENTIAL_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND TABLE_NAME = 'pengiriman'
              AND CONSTRAINT_NAME = 'fk_kirim_driver'
        ");

        if (!empty($fkExists)) {
            DB::statement("ALTER TABLE `pengiriman` DROP FOREIGN KEY `fk_kirim_driver`");
        }

        // Kembalikan ke NOT NULL (jika ada data null, isi fallback)
        $defaultDriver = DB::table('data_karyawan')->where('kategori_karyawan', 'driver')->value('kode_karyawan') ?? 'DRV-001';
        DB::table('pengiriman')->whereNull('kode_driver')->update(['kode_driver' => $defaultDriver]);

        DB::statement("ALTER TABLE `pengiriman` MODIFY COLUMN `kode_driver` VARCHAR(30) NOT NULL");

        DB::statement("
            ALTER TABLE `pengiriman`
            ADD CONSTRAINT `fk_kirim_driver`
            FOREIGN KEY (`kode_driver`) REFERENCES `data_karyawan` (`kode_karyawan`)
            ON DELETE RESTRICT ON UPDATE CASCADE
        ");
    }
};
