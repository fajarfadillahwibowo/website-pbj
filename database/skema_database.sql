-- =====================================================================
-- SKRIP SKEMA DATABASE SISTEM AKUNTANSI & OPERASIONAL PERUSAHAAN
-- Model: Strict Role-Based Access Control (RBAC) & Dashboard Ringkas
-- Ketentuan:
-- 1. Super Admin HANYA untuk kontrol akun, jabatan, dan hak akses (Isolasi Keamanan).
-- 2. Karyawan HANYA bisa melihat & mengedit modul sesuai jabatannya (Least Privilege).
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- 1. MODUL PENGGUNA, JABATAN & HAK AKSES SISTEM (RBAC)
-- =====================================================================

-- 1.1 Tabel Master Jabatan / Peran
DROP TABLE IF EXISTS `jabatan`;
CREATE TABLE `jabatan` (
    `id_jabatan` INT AUTO_INCREMENT NOT NULL,
    `kode_jabatan` VARCHAR(30) NOT NULL,
    `nama_jabatan` VARCHAR(100) NOT NULL,
    `deskripsi` VARCHAR(255) DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_jabatan`),
    UNIQUE KEY `uk_kode_jabatan` (`kode_jabatan`),
    UNIQUE KEY `uk_nama_jabatan` (`nama_jabatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.2 Tabel Master Modul (Fitur & Menu Sistem)
DROP TABLE IF EXISTS `modul`;
CREATE TABLE `modul` (
    `id_modul` INT AUTO_INCREMENT NOT NULL,
    `kode_modul` VARCHAR(50) NOT NULL,
    `nama_modul` VARCHAR(100) NOT NULL,
    `kategori_modul` ENUM('Admin Sistem', 'Master', 'Keuangan', 'Operasional', 'Logistik', 'Laporan') NOT NULL,
    `deskripsi` VARCHAR(255) DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_modul`),
    UNIQUE KEY `uk_kode_modul` (`kode_modul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.3 Tabel Relasi Hak Akses Jabatan (Matriks Hak Akses Granular)
DROP TABLE IF EXISTS `hak_akses_jabatan`;
CREATE TABLE `hak_akses_jabatan` (
    `id_hak_akses` INT AUTO_INCREMENT NOT NULL,
    `id_jabatan` INT NOT NULL,
    `id_modul` INT NOT NULL,
    `boleh_lihat` BOOLEAN NOT NULL DEFAULT FALSE,
    `boleh_tambah` BOOLEAN NOT NULL DEFAULT FALSE,
    `boleh_edit` BOOLEAN NOT NULL DEFAULT FALSE,
    `boleh_hapus` BOOLEAN NOT NULL DEFAULT FALSE,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_hak_akses`),
    UNIQUE KEY `uk_jabatan_modul` (`id_jabatan`, `id_modul`),
    KEY `idx_hak_akses_modul` (`id_modul`),
    CONSTRAINT `fk_hak_akses_jabatan` FOREIGN KEY (`id_jabatan`) 
        REFERENCES `jabatan` (`id_jabatan`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_hak_akses_modul` FOREIGN KEY (`id_modul`) 
        REFERENCES `modul` (`id_modul`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.4 Tabel Super Account (Khusus Kontrol Pengguna & Hak Akses)
DROP TABLE IF EXISTS `super_account`;
CREATE TABLE `super_account` (
    `id_super_account` INT AUTO_INCREMENT NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `nama_pemilik` VARCHAR(100) NOT NULL,
    `tanggal_create` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_super_account`),
    UNIQUE KEY `uk_super_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. MODUL MASTER KARYAWAN
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `data_karyawan`;
CREATE TABLE `data_karyawan` (
    `kode_karyawan` VARCHAR(30) NOT NULL,
    `nama_karyawan` VARCHAR(100) NOT NULL,
    `id_jabatan` INT NOT NULL,
    `kategori_karyawan` ENUM('staf', 'driver', 'teknisi', 'gudang', 'manajemen') NOT NULL DEFAULT 'staf',
    `no_identitas` VARCHAR(30) NOT NULL,
    `alamat` TEXT NOT NULL,
    `no_hp` VARCHAR(25) NOT NULL,
    `foto_ktp` VARCHAR(255) DEFAULT NULL,
    `file_kontrak` VARCHAR(255) DEFAULT NULL,
    `status_karyawan` ENUM('aktif', 'kontrak', 'tetap', 'non-aktif', 'berhenti') NOT NULL DEFAULT 'aktif',
    `tanggal_mulai_kerja` DATE DEFAULT NULL,
    `tanggal_berhenti` DATE DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_karyawan`),
    KEY `idx_karyawan_jabatan` (`id_jabatan`),
    CONSTRAINT `fk_karyawan_jabatan` FOREIGN KEY (`id_jabatan`) 
        REFERENCES `jabatan` (`id_jabatan`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.5 Tabel Account (Akun Login Staf Karyawan)
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
    `id_account` INT AUTO_INCREMENT NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `kode_karyawan` VARCHAR(30) NOT NULL,
    `id_jabatan` INT NOT NULL,
    `status_aktif` BOOLEAN NOT NULL DEFAULT TRUE,
    `tanggal_create` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_account`),
    UNIQUE KEY `uk_account_username` (`username`),
    KEY `idx_account_karyawan` (`kode_karyawan`),
    KEY `idx_account_jabatan` (`id_jabatan`),
    CONSTRAINT `fk_account_karyawan` FOREIGN KEY (`kode_karyawan`) 
        REFERENCES `data_karyawan` (`kode_karyawan`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_account_jabatan` FOREIGN KEY (`id_jabatan`) 
        REFERENCES `jabatan` (`id_jabatan`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.6 Tabel Riwayat Login
DROP TABLE IF EXISTS `riwayat_login`;
CREATE TABLE `riwayat_login` (
    `id_login` BIGINT AUTO_INCREMENT NOT NULL,
    `username` VARCHAR(50) NOT NULL,
    `jabatan` VARCHAR(100) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `perangkat` VARCHAR(255) DEFAULT NULL,
    `waktu_login` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status_login` ENUM('sukses', 'gagal') NOT NULL DEFAULT 'sukses',
    PRIMARY KEY (`id_login`),
    KEY `idx_login_username` (`username`),
    KEY `idx_login_waktu` (`waktu_login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 3. MODUL MASTER BISNIS & DISTRIBUSI
-- =====================================================================

-- 3.1 Master Wilayah
DROP TABLE IF EXISTS `data_wilayah`;
CREATE TABLE `data_wilayah` (
    `kode_wilayah` VARCHAR(30) NOT NULL,
    `nama_wilayah` VARCHAR(100) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_wilayah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2 Master Customer (Toko Bangunan)
DROP TABLE IF EXISTS `data_customer`;
CREATE TABLE `data_customer` (
    `kode_customer` VARCHAR(30) NOT NULL,
    `kode_wilayah` VARCHAR(30) NOT NULL,
    `nama_toko_bangunan` VARCHAR(150) NOT NULL,
    `nama_pemilik` VARCHAR(100) NOT NULL,
    `alamat` TEXT NOT NULL,
    `no_hp` VARCHAR(25) NOT NULL,
    `no_ktp` VARCHAR(30) DEFAULT NULL,
    `foto_ktp` VARCHAR(255) DEFAULT NULL,
    `plafon_piutang` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `saldo_piutang` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `saldo_deposit` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_customer`),
    KEY `idx_customer_wilayah` (`kode_wilayah`),
    CONSTRAINT `fk_customer_wilayah` FOREIGN KEY (`kode_wilayah`) 
        REFERENCES `data_wilayah` (`kode_wilayah`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.2.1 Master Toko Bangunan & Proyek Cabang
DROP TABLE IF EXISTS `data_toko_bangunan`;
CREATE TABLE `data_toko_bangunan` (
    `kode_toko` VARCHAR(30) NOT NULL,
    `kode_customer` VARCHAR(30) NOT NULL,
    `kode_wilayah` VARCHAR(30) NOT NULL,
    `nama_toko_bangunan` VARCHAR(150) NOT NULL,
    `tipe_lokasi` VARCHAR(50) NOT NULL DEFAULT 'toko_retail',
    `penanggung_jawab` VARCHAR(100) NOT NULL DEFAULT '-',
    `no_hp_toko` VARCHAR(25) NOT NULL DEFAULT '-',
    `alamat_lengkap` TEXT NOT NULL,
    `titik_koordinat` VARCHAR(100) DEFAULT NULL,
    `status_toko` VARCHAR(30) NOT NULL DEFAULT 'aktif',
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_toko`),
    KEY `idx_toko_customer` (`kode_customer`),
    KEY `idx_toko_wilayah` (`kode_wilayah`),
    CONSTRAINT `fk_toko_customer` FOREIGN KEY (`kode_customer`) 
        REFERENCES `data_customer` (`kode_customer`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_toko_wilayah` FOREIGN KEY (`kode_wilayah`) 
        REFERENCES `data_wilayah` (`kode_wilayah`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.3 Master Data Semen / Barang
DROP TABLE IF EXISTS `data_semen`;
CREATE TABLE `data_semen` (
    `kode_barang` VARCHAR(30) NOT NULL,
    `nama_barang` VARCHAR(150) NOT NULL,
    `jenis_barang` VARCHAR(50) NOT NULL,
    `satuan_barang` VARCHAR(20) NOT NULL DEFAULT 'Zak',
    `harga_pokok` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `harga_jual_standar` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.4 Master Gudang & List Gudang SO
DROP TABLE IF EXISTS `list_gudang_so`;
CREATE TABLE `list_gudang_so` (
    `kode_gudang` VARCHAR(30) NOT NULL,
    `nama_gudang` VARCHAR(100) NOT NULL,
    `jenis_gudang` VARCHAR(50) NOT NULL,
    `kode_barang` VARCHAR(30) NOT NULL,
    `plant` VARCHAR(50) NOT NULL,
    `harga_barang` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `stok_tersedia` INT NOT NULL DEFAULT 0,
    `distrik` VARCHAR(50) NOT NULL,
    `sub_distrik` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_gudang`),
    KEY `idx_gudang_barang` (`kode_barang`),
    CONSTRAINT `fk_gudang_barang` FOREIGN KEY (`kode_barang`) 
        REFERENCES `data_semen` (`kode_barang`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.5 Master Rekening Bank Perusahaan
DROP TABLE IF EXISTS `data_rekening`;
CREATE TABLE `data_rekening` (
    `id_rekening` INT AUTO_INCREMENT NOT NULL,
    `nomor_rekening` VARCHAR(50) NOT NULL,
    `nama_bank` VARCHAR(50) NOT NULL,
    `atas_nama` VARCHAR(100) NOT NULL,
    `saldo_rekening` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_rekening`),
    UNIQUE KEY `uk_nomor_rekening` (`nomor_rekening`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.6 Master Data KSO (Kerja Sama Operasional)
DROP TABLE IF EXISTS `data_kso`;
CREATE TABLE `data_kso` (
    `kode_kso` VARCHAR(30) NOT NULL,
    `nama_kso` VARCHAR(100) NOT NULL,
    `file_kontrak_kso` VARCHAR(255) DEFAULT NULL,
    `status_kso` ENUM('Aktif', 'Selesai', 'Ditangguhkan') NOT NULL DEFAULT 'Aktif',
    `pihak_mitra` VARCHAR(100) NOT NULL,
    `tanggal_mulai` DATE NOT NULL,
    `tanggal_selesai` DATE NOT NULL,
    `nilai_kontrak` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_kso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.6.1 Master Tarif Ongkos KSO
DROP TABLE IF EXISTS `ongkos_kso`;
CREATE TABLE `ongkos_kso` (
    `kode_oa` VARCHAR(30) NOT NULL,
    `kode_kso` VARCHAR(30) NOT NULL,
    `nama_oa` VARCHAR(100) NOT NULL,
    `muatan` VARCHAR(50) NOT NULL,
    `ongkos_angkut` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_oa`),
    KEY `idx_ongkos_kso` (`kode_kso`),
    CONSTRAINT `fk_ongkos_kso` FOREIGN KEY (`kode_kso`) 
        REFERENCES `data_kso` (`kode_kso`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3.7 Master Ongkos Angkut
DROP TABLE IF EXISTS `data_ongkos_angkut`;
CREATE TABLE `data_ongkos_angkut` (
    `id_ongkos` INT AUTO_INCREMENT NOT NULL,
    `kode_oa` VARCHAR(30) NOT NULL,
    `nama_oa` VARCHAR(150) NOT NULL,
    `kode_gudang` VARCHAR(30) DEFAULT NULL,
    `kontrak_oa` VARCHAR(100) DEFAULT NULL,
    `muatan_oa` VARCHAR(100) NOT NULL DEFAULT 'Semen Zak 50kg',
    `harga_oa` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `harga_kso` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `harga_kso_khusus` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `wilayah_oa` VARCHAR(100) NOT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_pada` DATETIME DEFAULT NULL,
    `diperbarui_pada` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id_ongkos`),
    UNIQUE KEY `uk_kode_oa` (`kode_oa`),
    KEY `idx_ongkos_gudang` (`kode_gudang`),
    KEY `idx_ongkos_wilayah` (`wilayah_oa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 4. MODUL AKUNTANSI & KEUANGAN
-- =====================================================================

-- 4.1 Master Bagan Akun / Chart of Accounts (COA)
DROP TABLE IF EXISTS `data_kode_akun`;
CREATE TABLE `data_kode_akun` (
    `kode_akun` VARCHAR(30) NOT NULL,
    `nama_akun` VARCHAR(100) NOT NULL,
    `tipe_akun` ENUM('Aktiva Lancar', 'Aktiva Tetap', 'Kewajiban Lancar', 'Kewajiban Jangka Panjang', 'Modal', 'Pendapatan', 'Harga Pokok Penjualan', 'Beban Operasional', 'Beban Lain-Lain', 'Pendapatan Lain-Lain') NOT NULL,
    `kelompok_akun` VARCHAR(50) NOT NULL,
    `saldo_normal` ENUM('Debit', 'Kredit') NOT NULL DEFAULT 'Debit',
    `saldo_awal` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `saldo_berjalan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.2 Jurnal Umum Akuntansi
DROP TABLE IF EXISTS `jurnal_umum`;
CREATE TABLE `jurnal_umum` (
    `id_jurnal` BIGINT AUTO_INCREMENT NOT NULL,
    `nomor_jurnal` VARCHAR(50) NOT NULL,
    `tanggal_transaksi` DATE NOT NULL,
    `kode_akun` VARCHAR(30) NOT NULL,
    `posisi` ENUM('Debit', 'Kredit') NOT NULL,
    `nominal` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `keterangan` VARCHAR(255) NOT NULL,
    `referensi_transaksi` VARCHAR(50) DEFAULT NULL,
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_jurnal`),
    KEY `idx_jurnal_nomor` (`nomor_jurnal`),
    KEY `idx_jurnal_akun` (`kode_akun`),
    KEY `idx_jurnal_tanggal` (`tanggal_transaksi`),
    CONSTRAINT `fk_jurnal_akun` FOREIGN KEY (`kode_akun`) 
        REFERENCES `data_kode_akun` (`kode_akun`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.3 Transaksi Penjualan
DROP TABLE IF EXISTS `penjualan`;
CREATE TABLE `penjualan` (
    `id_penjualan` INT AUTO_INCREMENT NOT NULL,
    `nomor_faktur` VARCHAR(50) NOT NULL,
    `tanggal_penjualan` DATE NOT NULL,
    `kode_customer` VARCHAR(30) NOT NULL,
    `kode_toko` VARCHAR(30) DEFAULT NULL,
    `kode_barang` VARCHAR(30) DEFAULT NULL,
    `nama_barang` VARCHAR(150) DEFAULT NULL,
    `satuan_barang` VARCHAR(30) DEFAULT 'Zak',
    `jumlah_zak` INT NOT NULL DEFAULT 0,
    `harga_satuan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `metode_pembayaran` ENUM('Tunai', 'Transfer', 'Kredit / Piutang', 'Potong Deposit') NOT NULL,
    `total_bruto` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `diskon` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `total_netto` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `jumlah_dibayar` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `sisa_piutang` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `status_pembayaran` ENUM('Lunas', 'Belum Lunas', 'Jatuh Tempo') NOT NULL DEFAULT 'Belum Lunas',
    `jatuh_tempo` DATE DEFAULT NULL,
    `id_rekening` INT DEFAULT NULL,
    `status_persetujuan` ENUM('draft', 'disetujui', 'terkunci') NOT NULL DEFAULT 'draft',
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_penjualan`),
    UNIQUE KEY `uk_nomor_faktur` (`nomor_faktur`),
    KEY `idx_penjualan_customer` (`kode_customer`),
    KEY `idx_penjualan_toko` (`kode_toko`),
    CONSTRAINT `fk_penjualan_customer` FOREIGN KEY (`kode_customer`) 
        REFERENCES `data_customer` (`kode_customer`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_penjualan_toko` FOREIGN KEY (`kode_toko`) 
        REFERENCES `data_toko_bangunan` (`kode_toko`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_penjualan_rekening` FOREIGN KEY (`id_rekening`) 
        REFERENCES `data_rekening` (`id_rekening`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.4 List Piutang Pelanggan (AR)
DROP TABLE IF EXISTS `list_piutang`;
CREATE TABLE `list_piutang` (
    `id_piutang` INT AUTO_INCREMENT NOT NULL,
    `id_penjualan` INT NOT NULL,
    `kode_customer` VARCHAR(30) NOT NULL,
    `jumlah_piutang` DECIMAL(15, 2) NOT NULL,
    `sisa_piutang` DECIMAL(15, 2) NOT NULL,
    `tanggal_terbit` DATE NOT NULL,
    `tanggal_jatuh_tempo` DATE NOT NULL,
    `status_piutang` ENUM('belum_lunas', 'sebagian', 'lunas', 'macet') NOT NULL DEFAULT 'belum_lunas',
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_piutang`),
    KEY `idx_piutang_penjualan` (`id_penjualan`),
    KEY `idx_piutang_customer` (`kode_customer`),
    CONSTRAINT `fk_piutang_penjualan` FOREIGN KEY (`id_penjualan`) 
        REFERENCES `penjualan` (`id_penjualan`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_piutang_customer` FOREIGN KEY (`kode_customer`) 
        REFERENCES `data_customer` (`kode_customer`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.5 List Deposit Pelanggan
DROP TABLE IF EXISTS `list_deposit`;
CREATE TABLE `list_deposit` (
    `id_deposit` INT AUTO_INCREMENT NOT NULL,
    `nomor_bukti_deposit` VARCHAR(50) NOT NULL,
    `kode_customer` VARCHAR(30) NOT NULL,
    `tanggal_deposit` DATE NOT NULL,
    `tipe_mutasi` ENUM('Masuk', 'Keluar / Terpakai') NOT NULL,
    `jumlah_nominal` DECIMAL(15, 2) NOT NULL,
    `saldo_akhir_deposit` DECIMAL(15, 2) NOT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_deposit`),
    UNIQUE KEY `uk_bukti_deposit` (`nomor_bukti_deposit`),
    KEY `idx_deposit_customer` (`kode_customer`),
    CONSTRAINT `fk_deposit_customer` FOREIGN KEY (`kode_customer`) 
        REFERENCES `data_customer` (`kode_customer`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.6 Transaksi Pengeluaran Kas/Bank (AP)
DROP TABLE IF EXISTS `pengeluaran`;
CREATE TABLE `pengeluaran` (
    `id_pengeluaran` INT AUTO_INCREMENT NOT NULL,
    `nomor_pengeluaran` VARCHAR(50) NOT NULL,
    `tanggal_pengeluaran` DATE NOT NULL,
    `kategori_pengeluaran` VARCHAR(100) NOT NULL,
    `kode_akun` VARCHAR(30) NOT NULL,
    `total_nominal` DECIMAL(15, 2) NOT NULL,
    `id_rekening_sumber` INT DEFAULT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `status_persetujuan` ENUM('draft', 'disetujui_spv', 'ditolak') NOT NULL DEFAULT 'draft',
    `disetujui_oleh` VARCHAR(50) DEFAULT NULL,
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_pengeluaran`),
    UNIQUE KEY `uk_nomor_pengeluaran` (`nomor_pengeluaran`),
    KEY `idx_pengeluaran_akun` (`kode_akun`),
    CONSTRAINT `fk_pengeluaran_akun` FOREIGN KEY (`kode_akun`) 
        REFERENCES `data_kode_akun` (`kode_akun`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_pengeluaran_rekening` FOREIGN KEY (`id_rekening_sumber`) 
        REFERENCES `data_rekening` (`id_rekening`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 5. MODUL OPERASIONAL, PENJUALAN SO, LOGISTIK & OPNAME GUDANG
-- =====================================================================

-- 5.1 Pembelian SO (Sales Order)
DROP TABLE IF EXISTS `pembelian_so`;
CREATE TABLE `pembelian_so` (
    `id_so` INT AUTO_INCREMENT NOT NULL,
    `nomor_so` VARCHAR(50) NOT NULL,
    `nomor_lo` VARCHAR(50) DEFAULT NULL,
    `tanggal_so` DATE NOT NULL,
    `kode_customer` VARCHAR(30) NOT NULL,
    `kode_gudang` VARCHAR(30) NOT NULL,
    `jenis_pengiriman` VARCHAR(10) NOT NULL DEFAULT 'FRC',
    `jumlah_zak` INT NOT NULL DEFAULT 0,
    `qty_pengambilan` INT NOT NULL DEFAULT 0,
    `harga_satuan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `total_harga` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `status_so` ENUM('draft', 'disetujui', 'diproses', 'dikirim', 'selesai', 'batal') NOT NULL DEFAULT 'draft',
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_so`),
    UNIQUE KEY `uk_nomor_so` (`nomor_so`),
    KEY `idx_so_customer` (`kode_customer`),
    KEY `idx_so_gudang` (`kode_gudang`),
    CONSTRAINT `fk_so_customer` FOREIGN KEY (`kode_customer`) 
        REFERENCES `data_customer` (`kode_customer`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_so_gudang` FOREIGN KEY (`kode_gudang`) 
        REFERENCES `list_gudang_so` (`kode_gudang`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5.2 Stok Opname Gudang
DROP TABLE IF EXISTS `opname_gudang`;
CREATE TABLE `opname_gudang` (
    `id_opname` INT AUTO_INCREMENT NOT NULL,
    `nomor_opname` VARCHAR(50) NOT NULL,
    `kode_gudang` VARCHAR(30) NOT NULL,
    `tanggal_opname` DATE NOT NULL,
    `stok_sistem` INT NOT NULL DEFAULT 0,
    `stok_fisik` INT NOT NULL DEFAULT 0,
    `selisih` INT NOT NULL DEFAULT 0,
    `keterangan_selisih` TEXT DEFAULT NULL,
    `status_konfirmasi` ENUM('draft', 'dikonfirmasi_spv') NOT NULL DEFAULT 'draft',
    `petugas_opname` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_opname`),
    UNIQUE KEY `uk_nomor_opname` (`nomor_opname`),
    KEY `idx_opname_gudang` (`kode_gudang`),
    CONSTRAINT `fk_opname_gudang` FOREIGN KEY (`kode_gudang`) 
        REFERENCES `list_gudang_so` (`kode_gudang`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 6. MODUL ASET, ARMADA KENDARAAN, SPAREPART & PERBAIKAN
-- =====================================================================

-- 6.1 Master Jenis Aset
DROP TABLE IF EXISTS `data_jenis_aset`;
CREATE TABLE `data_jenis_aset` (
    `kode_jenis_aset` VARCHAR(30) NOT NULL,
    `jenis_aset` VARCHAR(100) NOT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_jenis_aset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `data_jenis_aset` (`kode_jenis_aset`, `jenis_aset`, `keterangan`) VALUES
('AST-TRK', 'Kendaraan Armada Truk', 'Truk tronton, fuso, colt diesel, dan kendaraan logistik'),
('AST-TNH', 'Tanah & Bangunan Properti', 'Tanah kavling, bangunan gedung kantor, gudang semen, pos satpam, dan fasilitas properti'),
('AST-GDG', 'Mesin & Alat Berat Gudang', 'Forklift, genset gudang, conveyor, timbangan truk'),
('AST-OFC', 'Elektronik & Perabot Kantor', 'Laptop, PC komputer, printer, AC, meja kursi kantor')
ON DUPLICATE KEY UPDATE `jenis_aset` = VALUES(`jenis_aset`), `keterangan` = VALUES(`keterangan`);

-- 6.2 Master Data Aset Tetap Perusahaan (Finansial & Depresiasi PSAK 16)
DROP TABLE IF EXISTS `data_aset`;
CREATE TABLE `data_aset` (
    `kode_aset` VARCHAR(30) NOT NULL,
    `kode_jenis_aset` VARCHAR(30) NOT NULL,
    `nama_aset` VARCHAR(100) NOT NULL,
    `tanggal_pembelian` DATE NOT NULL,
    `harga_aset` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `harga_perolehan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `nilai_residu` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `umur_manfaat` INT NOT NULL DEFAULT 0,
    `metode_penyusutan` VARCHAR(50) NOT NULL DEFAULT 'Garis Lurus',
    `tarif_penyusutan` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
    `kode_akun_aset` VARCHAR(30) DEFAULT NULL,
    `kode_akun_akumulasi` VARCHAR(30) DEFAULT NULL,
    `kode_akun_beban` VARCHAR(30) DEFAULT NULL,
    `akumulasi_penyusutan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `nilai_buku` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `no_polisi` VARCHAR(20) DEFAULT NULL,
    `no_mesin` VARCHAR(50) DEFAULT NULL,
    `no_rangka` VARCHAR(50) DEFAULT NULL,
    `merek_aset` VARCHAR(50) DEFAULT NULL,
    `muatan` VARCHAR(50) DEFAULT NULL,
    `jenis_kendaraan` VARCHAR(50) DEFAULT NULL,
    `tahun_pembuatan` YEAR DEFAULT NULL,
    `tanggal_kir` DATE DEFAULT NULL,
    `tanggal_pajak` DATE DEFAULT NULL,
    `status_aset` ENUM('aktif', 'rusak', 'dalam_perbaikan', 'dijual', 'non-aktif') NOT NULL DEFAULT 'aktif',
    `nama_pemilik` VARCHAR(100) NOT NULL,
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_aset`),
    KEY `idx_aset_jenis` (`kode_jenis_aset`),
    KEY `idx_aset_akun_aset` (`kode_akun_aset`),
    KEY `idx_aset_akun_akumulasi` (`kode_akun_akumulasi`),
    KEY `idx_aset_akun_beban` (`kode_akun_beban`),
    CONSTRAINT `fk_aset_jenis` FOREIGN KEY (`kode_jenis_aset`) 
        REFERENCES `data_jenis_aset` (`kode_jenis_aset`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_aset_akun_aset` FOREIGN KEY (`kode_akun_aset`) 
        REFERENCES `data_kode_akun` (`kode_akun`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_aset_akun_akumulasi` FOREIGN KEY (`kode_akun_akumulasi`) 
        REFERENCES `data_kode_akun` (`kode_akun`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_aset_akun_beban` FOREIGN KEY (`kode_akun_beban`) 
        REFERENCES `data_kode_akun` (`kode_akun`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.3 Master Armada Kendaraan Operasional (Terpisah dari Aset Akuntansi)
DROP TABLE IF EXISTS `data_kendaraan`;
CREATE TABLE `data_kendaraan` (
    `kode_kendaraan` VARCHAR(30) NOT NULL,
    `kode_aset` VARCHAR(30) DEFAULT NULL,
    `no_polisi` VARCHAR(20) NOT NULL,
    `no_mesin` VARCHAR(50) DEFAULT NULL,
    `no_rangka` VARCHAR(50) DEFAULT NULL,
    `merek_kendaraan` VARCHAR(50) NOT NULL,
    `jenis_kendaraan` VARCHAR(50) NOT NULL DEFAULT 'Colt Diesel Double',
    `tipe_armada` VARCHAR(50) DEFAULT NULL,
    `muatan` VARCHAR(50) NOT NULL DEFAULT '8-10 Ton',
    `tahun_pembuatan` YEAR NOT NULL,
    `tanggal_kir` DATE DEFAULT NULL,
    `tanggal_pajak` DATE DEFAULT NULL,
    `status_kendaraan` ENUM('aktif', 'rusak', 'dalam_perbaikan', 'non-aktif') NOT NULL DEFAULT 'aktif',
    `nama_pemilik` VARCHAR(100) NOT NULL DEFAULT 'PT Pura Barutama',
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_kendaraan`),
    UNIQUE KEY `uk_kendaraan_nopol` (`no_polisi`),
    KEY `idx_kendaraan_aset` (`kode_aset`),
    CONSTRAINT `fk_kendaraan_aset` FOREIGN KEY (`kode_aset`) 
        REFERENCES `data_aset` (`kode_aset`) 
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.4 Riwayat Penyusutan Aset Tetap Bulanan
DROP TABLE IF EXISTS `riwayat_penyusutan`;
CREATE TABLE `riwayat_penyusutan` (
    `id_penyusutan` BIGINT AUTO_INCREMENT NOT NULL,
    `nomor_penyusutan` VARCHAR(50) NOT NULL,
    `kode_aset` VARCHAR(30) NOT NULL,
    `tanggal_penyusutan` DATE NOT NULL,
    `periode_bulan` INT NOT NULL,
    `periode_tahun` INT NOT NULL,
    `beban_penyusutan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `akumulasi_penyusutan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `nilai_buku` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `nomor_jurnal` VARCHAR(50) DEFAULT NULL,
    `keterangan` VARCHAR(255) DEFAULT NULL,
    `dibuat_oleh` VARCHAR(50) NOT NULL DEFAULT 'SPV Keuangan',
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_penyusutan`),
    UNIQUE KEY `uk_susut_nomor` (`nomor_penyusutan`),
    KEY `idx_susut_aset` (`kode_aset`),
    KEY `idx_susut_periode` (`periode_bulan`, `periode_tahun`),
    KEY `idx_susut_nomor_jurnal` (`nomor_jurnal`),
    CONSTRAINT `fk_susut_aset` FOREIGN KEY (`kode_aset`) 
        REFERENCES `data_aset` (`kode_aset`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.5 Master List Sparepart
DROP TABLE IF EXISTS `list_sparepart`;
CREATE TABLE `list_sparepart` (
    `kode_sparepart` VARCHAR(30) NOT NULL,
    `nama_sparepart` VARCHAR(100) NOT NULL,
    `kategori_part` VARCHAR(50) NOT NULL,
    `stok_part` INT NOT NULL DEFAULT 0,
    `satuan` VARCHAR(20) NOT NULL DEFAULT 'Pcs',
    `harga_satuan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kode_sparepart`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.6 Pembelian Sparepart
DROP TABLE IF EXISTS `pembelian_sparepart`;
CREATE TABLE `pembelian_sparepart` (
    `id_pembelian_part` INT AUTO_INCREMENT NOT NULL,
    `nomor_faktur_beli` VARCHAR(50) NOT NULL,
    `kode_sparepart` VARCHAR(30) NOT NULL,
    `tanggal_beli` DATE NOT NULL,
    `nama_supplier` VARCHAR(100) NOT NULL,
    `jumlah_beli` INT NOT NULL,
    `harga_beli` DECIMAL(15, 2) NOT NULL,
    `total_bayar` DECIMAL(15, 2) NOT NULL,
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_pembelian_part`),
    KEY `idx_beli_sparepart` (`kode_sparepart`),
    CONSTRAINT `fk_beli_sparepart` FOREIGN KEY (`kode_sparepart`) 
        REFERENCES `list_sparepart` (`kode_sparepart`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6.7 Perbaikan Kendaraan (SPK Bengkel Armada)
DROP TABLE IF EXISTS `perbaikan_kendaraan`;
CREATE TABLE `perbaikan_kendaraan` (
    `id_perbaikan` INT AUTO_INCREMENT NOT NULL,
    `nomor_spk_perbaikan` VARCHAR(50) NOT NULL,
    `kode_kendaraan` VARCHAR(30) NOT NULL,
    `tanggal_masuk` DATE NOT NULL,
    `tanggal_selesai` DATE DEFAULT NULL,
    `keluhan_kerusakan` TEXT NOT NULL,
    `tindakan_perbaikan` TEXT DEFAULT NULL,
    `biaya_jasa` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `biaya_sparepart` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `total_biaya` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `bengkel_pelaksana` VARCHAR(100) DEFAULT NULL,
    `status_perbaikan` VARCHAR(50) NOT NULL DEFAULT 'Dalam Proses',
    `pengawas_kendaraan` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_perbaikan`),
    UNIQUE KEY `uk_nomor_spk` (`nomor_spk_perbaikan`),
    KEY `idx_perbaikan_kendaraan` (`kode_kendaraan`),
    CONSTRAINT `fk_perbaikan_kendaraan` FOREIGN KEY (`kode_kendaraan`) 
        REFERENCES `data_kendaraan` (`kode_kendaraan`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 7. MODUL PENGIRIMAN & RILISAN (DISPATCHER & OPERASIONAL)
-- =====================================================================

-- 7.1 Pengiriman SO (Surat Jalan Distribusi)
DROP TABLE IF EXISTS `pengiriman`;
CREATE TABLE `pengiriman` (
    `id_pengiriman` INT AUTO_INCREMENT NOT NULL,
    `nomor_surat_jalan` VARCHAR(50) NOT NULL,
    `id_so` INT NOT NULL,
    `kode_kendaraan` VARCHAR(30) NOT NULL,
    `kode_driver` VARCHAR(30) NOT NULL,
    `tanggal_kirim` DATETIME NOT NULL,
    `status_pengiriman` ENUM('menunggu', 'dalam_perjalanan', 'terkirim', 'retur') NOT NULL DEFAULT 'menunggu',
    `keterangan` TEXT DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_pengiriman`),
    UNIQUE KEY `uk_nomor_surat_jalan` (`nomor_surat_jalan`),
    KEY `idx_kirim_so` (`id_so`),
    KEY `idx_kirim_kendaraan` (`kode_kendaraan`),
    KEY `idx_kirim_driver` (`kode_driver`),
    CONSTRAINT `fk_kirim_so` FOREIGN KEY (`id_so`) 
        REFERENCES `pembelian_so` (`id_so`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_kirim_kendaraan` FOREIGN KEY (`kode_kendaraan`) 
        REFERENCES `data_kendaraan` (`kode_kendaraan`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_kirim_driver` FOREIGN KEY (`kode_driver`) 
        REFERENCES `data_karyawan` (`kode_karyawan`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7.2 Rilisan (Berita Acara Penerimaan Barang)
DROP TABLE IF EXISTS `rilisan`;
CREATE TABLE `rilisan` (
    `id_rilisan` INT AUTO_INCREMENT NOT NULL,
    `nomor_rilisan` VARCHAR(50) NOT NULL,
    `id_pengiriman` INT NOT NULL,
    `tanggal_rilis` DATETIME NOT NULL,
    `nama_penerima` VARCHAR(100) NOT NULL,
    `foto_bukti_terima` VARCHAR(255) DEFAULT NULL,
    `catatan_rilis` TEXT DEFAULT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_rilisan`),
    UNIQUE KEY `uk_nomor_rilisan` (`nomor_rilisan`),
    KEY `idx_rilisan_pengiriman` (`id_pengiriman`),
    CONSTRAINT `fk_rilisan_pengiriman` FOREIGN KEY (`id_pengiriman`) 
        REFERENCES `pengiriman` (`id_pengiriman`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- 8. VIEW LAPORAN AKUNTANSI (DIREKTUR & MANAGER)
-- =====================================================================

-- 8.1 View Laporan Laba Rugi
CREATE OR REPLACE VIEW `tampilan_laporan_laba_rugi` AS
SELECT 
    dka.kelompok_akun,
    dka.tipe_akun,
    dka.kode_akun,
    dka.nama_akun,
    SUM(CASE WHEN ju.posisi = 'Kredit' THEN ju.nominal ELSE -ju.nominal END) AS total_nominal
FROM data_kode_akun dka
LEFT JOIN jurnal_umum ju ON dka.kode_akun = ju.kode_akun
WHERE dka.tipe_akun IN ('Pendapatan', 'Harga Pokok Penjualan', 'Beban Operasional', 'Beban Lain-Lain', 'Pendapatan Lain-Lain')
GROUP BY dka.kode_akun, dka.nama_akun, dka.tipe_akun, dka.kelompok_akun;

-- 8.2 View Laporan Neraca
CREATE OR REPLACE VIEW `tampilan_laporan_neraca` AS
SELECT 
    dka.kelompok_akun,
    dka.tipe_akun,
    dka.kode_akun,
    dka.nama_akun,
    SUM(CASE WHEN ju.posisi = 'Debit' THEN ju.nominal ELSE -ju.nominal END) AS total_saldo
FROM data_kode_akun dka
LEFT JOIN jurnal_umum ju ON dka.kode_akun = ju.kode_akun
WHERE dka.tipe_akun IN ('Aktiva Lancar', 'Aktiva Tetap', 'Kewajiban Lancar', 'Kewajiban Jangka Panjang', 'Modal')
GROUP BY dka.kode_akun, dka.nama_akun, dka.tipe_akun, dka.kelompok_akun;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- DATA AWAL (SEED DATA: 9 JABATAN + SUPER ADMIN RBAC RINGKAS)
-- =====================================================================

-- 1. Master Jabatan Sesuai Diagram
INSERT INTO `jabatan` (`id_jabatan`, `kode_jabatan`, `nama_jabatan`, `deskripsi`) VALUES
(1, 'SPV_KEUANGAN', 'SPV Keuangan', 'Supervisor Keuangan & Akuntansi'),
(2, 'STAFF_AR', 'Staff AR', 'Staf Piutang Penjualan (Accounts Receivable)'),
(3, 'STAFF_AP', 'Staff AP', 'Staf Pengeluaran & Pembelian SO (Accounts Payable)'),
(4, 'DISPATCHER', 'Dispatcher', 'Pengatur Armada & Surat Jalan Pengiriman'),
(5, 'PENGAWAS_DRIVER', 'Pengawas Driver', 'Pengawas Operasional Driver'),
(6, 'SPV_GUDANG', 'SPV Gudang', 'Supervisor Gudang & Stok'),
(7, 'DIREKTUR_MANAGER', 'Direktur & Manager', 'Pimpinan Eksekutif Pemantau Laporan Neraca & Laba Rugi'),
(8, 'SPV_OPERASIONAL', 'SPV Operasional', 'Supervisor Operasional Lapangan, Ongkos Angkut, Driver & KSO'),
(9, 'PENGAWAS_KENDARAAN', 'Pengawas Kendaraan', 'Pengawas Perbaikan Armada & Suku Cadang');

-- 2. Master Seluruh Modul Sistem
INSERT INTO `modul` (`id_modul`, `kode_modul`, `nama_modul`, `kategori_modul`, `deskripsi`) VALUES
-- Modul Kontrol Super Admin
(1, 'manajemen_user', 'Manajemen Pengguna & Akun', 'Admin Sistem', 'Kelola akun pengguna, reset password, dan status aktif'),
(2, 'manajemen_jabatan', 'Manajemen Jabatan & Peran', 'Admin Sistem', 'Kelola data jabatan/peran perusahaan'),
(3, 'manajemen_hak_akses', 'Pengaturan Hak Akses Modul', 'Admin Sistem', 'Konfigurasi izin lihat, tambah, edit, hapus per jabatan'),
(4, 'log_login', 'Riwayat & Log Login', 'Admin Sistem', 'Pencatatan riwayat login sistem'),

-- Modul Keuangan & Akuntansi
(5, 'penjualan', 'Penjualan', 'Keuangan', 'Faktur penjualan dan penerimaan'),
(6, 'piutang', 'List Piutang', 'Keuangan', 'Buku pembantu piutang (AR)'),
(7, 'deposit', 'List Deposit', 'Keuangan', 'Saldo deposit uang muka customer'),
(8, 'pengeluaran', 'Pengeluaran', 'Keuangan', 'Voucher beban dan pengeluaran kas/bank (AP)'),
(9, 'kode_akun', 'Data Kode Akun', 'Keuangan', 'Chart of Accounts (COA) akuntansi'),
(10, 'rekening', 'Data Rekening Bank', 'Keuangan', 'Rekening bank perusahaan'),
(11, 'laporan_neraca', 'Laporan Neraca', 'Laporan', 'Laporan posisi keuangan neraca'),
(12, 'laporan_laba_rugi', 'Laporan Laba Rugi', 'Laporan', 'Laporan kinerja laba rugi perusahaan'),

-- Modul Master Bisnis
(13, 'customer', 'Data Customer (Toko Bangunan)', 'Master', 'Master data customer'),
(14, 'wilayah', 'Data Wilayah', 'Master', 'Wilayah distribusi'),
(15, 'barang', 'Data Barang / Semen', 'Master', 'Katalog data semen dan barang'),
(16, 'aset', 'Aset Perusahaan / Kendaraan', 'Master', 'Data aset dan armada'),
(17, 'jenis_aset', 'Data Jenis Aset', 'Master', 'Kategori aset tetap'),
(18, 'karyawan', 'Data Karyawan', 'Master', 'Master data pegawai'),
(19, 'driver', 'Data Driver', 'Master', 'Data khusus driver'),

-- Modul Operasional & Logistik
(20, 'list_so', 'List SO / Pembelian SO', 'Operasional', 'Daftar dan transaksi Sales Order'),
(21, 'gudang_so', 'List Gudang SO', 'Operasional', 'Data lokasi gudang dan stok'),
(22, 'opname_gudang', 'Opname Gudang', 'Operasional', 'Stock opname fisik gudang'),
(23, 'pengiriman', 'Pengiriman', 'Operasional', 'Surat jalan dan rute pengiriman armada'),
(24, 'ongkos_angkut', 'Data Ongkos Angkut', 'Operasional', 'Tarif ongkos kirim rute'),
(25, 'kso', 'Data KSO', 'Operasional', 'Kerja sama operasional mitra'),
(26, 'rilisan', 'Rilisan', 'Operasional', 'Berita acara serah terima barang'),
(27, 'perbaikan_kendaraan', 'Perbaikan Kendaraan', 'Operasional', 'SPK bengkel dan servis armada'),
(28, 'sparepart', 'List Sparepart & Pembelian', 'Operasional', 'Stok dan pembelian suku cadang'),
(29, 'toko_bangunan', 'Master Toko Bangunan & Proyek', 'Master', 'Master cabang toko retail, proyek, dan gudang transit');

-- 3. Matriks Hak Akses Granular Tiap Jabatan (Dashboard Ringkas & Terfokus)

-- 3.1 SPV Keuangan (Hanya Melihat & Mengelola Finansial, Master Terkait, dan List SO)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(1, 5, TRUE, TRUE, TRUE, TRUE),   -- Penjualan
(1, 6, TRUE, TRUE, TRUE, TRUE),   -- List Piutang
(1, 7, TRUE, TRUE, TRUE, TRUE),   -- List Deposit
(1, 9, TRUE, TRUE, TRUE, TRUE),   -- Data Kode Akun
(1, 10, TRUE, TRUE, TRUE, TRUE),  -- Data Rekening
(1, 13, TRUE, TRUE, TRUE, TRUE),  -- Data Customer (Toko Bangunan)
(1, 14, TRUE, TRUE, TRUE, TRUE),  -- Data Wilayah
(1, 15, TRUE, TRUE, TRUE, TRUE),  -- Data Barang / Semen
(1, 16, TRUE, TRUE, TRUE, TRUE),  -- Aset Perusahaan
(1, 17, TRUE, TRUE, TRUE, TRUE),  -- Data Jenis Aset
(1, 18, TRUE, TRUE, TRUE, TRUE),  -- Data Karyawan
(1, 20, TRUE, TRUE, TRUE, TRUE),  -- List SO
(1, 29, TRUE, TRUE, TRUE, TRUE);  -- Master Toko Bangunan & Proyek

-- 3.2 Staff AR (HANYA Penjualan, Customer, Toko Bangunan, List Piutang, List Deposit, Data Rekening)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(2, 5, TRUE, TRUE, TRUE, FALSE),  -- Penjualan
(2, 6, TRUE, TRUE, TRUE, FALSE),  -- List Piutang
(2, 7, TRUE, TRUE, TRUE, FALSE),  -- List Deposit
(2, 10, TRUE, TRUE, TRUE, FALSE), -- Data Rekening
(2, 13, TRUE, TRUE, TRUE, FALSE), -- Data Customer
(2, 29, TRUE, TRUE, TRUE, FALSE); -- Master Toko Bangunan & Proyek

-- 3.3 Staff AP (HANYA Pengeluaran, Rilisan, Pembelian SO, List SO, List Gudang SO)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(3, 8, TRUE, TRUE, TRUE, FALSE),  -- Pengeluaran
(3, 20, TRUE, TRUE, TRUE, FALSE), -- List SO / Pembelian SO
(3, 21, TRUE, TRUE, TRUE, FALSE), -- List Gudang SO
(3, 26, TRUE, TRUE, TRUE, FALSE); -- Rilisan

-- 3.4 Dispatcher (HANYA Data Kendaraan, Pengiriman, Data Driver)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(4, 16, TRUE, FALSE, FALSE, FALSE), -- Data Kendaraan (Lihat saja)
(4, 19, TRUE, FALSE, FALSE, FALSE), -- Data Driver (Lihat saja)
(4, 23, TRUE, TRUE, TRUE, FALSE);  -- Pengiriman (Surat Jalan)

-- 3.5 Pengawas Driver (HANYA Data Driver)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(5, 19, TRUE, TRUE, TRUE, FALSE); -- Data Driver

-- 3.6 SPV Gudang (HANYA Data Gudang SO, Opname Gudang)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(6, 21, TRUE, TRUE, TRUE, TRUE),  -- List Gudang SO
(6, 22, TRUE, TRUE, TRUE, TRUE);  -- Opname Gudang

-- 3.7 Direktur & Manager (HANYA Laporan Neraca & Laporan Laba Rugi)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(7, 11, TRUE, FALSE, FALSE, FALSE), -- Laporan Neraca (Lihat saja)
(7, 12, TRUE, FALSE, FALSE, FALSE); -- Laporan Laba Rugi (Lihat saja)

-- 3.8 SPV Operasional (HANYA Ongkos Angkut, Opname Gudang, Driver, Kendaraan, Pengiriman, KSO)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(8, 16, TRUE, TRUE, TRUE, FALSE), -- Data Kendaraan
(8, 19, TRUE, TRUE, TRUE, FALSE), -- Data Driver
(8, 22, TRUE, TRUE, TRUE, FALSE), -- Opname Gudang
(8, 23, TRUE, TRUE, TRUE, FALSE), -- Pengiriman
(8, 24, TRUE, TRUE, TRUE, FALSE), -- Data Ongkos Angkut
(8, 25, TRUE, TRUE, TRUE, FALSE); -- Data KSO

-- 3.9 Pengawas Kendaraan (HANYA Perbaikan Kendaraan, List Sparepart & Pembelian)
INSERT INTO `hak_akses_jabatan` (`id_jabatan`, `id_modul`, `boleh_lihat`, `boleh_tambah`, `boleh_edit`, `boleh_hapus`) VALUES
(9, 27, TRUE, TRUE, TRUE, FALSE), -- Perbaikan Kendaraan
(9, 28, TRUE, TRUE, TRUE, FALSE); -- List Sparepart & Pembelian

-- 4. Contoh Akun Awal
INSERT INTO `super_account` (`username`, `password`, `nama_pemilik`) VALUES
('superadmin', '$2y$12$eA8jD5Q0G/JkWuCsk5/J6eN8cT7sB1gqK3p5oV2yW4rZ6m7x8y9z0', 'Administrator Kontrol Akun');

INSERT INTO `data_wilayah` (`kode_wilayah`, `nama_wilayah`) VALUES
('WLY-001', 'Wilayah Jakarta & Sekitarnya'),
('WLY-002', 'Wilayah Jawa Barat'),
('WLY-003', 'Wilayah Jawa Tengah');

INSERT INTO `data_semen` (`kode_barang`, `nama_barang`, `jenis_barang`, `satuan_barang`, `harga_pokok`, `harga_jual_standar`) VALUES
('SMN-001', 'Semen Portland Komposit 40kg', 'PCC', 'Zak', 52000.00, 58000.00),
('SMN-002', 'Semen Portland Komposit 50kg', 'PCC', 'Zak', 64000.00, 71000.00);

INSERT INTO `data_karyawan` (`kode_karyawan`, `nama_karyawan`, `id_jabatan`, `kategori_karyawan`, `no_identitas`, `alamat`, `no_hp`, `status_karyawan`, `tanggal_mulai_kerja`) VALUES
('KRY-001', 'Siti Rahmawati', 1, 'staf', '3201234567890001', 'Jl. Merdeka No. 10', '081234567890', 'tetap', '2024-01-15'),
('KRY-002', 'Dewi Anggraeni', 2, 'staf', '3201234567890002', 'Jl. Sudirman No. 45', '081298765432', 'tetap', '2024-02-01'),
('KRY-003', 'Rian Hidayat', 3, 'staf', '3201234567890003', 'Jl. Gatot Subroto No. 12', '081233445566', 'tetap', '2024-02-15'),
('KRY-004', 'Ahmad Supriyadi', 7, 'manajemen', '3201234567890004', 'Jl. Thamrin No. 88', '081122334455', 'tetap', '2023-05-01');

INSERT INTO `account` (`username`, `password`, `kode_karyawan`, `id_jabatan`, `status_aktif`) VALUES
('spv_keuangan', '$2y$12$eA8jD5Q0G/JkWuCsk5/J6eN8cT7sB1gqK3p5oV2yW4rZ6m7x8y9z0', 'KRY-001', 1, TRUE),
('staff_ar', '$2y$12$eA8jD5Q0G/JkWuCsk5/J6eN8cT7sB1gqK3p5oV2yW4rZ6m7x8y9z0', 'KRY-002', 2, TRUE),
('staff_ap', '$2y$12$eA8jD5Q0G/JkWuCsk5/J6eN8cT7sB1gqK3p5oV2yW4rZ6m7x8y9z0', 'KRY-003', 3, TRUE),
('direktur', '$2y$12$eA8jD5Q0G/JkWuCsk5/J6eN8cT7sB1gqK3p5oV2yW4rZ6m7x8y9z0', 'KRY-004', 7, TRUE);
