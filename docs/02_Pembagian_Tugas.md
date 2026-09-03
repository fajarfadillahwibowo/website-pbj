# Panduan Kerja & Pembagian Tugas Tim (2 Pengembang)
**Proyek:** Sistem Informasi Akuntansi & Distribusi Semen Terpadu  
**Stack Teknologi:** Laravel 13 · PHP 8.3 · Tailwind CSS v4 · Alpine.js · MySQL Laragon  
**Status Fondasi:** Fondasi Inti Selesai (24 Tampilan View, RBAC Matriks, Migrasi & Seeder Berfungsi 100%)

---

## 📌 1. Kesiapan Fondasi Proyek (Status: Siap Bagi Tugas)

Seluruh fondasi inti telah disiapkan dan diverifikasi dengan kode status **`200 OK` (25 Tampilan View Aktif)**:
- [x] **Autentikasi & RBAC:** Multi-tabel (`super_account` dan `account`) dengan 10 peran pengguna dan kata sandi default `password123`.
- [x] **Database & Migrasi:** 29+ tabel bisnis termigrasi via:
  - `2026_09_01_000001_create_skema_database_lengkap.php`
  - `2026_09_02_000001_create_ongkos_kso_and_update_data_kso.php`
  - `2026_09_02_000002_update_status_perbaikan_column.php`
  - `2026_09_02_000003_buat_tabel_data_toko_bangunan.php` (Relasi 1:N Customer ke Cabang/Outlet)
  - `2026_09_02_000004_update_kolom_pembelian_so.php` (Kolom `nomor_lo`, `jenis_pengiriman`, `qty_pengambilan` sesuai patokan `docs/data wajib ada`).
- [x] **Navigasi Dinamis & Proteksi Read-Only:** Sidebar otomatis memfilter menu sesuai peran aktif dan memberikan badge **Lihat (Read-Only)** pada modul di luar wewenang modifikasi peran.
- [x] **Template View:** Seluruh 25 file Blade di `resources/views/` (termasuk menu baru **List SO**) sudah tersedia dengan desain enterprise, dropdown kustom, modal `overflow-visible`, dan input tanggal standar.

---

## 🧭 2. Pembagian Ruang Lingkup Kerja

| Pengembang | Domain Kerja | Peran / Jabatan PRD | Branch Utama Kerja | Status |
|---|---|---|---|---|
| **Developer 1** | **Core, Keuangan, Master Data & Eksekutif** | Super Admin, SPV Keuangan, Staff AR, Staff AP, Direktur & Manager | `web-dev1` | **95% Selesai (Menunggu Auto-Journal Service)** |
| **Developer 2** | **Logistik, Distribusi, Gudang & Bengkel** | Dispatcher, SPV Operasional, SPV Gudang, Pengawas Driver, Pengawas Kendaraan | `web-dev2` | **100% Selesai & Terintegrasi ✅** |

---

## 🔄 3. Riwayat Pembaruan Setelah Pull (`origin/main`)

Berikut adalah rekapitulasi perubahan yang telah diterapkan setelah sinkronisasi dan pull terakhir:

1. **Sinkronisasi Commit `5c104ee` (Hak Akses Read-Only Driver)**:
   - Pembatasan hak akses modul Data Karyawan (Driver) menjadi *read-only* khusus untuk peran SPV Operasional pada `DriverController.php`, `sidebar.blade.php`, dan `driver.blade.php`.
2. **Penyelarasan Skema Database Patokan (`docs/data wajib ada`)**:
   - Menambahkan kolom `nomor_lo`, `jenis_pengiriman`, dan `qty_pengambilan` ke tabel `pembelian_so` via migrasi `2026_09_02_000004_update_kolom_pembelian_so.php`.
   - Menambahkan properti fillable terkait pada model `PembelianSO.php`.
