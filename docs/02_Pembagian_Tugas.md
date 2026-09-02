# Panduan Kerja & Pembagian Tugas Tim (2 Pengembang)
**Proyek:** Sistem Informasi Akuntansi & Distribusi Semen Terpadu  
**Stack Teknologi:** Laravel 13 · PHP 8.3 · Tailwind CSS v4 · Alpine.js · MySQL Laragon  
**Status Fondasi:** Fondasi Inti Selesai (24 Tampilan View, RBAC Matriks, Migrasi & Seeder Berfungsi 100%)

---

## 📌 1. Kesiapan Fondasi Proyek (Status: Siap Bagi Tugas)

Seluruh fondasi inti telah disiapkan dan diverifikasi dengan kode status **`200 OK`**:
- [x] **Autentikasi & RBAC:** Multi-tabel (`super_account` dan `account`) dengan 10 peran pengguna dan kata sandi default `password123`.
- [x] **Database & Migrasi:** 28+ tabel bisnis termigrasi via `2026_09_01_000001_create_skema_database_lengkap.php`, `2026_09_02_000001_create_ongkos_kso_and_update_data_kso.php`, `2026_09_02_000002_update_status_perbaikan_column.php`, & `AutentikasiSeeder.php`.
- [x] **Navigasi Dinamis:** Sidebar otomatis memfilter menu sesuai peran aktif (didukung simulator role di topbar).
- [x] **Template View:** Seluruh file Blade di `resources/views/` sudah tersedia dengan struktur tabel, tombol aksi, filter pencarian, dan desain enterprise.

---

## 🧭 2. Pembagian Ruang Lingkup Kerja

| Pengembang | Domain Kerja | Peran / Jabatan PRD | Branch Utama Kerja | Status |
|---|---|---|---|---|
| **Developer 1** | **Core, Keuangan, Master Data & Eksekutif** | Super Admin, SPV Keuangan, Staff AR, Staff AP, Direktur & Manager | `web-dev1` | **100% Selesai ✅** |
| **Developer 2** | **Logistik, Distribusi, Gudang & Bengkel** | Dispatcher, SPV Operasional, SPV Gudang, Pengawas Driver, Pengawas Kendaraan | `web-dev2` | **100% Selesai ✅** |

---

## 👨‍💻 3. Checklist Tugas Rinci DEVELOPER 1 (Status: ✅ 100% Selesai & Terverifikasi)

**Tanggung Jawab:** Menyelesaikan logika CRUD, modal form input, perhitungan saldo/finansial, validasi data, dan standarisasi UI dropdown untuk modul Core & Keuangan.

### 3.1. Modul Super Admin (Kelola Akun Staf)
- **File Controller:** [`KelolaAkunController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Autentikasi/KelolaAkunController.php)
- **File View:** [`superadmin/kelola_akun.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/superadmin/kelola_akun.blade.php)
- **Model Terkait:** `Pengguna.php`, `Jabatan.php`, `SuperAccount.php`
- **Target Pengerjaan:**
  - [x] Tambah akun baru ke tabel `account` terhubung ke `data_karyawan` dan `jabatan`.
  - [x] Fitur Reset Password akun menjadi `password123` (bcrypt).
  - [x] Toggle status akun (`1: Aktif` / `0: Nonaktif`).
  - [x] Integrasi dropdown kustom pemilihan karyawan & peran RBAC.

