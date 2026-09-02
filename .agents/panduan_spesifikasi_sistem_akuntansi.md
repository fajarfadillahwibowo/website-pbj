# Panduan Spesifikasi & Arsitektur Sistem Informasi Akuntansi Terintegrasi

Dokumen ini merupakan spesifikasi teknis, arsitektur data, dan logika perhitungan akuntansi lengkap yang diekstraksi dari analisis sistem operasional distribusi semen PT Pura Balkom Jaya Utama (PBJ) dan diimplementasikan secara langsung pada sistem aplikasi web akuntansi berbasis Laravel.

---

## DAFTAR ISI
1. Ringkasan Eksekutif & Prinsip Desain
2. Bagan Akun Standar (Chart of Accounts)
3. Desain Skema Basis Data Relasional Terpadu
4. Matriks Otomasi Jurnal (Auto-Journal Engine)
5. Logika Perhitungan & Algoritma Akuntansi
6. Logika Transaksi Operasional, Kontrol Stok & Relasi Toko Cabang
7. Logika Master Aset & Amortisasi Penyusutan
8. Integritas Data, Audit Trail, & Transaksi Atomik
9. Panduan Antarmuka Pengguna & Komponen UI Kustom
10. Skema Database Master Data Lengkap (DDL Terverifikasi)
11. Nilai Domain, Referensi Enum & Rekening Bank
12. Struktur Modul Penggajian Karyawan & Upah Driver
13. Hierarki 10 Peran Pengguna (Role-Based Access Control)
14. Alur Bisnis Lengkap & Diagram Proses Terintegrasi
15. Laporan Keuangan yang Dihasilkan Sistem
16. Referensi Pemetaan Nama Kolom (Field Mapping)
17. Generator Kode Otomatis & Algoritma Gap-Filling

---

## 1. RINGKASAN EKSEKUTIF & PRINSIP DESAIN

### A. Prinsip Double-Entry Bookkeeping Murni
Setiap transaksi keuangan wajib dicatat dengan prinsip pembukuan berpasangan. Total nilai sisi Debit harus selalu sama dengan total nilai sisi Kredit ($\sum \text{Debit} = \sum \text{Kredit}$).

### B. Konsep Immutable Ledger (Buku Besar Abadi)
Transaksi jurnal yang telah divalidasi dan diposting tidak boleh diubah secara destruktif (`UPDATE` nilai nominal) atau dihapus langsung (`HARD DELETE`). Setiap koreksi kesalahan wajib dilakukan melalui mekanisme **Jurnal Pembalik (Reversal Entry)** atau **Jurnal Penyesuaian (Adjustment Entry)**.

### C. Pemisahan Transaksi Operasional vs Pencatatan Akuntansi
Modul operasional (Penebusan SO Pabrik, Pengiriman Surat Jalan, Faktur Penjualan, Rilisan Uang Jalan, Pengeluaran Kas) bertindak sebagai pemicu (*event triggers*), sementara mesin pencatatan akuntansi (*Auto-Journal Engine*) secara otomatis membuat baris jurnal terkait di dalam transaksi atomik database (`DB::transaction`).

### D. Arsitektur Relasi 1:N Entitas Customer Pemilik ke Toko Bangunan & Proyek
Dalam operasional riil PT PBJ, entitas pemilik modal/finansial terpisah dari titik outlet fisik pengiriman:
* **Entitas Pemilik (`data_customer`)**: Memegang hak legal, plafon kredit terpusat, saldo piutang berjalan, dan saldo deposit.
* **Entitas Cabang / Drop Point (`data_toko_bangunan`)**: Titik outlet toko retail fisik, lokasi proyek kontraktor, atau gudang transit tempat semen dikirimkan.

---

## 2. BAGAN AKUN STANDAR (CHART OF ACCOUNTS)

Berikut adalah hierarki bagan akun standar industri distribusi semen, armada logistik, dan perdagangan umum dengan penetapan posisi saldo normal:

