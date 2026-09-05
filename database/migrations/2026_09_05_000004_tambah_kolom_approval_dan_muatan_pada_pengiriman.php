<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pengiriman')) {
            // Ubah enum status_pengiriman untuk mendukung status ditolak
            DB::statement("ALTER TABLE `pengiriman` MODIFY COLUMN `status_pengiriman` ENUM('menunggu', 'dalam_perjalanan', 'terkirim', 'ditolak', 'retur') NOT NULL DEFAULT 'menunggu'");

            Schema::table('pengiriman', function (Blueprint $table) {
                if (!Schema::hasColumn('pengiriman', 'jumlah_zak')) {
                    $table->integer('jumlah_zak')->default(0)->after('id_so');
                }
                if (!Schema::hasColumn('pengiriman', 'disetujui_oleh')) {
                    $table->string('disetujui_oleh', 50)->nullable()->after('status_pengiriman');
                }
                if (!Schema::hasColumn('pengiriman', 'disetujui_pada')) {
                    $table->dateTime('disetujui_pada')->nullable()->after('disetujui_oleh');
                }
                if (!Schema::hasColumn('pengiriman', 'alasan_penolakan')) {
                    $table->text('alasan_penolakan')->nullable()->after('disetujui_pada');
                }
                if (!Schema::hasColumn('pengiriman', 'status_penerimaan_gudang')) {
                    $table->enum('status_penerimaan_gudang', ['belum_diterima', 'diterima_gudang', 'direct_customer'])->default('belum_diterima')->after('alasan_penolakan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pengiriman')) {
            Schema::table('pengiriman', function (Blueprint $table) {
                $kolomDihapus = [];
                if (Schema::hasColumn('pengiriman', 'jumlah_zak')) $kolomDihapus[] = 'jumlah_zak';
                if (Schema::hasColumn('pengiriman', 'disetujui_oleh')) $kolomDihapus[] = 'disetujui_oleh';
                if (Schema::hasColumn('pengiriman', 'disetujui_pada')) $kolomDihapus[] = 'disetujui_pada';
                if (Schema::hasColumn('pengiriman', 'alasan_penolakan')) $kolomDihapus[] = 'alasan_penolakan';
                if (Schema::hasColumn('pengiriman', 'status_penerimaan_gudang')) $kolomDihapus[] = 'status_penerimaan_gudang';

                if (!empty($kolomDihapus)) {
                    $table->dropColumn($kolomDihapus);
                }
            });

            DB::statement("ALTER TABLE `pengiriman` MODIFY COLUMN `status_pengiriman` ENUM('menunggu', 'dalam_perjalanan', 'terkirim', 'retur') NOT NULL DEFAULT 'menunggu'");
        }
    }
};
