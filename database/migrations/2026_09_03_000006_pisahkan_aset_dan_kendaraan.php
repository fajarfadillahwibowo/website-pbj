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
        // 1. Tambahkan COA Lengkap untuk Aktiva Tetap dan Beban Penyusutan
        $akunBaru = [
            ['kode_akun' => '1200', 'nama_akun' => 'Tanah & Lahan', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Neraca', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1204', 'nama_akun' => 'Bangunan & Gedung Kantor', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Neraca', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1205', 'nama_akun' => 'Akumulasi Penyusutan Bangunan', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Neraca', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '1206', 'nama_akun' => 'Peralatan & Inventaris Kantor', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Neraca', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '1207', 'nama_akun' => 'Akumulasi Penyusutan Alat Kantor', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Neraca', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '1208', 'nama_akun' => 'Akumulasi Penyusutan Fasilitas Gudang', 'tipe_akun' => 'Aktiva Tetap', 'kelompok_akun' => 'Neraca', 'saldo_normal' => 'Kredit'],
            ['kode_akun' => '6105', 'nama_akun' => 'Beban Penyusutan Kendaraan Truk', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Laba Rugi', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6106', 'nama_akun' => 'Beban Penyusutan Bangunan & Gedung', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Laba Rugi', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6107', 'nama_akun' => 'Beban Penyusutan Fasilitas Gudang', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Laba Rugi', 'saldo_normal' => 'Debit'],
            ['kode_akun' => '6108', 'nama_akun' => 'Beban Penyusutan Peralatan Kantor', 'tipe_akun' => 'Beban Operasional', 'kelompok_akun' => 'Laba Rugi', 'saldo_normal' => 'Debit'],
        ];

        foreach ($akunBaru as $akun) {
            $ada = DB::table('data_kode_akun')->where('kode_akun', $akun['kode_akun'])->exists();
            if (!$ada) {
                DB::table('data_kode_akun')->insert(array_merge($akun, [
                    'saldo_awal' => 0.00,
                    'saldo_berjalan' => 0.00,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]));
            }
        }

        // 2. Tambahkan / sesuaikan jenis aset di data_jenis_aset
        $jenisAsetBaru = [
            ['kode_jenis_aset' => 'AST-TNH', 'jenis_aset' => 'Tanah & Lahan', 'keterangan' => 'Aktiva tetap tanah/lahan usaha (tidak mengalami penyusutan/depresiasi)'],
            ['kode_jenis_aset' => 'AST-BDG', 'jenis_aset' => 'Bangunan & Gedung', 'keterangan' => 'Gedung kantor, mess staf, dan pos operasional (umur 20 tahun, tarif 5%)'],
            ['kode_jenis_aset' => 'AST-TRK', 'jenis_aset' => 'Armada Truk & Tronton', 'keterangan' => 'Truk armada ekspedisi pengangkut semen (umur 8 tahun, tarif 12.5%)'],
            ['kode_jenis_aset' => 'AST-GDG', 'jenis_aset' => 'Peralatan & Fasilitas Gudang', 'keterangan' => 'Forklift, konveyor, dan timbangan semen gudang (umur 8 tahun, tarif 12.5%)'],
            ['kode_jenis_aset' => 'AST-OFC', 'jenis_aset' => 'Peralatan & Inventaris Kantor', 'keterangan' => 'Komputer, printer, server, AC, dan inventaris kantor (umur 4 tahun, tarif 25%)'],
        ];

        foreach ($jenisAsetBaru as $jenis) {
            $ada = DB::table('data_jenis_aset')->where('kode_jenis_aset', $jenis['kode_jenis_aset'])->exists();
            if (!$ada) {
                DB::table('data_jenis_aset')->insert(array_merge($jenis, [
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]));
            }
        }

        // 3. Tambahkan kolom-kolom akuntansi penyusutan pada data_aset
        Schema::table('data_aset', function (Blueprint $table) {
            if (!Schema::hasColumn('data_aset', 'harga_perolehan')) {
                $table->decimal('harga_perolehan', 15, 2)->default(0.00)->after('harga_aset');
            }
            if (!Schema::hasColumn('data_aset', 'nilai_residu')) {
                $table->decimal('nilai_residu', 15, 2)->default(0.00)->after('harga_perolehan');
            }
            if (!Schema::hasColumn('data_aset', 'umur_manfaat')) {
                $table->integer('umur_manfaat')->default(0)->after('nilai_residu');
            }
            if (!Schema::hasColumn('data_aset', 'metode_penyusutan')) {
                $table->string('metode_penyusutan', 50)->default('Garis Lurus')->after('umur_manfaat');
            }
            if (!Schema::hasColumn('data_aset', 'tarif_penyusutan')) {
                $table->decimal('tarif_penyusutan', 5, 2)->default(0.00)->after('metode_penyusutan');
            }
            if (!Schema::hasColumn('data_aset', 'kode_akun_aset')) {
                $table->string('kode_akun_aset', 30)->nullable()->after('tarif_penyusutan');
            }
            if (!Schema::hasColumn('data_aset', 'kode_akun_akumulasi')) {
                $table->string('kode_akun_akumulasi', 30)->nullable()->after('kode_akun_aset');
            }
            if (!Schema::hasColumn('data_aset', 'kode_akun_beban')) {
                $table->string('kode_akun_beban', 30)->nullable()->after('kode_akun_akumulasi');
            }
            if (!Schema::hasColumn('data_aset', 'akumulasi_penyusutan')) {
                $table->decimal('akumulasi_penyusutan', 15, 2)->default(0.00)->after('kode_akun_beban');
            }
            if (!Schema::hasColumn('data_aset', 'nilai_buku')) {
                $table->decimal('nilai_buku', 15, 2)->default(0.00)->after('akumulasi_penyusutan');
            }
        });

        // Sinkronkan data nilai aset lama
        DB::statement("UPDATE `data_aset` SET `harga_perolehan` = `harga_aset`, `nilai_buku` = `harga_aset` WHERE `harga_perolehan` = 0 OR `nilai_buku` = 0");
        DB::statement("UPDATE `data_aset` SET `umur_manfaat` = 8, `tarif_penyusutan` = 12.50, `metode_penyusutan` = 'Garis Lurus', `kode_akun_aset` = '1201', `kode_akun_akumulasi` = '1202', `kode_akun_beban` = '6105' WHERE `kode_jenis_aset` = 'AST-TRK'");
        DB::statement("UPDATE `data_aset` SET `umur_manfaat` = 8, `tarif_penyusutan` = 12.50, `metode_penyusutan` = 'Garis Lurus', `kode_akun_aset` = '1203', `kode_akun_akumulasi` = '1208', `kode_akun_beban` = '6107' WHERE `kode_jenis_aset` = 'AST-GDG'");

        // 4. Buat Tabel data_kendaraan (Master Fisik Armada Ekspedisi Operasional)
        if (!Schema::hasTable('data_kendaraan')) {
            Schema::create('data_kendaraan', function (Blueprint $table) {
                $table->string('kode_kendaraan', 30)->primary();
                $table->string('kode_aset', 30)->nullable();
                $table->string('no_polisi', 20);
                $table->string('no_mesin', 50)->nullable();
                $table->string('no_rangka', 50)->nullable();
                $table->string('merek_kendaraan', 50)->nullable();
                $table->string('jenis_kendaraan', 50)->default('Colt Diesel Double');
                $table->string('tipe_armada', 50)->nullable();
                $table->string('muatan', 50)->default('200 Zak (8 Ton)');
                $table->year('tahun_pembuatan')->nullable();
                $table->date('tanggal_kir')->nullable();
                $table->date('tanggal_pajak')->nullable();
                $table->enum('status_kendaraan', ['aktif', 'rusak', 'dalam_perbaikan', 'non-aktif'])->default('aktif');
                $table->string('nama_pemilik', 100)->default('PT Pura Balkom Jaya Utama');
                $table->timestamp('dibuat_pada')->useCurrent();
                $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();

                $table->index('kode_aset', 'idx_kendaraan_aset');
                $table->index('no_polisi', 'idx_kendaraan_nopol');

                $table->foreign('kode_aset', 'fk_kendaraan_aset')
                    ->references('kode_aset')
                    ->on('data_aset')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }

        // Pindahkan armada truk yang ada di data_aset ke data_kendaraan
        $asetTruk = DB::table('data_aset')->where('kode_jenis_aset', 'AST-TRK')->get();
        $nomorUrut = 1;
        foreach ($asetTruk as $truk) {
            $kodeKnd = 'KND-' . str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
            $adaKnd = DB::table('data_kendaraan')->where('kode_kendaraan', $kodeKnd)->orWhere('kode_aset', $truk->kode_aset)->exists();
            if (!$adaKnd) {
                DB::table('data_kendaraan')->insert([
                    'kode_kendaraan'   => $kodeKnd,
                    'kode_aset'        => $truk->kode_aset,
                    'no_polisi'        => $truk->no_polisi ?? '-',
                    'no_mesin'         => $truk->no_mesin,
                    'no_rangka'        => $truk->no_rangka,
                    'merek_kendaraan'  => $truk->merek_aset,
                    'jenis_kendaraan'  => $truk->jenis_kendaraan ?? 'Colt Diesel Double',
                    'tipe_armada'      => $truk->jenis_kendaraan,
                    'muatan'           => $truk->muatan ?? '200 Zak (8 Ton)',
                    'tahun_pembuatan'  => $truk->tahun_pembuatan,
                    'tanggal_kir'      => $truk->tanggal_kir,
                    'tanggal_pajak'    => $truk->tanggal_pajak,
                    'status_kendaraan' => $truk->status_aset ?? 'aktif',
                    'nama_pemilik'     => $truk->nama_pemilik ?? 'PT Pura Balkom Jaya Utama',
                    'dibuat_pada'      => now(),
                    'diperbarui_pada'  => now(),
                ]);
            }
            $nomorUrut++;
        }

        // 5. Buat Tabel riwayat_penyusutan
        if (!Schema::hasTable('riwayat_penyusutan')) {
            Schema::create('riwayat_penyusutan', function (Blueprint $table) {
                $table->increments('id_penyusutan');
                $table->string('nomor_penyusutan', 50)->unique('uk_nomor_penyusutan');
                $table->string('kode_aset', 30);
                $table->date('tanggal_penyusutan');
                $table->unsignedTinyInteger('periode_bulan');
                $table->year('periode_tahun');
                $table->decimal('beban_penyusutan', 15, 2)->default(0.00);
                $table->decimal('akumulasi_penyusutan', 15, 2)->default(0.00);
                $table->decimal('nilai_buku', 15, 2)->default(0.00);
                $table->string('nomor_jurnal', 50)->nullable();
                $table->string('keterangan', 255)->nullable();
                $table->string('dibuat_oleh', 50)->default('spv_keuangan');
                $table->timestamp('dibuat_pada')->useCurrent();

                $table->index('kode_aset', 'idx_penyusutan_aset');
                $table->index(['periode_tahun', 'periode_bulan'], 'idx_penyusutan_periode');
                $table->index('nomor_jurnal', 'idx_penyusutan_jurnal');

                $table->foreign('kode_aset', 'fk_penyusutan_aset')
                    ->references('kode_aset')
                    ->on('data_aset')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');
            });
        }

        // 6. Alihkan relasi tabel pengiriman dan perbaikan_kendaraan ke data_kendaraan
        $trukPertama = DB::table('data_kendaraan')->first();
        $kodeTrukDefault = $trukPertama ? $trukPertama->kode_kendaraan : 'KND-001';

        // B. Update kolom kode_aset menjadi kode_kendaraan di pengiriman
        try {
            DB::statement("ALTER TABLE `pengiriman` DROP FOREIGN KEY `fk_kirim_aset`");
        } catch (\Exception $e) {}

        if (Schema::hasColumn('pengiriman', 'kode_aset') && !Schema::hasColumn('pengiriman', 'kode_kendaraan')) {
            DB::statement("ALTER TABLE `pengiriman` CHANGE COLUMN `kode_aset` `kode_kendaraan` VARCHAR(30) NOT NULL");
        }

        // Update nilai pengiriman agar merujuk ke kode_kendaraan yang valid
        DB::table('pengiriman')->update(['kode_kendaraan' => $kodeTrukDefault]);

        try {
            DB::statement("ALTER TABLE `pengiriman` ADD CONSTRAINT `fk_kirim_kendaraan` FOREIGN KEY (`kode_kendaraan`) REFERENCES `data_kendaraan` (`kode_kendaraan`) ON DELETE RESTRICT ON UPDATE CASCADE");
        } catch (\Exception $e) {}

        // C. Update kolom kode_aset menjadi kode_kendaraan di perbaikan_kendaraan
        try {
            DB::statement("ALTER TABLE `perbaikan_kendaraan` DROP FOREIGN KEY `fk_perbaikan_aset`");
        } catch (\Exception $e) {}

        if (Schema::hasColumn('perbaikan_kendaraan', 'kode_aset') && !Schema::hasColumn('perbaikan_kendaraan', 'kode_kendaraan')) {
            DB::statement("ALTER TABLE `perbaikan_kendaraan` CHANGE COLUMN `kode_aset` `kode_kendaraan` VARCHAR(30) NOT NULL");
        }

        // Update nilai perbaikan_kendaraan agar merujuk ke kode_kendaraan yang valid
        DB::table('perbaikan_kendaraan')->update(['kode_kendaraan' => $kodeTrukDefault]);

        try {
            DB::statement("ALTER TABLE `perbaikan_kendaraan` ADD CONSTRAINT `fk_perbaikan_kendaraan` FOREIGN KEY (`kode_kendaraan`) REFERENCES `data_kendaraan` (`kode_kendaraan`) ON DELETE RESTRICT ON UPDATE CASCADE");
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE `pengiriman` DROP FOREIGN KEY `fk_kirim_kendaraan`");
            DB::statement("ALTER TABLE `pengiriman` CHANGE COLUMN `kode_kendaraan` `kode_aset` VARCHAR(30) NOT NULL");
            DB::statement("ALTER TABLE `pengiriman` ADD CONSTRAINT `fk_kirim_aset` FOREIGN KEY (`kode_aset`) REFERENCES `data_aset` (`kode_aset`) ON DELETE RESTRICT ON UPDATE CASCADE");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE `perbaikan_kendaraan` DROP FOREIGN KEY `fk_perbaikan_kendaraan`");
            DB::statement("ALTER TABLE `perbaikan_kendaraan` CHANGE COLUMN `kode_kendaraan` `kode_aset` VARCHAR(30) NOT NULL");
            DB::statement("ALTER TABLE `perbaikan_kendaraan` ADD CONSTRAINT `fk_perbaikan_aset` FOREIGN KEY (`kode_aset`) REFERENCES `data_aset` (`kode_aset`) ON DELETE RESTRICT ON UPDATE CASCADE");
        } catch (\Exception $e) {}

        Schema::dropIfExists('riwayat_penyusutan');
        Schema::dropIfExists('data_kendaraan');
    }
};
