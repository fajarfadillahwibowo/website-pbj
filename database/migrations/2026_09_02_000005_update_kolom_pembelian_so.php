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
        Schema::table('pembelian_so', function (Blueprint $table) {
            if (!Schema::hasColumn('pembelian_so', 'nomor_lo')) {
                $table->string('nomor_lo', 50)->nullable()->after('nomor_so');
            }
            if (!Schema::hasColumn('pembelian_so', 'jenis_pengiriman')) {
                $table->string('jenis_pengiriman', 10)->default('FRC')->after('kode_gudang');
            }
            if (!Schema::hasColumn('pembelian_so', 'qty_pengambilan')) {
                $table->integer('qty_pengambilan')->default(0)->after('jumlah_zak');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembelian_so', function (Blueprint $table) {
            if (Schema::hasColumn('pembelian_so', 'nomor_lo')) {
                $table->dropColumn('nomor_lo');
            }
            if (Schema::hasColumn('pembelian_so', 'jenis_pengiriman')) {
                $table->dropColumn('jenis_pengiriman');
            }
            if (Schema::hasColumn('pembelian_so', 'qty_pengambilan')) {
                $table->dropColumn('qty_pengambilan');
            }
        });
    }
};
