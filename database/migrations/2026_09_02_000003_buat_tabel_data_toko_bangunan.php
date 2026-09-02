<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi pembuatan tabel data_toko_bangunan dan relasi ke data_customer & penjualan.
     */
    public function up(): void
    {
        if (!Schema::hasTable('data_toko_bangunan')) {
            Schema::create('data_toko_bangunan', function (Blueprint $table) {
                $table->string('kode_toko', 30)->primary();
                $table->string('kode_customer', 30);
                $table->string('kode_wilayah', 30);
                $table->string('nama_toko_bangunan', 150);
                $table->string('tipe_lokasi', 50)->default('toko_retail'); // toko_retail, proyek_kontraktor, gudang_transit
                $table->string('penanggung_jawab', 100)->default('-');
                $table->string('no_hp_toko', 25)->default('-');
                $table->text('alamat_lengkap');
                $table->string('titik_koordinat', 100)->nullable();
                $table->string('status_toko', 30)->default('aktif'); // aktif, non-aktif
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

                $table->index('kode_customer', 'idx_toko_customer');
                $table->index('kode_wilayah', 'idx_toko_wilayah');
            });
        }

        // Tambahkan kolom kode_toko pada tabel penjualan jika belum ada
        if (Schema::hasTable('penjualan') && !Schema::hasColumn('penjualan', 'kode_toko')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->string('kode_toko', 30)->nullable()->after('kode_customer');
                $table->index('kode_toko', 'idx_penjualan_toko');
            });
        }
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        if (Schema::hasTable('penjualan') && Schema::hasColumn('penjualan', 'kode_toko')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->dropIndex('idx_penjualan_toko');
                $table->dropColumn('kode_toko');
            });
        }

        Schema::dropIfExists('data_toko_bangunan');
    }
};
