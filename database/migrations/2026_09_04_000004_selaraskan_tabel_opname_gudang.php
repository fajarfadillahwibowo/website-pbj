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
        if (Schema::hasTable('opname_gudang')) {
            // Periksa apakah kolom nomor_opname belum ada (skema lama)
            if (!Schema::hasColumn('opname_gudang', 'nomor_opname')) {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                DB::statement('DROP TABLE IF EXISTS `opname_gudang`;');
                DB::statement("
                    CREATE TABLE `opname_gudang` (
                        `id_opname` INT AUTO_INCREMENT NOT NULL,
                        `nomor_opname` VARCHAR(50) NOT NULL,
                        `kode_gudang` VARCHAR(30) NOT NULL,
                        `tanggal_opname` DATE NOT NULL,
                        `stok_sistem` INT NOT NULL DEFAULT 0,
                        `stok_fisik` INT NOT NULL DEFAULT 0,
                        `selisih` INT NOT NULL DEFAULT 0,
                        `keterangan_selisih` TEXT DEFAULT NULL,
                        `status_konfirmasi` ENUM('draft', 'dikonfirmasi_spv') NOT NULL DEFAULT 'draft',
                        `petugas_opname` VARCHAR(50) NOT NULL,
                        `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (`id_opname`),
                        UNIQUE KEY `uk_nomor_opname` (`nomor_opname`),
                        KEY `idx_opname_gudang` (`kode_gudang`),
                        CONSTRAINT `fk_opname_gudang` FOREIGN KEY (`kode_gudang`) 
                            REFERENCES `list_gudang_so` (`kode_gudang`) 
                            ON DELETE RESTRICT ON UPDATE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ");
                DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skema opname_gudang tetap dipertahankan
    }
};