### 3.2. Modul Master Data Sentral
- **File Controller:** [`CustomerController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Master/CustomerController.php), [`BarangController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Master/BarangController.php), [`WilayahController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Master/WilayahController.php), [`KaryawanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Master/KaryawanController.php), [`JenisAsetController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Master/JenisAsetController.php)
- **File View:** 
  - [`master/customer/index.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/master/customer/index.blade.php) (Tabel `data_customer`)
  - [`master/barang/index.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/master/barang/index.blade.php) (Tabel `data_semen`)
  - [`master/wilayah/index.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/master/wilayah/index.blade.php) (Tabel `data_wilayah`)
  - [`master/karyawan/index.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/master/karyawan/index.blade.php) (Tabel `data_karyawan`)
- **Target Pengerjaan:**
  - [x] CRUD Customer: Input nama toko, pemilik, no telp, limit piutang, plafon kredit, dan filter wilayah kustom.
  - [x] CRUD Produk Semen: Input nama merek, tipe zak/curah, harga beli pabrik, harga jual default, dan filter jenis kustom.
  - [x] CRUD Wilayah & Zonasi Distribusi (penghitung mitra toko dan proteksi hapus berelasi).
  - [x] CRUD Karyawan (Staf kantor, teknisi, supir, pengawas) dengan tab kategori dan modal dropdown kustom.
  - [x] CRUD Data Jenis Aset Kendaraan (Tabel `data_jenis_aset`).

### 3.3. Modul Account Receivable (AR & Penjualan)
- **File Controller:** [`FakturPenjualanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/AR/FakturPenjualanController.php), [`PiutangController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/AR/PiutangController.php), [`DepositCustomerController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/AR/DepositCustomerController.php)
- **File View:**
  - [`keuangan/ar/faktur_penjualan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/ar/faktur_penjualan.blade.php) (Tabel `faktur_penjualan` & `penjualan`)
  - [`keuangan/ar/list_piutang.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/ar/list_piutang.blade.php) (Tabel `list_piutang`)
  - [`keuangan/ar/deposit_customer.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/ar/deposit_customer.blade.php) (Tabel `list_deposit`)
- **Target Pengerjaan:**
  - [x] Input Faktur Penjualan Baru dengan opsi metode: `Tunai`, `Kredit (Piutang)`, atau `Potong Deposit` beserta validasi limit & auto-posting piutang.
  - [x] **CRUD Lengkap List Piutang (SPV Keuangan & Staff AR):**
    - [x] Tambah Catatan Piutang Baru dengan Dual-Mode Generator Nomor Faktur Otomatis (🔵 Daur Ulang Gap-Filling vs 🟣 Acak Anti-Tebak).
    - [x] Modal Detail Interaktif dengan Visual Progress Bar Pelunasan (% & Nominal) dan Sinkronisasi Plafon Kredit Toko.
    - [x] Analisis *Aging* Jatuh Tempo Real-Time (*"Lewat X Hari"*, *"Jatuh Tempo Hari Ini"*, *"X Hari Lagi"*, *"Lunas"*).
    - [x] Edit Data Piutang (penyesuaian tanggal jatuh tempo, status, nominal piutang).
    - [x] Form Pembayaran Cicilan & Pelunasan Cepat (Shortcut 25%, 50%, 75%, 100% Lunas).
    - [x] Hapus Catatan Piutang dengan sinkronisasi pengembalian saldo piutang customer di database.
  - [x] Top Up Saldo Deposit Customer & Mutasi Saldo otomatis.
  - [x] Filter status, filter customer & dropdown kustom interaktif pada seluruh tabel dan modal AR.
  - [x] Standarisasi Universal Generator Kode Otomatis (*Daur Ulang Slot Kosong / Gap-Filling* vs *Kode Acak Anti-Tebak*) dan Indikator **🕒 Riwayat Terakhir Diedit Real-Time** pada seluruh modul & sidebar aplikasi.

### 3.4. Modul Account Payable (AP & Pengeluaran Kas)
- **File Controller:** [`PembelianSOController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/AP/PembelianSOController.php), [`PengeluaranKasController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/AP/PengeluaranKasController.php), [`HutangSupplierController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/AP/HutangSupplierController.php)
- **File View:**
  - [`keuangan/ap/pembelian_so.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/ap/pembelian_so.blade.php) (Tabel `pembelian_so`)
  - [`keuangan/ap/pengeluaran_kas.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/ap/pengeluaran_kas.blade.php) (Tabel `pengeluaran`)
  - [`keuangan/ap/list_rilisan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/ap/list_rilisan.blade.php) (Tabel `rilisan`)
- **Target Pengerjaan:**
  - [x] Pencatatan Pembelian SO ke Pabrik Semen (alokasi customer dan gudang plant).
  - [x] Input Pengeluaran Kas Operasional (BBM armada, tol, operasional kantor) terhubung akun beban COA dan rekening bank.
  - [x] Pencatatan Rilisan Kas Bon / Uang Jalan Supir terhubung akun 1107.
  - [x] Filter kategori & dropdown kustom interaktif pada seluruh tabel dan modal AP.

### 3.5. Modul Akuntansi & Laporan Eksekutif
- **File Controller:** [`KodeAkunController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/Akuntansi/KodeAkunController.php), [`JurnalUmumController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/Akuntansi/JurnalUmumController.php), [`AsetPerusahaanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Keuangan/Akuntansi/AsetPerusahaanController.php), [`LaporanEksekutifController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Laporan/LaporanEksekutifController.php)
- **File View:**
  - [`keuangan/akuntansi/kode_akun.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/akuntansi/kode_akun.blade.php) (Tabel `data_kode_akun`)
  - [`keuangan/akuntansi/jurnal_umum.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/akuntansi/jurnal_umum.blade.php) (Tabel `jurnal_umum`)
  - [`keuangan/akuntansi/aset_perusahaan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/keuangan/akuntansi/aset_perusahaan.blade.php) (Tabel `data_aset`)
  - [`laporan/neraca.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/laporan/neraca.blade.php), [`laporan/laba_rugi.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/laporan/laba_rugi.blade.php), & [`laporan/arus_kas.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/laporan/arus_kas.blade.php)
- **Target Pengerjaan:**
  - [x] CRUD Data Kode Akun (Bagan Akun COA) terstruktur (`kode_akun`, `nama_akun`, `tipe_akun`, `kelompok_akun`, `saldo`), dual-mode generator (Daur Ulang vs Acak), dan proteksi hapus berelasi.
  - [x] Laporan Posisi Keuangan (Neraca Eksekutif) komparatif Aktiva vs Pasiva, kalkulasi otomatis aset/kewajiban/modal seimbang (balance), dan ekspor cetak PDF.
  - [x] Pencatatan Jurnal Umum Double-Entry otomatis dari transaksi dan manual adjustment.
  - [x] Ringkasan Laba Rugi & Arus Kas periode berjalan (Ekspor PDF/Cetak).
  - [x] Standardisasi Validasi NIK Resmi Indonesia (tepat 16 digit angka numerik) pada Master Karyawan, Driver, Customer.

---

## 🚚 4. Checklist Tugas Rinci DEVELOPER 2 (Status: ✅ 100% Selesai & Terverifikasi)

**Tanggung Jawab:** Menyelesaikan logika pengiriman, alokasi armada truk, penugasan driver supir, mutasi stok gudang semen, stock opname, kemitraan KSO, dan bengkel servis.

### 4.1. Modul Gudang & Manajemen Persediaan (SPV Gudang)
- **File Controller:** [`StokGudangController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Gudang/StokGudangController.php), [`StockOpnameController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Gudang/StockOpnameController.php)
- **File View:**
  - [`operasional/gudang/stok.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/gudang/stok.blade.php) (Tabel `list_gudang_so`)
  - [`operasional/gudang/opname.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/gudang/opname.blade.php) (Tabel `opname_gudang`)
- **Target Pengerjaan:**
  - [x] Standarisasi kolom Data Gudang SPV Gudang: `kode_gudang`, `nama_gudang`, `jenis_gudang`, `kode_barang`, `plant`, `harga_barang`, `stok_tersedia`.
  - [x] Standardisasi format kode gudang kombinasi huruf-angka (`GDG-PBJ1`, `GDG-PBJ2`) serta pembersihan record usang `GDG-PUSAT` dari database.
  - [x] Pemantauan stok per gudang dan riwayat mutasi stok (tambah masuk / kurang keluar / set fisik kuantitas).
  - [x] Formulir Stock Opname Fisik, kalkulasi selisih otomatis secara real-time, generator No. Opname cerdas, dan tombol persetujuan SPV Gudang yang langsung mensinkronkan stok fisik ke master gudang.

### 4.2. Modul Armada Kendaraan & Data Driver (Pengawas Driver & Pengawas Kendaraan)
- **File Controller:** [`KendaraanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Armada/KendaraanController.php), [`DriverController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Armada/DriverController.php)
- **File View:**
  - [`operasional/armada/kendaraan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/armada/kendaraan.blade.php) (Tabel `data_aset` & `data_jenis_aset`)
  - [`operasional/armada/driver.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/armada/driver.blade.php) (Tabel `data_karyawan` filter driver)
- **Target Pengerjaan:**
  - [x] CRUD Kendaraan: Plat nomor/kode aset, nama aset, merek, jenis aset, kapasitas zak/tonase, tanggal KIR & pajak, harga pembelian, serta integrasi tab Data Jenis Aset dalam satu halaman.
  - [x] CRUD Driver / Sopir: Generator kode supir cerdas (Mode Daur Ulang Slot Kosong vs Kode Acak Anti-Tebak), manajemen status supir (`Standby`, `Jalan`, `Cuti/Izin`), serta label **🕒 Riwayat Terakhir Diedit Real-Time** pada tiap baris.

### 4.3. Modul Dispatcher & Surat Jalan Pengiriman (Dispatcher & SPV Operasional)
- **File Controller:** [`SuratJalanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Pengiriman/SuratJalanController.php), [`KSOController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/KSO/KSOController.php)
- **File View:**
  - [`operasional/pengiriman/surat_jalan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/pengiriman/surat_jalan.blade.php) (Tabel `pengiriman`)
  - [`operasional/kso/index.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/kso/index.blade.php) (Tabel `data_kso` & `ongkos_kso`)
- **Target Pengerjaan:**
  - [x] Pembuatan Surat Jalan (SJ) pengiriman semen dengan generator nomor surat jalan otomatis.
  - [x] Dropdown pemilihan supir yang berstatus *Standby* & armada truk yang siap jalan.
  - [x] Perhitungan otomatis ongkos angkut berdasarkan tarif trayek dan jumlah muatan.
  - [x] Update status pengiriman (`Draft` -> `Muat` -> `Jalan` -> `Terkirim/Selesai`).
  - [x] Cetak dokumen Surat Jalan resmi format jalan sopir PT Pura Balkom Jaya.
  - [x] **CRUD Data KSO (Kerja Sama Operasional) & Ongkos KSO:** 2 Tab terpadu untuk master kemitraan KSO (upload file kontrak, nilai kontrak, masa aktif) dan standardisasi tarif trayek rute ongkos angkut KSO (`ongkos_kso`).

### 4.4. Modul Bengkel & Perbaikan Kendaraan (Pengawas Kendaraan)
- **File Controller:** [`PerbaikanKendaraanController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Bengkel/PerbaikanKendaraanController.php), [`PembelianSparepartController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Bengkel/PembelianSparepartController.php), [`SparepartController.php`](file:///d:/laragon/www/website-pbj/app/Http/Controllers/Operasional/Bengkel/SparepartController.php)
- **File View:**
  - [`operasional/bengkel/perbaikan.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/bengkel/perbaikan.blade.php) (Tabel `perbaikan_kendaraan`)
  - [`operasional/bengkel/pembelian_sparepart.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/bengkel/pembelian_sparepart.blade.php) (Tabel `pembelian_sparepart`)
  - [`operasional/bengkel/sparepart.blade.php`](file:///d:/laragon/www/website-pbj/resources/views/operasional/bengkel/sparepart.blade.php) (Tabel `list_sparepart`)
- **Target Pengerjaan:**
  - [x] Pembuatan Surat Perintah Kerja (SPK) servis kendaraan bengkel, generator nomor SPK otomatis, live kalkulasi biaya jasa + sparepart, ubah status cepat, dan cetak lembar SPK resmi.
  - [x] CRUD Faktur Pembelian & Pengadaan Sparepart dari supplier dengan live kalkulator total bayar dan auto-sync penambahan kuantitas fisik ke master stok sparepart.
  - [x] CRUD Katalog & Stok Sparepart Truk dengan 4 kartu KPI, modal mutasi cepat (masuk/keluar/atur), dan badge level ketersediaan stok (`Aman`, `Menipis`, `Habis`).

---

## 🔗 5. Kontrak Data Antar-Pengembang (Data Contract)

Agar tidak terjadi benturan integrasi (*integration conflict*):

1. **Penjualan (Dev 1) $\rightarrow$ Pengiriman (Dev 2):**
   - Saat Developer 1 membuat transaksi di `penjualan` berstatus `SIAP_KIRIM`, data tersebut langsung terbaca oleh Developer 2 di modul Dispatcher untuk diterbitkan `pengiriman` / surat jalan.
2. **Surat Jalan Selesai (Dev 2) $\rightarrow$ Pengurangan Stok (Dev 2) & HPP (Dev 1):**
   - Saat surat jalan disetujui, stok di `list_gudang_so` berkurang otomatis.
3. **Biaya Bengkel / Sparepart (Dev 2) $\rightarrow$ Pengeluaran Kas AP (Dev 1):**
   - SPK servis yang selesai di bengkel dapat diambil total biayanya oleh Developer 1 ke modul `pengeluaran`.

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
