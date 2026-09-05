<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('data_kso')) {
            Schema::table('data_kso', function (Blueprint $table) {
                $kolomDihapus = [];
                foreach (['status_kso', 'pihak_mitra', 'tanggal_mulai', 'tanggal_selesai', 'nilai_kontrak', 'keterangan'] as $kolom) {
                    if (Schema::hasColumn('data_kso', $kolom)) {
                        $kolomDihapus[] = $kolom;
                    }
                }
                if (!empty($kolomDihapus)) {
                    $table->dropColumn($kolomDihapus);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('data_kso')) {
            Schema::table('data_kso', function (Blueprint $table) {
                if (!Schema::hasColumn('data_kso', 'status_kso')) {
                    $table->enum('status_kso', ['Aktif', 'Selesai', 'Ditangguhkan'])->default('Aktif')->after('file_kontrak_kso');
                }
                if (!Schema::hasColumn('data_kso', 'pihak_mitra')) {
                    $table->string('pihak_mitra', 100)->default('-')->after('status_kso');
                }
                if (!Schema::hasColumn('data_kso', 'tanggal_mulai')) {
                    $table->date('tanggal_mulai')->nullable()->after('pihak_mitra');
                }
                if (!Schema::hasColumn('data_kso', 'tanggal_selesai')) {
                    $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
                }
                if (!Schema::hasColumn('data_kso', 'nilai_kontrak')) {
                    $table->decimal('nilai_kontrak', 15, 2)->default(0.00)->after('tanggal_selesai');
                }
                if (!Schema::hasColumn('data_kso', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('nilai_kontrak');
                }
            });
        }
    }
};
