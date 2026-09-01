# Panduan Kerja & Pembagian Tugas Tim (2 Pengembang)
**Proyek:** Sistem Informasi Akuntansi & Distribusi Semen Terpadu  
**Stack Teknologi:** Laravel 13 · PHP 8.3 · Tailwind CSS v4 · Alpine.js · MySQL Laragon  
**Status Fondasi:** Fondasi Inti Selesai (24 Tampilan View, RBAC Matriks, Migrasi & Seeder Berfungsi 100%)

---

## 📌 1. Kesiapan Fondasi Proyek (Status: Siap Bagi Tugas)

Seluruh fondasi inti telah disiapkan dan diverifikasi dengan kode status **`200 OK`**:
- [x] **Autentikasi & RBAC:** Multi-tabel (`super_account` dan `account`) dengan 10 peran pengguna dan kata sandi default `password123`.
- [x] **Database & Migrasi:** 28 tabel bisnis termigrasi via `2026_09_01_000001_create_skema_database_lengkap.php` & `AutentikasiSeeder.php`.
- [x] **Navigasi Dinamis:** Sidebar otomatis memfilter menu sesuai peran aktif (didukung simulator role di topbar).
- [x] **Template View:** Seluruh 24 file Blade di `resources/views/` sudah tersedia dengan struktur tabel, tombol aksi, filter pencarian, dan desain enterprise.

---

## 🧭 2. Pembagian Ruang Lingkup Kerja

| Pengembang | Domain Kerja | Peran / Jabatan PRD | Branch Utama Kerja |
|---|---|---|---|
| **Developer 1** | **Core, Keuangan, Master Data & Eksekutif** | Super Admin, SPV Keuangan, Staff AR, Staff AP, Direktur & Manager | `web-dev1` |
| **Developer 2** | **Logistik, Distribusi, Gudang & Bengkel** | Dispatcher, SPV Operasional, SPV Gudang, Pengawas Driver, Pengawas Kendaraan | `web-dev2` |

---

## 👨‍💻 3. Checklist Tugas Rinci DEVELOPER 1

**Tanggung Jawab:** Menyelesaikan logika CRUD, modal form input, perhitungan saldo/finansial, dan validasi data untuk modul Core & Keuangan.

