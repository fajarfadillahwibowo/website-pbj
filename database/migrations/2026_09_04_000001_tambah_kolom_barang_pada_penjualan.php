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
        if (Schema::hasTable('penjualan')) {
            Schema::table('penjualan', function (Blueprint $table) {
                if (!Schema::hasColumn('penjualan', 'kode_barang')) {
                    $table->string('kode_barang', 30)->nullable()->after('kode_toko');
                }
                if (!Schema::hasColumn('penjualan', 'nama_barang')) {
                    $table->string('nama_barang', 150)->nullable()->after('kode_barang');
                }
                if (!Schema::hasColumn('penjualan', 'satuan_barang')) {
                    $table->string('satuan_barang', 30)->default('Zak')->after('nama_barang');
                }
                if (!Schema::hasColumn('penjualan', 'jumlah_zak')) {
                    $table->integer('jumlah_zak')->default(0)->after('satuan_barang');
                }
                if (!Schema::hasColumn('penjualan', 'harga_satuan')) {
                    $table->decimal('harga_satuan', 15, 2)->default(0.00)->after('jumlah_zak');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('penjualan')) {
            Schema::table('penjualan', function (Blueprint $table) {
                $columns = ['kode_barang', 'nama_barang', 'satuan_barang', 'jumlah_zak', 'harga_satuan'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('penjualan', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
