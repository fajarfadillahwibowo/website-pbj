# 🧠 Memori Proyek: Website Sistem Distribusi & Keuangan PT Pura Balkom Jaya Utama (PBJ)

## 📌 Status Terkini
- **Branch Aktif:** `web-dev1`
- **Fokus Eksekusi:** Pengerjaan Penuh Seluruh Modul Developer 1 (Sesuai `docs/02_Pembagian_Tugas.md`), Standarisasi Komponen Dropdown Kustom, dan Penerapan *Staggered Entrance Animation* pada Seluruh View.
- **Status Developer 1:** ✅ **100% SELESAI & TERVERIFIKASI (HTTP 200 OK pada seluruh modul)**.
- **Status Standarisasi UI Dropdown:** ✅ **100% SELESAI & TERVERIFIKASI** (Semua native `<select>` diganti dengan `<x-dropdown-kustom>`).
- **Status Staggered Entrance Animation:** ✅ **100% SELESAI & TERVERIFIKASI** (Terapkan `.animasi-masuk`, `.wadah-bertingkat`, `.tabel-bertingkat`, `.animasi-skala` di seluruh 18 view).

---

## ⚡ Animasi Staggered Entrance (CSS & Micro-Animations)
- **Definisi CSS di `resources/css/app.css`:**
  - Keyframes `@keyframes muncul-bertingkat` (translasi vertikal 10px ke 0, opacity 0 ke 1) dan `@keyframes muncul-skala` (scale 0.97 ke 1, opacity 0 ke 1) dengan easing `cubic-bezier(0.16, 1, 0.3, 1)`.
  - Class `.animasi-masuk`, `.wadah-bertingkat`, `.tabel-bertingkat`, `.animasi-skala`, serta helper tunda `.tunda-1` s.d. `.tunda-8`.
  - Fallback `@media (prefers-reduced-motion: reduce)` untuk aksesibilitas WCAG.
- **Terapkan di seluruh modul:** Dashboard, Superadmin Kelola Akun, Master (Customer, Barang, Wilayah, Karyawan), Keuangan AR (Faktur, Piutang, Deposit), Keuangan AP (Pembelian SO, Pengeluaran Kas, Rilisan), Akuntansi (Bagan Akun COA, Jurnal Umum, Aset Perusahaan), dan Laporan Eksekutif (Neraca, Laba Rugi, Arus Kas).

---

## 🎨 Standarisasi Dropdown Kustom (Alpine.js + Tailwind CSS)

### 1. Komponen Baru: `resources/views/components/dropdown-kustom.blade.php`
- **Fitur Unggulan:**
  - Desain compact, modern, dan elegan (bebas dari template bawaan OS yang kaku).
  - Animasi transisi floating popover yang halus (`transition:enter`, `transition:leave`).
  - Mendukung pencarian/filter cepat untuk daftar panjang dan scrollbar minimalis kustom.
  - Indikator checkmark aktif dan subteks badge informasi (misal: plafon, deposit, kode COA).
  - Mendukung sinkronisasi edit modal Alpine.js via `modelBind`.
  - Mendukung filter auto-submit instan via `submitOnChange="true"`.
  - Aksesibilitas keyboard (Escape / Click Outside to close).

### 2. Global Styling: `resources/css/app.css`
- Menambahkan kustomisasi scrollbar, reset `appearance: none`, dan custom chevron icon untuk fallback element.

