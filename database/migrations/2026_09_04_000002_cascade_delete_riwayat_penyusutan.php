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
        if (Schema::hasTable('riwayat_penyusutan')) {
            Schema::table('riwayat_penyusutan', function (Blueprint $table) {
                try {
                    $table->dropForeign('fk_penyusutan_aset');
                } catch (\Exception $e) {}

                $table->foreign('kode_aset', 'fk_penyusutan_aset')
                    ->references('kode_aset')
                    ->on('data_aset')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('riwayat_penyusutan')) {
            Schema::table('riwayat_penyusutan', function (Blueprint $table) {
                try {
                    $table->dropForeign('fk_penyusutan_aset');
                } catch (\Exception $e) {}

                $table->foreign('kode_aset', 'fk_penyusutan_aset')
                    ->references('kode_aset')
                    ->on('data_aset')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            });
        }
    }
};
