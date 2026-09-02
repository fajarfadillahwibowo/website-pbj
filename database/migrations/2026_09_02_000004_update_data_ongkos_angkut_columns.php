<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi penyesuaian kolom data ongkos angkut.
     */
    public function up(): void
    {
        // Jika tabel sudah ada, kita sesuaikan kolomnya secara aman
        if (Schema::hasTable('data_ongkos_angkut')) {
            Schema::dropIfExists('data_ongkos_angkut');
        }

        Schema::create('data_ongkos_angkut', function (Blueprint $table) {
            $table->increments('id_ongkos');
            $table->string('kode_oa', 30)->unique();
            $table->string('nama_oa', 150);
            $table->string('kode_gudang', 30)->nullable();
            $table->string('kontrak_oa', 100)->nullable();
            $table->string('muatan_oa', 100)->default('Semen Zak 50kg');
            $table->decimal('harga_oa', 15, 2)->default(0.00);
            $table->decimal('harga_kso', 15, 2)->default(0.00);
            $table->decimal('harga_kso_khusus', 15, 2)->default(0.00);
            $table->string('wilayah_oa', 100);
            $table->text('keterangan')->nullable();
            $table->dateTime('dibuat_pada')->nullable();
            $table->dateTime('diperbarui_pada')->nullable();

            $table->index('kode_gudang');
            $table->index('wilayah_oa');
        });

        // Masukkan data awal representatif
        DB::table('data_ongkos_angkut')->insert([
            [
                'kode_oa' => 'OA-001',
                'nama_oa' => 'Rute Distribusi Cikarang - Bekasi Barat',
                'kode_gudang' => 'GDG-PBJ1',
                'kontrak_oa' => 'KTR/PBJ-OA/2026/01',
                'muatan_oa' => 'Semen Zak 50kg',
                'harga_oa' => 2500.00,
                'harga_kso' => 2200.00,
                'harga_kso_khusus' => 2000.00,
                'wilayah_oa' => 'Bekasi & Sekitarnya',
                'keterangan' => 'Tarif standar angkutan zak semen gudang utama ke toko bangunan zona 1.',
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
            [
                'kode_oa' => 'OA-002',
                'nama_oa' => 'Rute Curah Plant Cirebon - Gudang Utama',
                'kode_gudang' => 'GDG-PBJ1',
                'kontrak_oa' => 'KTR/PBJ-OA/2026/02',
                'muatan_oa' => 'Curah Semen (Ton)',
                'harga_oa' => 65000.00,
                'harga_kso' => 58000.00,
                'harga_kso_khusus' => 55000.00,
                'wilayah_oa' => 'Jawa Barat & Pantura',
                'keterangan' => 'Tarif angkut truk tronton bulk semen curah antar-pabrik.',
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
            [
                'kode_oa' => 'OA-003',
                'nama_oa' => 'Rute Distribusi Karawang - Cikampek',
                'kode_gudang' => 'GDG-PBJ1',
                'kontrak_oa' => 'KTR/PBJ-OA/2026/03',
                'muatan_oa' => 'Semen Zak 40kg',
                'harga_oa' => 2100.00,
                'harga_kso' => 1900.00,
                'harga_kso_khusus' => 1750.00,
                'wilayah_oa' => 'Karawang & Purwakarta',
                'keterangan' => 'Pengiriman reguler armada Colt Diesel Double (CDD).',
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ],
        ]);
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_ongkos_angkut');
    }
};
