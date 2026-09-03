<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi penyelarasan 6 foreign key constraints agar 100% identik dengan skema_database.sql.
     */
    public function up(): void
    {
        // 1. Relasi data_toko_bangunan ke data_customer dan data_wilayah
        if (Schema::hasTable('data_toko_bangunan') && Schema::hasTable('data_customer') && Schema::hasTable('data_wilayah')) {
            Schema::table('data_toko_bangunan', function (Blueprint $table) {
                $table->foreign('kode_customer', 'fk_toko_customer')
                    ->references('kode_customer')->on('data_customer')
                    ->onDelete('restrict')->onUpdate('cascade');

                $table->foreign('kode_wilayah', 'fk_toko_wilayah')
                    ->references('kode_wilayah')->on('data_wilayah')
                    ->onDelete('restrict')->onUpdate('cascade');
            });
        }

        // 2. Relasi penjualan ke data_toko_bangunan
        if (Schema::hasTable('penjualan') && Schema::hasTable('data_toko_bangunan')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->foreign('kode_toko', 'fk_penjualan_toko')
                    ->references('kode_toko')->on('data_toko_bangunan')
                    ->onDelete('set null')->onUpdate('cascade');
            });
        }

        // 3. Relasi data_aset ke data_kode_akun
        if (Schema::hasTable('data_aset') && Schema::hasTable('data_kode_akun')) {
            Schema::table('data_aset', function (Blueprint $table) {
                $table->foreign('kode_akun_aset', 'fk_aset_akun_aset')
                    ->references('kode_akun')->on('data_kode_akun')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('kode_akun_akumulasi', 'fk_aset_akun_akumulasi')
                    ->references('kode_akun')->on('data_kode_akun')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->foreign('kode_akun_beban', 'fk_aset_akun_beban')
                    ->references('kode_akun')->on('data_kode_akun')
                    ->onDelete('set null')->onUpdate('cascade');
            });
        }
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        if (Schema::hasTable('data_aset')) {
            Schema::table('data_aset', function (Blueprint $table) {
                $table->dropForeign('fk_aset_akun_aset');
                $table->dropForeign('fk_aset_akun_akumulasi');
                $table->dropForeign('fk_aset_akun_beban');
            });
        }

        if (Schema::hasTable('penjualan')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $table->dropForeign('fk_penjualan_toko');
            });
        }

        if (Schema::hasTable('data_toko_bangunan')) {
            Schema::table('data_toko_bangunan', function (Blueprint $table) {
                $table->dropForeign('fk_toko_customer');
                $table->dropForeign('fk_toko_wilayah');
            });
        }
    }
};
