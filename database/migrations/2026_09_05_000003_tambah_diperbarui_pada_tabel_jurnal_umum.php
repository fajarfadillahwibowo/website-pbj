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
        if (Schema::hasTable('jurnal_umum') && !Schema::hasColumn('jurnal_umum', 'diperbarui_pada')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->timestamp('diperbarui_pada')->nullable()->after('dibuat_pada');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jurnal_umum') && Schema::hasColumn('jurnal_umum', 'diperbarui_pada')) {
            Schema::table('jurnal_umum', function (Blueprint $table) {
                $table->dropColumn('diperbarui_pada');
            });
        }
    }
};
