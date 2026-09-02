# 🧠 Memori Proyek: Website Sistem Distribusi & Keuangan PT Pura Balkom Jaya Utama (PBJ)

## 📌 Status Terkini
- **Branch Aktif:** `web-dev1`
- **Fokus Eksekusi:** Pengerjaan Penuh Seluruh Modul Developer 1 (Sesuai `docs/02_Pembagian_Tugas.md`).
- **Status Developer 1:** ✅ **100% SELESAI & TERVERIFIKASI (HTTP 200 OK pada seluruh modul)**.

---

## 🏗️ Modul yang Telah Diselesaikan (Developer 1)

### 1. Seeder Database Realistis
- `database/seeders/MasterDataSeeder.php`: Master Wilayah (6), Produk Semen (6), Customer Toko (5), Rekening Bank (3), Gudang SO (3).
- `database/seeders/KeuanganSeeder.php`: Bagan Akun COA (22 akun standar), Data Aset Tetap, Faktur Penjualan Awal, Buku Piutang, Mutasi Deposit, dan Pengeluaran Kas.
- Status: Berhasil dijalankan ke MySQL via `php artisan db:seed`.

### 2. Modul Super Admin (Kelola Akun)
- **Controller:** `app/Http/Controllers/Autentikasi/KelolaAkunController.php` (`store`, `resetPassword`, `toggleStatus`)
- **View:** `resources/views/superadmin/kelola_akun.blade.php`
- **Fitur:** CRUD Akun, Reset Password default `password123`, Toggle Status Aktif/Nonaktif.

### 3. Modul Master Data Sentral
- **Customer Toko:** `CustomerController.php` + `master/customer/index.blade.php` (CRUD, modal Alpine.js, filter wilayah, kalkulasi plafon, piutang, dan deposit).
- **Semen / Barang:** `BarangController.php` + `master/barang/index.blade.php` (CRUD semen zak & curah, kalkulasi margin estimasi).
- **Wilayah Distribusi:** `WilayahController.php` + `master/wilayah/index.blade.php` (CRUD wilayah, penghitung jumlah mitra toko, proteksi hapus berelasi).
- **Karyawan & Driver:** `KaryawanController.php` + `master/karyawan/index.blade.php` (CRUD karyawan, tab filter staf/driver/gudang/teknisi/manajemen).

### 4. Modul Account Receivable (AR & Penjualan)
- **Faktur Penjualan:** `FakturPenjualanController.php` + `keuangan/ar/faktur_penjualan.blade.php` (Penerbitan faktur `INV-YYYYMMDD-XXX`, validasi plafon kredit toko, pemotongan saldo deposit otomatis, pencatatan otomatis ke `list_piutang`).
- **List Piutang:** `PiutangController.php` + `keuangan/ar/list_piutang.blade.php` (Monitoring buku pembantu piutang, modal pelunasan cicilan, mutasi otomatis saldo customer & status faktur).
- **Deposit Customer:** `DepositCustomerController.php` + `keuangan/ar/deposit_customer.blade.php` (Monitoring saldo uang muka, riwayat mutasi masuk/keluar, modal top-up deposit interaktif).

### 5. Modul Account Payable (AP & Kas Keluar)
- **Pembelian SO Pabrik:** `PembelianSOController.php` + `keuangan/ap/pembelian_so.blade.php` (Penerbitan SO semen ke pabrik, kalkulasi volume zak & harga, alokasi gudang/plant).
- **Pengeluaran Kas:** `PengeluaranKasController.php` + `keuangan/ap/pengeluaran_kas.blade.php` (Pencatatan kas operasional kantor, BBM & Tol armada terhubung akun beban COA, pemotongan saldo rekening sumber).
- **Rilisan Kas Bon Supir:** `HutangSupplierController.php` + `keuangan/ap/list_rilisan.blade.php` (Rilisan uang jalan supir armada truk terintegrasi akun 1107).

### 6. Modul Akuntansi & Laporan Eksekutif
- **Bagan Akun (COA):** `KodeAkunController.php` + `keuangan/akuntansi/kode_akun.blade.php` (Klasifikasi aktiva, kewajiban, modal, pendapatan, beban, modal CRUD).
- **Buku Jurnal Umum:** `JurnalUmumController.php` + `keuangan/akuntansi/jurnal_umum.blade.php` (Entri transaksi double-entry debit-kredit berpasangan otomatis, verifikasi status keseimbangan saldo).
- **Aset Perusahaan:** `AsetPerusahaanController.php` + `keuangan/akuntansi/aset_perusahaan.blade.php` (Inventaris aset tetap armada truk, gudang, kalkulasi total nilai perolehan).
- **Laporan Eksekutif:** `LaporanEksekutifController.php`
  - `resources/views/laporan/neraca.blade.php`: Posisi keuangan aktiva vs passiva (kewajiban & ekuitas) real-time.
  - `resources/views/laporan/laba_rugi.blade.php`: Perhitungan pendapatan penjualan semen, HPP pabrik, biaya operasional, dan laba bersih setelah pajak.
  - `resources/views/laporan/arus_kas.blade.php`: Arus kas aktivitas operasional penerimaan customer dan kas keluar serta saldo akhir kas & bank.

---

## 🌐 Status Server & Endpoint
- **Laravel Artisan Dev Server:** `http://127.0.0.1:8000` (Running)
- **Vite Dev Server:** `http://localhost:5173` (Running)
- **Laragon Apache:** `http://localhost/laravel1/public` (200 OK)
- **MySQL Database:** `127.0.0.1:3306` (200 OK)