3. **Penyediaan Modul Baru 'List SO' (Monitoring Kuota Semen Real-Time)**:
   - Controller [`ListSOController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/ListSOController.php) untuk agregasi kuota zak vs realisasi terambil.
   - View [`list_so.blade.php`](file:///c:/laragon/www/laravel1/resources/views/keuangan/ap/list_so.blade.php) dengan progress bar kuota dan kartu statistik KPI.
   - Rute `/keuangan/ap/list-so` terdaftar di `routes/web.php`.
4. **Standardisasi Mode Read-Only Granular untuk 3 Peran Keuangan**:
   - Method `apakahReadOnly(kodeModul)` pada `resources/views/layouts/app.blade.php`.
   - Proteksi tombol aksi dan badge indikator *Mode Lihat Saja (Read-Only)* pada `faktur_penjualan.blade.php`, `pembelian_so.blade.php`, `pengeluaran_kas.blade.php`, dan `list_rilisan.blade.php`.
   - Pemutakhiran sidebar dengan penanda status baca.
5. **Keterangan Riwayat Terakhir Diedit pada Data Ongkos Angkut (SPV Operasional)**:
   - Menambahkan kolom keterangan riwayat pembaruan data terakhir (*timestamp last edited*) pada kolom aksi master tarif ongkos angkut selaras dengan modul master lainnya.
6. **Standardisasi NIK 16 Digit & Upload Berkas Karyawan Driver (Dispatcher)**:
   - Pembuatan format khusus No. KTP/NIK 16 digit khas Indonesia dengan validasi otomatis.
   - Penambahan fitur upload Foto KTP dan Dokumen Kontrak Kerja supir (maksimal 2 MB) lengkap dengan pratinjau instan dan tombol unduh dokumen.
7. **Penyelarasan Fitur CRUD 'Data Jenis Aset' Antar-Modul**:
   - Menyelaraskan 100% fitur CRUD database *Data Jenis Aset* pada halaman Data Kendaraan (Dispatcher) dengan fitur pada *Aset Perusahaan* (SPV Keuangan).
8. **Revisi Identitas Perusahaan & Logo HD PT Putra Balkom Jaya**:
   - Pemutakhiran nama perusahaan menjadi "Putra Balkom Jaya" di seluruh antarmuka dan penambahan logo resmi beresolusi tinggi (HD) di atas nama PT tanpa pengurangan resolusi atau pemotongan.
9. **Mesin Dynamic Content Swapping SPA pada Seluruh Sidebar & Semua Role**:
   - Pengubahan navigasi sidebar menjadi SPA dinamis parsial: saat menu diklik, hanya konten fitur (`<main id="kontenUtama">`) yang terefresh, sedangkan sidebar tetap persisten dan tidak pernah terefresh.
   - Mengeliminasi tuntas bug layar melompat ke atas (*jump-to-top bug*) pada seluruh 10 role/aktor.
   - Indikator loading bar halus (*YouTube/GitHub style*), dukungan tombol navigasi browser (*Back/Forward*), dan pencarian filter GET tanpa reload sidebar.

---

## ⏳ 4. Daftar Tugas yang Belum Tereksekusi (Pending Tasks)

Berikut adalah daftar pekerjaan yang belum dieksekusi dan dijadwalkan untuk tahap berikutnya:

### A. Mesin Otomasi Jurnal Terpadu (Auto-Journal Engine)
- [ ] **Service Class `MesinJurnalOtomatis`**:
  - [ ] Pembuatan `App\Services\Keuangan\MesinJurnalOtomatis.php` dengan transaksi atomik `DB::transaction`.
  - [ ] Validasi keseimbangan otomatis ($\sum \text{Debit} = \sum \text{Kredit}$) sebelum simpan jurnal.
  - [ ] Pemeriksaan *Idempotency Guard* berbasis `referensi_transaksi` untuk mencegah duplikasi jurnal ganda.
- [ ] **Integrasi Controller ke Auto-Journal**:
  - [ ] Auto-journal Faktur Penjualan (`FakturPenjualanController@store`): Penjualan tunai, transfer, kredit tempo piutang, dan potong deposit.
  - [ ] Auto-journal Penebusan SO Pabrik (`PembelianSOController@store`): Akun persediaan vs kas/bank/uang muka pabrik.
  - [ ] Auto-journal Pengeluaran Kas AP (`PengeluaranKasController@store`): Pembebanan biaya BBM, tol, servis, dan kantor.
  - [ ] Auto-journal Rilisan Uang Jalan Supir (`HutangSupplierController@store`): Kas rilisan uang jalan (Akun 1107).
  - [ ] Auto-update `saldo_berjalan` pada tabel `data_kode_akun` secara real-time.

### B. Sinkronisasi Operasional ke Kuota SO & Upah Driver
- [ ] **Pengurangan Kuota Otomatis pada `pembelian_so`**:
  - [ ] Saat Surat Jalan diterbitkan di modul Dispatcher (`SuratJalanController`), otomatis menambahkan nilai `qty_pengambilan` pada SO terkait dan mengubah status menjadi `Selesai` jika kuota terpenuhi.
- [ ] **Pencatatan Upah Ritase Supir Otomatis**:
  - [ ] Kalkulasi upah ritase driver per surat jalan selesai ke tabel rekap upah driver.

### C. Ekspor Laporan & Cetak Dokumen Resmi
- [ ] Integrasi cetak/ekspor PDF untuk Laporan Neraca, Laba Rugi, dan Arus Kas.
- [ ] Template cetak invoice faktur penjualan resmi PT PBJ.

---

## 👨‍💻 5. Checklist Tugas Rinci DEVELOPER 1 (Status: 95% Selesai)

**Tanggung Jawab:** Menyelesaikan logika CRUD, modal form input, perhitungan saldo/finansial, validasi data, dan standarisasi UI dropdown untuk modul Core & Keuangan.

### 5.1. Modul Super Admin (Kelola Akun Staf)
- **File Controller:** [`KelolaAkunController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Autentikasi/KelolaAkunController.php)
- **File View:** [`superadmin/kelola_akun.blade.php`](file:///c:/laragon/www/laravel1/resources/views/superadmin/kelola_akun.blade.php)
- **Target Pengerjaan:**
  - [x] Tambah akun baru ke tabel `account` terhubung ke `data_karyawan` dan `jabatan`.
  - [x] Fitur Reset Password akun menjadi `password123` (bcrypt).
  - [x] Toggle status akun (`1: Aktif` / `0: Nonaktif`).
  - [x] Integrasi dropdown kustom pemilihan karyawan & peran RBAC.

### 5.2. Modul Master Data Sentral
- **File Controller:** [`CustomerController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/CustomerController.php), [`TokoBangunanController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/TokoBangunanController.php), [`BarangController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/BarangController.php), [`WilayahController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/WilayahController.php), [`KaryawanController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/KaryawanController.php), [`JenisAsetController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Master/JenisAsetController.php)
- **File View:** 
  - [`master/customer/index.blade.php`](file:///c:/laragon/www/laravel1/resources/views/master/customer/index.blade.php) (Tabel `data_customer`)
  - [`master/toko_bangunan/index.blade.php`](file:///c:/laragon/www/laravel1/resources/views/master/toko_bangunan/index.blade.php) (Tabel `data_toko_bangunan`)
  - [`master/barang/index.blade.php`](file:///c:/laragon/www/laravel1/resources/views/master/barang/index.blade.php) (Tabel `data_semen`)
  - [`master/wilayah/index.blade.php`](file:///c:/laragon/www/laravel1/resources/views/master/wilayah/index.blade.php) (Tabel `data_wilayah`)
  - [`master/karyawan/index.blade.php`](file:///c:/laragon/www/laravel1/resources/views/master/karyawan/index.blade.php) (Tabel `data_karyawan`)
- **Target Pengerjaan:**
  - [x] CRUD Customer (Entitas Pemilik & Plafon Kredit Terpusat).
  - [x] CRUD Toko Bangunan & Proyek Cabang (Relasi 1:N Customer ke Cabang).
  - [x] CRUD Produk Semen dengan generator kode `SMN-xxx`.
  - [x] CRUD Wilayah & Zonasi Distribusi dengan generator `WLY-xxx`.
  - [x] CRUD Karyawan dengan generator kode per jabatan (`ADM-`, `KEU-`, `SAR-`, `SAP-`, `DSP-`, `DRV-`, dll.).
  - [x] CRUD Jenis Aset Kendaraan (`data_jenis_aset`).

### 5.3. Modul Account Receivable (AR & Penjualan)
- **File Controller:** [`FakturPenjualanController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/FakturPenjualanController.php), [`PiutangController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/PiutangController.php), [`DepositCustomerController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/DepositCustomerController.php)
- **Target Pengerjaan:**
  - [x] Input Faktur Penjualan Baru dengan pilihan Toko Bangunan & auto-detect Customer Pemilik.
  - [x] Opsi metode pembayaran: `Tunai`, `Transfer`, `Kredit / Piutang`, `Potong Deposit`.
  - [x] List Piutang & Form Pelunasan / Cicilan Pembayaran Piutang Toko.
  - [x] Top Up Saldo Deposit Customer & Mutasi Saldo.
  - [x] Proteksi Read-Only untuk Staff AP.

### 5.4. Modul Account Payable (AP & Pengeluaran Kas)
- **File Controller:** [`PembelianSOController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/PembelianSOController.php), [`ListSOController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/ListSOController.php), [`PengeluaranKasController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/PengeluaranKasController.php), [`HutangSupplierController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/HutangSupplierController.php)
- **Target Pengerjaan:**
  - [x] Input Pembelian SO ke Pabrik Semen SIG.
  - [x] Monitoring List SO & Realisasi Kuota Pengambilan Semen per Nomor SO/LO.
  - [x] Input Pengeluaran Kas Operasional (BBM armada, tol, operasional kantor).
  - [x] Pencatatan Rilisan Kas Bon / Uang Jalan Supir (Akun 1107).
  - [x] Proteksi Read-Only untuk Staff AR.

### 5.5. Modul Akuntansi & Laporan Eksekutif
- **File Controller:** [`KodeAkunController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/Akuntansi/KodeAkunController.php), [`JurnalUmumController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/Akuntansi/JurnalUmumController.php), [`AsetPerusahaanController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/Akuntansi/AsetPerusahaanController.php), [`LaporanEksekutifController.php`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Laporan/LaporanEksekutifController.php)
- **Target Pengerjaan:**
  - [x] CRUD Bagan Akun Standar (COA) dengan saldo normal Debit/Kredit.
  - [x] Pencatatan Jurnal Umum Double-Entry manual.
  - [x] Aset Tetap Perusahaan & Amortisasi Penyusutan.
  - [x] Laporan Neraca, Laba Rugi, & Arus Kas periode berjalan.

---

## 🚚 6. Checklist Tugas Rinci DEVELOPER 2 (Status: ✅ 100% Selesai & Terverifikasi)

**Tanggung Jawab:** Menyelesaikan logika pengiriman, alokasi armada truk, penugasan driver supir, mutasi stok gudang semen, stock opname, kemitraan KSO, master data ongkos angkut, dan bengkel servis.

### 6.1. Modul Gudang & Manajemen Persediaan (SPV Gudang)
- **File Controller:** [`StokGudangController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Gudang/StokGudangController.php), [`StockOpnameController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Gudang/StockOpnameController.php)
- **File View:**
  - [`operasional/gudang/stok.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/gudang/stok.blade.php) (Tabel `list_gudang_so`)
  - [`operasional/gudang/opname.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/gudang/opname.blade.php) (Tabel `opname_gudang`)
- **Target Pengerjaan:**
  - [x] Pemantauan stok per gudang dan riwayat mutasi stok (tambah masuk / kurang keluar / set fisik kuantitas).
  - [x] Formulir Stock Opname Fisik, kalkulasi selisih otomatis secara real-time, generator No. Opname cerdas, dan tombol persetujuan SPV Gudang yang langsung mensinkronkan stok fisik ke master gudang.

### 6.2. Modul Armada Kendaraan & Data Driver (Pengawas Driver & Pengawas Kendaraan)
- **File Controller:** [`KendaraanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Armada/KendaraanController.php), [`DriverController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Armada/DriverController.php)
- **File View:**
  - [`operasional/armada/kendaraan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/armada/kendaraan.blade.php) (Tabel `data_aset` & `data_jenis_aset`)
  - [`operasional/armada/driver.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/armada/driver.blade.php) (Tabel `data_karyawan` filter driver)
- **Target Pengerjaan:**
  - [x] CRUD Kendaraan: Plat nomor/kode aset, nama aset, merek, jenis aset, kapasitas zak/tonase, tanggal KIR & pajak, harga pembelian, serta integrasi tab Data Jenis Aset dalam satu halaman.
  - [x] CRUD Driver / Sopir: Generator kode supir cerdas (Mode Daur Ulang Slot Kosong vs Kode Acak Anti-Tebak), manajemen status supir (`Standby`, `Jalan`, `Cuti/Izin`), serta label **🕒 Riwayat Terakhir Diedit Real-Time** pada tiap baris.
  - [x] **Format Khusus NIK / No. KTP 16 Digit:** Validasi input angka 16 digit khas Indonesia dengan pesan error dinamis pada modal tambah/edit supir.
  - [x] **Upload Berkas Karyawan (Foto KTP & Dokumen Kontrak Kerja):** Fitur upload file maksimal 2 MB dengan validasi tipe file, pratinjau gambar instan, dan tautan unduh/lihat dokumen terintegrasi.
  - [x] **Penyelarasan Fitur CRUD 'Data Jenis Aset':** CRUD jenis aset pada Data Kendaraan (Dispatcher) 100% selaras dan konsisten dengan master Aset Perusahaan (SPV Keuangan).
  - [x] **Pembatasan RBAC Role SPV Operasional:** Modul *Data Karyawan (Driver)* diset menjadi **Read-Only (Hanya Lihat)** untuk SPV Operasional dengan proteksi frontend (sembunyikan tombol Tambah/Edit/Hapus) dan proteksi backend `DriverController.php`. SPV Operasional hanya memantau data driver armada dan tidak dapat melihat karyawan selain driver.

### 6.3. Modul Dispatcher, Surat Jalan & Ongkos Angkut (Dispatcher & SPV Operasional)
- **File Controller:** [`SuratJalanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Pengiriman/SuratJalanController.php), [`OngkosAngkutController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Pengiriman/OngkosAngkutController.php), [`KSOController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/KSO/KSOController.php)
- **File View:**
  - [`operasional/pengiriman/surat_jalan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/pengiriman/surat_jalan.blade.php) (Tabel `pengiriman`)
  - [`operasional/pengiriman/ongkos_angkut.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/pengiriman/ongkos_angkut.blade.php) (Tabel `data_ongkos_angkut`)
  - [`operasional/kso/index.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/kso/index.blade.php) (Tabel `data_kso` & `ongkos_kso`)
- **Target Pengerjaan:**
  - [x] Pembuatan Surat Jalan (SJ) pengiriman semen dengan generator nomor surat jalan otomatis.
  - [x] Dropdown pemilihan supir yang berstatus *Standby* & armada truk yang siap jalan.
  - [x] Perhitungan otomatis ongkos angkut berdasarkan tarif trayek dan jumlah muatan.
  - [x] Update status pengiriman (`Draft` -> `Muat` -> `Jalan` -> `Terkirim/Selesai`).
  - [x] Cetak dokumen Surat Jalan resmi format jalan sopir PT Putra Balkom Jaya.
  - [x] **CRUD Master Data Ongkos Angkut (9 Atribut):** Implementasi modul tarif pengiriman distribusi dengan kolom lengkap: `kode_oa`, `nama_oa`, `kode_gudang`, `kontrak_oa`, `muatan_oa`, `harga_oa`, `harga_kso`, `harga_kso_khusus`, `wilayah_oa` dilengkapi filter pencarian, smart auto-numbering, dan kalkulator KPI.
  - [x] **Keterangan Riwayat Terakhir Diedit pada Ongkos Angkut:** Menampilkan badge waktu dan tanggal pembaruan data terakhir pada kolom aksi untuk transparansi audit SPV Operasional.
  - [x] **CRUD Data KSO (Kerja Sama Operasional) & Ongkos KSO:** 2 Tab terpadu untuk master kemitraan KSO (upload file kontrak, nilai kontrak, masa aktif) dan standardisasi tarif trayek rute ongkos angkut KSO (`ongkos_kso`).

### 6.4. Modul Bengkel & Perbaikan Kendaraan (Pengawas Kendaraan)
- **File Controller:** [`PerbaikanKendaraanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Bengkel/PerbaikanKendaraanController.php), [`PembelianSparepartController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Bengkel/PembelianSparepartController.php), [`SparepartController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Bengkel/SparepartController.php)
- **File View:**
  - [`operasional/bengkel/perbaikan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/bengkel/perbaikan.blade.php) (Tabel `perbaikan_kendaraan`)
  - [`operasional/bengkel/pembelian_sparepart.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/bengkel/pembelian_sparepart.blade.php) (Tabel `pembelian_sparepart`)
  - [`operasional/bengkel/sparepart.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/bengkel/sparepart.blade.php) (Tabel `list_sparepart`)
- **Target Pengerjaan:**
  - [x] Pembuatan Surat Perintah Kerja (SPK) servis kendaraan bengkel, generator nomor SPK otomatis, live kalkulasi biaya jasa + sparepart, ubah status cepat, dan cetak lembar SPK resmi.
  - [x] CRUD Faktur Pembelian & Pengadaan Sparepart dari supplier dengan live kalkulator total bayar dan auto-sync penambahan kuantitas fisik ke master stok sparepart.
  - [x] CRUD Katalog & Stok Sparepart Truk dengan 4 kartu KPI, modal mutasi cepat (masuk/keluar/atur), dan badge level ketersediaan stok (`Aman`, `Menipis`, `Habis`).

### 6.5. Modul Branding Perusahaan & Arsitektur Navigasi SPA Terpadu (Lintas Seluruh Role)
- **File Inti:** [`resources/views/layouts/app.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/layouts/app.blade.php), [`resources/views/layouts/sidebar.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/layouts/sidebar.blade.php), [`resources/views/layouts/header.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/layouts/header.blade.php)
- **Target Pengerjaan:**
  - [x] **Revisi Identitas Perusahaan & Logo HD PT Putra Balkom Jaya:** Penyesuaian nama PT menjadi "PT Putra Balkom Jaya" dan logo HD diposisikan di atas nama PT dengan kualitas grafis tinggi tanpa terpotong.
  - [x] **Mesin SPA Dynamic Content Swapping (Sidebar Tidak Kerefresh):** Navigasi sidebar berjalan secara parsial tanpa full page reload pada seluruh 29 menu dan seluruh 10 role/aktor.
  - [x] **Eliminasi Tuntas Bug Layar Melompat ke Atas (Jump-to-Top):** Menghilangkan scroll jump saat mengklik menu bawah (seperti *Laporan Laba Rugi* atau *Laporan Neraca* pada role SPV Keuangan), posisi scroll sidebar tersimpan dan dipulihkan 100% presisi.
  - [x] **Indikator Loading Bar Halus:** Progres bar gradien modern (YouTube/GitHub style) di bagian paling atas layar saat transisi konten.
  - [x] **Sinkronisasi State Role Instan:** State role sinkron secara real-time antara frontend (`localStorage`) dan sesi backend Laravel via `/api/sinkronisasi-role`.
  - [x] **Dukungan Penuh Browser History (`popstate`) & Form Filter GET:** Tombol Back/Forward browser dan form pencarian filter berjalan mulus tanpa reload sidebar.

---

## 🌿 7. Alur Kerja Git Tim (Branching Protocol)

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
   ```
2. **Standar Setup Lokal:**
   ```powershell
   composer install
   php artisan migrate
   php artisan db:seed
   php artisan serve
   ```
3. **Standar Penamaan Bahasa Indonesia:** Seluruh variabel, fungsi controller, dan komentar kode wajib mengikuti Bahasa Indonesia sesuai aturan sistem.