### 3. File yang Telah Diimplementasikan Dropdown Kustom:
1. `resources/views/superadmin/kelola_akun.blade.php` (Pilih Karyawan, Pilih Jabatan RBAC)
2. `resources/views/master/customer/index.blade.php` (Filter Wilayah, Modal Tambah & Edit Wilayah)
3. `resources/views/master/barang/index.blade.php` (Filter Jenis, Modal Tambah & Edit Jenis Kemasan)
4. `resources/views/master/karyawan/index.blade.php` (Modal Tambah & Edit: Kategori Karyawan, Jabatan Sistem, Status Kepegawaian)
5. `resources/views/keuangan/ar/faktur_penjualan.blade.php` (Filter Status, Filter Metode, Modal Terbitkan Faktur: Customer, Metode Pembayaran)
6. `resources/views/keuangan/ar/list_piutang.blade.php` (Filter Status Piutang)
7. `resources/views/keuangan/ar/deposit_customer.blade.php` (Filter Tipe Mutasi, Modal Top Up Customer)
8. `resources/views/keuangan/ap/pembelian_so.blade.php` (Filter Status SO, Modal Buat SO: Customer, Gudang Pabrik)
9. `resources/views/keuangan/ap/pengeluaran_kas.blade.php` (Filter Kategori, Modal Pengeluaran: Kategori Biaya, Akun Beban COA, Rekening Sumber)
10. `resources/views/keuangan/ap/list_rilisan.blade.php` (Modal Rilis Uang Jalan: Driver Supir, Rekening Sumber)
11. `resources/views/keuangan/akuntansi/kode_akun.blade.php` (Filter Tipe Akun, Modal Tambah & Edit: Tipe Akun, Saldo Normal)
12. `resources/views/keuangan/akuntansi/jurnal_umum.blade.php` (Filter Posisi, Modal Jurnal: Akun Sisi Debet, Akun Sisi Kredit)
13. `resources/views/keuangan/akuntansi/aset_perusahaan.blade.php` (Filter Jenis Aset, Modal Tambah: Jenis Aset)

---

## 🏗️ Modul yang Telah Diselesaikan (Developer 1)

### 1. Seeder Database Realistis
- `database/seeders/MasterDataSeeder.php`: Master Wilayah (6), Produk Semen (6), Customer Toko (5), Rekening Bank (3), Gudang SO (3).
- `database/seeders/KeuanganSeeder.php`: Bagan Akun COA (22 akun standar), Data Aset Tetap, Faktur Penjualan Awal, Buku Piutang, Mutasi Deposit, dan Pengeluaran Kas.

### 2. Modul Super Admin (Kelola Akun)
- **Controller:** `app/Http/Controllers/Autentikasi/KelolaAkunController.php`
- **View:** `resources/views/superadmin/kelola_akun.blade.php`

### 3. Modul Master Data Sentral
- **Customer Toko:** `CustomerController.php` + `master/customer/index.blade.php`
- **Semen / Barang:** `BarangController.php` + `master/barang/index.blade.php`
- **Wilayah Distribusi:** `WilayahController.php` + `master/wilayah/index.blade.php`
- **Karyawan & Driver:** `KaryawanController.php` + `master/karyawan/index.blade.php`

### 4. Modul Account Receivable (AR & Penjualan)
- **Faktur Penjualan:** `FakturPenjualanController.php` + `keuangan/ar/faktur_penjualan.blade.php`
- **List Piutang:** `PiutangController.php` + `keuangan/ar/list_piutang.blade.php`
- **Deposit Customer:** `DepositCustomerController.php` + `keuangan/ar/deposit_customer.blade.php`

### 5. Modul Account Payable (AP & Kas Keluar)
- **Pembelian SO Pabrik:** `PembelianSOController.php` + `keuangan/ap/pembelian_so.blade.php`
- **Pengeluaran Kas:** `PengeluaranKasController.php` + `keuangan/ap/pengeluaran_kas.blade.php`
- **Rilisan Kas Bon Supir:** `HutangSupplierController.php` + `keuangan/ap/list_rilisan.blade.php`

### 6. Modul Akuntansi & Laporan Eksekutif
- **Bagan Akun (COA):** `KodeAkunController.php` + `keuangan/akuntansi/kode_akun.blade.php`
- **Buku Jurnal Umum:** `JurnalUmumController.php` + `keuangan/akuntansi/jurnal_umum.blade.php`
- **Aset Perusahaan:** `AsetPerusahaanController.php` + `keuangan/akuntansi/aset_perusahaan.blade.php`
- **Laporan Eksekutif:** `LaporanEksekutifController.php` (`neraca`, `laba_rugi`, `arus_kas`)

---

## 🌐 Status Server & Endpoint
- **Laravel Artisan Dev Server:** `http://127.0.0.1:8000` (Running)
- **Vite Dev Server:** `http://localhost:5173` (Running)
- **Laragon Apache:** `http://localhost/laravel1/public` (200 OK)
- **MySQL Database:** `127.0.0.1:3306` (200 OK)