| Kode Akun | Nama Akun | Kategori Finansial | Sub-Kategori | Saldo Normal | Status Akun |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **1000** | **ASET / AKTIVA** | Aktiva Lancar / Tetap | Header Klasifikasi | DEBIT | Induk |
| 1100 | Aset Lancar | Aktiva Lancar | Header Sub | DEBIT | Induk |
| 1110 | Kas & Setara Kas | Aktiva Lancar | Kas | DEBIT | Induk |
| 1111 | Kas Operasional Kantor | Aktiva Lancar | Kas | DEBIT | Transaksi |
| 1112 | Kas Kecil (Petty Cash) | Aktiva Lancar | Kas | DEBIT | Transaksi |
| 1107 | Kas Rilisan Uang Jalan Driver | Aktiva Lancar | Kas Operasional | DEBIT | Transaksi |
| 1120 | Rekening Bank Perusahaan | Aktiva Lancar | Bank | DEBIT | Induk |
| 1121 | Bank BRI Operasional | Aktiva Lancar | Bank | DEBIT | Transaksi |
| 1122 | Bank Mandiri Operasional | Aktiva Lancar | Bank | DEBIT | Transaksi |
| 1130 | Piutang Usaha | Aktiva Lancar | Piutang | DEBIT | Induk |
| 1131 | Piutang Penjualan Toko Retail | Aktiva Lancar | Piutang | DEBIT | Transaksi |
| 1132 | Piutang Penjualan Proyek / KSO | Aktiva Lancar | Piutang | DEBIT | Transaksi |
| 1140 | Persediaan Barang Dagang | Aktiva Lancar | Persediaan | DEBIT | Induk |
| 1141 | Persediaan Semen Kantong (Zak) | Aktiva Lancar | Persediaan | DEBIT | Transaksi |
| 1142 | Persediaan Semen Curah (Big Bag/Bulk)| Aktiva Lancar | Persediaan | DEBIT | Transaksi |
| 1150 | Uang Muka & Biaya Dibayar Dimuka | Aktiva Lancar | Uang Muka | DEBIT | Induk |
| 1151 | Uang Muka Penebusan Pabrik (CL SIG)| Aktiva Lancar | Plafon / Deposit | DEBIT | Transaksi |
| 1152 | Uang Muka Pembelian Supplier | Aktiva Lancar | Uang Muka | DEBIT | Transaksi |
| 1200 | Aset Tetap | Aktiva Tetap | Header Sub | DEBIT | Induk |
| 1210 | Tanah | Aktiva Tetap | Non-Depresiasi | DEBIT | Transaksi |
| 1220 | Bangunan & Prasarana Gudang | Aktiva Tetap | Depresiasi | DEBIT | Transaksi |
| 1229 | Akumulasi Penyusutan Bangunan | Aktiva Tetap (Kontra)| Kontra Aset | KREDIT | Transaksi |
| 1230 | Armada Kendaraan Truk Logistik | Aktiva Tetap | Depresiasi | DEBIT | Transaksi |
| 1239 | Akumulasi Penyusutan Kendaraan | Aktiva Tetap (Kontra)| Kontra Aset | KREDIT | Transaksi |
| 1240 | Inventaris & Peralatan Kantor | Aktiva Tetap | Depresiasi | DEBIT | Transaksi |
| 1249 | Akumulasi Penyusutan Inventaris | Aktiva Tetap (Kontra)| Kontra Aset | KREDIT | Transaksi |
| **2000** | **KEWAJIBAN / UTANG** | Kewajiban | Header Klasifikasi | KREDIT | Induk |
| 2100 | Kewajiban Jangka Pendek | Kewajiban Lancar | Header Sub | KREDIT | Induk |
| 2110 | Utang Usaha & Supplier | Kewajiban Lancar | Utang Dagang | KREDIT | Induk |
| 2111 | Utang Penebusan Semen (SIG) | Kewajiban Lancar | Utang Dagang | KREDIT | Transaksi |
| 2112 | Utang Ekspedisi & Vendor Sparepart | Kewajiban Lancar | Utang Dagang | KREDIT | Transaksi |
| 2120 | Titipan Dana Pelanggan | Kewajiban Lancar | Deposit Pelanggan | KREDIT | Induk |
| 2121 | Titipan Deposit Toko Bangunan | Kewajiban Lancar | Deposit Pelanggan | KREDIT | Transaksi |
| 2130 | Beban Akrual & Utang Gaji | Kewajiban Lancar | Akrual | KREDIT | Induk |
| 2131 | Utang Gaji Karyawan | Kewajiban Lancar | Gaji | KREDIT | Transaksi |
| 2132 | Utang Upah Ritase Driver | Kewajiban Lancar | Upah | KREDIT | Transaksi |
| 2140 | Utang Kredit Truk (Bagian Lancar) | Kewajiban Lancar | Utang Leasing | KREDIT | Transaksi |
| 2200 | Kewajiban Jangka Panjang | Kewajiban Jk Panjang| Header Sub | KREDIT | Induk |
| 2210 | Utang Leasing Armada Jangka Panjang | Kewajiban Jk Panjang| Utang Leasing | KREDIT | Transaksi |
| **3000** | **EKUITAS / MODAL** | Modal | Header Klasifikasi | KREDIT | Induk |
| 3100 | Modal Pemilik Perusahaan | Modal | Modal Saham | KREDIT | Transaksi |
| 3200 | Laba Ditahan (Retained Earnings) | Modal | Laba Ditahan | KREDIT | Transaksi |
| 3300 | Laba Tahun Berjalan | Modal | Laba Berjalan | KREDIT | Transaksi |
| 3400 | Prive Pemilik | Modal (Kontra) | Prive | DEBIT | Transaksi |
| **4000** | **PENDAPATAN USAHA** | Pendapatan | Header Klasifikasi | KREDIT | Induk |
| 4100 | Pendapatan Penjualan Semen | Pendapatan | Penjualan | KREDIT | Transaksi |
| 4200 | Pendapatan Jasa Angkut / Logistik | Pendapatan | Jasa | KREDIT | Transaksi |
| 4300 | Potongan & Diskon Penjualan | Pendapatan (Kontra) | Kontra Pendapatan | DEBIT | Transaksi |
| **5000** | **BEBAN POKOK PENJUALAN (HPP)** | Harga Pokok | Header Klasifikasi | DEBIT | Induk |
| 5100 | Beban Pokok Penebusan Semen | Harga Pokok | HPP Barang | DEBIT | Transaksi |
| 5200 | Beban Ongkos Angkut Pabrik (OA) | Harga Pokok | HPP Angkut | DEBIT | Transaksi |
| **6000** | **BEBAN OPERASIONAL & UMUM** | Beban Operasional | Header Klasifikasi | DEBIT | Induk |
| 6100 | Beban Gaji & SDM | Beban Operasional | SDM | DEBIT | Induk |
| 6110 | Beban Gaji Karyawan Tetap | Beban Operasional | SDM | DEBIT | Transaksi |
| 6120 | Beban Upah Ritase Driver | Beban Operasional | SDM | DEBIT | Transaksi |
| 6130 | Beban BPJS Ketenagakerjaan & Kes. | Beban Operasional | SDM | DEBIT | Transaksi |
| 6200 | Beban Operasional Armada & Bengkel | Beban Operasional | Armada | DEBIT | Induk |
| 6210 | Beban BBM Armada Truk | Beban Operasional | Armada | DEBIT | Transaksi |
| 6220 | Beban Pemeliharaan, Servis & Sparepart | Beban Operasional | Armada | DEBIT | Transaksi |
| 6230 | Beban Pajak, STNK, & KIR Kendaraan | Beban Operasional | Armada | DEBIT | Transaksi |
| 6300 | Beban Penyusutan Aset Tetap | Beban Operasional | Amortisasi | DEBIT | Induk |
| 6310 | Beban Penyusutan Bangunan | Beban Operasional | Amortisasi | DEBIT | Transaksi |
| 6320 | Beban Penyusutan Armada Truk | Beban Operasional | Amortisasi | DEBIT | Transaksi |
| 6330 | Beban Penyusutan Inventaris Kantor | Beban Operasional | Amortisasi | DEBIT | Transaksi |
| 6400 | Beban Administrasi & Kantor | Beban Operasional | Kantor | DEBIT | Transaksi |
| **7000** | **PENDAPATAN & BEBAN LAIN-LAIN** | Non-Operasional | Header Klasifikasi | KREDIT | Induk |
| 7100 | Pendapatan Bunga Bank | Pendapatan Lain | Pendapatan Lain | KREDIT | Transaksi |
| 7200 | Beban Administrasi Bank | Beban Lain-Lain | Beban Lain | DEBIT | Transaksi |
| 7300 | Beban Bunga Kredit / Leasing | Beban Lain-Lain | Beban Lain | DEBIT | Transaksi |