### 3.1. Modul Super Admin (Kelola Akun Staf)
- **File Controller:** [KelolaAkunController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Autentikasi/KelolaAkunController.php)
- **File View:** [superadmin/kelola_akun.blade.php](file:///c:/laragon/www/laravel1/resources/views/superadmin/kelola_akun.blade.php)
- **Model Terkait:** `Pengguna.php`, `Jabatan.php`, `SuperAccount.php`
- **Target Pengerjaan:**
  - [ ] Tambah akun baru ke tabel `account` terhubung ke `data_karyawan` dan `jabatan`.
  - [ ] Fitur Reset Password akun menjadi `password123` (bcrypt).
  - [ ] Toggle status akun (`1: Aktif` / `0: Nonaktif`).

### 3.2. Modul Master Data Sentral
- **File Controller:** [CustomerController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/CustomerController.php), [BarangController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/BarangController.php), [WilayahController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/WilayahController.php), [KaryawanController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/KaryawanController.php)
- **File View:** 
  - [master/customer/index.blade.php](file:///c:/laragon/www/laravel1/resources/views/master/customer/index.blade.php) (Tabel `data_customer`)
  - [master/barang/index.blade.php](file:///c:/laragon/www/laravel1/resources/views/master/barang/index.blade.php) (Tabel `data_semen`)
  - [master/wilayah/index.blade.php](file:///c:/laragon/www/laravel1/resources/views/master/wilayah/index.blade.php) (Tabel `wilayah`)
  - [master/karyawan/index.blade.php](file:///c:/laragon/www/laravel1/resources/views/master/karyawan/index.blade.php) (Tabel `data_karyawan`)
- **Target Pengerjaan:**
  - [ ] CRUD Customer: Input nama toko, pemilik, no telp, limit piutang, dan plafon kredit.
  - [ ] CRUD Produk Semen: Input nama merek, tipe zak/curah, harga beli pabrik, dan harga jual default.
  - [ ] CRUD Wilayah & Zonasi Distribusi.
  - [ ] CRUD Karyawan (Staf kantor, teknisi, supir, pengawas).

### 3.3. Modul Account Receivable (AR & Penjualan)
- **File Controller:** [FakturPenjualanController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/FakturPenjualanController.php), [PiutangController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/PiutangController.php), [DepositCustomerController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/DepositCustomerController.php)
- **File View:**
  - [keuangan/ar/faktur_penjualan.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/ar/faktur_penjualan.blade.php) (Tabel `faktur_penjualan` & `penjualan`)
  - [keuangan/ar/list_piutang.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/ar/list_piutang.blade.php) (Tabel `piutang`)
  - [keuangan/ar/deposit_customer.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/ar/deposit_customer.blade.php) (Tabel `deposit_customer`)
- **Target Pengerjaan:**
  - [ ] Input Faktur Penjualan Baru dengan opsi metode: `Tunai`, `Kredit (Piutang)`, atau `Potong Deposit`.
  - [ ] List Piutang & Form Pelunasan / Cicilan Pembayaran Piutang Toko.
  - [ ] Top Up Saldo Deposit Customer & Mutasi Saldo.

### 3.4. Modul Account Payable (AP & Pengeluaran Kas)
- **File Controller:** [PembelianSOController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/PembelianSOController.php), [PengeluaranKasController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/PengeluaranKasController.php), [HutangSupplierController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/HutangSupplierController.php)
- **File View:**
  - [keuangan/ap/pembelian_so.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/ap/pembelian_so.blade.php)
  - [keuangan/ap/pengeluaran_kas.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/ap/pengeluaran_kas.blade.php)
  - [keuangan/ap/list_rilisan.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/ap/list_rilisan.blade.php)
- **Target Pengerjaan:**
  - [ ] Pencatatan Pembelian SO ke Pabrik Semen (PT Semen Indonesia / Distributor).
  - [ ] Input Pengeluaran Kas Operasional (BBM armada, tol, operasional kantor).
  - [ ] Pencatatan Rilisan Kas Bon / Uang Jalan Supir.

### 3.5. Modul Akuntansi & Laporan Eksekutif
- **File Controller:** [KodeAkunController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/Akuntansi/KodeAkunController.php), [JurnalUmumController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/Akuntansi/JurnalUmumController.php), [AsetPerusahaanController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/Akuntansi/AsetPerusahaanController.php), [LaporanEksekutifController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Laporan/LaporanEksekutifController.php)
- **File View:**
  - [keuangan/akuntansi/kode_akun.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/akuntansi/kode_akun.blade.php) (Tabel `kode_akun`)
  - [keuangan/akuntansi/jurnal_umum.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/akuntansi/jurnal_umum.blade.php) (Tabel `jurnal_umum` & `detail_jurnal`)
  - [keuangan/akuntansi/aset_perusahaan.blade.php](file:///c:/laragon/www/laravel1/resources/views/keuangan/akuntansi/aset_perusahaan.blade.php) (Tabel `aset_perusahaan`)
  - [laporan/neraca.blade.php](file:///c:/laragon/www/laravel1/resources/views/laporan/neraca.blade.php) & [laporan/laba_rugi.blade.php](file:///c:/laragon/www/laravel1/resources/views/laporan/laba_rugi.blade.php)
- **Target Pengerjaan:**
  - [ ] CRUD Bagan Akun Standar (COA) dengan saldo normal Debet/Kredit.
  - [ ] Pencatatan Jurnal Umum Double-Entry otomatis dari transaksi dan manual adjustment.
  - [ ] Ringkasan Neraca & Perhitungan Laba Rugi periode berjalan (Ekspor PDF/Cetak).

---

## 🚚 4. Checklist Tugas Rinci DEVELOPER 2

**Tanggung Jawab:** Menyelesaikan logika pengiriman, alokasi armada truk, penugasan driver supir, mutasi stok gudang semen, stock opname, dan bengkel servis.

### 4.1. Modul Gudang & Manajemen Persediaan
- **File Controller:** [StokGudangController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Gudang/StokGudangController.php), [OpnameGudangController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Gudang/OpnameGudangController.php)
- **File View:**
  - [operasional/gudang/stok.blade.php](file:///c:/laragon/www/laravel1/resources/views/operasional/gudang/stok.blade.php) (Tabel `gudang_semen` & `mutasi_stok`)
  - [operasional/gudang/opname.blade.php](file:///c:/laragon/www/laravel1/resources/views/operasional/gudang/opname.blade.php) (Tabel `stock_opname` & `detail_stock_opname`)
- **Target Pengerjaan:**
  - [ ] Pemantauan stok per gudang dan riwayat mutasi stok (masuk dari pabrik / keluar ke surat jalan).
  - [ ] Formulir Stock Opname Fisik, kalkulasi selisih otomatis, dan tombol persetujuan SPV Gudang.

### 4.2. Modul Armada Kendaraan & Data Driver
- **File Controller:** [KendaraanController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Armada/KendaraanController.php), [DriverController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Armada/DriverController.php)
- **File View:**
  - [operasional/armada/kendaraan.blade.php](file:///c:/laragon/www/laravel1/resources/views/operasional/armada/kendaraan.blade.php) (Tabel `kendaraan`)
  - [operasional/armada/driver.blade.php](file:///c:/laragon/www/laravel1/resources/views/operasional/armada/driver.blade.php) (Tabel `data_karyawan` filter driver)
- **Target Pengerjaan:**
  - [ ] CRUD Kendaraan: Plat nomor, tipe truk (Tronton/Colt Diesel), kapasitas zak/tonase, dan status operasi.
  - [ ] Manajemen status supir (`Standby`, `Dalam Perjalanan`, `Cuti/Izin`).

### 4.3. Modul Dispatcher & Surat Jalan Pengiriman
- **File Controller:** [SuratJalanController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Pengiriman/SuratJalanController.php), [OngkosAngkutController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Pengiriman/OngkosAngkutController.php), [SalesOrderController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Pengiriman/SalesOrderController.php)
- **File View:**
  - [operasional/pengiriman/surat_jalan.blade.php](file:///c:/laragon/www/laravel1/resources/views/operasional/pengiriman/surat_jalan.blade.php) (Tabel `surat_jalan` & `pengiriman`)
  - [operasional/pengiriman/ongkos_angkut.blade.php](file:///c:/laragon/www/laravel1/resources/views/operasional/pengiriman/ongkos_angkut.blade.php) (Tabel `ongkos_angkut`)
- **Target Pengerjaan:**
  - [ ] Pembuatan Surat Jalan (SJ) dari Sales Order (SO) yang siap kirim.
  - [ ] Dropdown pemilihan supir yang berstatus *Standby* & armada truk yang siap jalan.
  - [ ] Perhitungan otomatis ongkos angkut berdasarkan zonasi wilayah tujuan.
  - [ ] Update status pengiriman (`Draft` -> `Muat` -> `Dalam Perjalanan` -> `Terkirim/Selesai`).
  - [ ] Cetak dokumen Surat Jalan PDF (format cetak jalan sopir).

### 4.4. Modul Bengkel & Perbaikan Kendaraan
- **File Controller:** [PerbaikanKendaraanController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Bengkel/PerbaikanKendaraanController.php), [SparepartController.php](file:///c:/laragon/www/laravel1/app/Http/Controllers/Operasional/Bengkel/SparepartController.php)
- **File View:**
  - [operasional/bengkel/perbaikan.blade.php](file:///c:/laragon/www/laravel1/resources/views/operasional/bengkel/perbaikan.blade.php) (Tabel `surat_perintah_kerja` & `detail_perbaikan`)
- **Target Pengerjaan:**
  - [ ] Pembuatan Surat Perintah Kerja (SPK) servis kendaraan bengkel.
  - [ ] Input sparepart yang diganti dan kalkulasi total biaya perbaikan armada.

---

## 🔗 5. Kontrak Data Antar-Pengembang (Data Contract)

Agar tidak terjadi benturan integrasi (*integration conflict*):

1. **Penjualan (Dev 1) $\rightarrow$ Pengiriman (Dev 2):**
   - Saat Developer 1 membuat transaksi di `faktur_penjualan` / `sales_order` berstatus `SIAP_KIRIM`, data tersebut langsung terbaca oleh Developer 2 di modul Dispatcher untuk diterbitkan `surat_jalan`.
2. **Surat Jalan Selesai (Dev 2) $\rightarrow$ Pengurangan Stok (Dev 2) & HPP (Dev 1):**
   - Saat surat jalan disetujui, stok di `gudang_semen` berkurang otomatis dan tercatat di `mutasi_stok`.
3. **Biaya Bengkel / Sparepart (Dev 2) $\rightarrow$ Pengeluaran Kas AP (Dev 1):**
   - SPK servis yang selesai di bengkel dapat diambil total biayanya oleh Developer 1 ke modul `pengeluaran_kas`.

---

## 🌿 6. Alur Kerja Git Tim (Branching Protocol)

```text
[main] ─────────────────────────────────────────────────────────────► [main (Produksi)]
  │                                                            ▲
  ├──► [web-dev1] ───────────────────► (PR & Merge ke main) ──┤
  │                                                            │
  └──► [web-dev2] ───────────────────► (PR & Merge ke main) ──┘
```

1. **Sinkronisasi Awal Pengembang:**
   ```powershell
   git checkout main
   git pull origin main
   # Developer 1 bekerja di branch: web-dev1
   # Developer 2 membuat branch baru: git checkout -b web-dev2
   ```
2. **Standar Setup Lokal Pengembang Baru:**
   ```powershell
   composer install
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```
3. **Standar Penamaan Bahasa Indonesia:** Seluruh variabel, fungsi controller, dan komentar kode wajib mengikuti Bahasa Indonesia sesuai aturan sistem.
