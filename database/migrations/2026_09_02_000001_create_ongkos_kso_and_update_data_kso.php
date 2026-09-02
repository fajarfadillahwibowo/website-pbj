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
                if (!Schema::hasColumn('data_kso', 'file_kontrak_kso')) {
                    $table->string('file_kontrak_kso', 255)->nullable()->after('nama_kso');
                }
                if (!Schema::hasColumn('data_kso', 'status_kso')) {
                    $table->enum('status_kso', ['Aktif', 'Selesai', 'Ditangguhkan'])->default('Aktif')->after('file_kontrak_kso');
                }
            });
        }

        if (!Schema::hasTable('ongkos_kso')) {
            Schema::create('ongkos_kso', function (Blueprint $table) {
                $table->string('kode_oa', 30)->primary();
                $table->string('kode_kso', 30);
                $table->string('nama_oa', 100);
                $table->string('muatan', 50);
                $table->decimal('ongkos_angkut', 15, 2)->default(0);
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

                $table->foreign('kode_kso')->references('kode_kso')->on('data_kso')->onDelete('cascade')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ongkos_kso');
        if (Schema::hasTable('data_kso')) {
            Schema::table('data_kso', function (Blueprint $table) {
                if (Schema::hasColumn('data_kso', 'file_kontrak_kso')) {
                    $table->dropColumn('file_kontrak_kso');
                }
                if (Schema::hasColumn('data_kso', 'status_kso')) {
                    $table->dropColumn('status_kso');
                }
            });
        }
    }
};