---

## 3. DESAIN SKEMA BASIS DATA RELASIONAL TERPADU

Berikut adalah skema DDL relasional MySQL yang selaras dengan implementasi database Laravel PT PBJ:

```sql
-- 1. TABEL BAGAN AKUN (COA)
CREATE TABLE `data_kode_akun` (
    `kode_akun` VARCHAR(30) NOT NULL PRIMARY KEY,
    `nama_akun` VARCHAR(100) NOT NULL,
    `tipe_akun` ENUM('Aktiva Lancar', 'Aktiva Tetap', 'Kewajiban Lancar', 'Kewajiban Jangka Panjang', 'Modal', 'Pendapatan', 'Harga Pokok Penjualan', 'Beban Operasional', 'Beban Lain-Lain', 'Pendapatan Lain-Lain') NOT NULL,
    `kelompok_akun` VARCHAR(50) NOT NULL,
    `saldo_normal` ENUM('Debit', 'Kredit') NOT NULL DEFAULT 'Debit',
    `saldo_awal` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `saldo_berjalan` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABEL JURNAL UMUM (DOUBLE-ENTRY)
CREATE TABLE `jurnal_umum` (
    `id_jurnal` BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `nomor_jurnal` VARCHAR(50) NOT NULL,
    `tanggal_transaksi` DATE NOT NULL,
    `kode_akun` VARCHAR(30) NOT NULL,
    `posisi` ENUM('Debit', 'Kredit') NOT NULL,
    `nominal` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `keterangan` VARCHAR(255) NOT NULL,
    `referensi_transaksi` VARCHAR(50) DEFAULT NULL,
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_jurnal_nomor` (`nomor_jurnal`),
    INDEX `idx_jurnal_akun` (`kode_akun`),
    INDEX `idx_jurnal_tanggal` (`tanggal_transaksi`),
    CONSTRAINT `fk_jurnal_akun` FOREIGN KEY (`kode_akun`) 
        REFERENCES `data_kode_akun` (`kode_akun`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABEL MASTER CUSTOMER (ENTITAS LEGAL & FINANSIAL TERPUSAT)
CREATE TABLE `data_customer` (
    `kode_customer` VARCHAR(30) NOT NULL PRIMARY KEY,
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
    INDEX `idx_customer_wilayah` (`kode_wilayah`),
    CONSTRAINT `fk_customer_wilayah` FOREIGN KEY (`kode_wilayah`) 
        REFERENCES `data_wilayah` (`kode_wilayah`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABEL DATA TOKO BANGUNAN & PROYEK CABANG (TITIK PENGIRIMAN FISIK)
CREATE TABLE `data_toko_bangunan` (
    `kode_toko` VARCHAR(30) NOT NULL PRIMARY KEY,
    `kode_customer` VARCHAR(30) NOT NULL,
    `kode_wilayah` VARCHAR(30) NOT NULL,
    `nama_toko_bangunan` VARCHAR(150) NOT NULL,
    `tipe_lokasi` ENUM('toko_retail', 'proyek_kontraktor', 'gudang_transit') NOT NULL DEFAULT 'toko_retail',
    `penanggung_jawab` VARCHAR(100) NOT NULL,
    `no_hp_toko` VARCHAR(30) NOT NULL,
    `alamat_lengkap` TEXT NOT NULL,
    `titik_koordinat` VARCHAR(100) DEFAULT NULL,
    `status_toko` ENUM('aktif', 'non_aktif') NOT NULL DEFAULT 'aktif',
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_toko_customer` (`kode_customer`),
    INDEX `idx_toko_wilayah` (`kode_wilayah`),
    CONSTRAINT `fk_toko_customer` FOREIGN KEY (`kode_customer`) 
        REFERENCES `data_customer` (`kode_customer`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_toko_wilayah` FOREIGN KEY (`kode_wilayah`) 
        REFERENCES `data_wilayah` (`kode_wilayah`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABEL TRANSAKSI FAKTUR PENJUALAN (AR)
CREATE TABLE `penjualan` (
    `id_penjualan` INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    `nomor_faktur` VARCHAR(50) NOT NULL UNIQUE,
    `tanggal_penjualan` DATE NOT NULL,
    `kode_customer` VARCHAR(30) NOT NULL,
    `kode_toko` VARCHAR(30) DEFAULT NULL,
    `metode_pembayaran` ENUM('Tunai', 'Transfer', 'Kredit / Piutang', 'Potong Deposit') NOT NULL,
    `total_bruto` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `diskon` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `total_netto` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `jumlah_dibayar` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `sisa_piutang` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    `status_pembayaran` ENUM('Lunas', 'Belum Lunas', 'Jatuh Tempo') NOT NULL DEFAULT 'Belum Lunas',
    `jatuh_tempo` DATE DEFAULT NULL,
    `id_rekening` INT DEFAULT NULL,
    `status_persetujuan` ENUM('draft', 'disetujui', 'terkunci') NOT NULL DEFAULT 'disetujui',
    `dibuat_oleh` VARCHAR(50) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `diperbarui_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_penjualan_customer` (`kode_customer`),
    INDEX `idx_penjualan_toko` (`kode_toko`),
    CONSTRAINT `fk_penjualan_customer` FOREIGN KEY (`kode_customer`) 
        REFERENCES `data_customer` (`kode_customer`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_penjualan_toko` FOREIGN KEY (`kode_toko`) 
        REFERENCES `data_toko_bangunan` (`kode_toko`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. MATRIKS OTOMASI JURNAL (AUTO-JOURNAL ENGINE)

Berikut adalah matriks aturan jurnal otomatis yang dieksekusi saat setiap transaksi operasional divalidasi:

| Peristiwa Transaksi | Dokumen Sumber | Sisi Debit (Akun & Nilai) | Sisi Kredit (Akun & Nilai) | Keterangan Sistem |
| :--- | :--- | :--- | :--- | :--- |
| **1. Penebusan SO Semen SIG (Pabrik)** | `pembelian_so` | `1141 Persediaan Semen Kantong` ($Total SO$) | `1151 Uang Muka Penebusan (CL SIG)` / `1121 Bank BRI` | Menambah stok persediaan LO dan memotong plafon penebusan pabrik. |
| **2. Penjualan Semen Tunai / Transfer** | `penjualan` | 1. `1111 Kas` / `1121 Bank BRI` ($Total Netto$)<br>2. `5100 HPP Semen` ($Qty \times HPP$) | 1. `4100 Pendapatan Penjualan` ($Total Netto$)<br>2. `1141 Persediaan Semen` ($Qty \times HPP$) | Perpetual inventory: mengakui omset dan memotong persediaan seketika. |
| **3. Penjualan Semen Kredit Tempo** | `penjualan` & `list_piutang` | 1. `1131 Piutang Penjualan Toko` ($Total Netto$)<br>2. `5100 HPP Semen` ($Qty \times HPP$) | 1. `4100 Pendapatan Penjualan` ($Total Netto$)<br>2. `1141 Persediaan Semen` ($Qty \times HPP$) | Muncul piutang dagang dan menambah saldo piutang `data_customer`. |
| **4. Penjualan Semen Potong Deposit** | `penjualan` & `deposit_customer` | 1. `2121 Titipan Deposit Toko` ($Total Netto$)<br>2. `5100 HPP Semen` ($Qty \times HPP$) | 1. `4100 Pendapatan Penjualan` ($Total Netto$)<br>2. `1141 Persediaan Semen` ($Qty \times HPP$) | Mengurangi saldo kewajiban deposit `data_customer`. |
| **5. Penerimaan Top-Up Deposit Toko** | `deposit_customer` | `1121 Bank BRI` / `1111 Kas` ($Nominal Top-Up$) | `2121 Titipan Deposit Toko` ($Nominal Top-Up$) | Menambah saldo titipan dana pelanggan. |
| **6. Pelunasan Cicilan / Piutang Toko**| `list_piutang` | `1121 Bank BRI` / `1111 Kas` ($Nominal Dibayar$) | `1131 Piutang Penjualan Toko` ($Nominal Dibayar$) | Mengurangi sisa piutang berjalan customer. |
| **7. Rilisan Uang Jalan Supir Armada** | `data_rilisan_uang_jalan` | `1107 Kas Rilisan Uang Jalan Driver` ($Uang Jalan$) | `1111 Kas Kantor` / `1121 Bank BRI` ($Uang Jalan$) | Alokasi dana operasional perjalanan supir truk. |
| **8. Pengeluaran Kas BBM & Tol Armada** | `pengeluaran` | `6210 Beban BBM Armada Truk` ($Nominal$) | `1111 Kas Kantor` / `1107 Kas Rilisan` ($Nominal$) | Pembebanan biaya langsung per nomor polisi kendaraan. |
| **9. Servis Kendaraan & Beli Sparepart**| `spk_perbaikan` / `pembelian_sparepart` | `6220 Beban Pemeliharaan & Sparepart` ($Nominal$) | `1111 Kas` / `2112 Utang Vendor` ($Nominal$) | Pencatatan perbaikan armada truk. |
| **10. Posting Penyusutan Bulanan Aset** | `aset_perusahaan` | `6320 Beban Penyusutan Kendaraan` ($Depresiasi$) | `1239 Akumulasi Penyusutan Kendaraan` ($Depresiasi$) | Amortisasi non-kas bulanan aset tetap. |

---

## 5. LOGIKA PERHITUNGAN & ALGORITMA AKUNTANSI

### A. Algoritma Perhitungan Saldo Berjalan Buku Besar
Kalkulasi saldo buku besar akun dieksekusi secara dinamis dengan mengacu pada atribut `saldo_normal` dari `data_kode_akun`:

```php
function hitungSaldoAkhirAkun(string $kode_akun, string $tanggal_sampai): float
{
    $akun = DB::table('data_kode_akun')->where('kode_akun', $kode_akun)->firstOrFail();
    $saldo_awal = (float) $akun->saldo_awal;

    $total_debit = (float) DB::table('jurnal_umum')
        ->where('kode_akun', $kode_akun)
        ->where('posisi', 'Debit')
        ->where('tanggal_transaksi', '<=', $tanggal_sampai)
        ->sum('nominal');

    $total_kredit = (float) DB::table('jurnal_umum')
        ->where('kode_akun', $kode_akun)
        ->where('posisi', 'Kredit')
        ->where('tanggal_transaksi', '<=', $tanggal_sampai)
        ->sum('nominal');

    if ($akun->saldo_normal === 'Debit') {
        return ($saldo_awal + $total_debit) - $total_kredit;
    } else {
        return ($saldo_awal + $total_kredit) - $total_debit;
    }
}
```

### B. Validasi Keseimbangan Jurnal ($\sum \text{Debit} = \sum \text{Kredit}$)
Setiap nomor transaksi jurnal wajib dipastikan seimbang sebelum disimpan ke basis data:

$$\left| \sum \text{Debit} - \sum \text{Kredit} \right| < 0.01$$

---

## 6. LOGIKA TRANSAKSI OPERASIONAL & KONTROL STOK

```text
[Customer Induk Terdaftar di data_customer]
    ├── Plafon Kredit: Rp 150.000.000
    ├── Saldo Piutang: Rp 35.000.000
    └── Sisa Limit Kredit: Rp 115.000.000
           │
           ▼
[Memiliki Cabang di data_toko_bangunan]
    ├── TKB-001: TB Maju Jaya Cikarang (Retail)
    ├── TKB-002: TB Maju Jaya Cibitung (Retail)
    └── TKB-003: Proyek Ruko Grand Cikarang (Proyek)
           │
           ▼
[Form Tambah Faktur Penjualan]
    1. Pilih Toko Tujuan Kirim: TKB-001
    2. Sistem mendeteksi otomatis: Customer Pemilik CUST-001
    3. Cek Plafon: (Rp 35.000.000 + Tagihan Baru) <= Rp 150.000.000
    4. Jika Lolos -> Terbitkan Faktur & Auto-Journal
```

---

## 7. LOGIKA MASTER ASET & AMORTISASI PENYUSUTAN

### A. Metode Saldo Menurun Ganda (Double Declining Balance)
Digunakan khusus untuk Armada Truk dan Peralatan Berat:
1. **Tarif Tahunan**: $\text{Tarif} = \left(\frac{1}{\text{Masa Manfaat (Tahun)}}\right) \times 2$
2. **Beban Bulanan**: $\text{Beban} = \frac{\text{Nilai Buku Awal Tahun} \times \text{Tarif}}{12}$

### B. Metode Garis Lurus (Straight-Line)
Digunakan untuk Bangunan Gudang & Inventaris Kantor:
$$\text{Beban Bulanan} = \frac{\text{Harga Perolehan} - \text{Nilai Residu}}{\text{Masa Manfaat (Tahun)} \times 12}$$

---

## 8. HIERARKI 10 PERAN PENGGUNA (ROLE-BASED ACCESS CONTROL)

| Role | Nama Jabatan | Prefix Kode | Lingkup Modul & Otoritas |
| :---: | :--- | :---: | :--- |
| **01** | **Super Admin** | `ADM-` | Manajemen Akun, RBAC, Reset Sandi, dan Konfigurasi Master Global. |
| **02** | **SPV Keuangan** | `KEU-` | Otorisasi Kas, Buku Besar, Neraca, Laba Rugi, dan Arus Kas. |
| **03** | **Staff AR (Piutang)** | `SAR-` | Faktur Penjualan, Monitoring Piutang Toko, Pelunasan & Deposit. |
| **04** | **Staff AP (Hutang)** | `SAP-` | Pembelian SO Pabrik, Pengeluaran Kas Operasional, dan Rilisan Uang Jalan. |
| **05** | **Dispatcher** | `DSP-` | Surat Jalan Logistik, Manajemen Kuota SO Aktif, dan Penugasan Armada. |
| **06** | **Pengawas Driver** | `PDR-` | Monitoring Supir Armada, Status SIM, dan Rekap Ritase Driver. |
| **07** | **SPV Gudang** | `GDG-` | Stok Fisik Semen, Mutasi Alokasi Gudang, dan Stock Opname Berkala. |
| **08** | **Direktur / Manager** | `MGR-` | Dashboard Eksekutif, Monitoring Omset, Laba Bersih, dan Posisi Aset. |
| **09** | **SPV Operasional** | `OPS-` | Manajemen Bengkel Servis, SPK Perbaikan Truk, Master Sparepart, dan KSO. |
| **10** | **Pengawas Kendaraan** | `PKN-` | Monitoring Truk Armada, Kelayakan Jalan, Pajak/STNK/KIR, dan Servis. |

---

## 9. GENERATOR KODE OTOMATIS & ALGORITMA GAP-FILLING

Seluruh modul menggunakan [`app/Helpers/GeneratorKodeOtomatis.php`](file:///c:/laragon/www/laravel1/app/Helpers/GeneratorKodeOtomatis.php) dengan algoritma *Lowest Missing Positive Integer* agar penomoran sekuensial selalu padat tanpa celah:

* `CUST-001`: Master Customer Pemilik
* `TKB-001`: Master Toko Bangunan & Proyek Cabang
* `SMN-001`: Master Produk Semen
* `WLY-001`: Master Wilayah Distribusi
* `AST-001`: Master Aset Tetap Perusahaan
* `SJ-001`: Surat Jalan Pengiriman Semen
* `SPK-001`: Surat Perintah Kerja Servis Bengkel
* `PRT-001`: Master Sparepart Truk
* `FB-SP-001`: Faktur Pembelian Sparepart
* `KSO-001` & `OAK-001`: Kontrak KSO & Tarif Ongkos Angkut KSO
* `JNS-001`: Master Jenis Klasifikasi Truk
