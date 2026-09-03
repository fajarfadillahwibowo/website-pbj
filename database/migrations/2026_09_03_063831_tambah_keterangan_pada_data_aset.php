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
        if (!Schema::hasColumn('data_aset', 'keterangan')) {
            Schema::table('data_aset', function (Blueprint $table) {
                $table->text('keterangan')->nullable()->after('nama_pemilik');
            });
        }

        // 1. Perbarui data jenis aset AST-TNH menjadi gabungan Tanah & Bangunan Properti
        \Illuminate\Support\Facades\DB::table('data_jenis_aset')
            ->where('kode_jenis_aset', 'AST-TNH')
            ->update([
                'jenis_aset' => 'Tanah & Bangunan Properti',
                'keterangan' => 'Tanah kavling, bangunan gedung kantor, gudang semen, pos satpam, dan fasilitas properti',
                'diperbarui_pada' => now(),
            ]);

        // 2. Alihkan setiap aset yang tadinya berjenis AST-BDG ke AST-TNH
        \Illuminate\Support\Facades\DB::table('data_aset')
            ->where('kode_jenis_aset', 'AST-BDG')
            ->update(['kode_jenis_aset' => 'AST-TNH']);

        // 3. Hapus kategori AST-BDG agar tidak ada duplikasi
        \Illuminate\Support\Facades\DB::table('data_jenis_aset')
            ->where('kode_jenis_aset', 'AST-BDG')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('data_aset', 'keterangan')) {
            Schema::table('data_aset', function (Blueprint $table) {
                $table->dropColumn('keterangan');
            });
        }
    }
};
