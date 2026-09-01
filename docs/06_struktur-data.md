# Rencana Arsitektur & Struktur Folder Laravel (Domain & Sidebar Modular)

Sebagai Senior Fullstack Web Developer Laravel, saya telah merancang struktur folder dan pengorganisasian file berbasis **Domain Modular / Sidebar Halaman** untuk proyek **Sistem Informasi Akuntansi & Distribusi Semen Terpadu**.

Tujuan arsitektur ini:
1. **Clean Code & Modul Terisolasi**: Setiap fitur/halaman di sidebar memiliki ruang tersendiri sehingga pengembang (Developer 1 & 2) tidak bingung atau memicu *merge conflict*.
2. **Standard Namespace Laravel 13**: Menggunakan PSR-4 Namespace yang rapi untuk Controller dan Model.
3. **Persistensi & Keterbacaan**: Memudahkan pelacakan bug, penambahan fitur baru, serta pengujian (*testing*).

---

## User Review Required

> [!IMPORTANT]
> - **Penamaan Bahasa Indonesia**: Sesuai dengan aturan standar proyek (`penamaan-bahasa-indonesia`), seluruh nama folder, kelas, metode, dan berkas Blade menggunakan Bahasa Indonesia yang konsisten.
> - **Pemisahan Domain**: Struktur Controllers, Models, Views, Seeders, dan Migrations akan disesuaikan dengan hierarki sidebar utama aplikasi.

---

## Struktur Folder & Logika Berkas yang Diusulkan

### 1. Controllers (`app/Http/Controllers/`)

Setiap grup menu/sidebar memiliki subfolder tersendiri di dalam `Controllers`:

```text
app/Http/Controllers/
├── Autentikasi/
│   ├── AutentikasiController.php       # Login, Logout, Sesi
│   └── KelolaAkunController.php        # Kelola Staf, Hak Akses & Status User
├── Master/
│   ├── CustomerController.php          # Data Customer & Toko Bangunan
│   ├── BarangController.php            # Semen Zak, Curah & Price List
│   ├── WilayahController.php           # Zonasi & Area Distribusi
│   └── KaryawanController.php          # Staf & Administrasi
├── Keuangan/
│   ├── AR/                             # Account Receivable (Piutang)
│   │   ├── FakturPenjualanController.php
│   │   ├── PiutangController.php
│   │   └── DepositCustomerController.php
│   ├── AP/                             # Account Payable (Hutang)
│   │   ├── PembelianSOController.php
│   │   ├── PengeluaranKasController.php
│   │   └── HutangSupplierController.php
│   └── Akuntansi/
│       ├── KodeAkunController.php      # Chart of Accounts (COA)
│       ├── JurnalUmumController.php    # Jurnal Otomatis & Manual
│       └── AsetPerusahaanController.php# Inventaris & Depresiasi
├── Operasional/
│   ├── Gudang/
│   │   ├── StokGudangController.php    # Real-time stok & Mutasi
│   │   ├── StockOpnameController.php   # Adjustment stok & Opname
│   │   └── PenerimaanBarangController.php
│   ├── Armada/
│   │   ├── KendaraanController.php     # Truk, Plat Nomor, Tonase
│   │   └── DriverController.php        # Profil Driver & Status
│   ├── Pengiriman/
│   │   ├── SalesOrderController.php    # SO Siap Kirim
│   │   ├── SuratJalanController.php    # Surat Jalan (SJ) Dispatcher
│   │   └── OngkosAngkutController.php  # Hitung Tarif per Zak/Ton
│   ├── Bengkel/
│   │   ├── PerbaikanKendaraanController.php # Servis & SPK Bengkel
│   │   └── SparepartController.php     # Inventaris Komponen Truk
│   └── Monitoring/
│       ├── MonitoringOperasionalController.php
│       └── KSOController.php           # Kerja Sama Operasional
└── Laporan/
    ├── LaporanNeracaController.php
    ├── LaporanLabaRugiController.php
    └── LaporanArusKasController.php
```

---

### 2. Models (`app/Models/`)

Model dikelompokkan berdasarkan konteks domain bisnis:

```text
app/Models/
├── Autentikasi/
│   ├── Pengguna.php                    # Model User Utama
│   ├── Jabatan.php                     # Role / Peran
│   ├── HakAksesJabatan.php             # Permission Per Role
│   └── RiwayatAktivitas.php            # Audit Trail Activity Log
├── Master/
│   ├── Customer.php
│   ├── TokoBangunan.php
│   ├── Barang.php                      # Produk Semen
│   ├── Wilayah.php
│   ├── Supplier.php
│   └── Karyawan.php
├── Keuangan/
│   ├── FakturPenjualan.php
│   ├── RincianFakturPenjualan.php
│   ├── Piutang.php
│   ├── PelunasanPiutang.php
│   ├── DepositCustomer.php
│   ├── PembelianSO.php
│   ├── PengeluaranKas.php
│   ├── KodeAkun.php                    # COA
│   ├── JurnalUmum.php
│   ├── RincianJurnal.php
│   └── AsetPerusahaan.php
└── Operasional/
    ├── Gudang.php
    ├── StokGudang.php
    ├── MutasiStok.php
    ├── OpnameGudang.php
    ├── RincianOpnameGudang.php
    ├── Kendaraan.php
    ├── Driver.php
    ├── SalesOrder.php
    ├── SuratJalan.php
    ├── RincianSuratJalan.php
    ├── OngkosAngkut.php
    ├── Sparepart.php
    ├── PerbaikanKendaraan.php
    └── KSO.php
```

---

### 3. Views (`resources/views/`)

Tampilan Blade terbagi secara presisi mengikuti struktur navigasi sidebar:

```text
resources/views/
├── layouts/
│   ├── app.blade.php                   # Layout Utama
│   ├── sidebar.blade.php               # Navigasi Menu Samping Dinamis
│   ├── header.blade.php                # Topbar, Profil & Dark Mode
│   └── footer.blade.php
├── auth/
│   └── login.blade.php                 # Halaman Login Centered
├── dashboard/
│   └── index.blade.php                 # Dashboard Dinamis Per Role
├── master/
│   ├── customer/index.blade.php
│   ├── barang/index.blade.php
│   ├── wilayah/index.blade.php
│   └── karyawan/index.blade.php
├── keuangan/
│   ├── ar/                             # Account Receivable
│   │   ├── faktur_penjualan.blade.php
│   │   ├── list_piutang.blade.php
│   │   └── deposit_customer.blade.php
│   ├── ap/                             # Account Payable
│   │   ├── pembelian_so.blade.php
│   │   ├── pengeluaran_kas.blade.php
│   │   └── list_rilisan.blade.php
│   └── akuntansi/
│       ├── kode_akun.blade.php
│       ├── jurnal_umum.blade.php
│       └── aset_perusahaan.blade.php
├── operasional/
│   ├── gudang/
│   │   ├── stok.blade.php
│   │   ├── mutasi_stok.blade.php
│   │   └── opname.blade.php
│   ├── armada/
│   │   ├── kendaraan.blade.php
│   │   └── driver.blade.php
│   ├── pengiriman/
│   │   ├── surat_jalan.blade.php
│   │   ├── buat_surat_jalan.blade.php
│   │   └── ongkos_angkut.blade.php
│   ├── bengkel/
│   │   ├── perbaikan.blade.php
│   │   ├── sparepart.blade.php
│   │   └── pembelian_sparepart.blade.php
│   └── monitoring/
│       ├── index.blade.php
│       └── kso.blade.php
└── laporan/
    ├── neraca.blade.php
    ├── laba_rugi.blade.php
    └── arus_kas.blade.php
```

---

### 4. Seeders (`database/seeders/`)

Pengelompokan seeder modular agar pengujian data awal dapat dipanggil secara mandiri maupun kolektif:

```text
database/seeders/
├── DatabaseSeeder.php                   # Seeder Utama (Panggil semua seeder)
├── AutentikasiSeeder.php                # Seeder Jabatan, Hak Akses, User Demo
├── MasterDataSeeder.php                 # Seeder Semen, Customer, Wilayah, Karyawan
├── KeuanganSeeder.php                   # Seeder COA (Kode Akun Standard), Kas, Aset
└── OperasionalSeeder.php                # Seeder Gudang, Kendaraan, Driver, Sparepart
```

---

### 5. Migrations (`database/migrations/`)

Migrasi diurutkan menggunakan penomoran timestamp/sekuensial agar relasi *Foreign Key* tidak error saat `php artisan migrate:fresh`:

1. `0001_01_01_000000_buat_tabel_autentikasi_dan_jabatan.php` (Pengguna, Jabatan, Hak Akses, Riwayat Aktivitas)
2. `2026_01_01_000001_buat_tabel_master_data.php` (Barang, Customer, Toko, Wilayah, Karyawan, Supplier)
3. `2026_01_01_000002_buat_tabel_operasional_logistik.php` (Gudang, Stok, Kendaraan, Driver, Sparepart)
4. `2026_01_01_000003_buat_tabel_transaksi_distribusi.php` (Sales Order, Surat Jalan, Ongkos Angkut, Mutasi Stok)
5. `2026_01_01_000004_buat_tabel_keuangan_dan_akuntansi.php` (Faktur Penjualan, Piutang, AP/Kas, COA, Jurnal Umum, Aset)

---

## Verification Plan

### Manual Verification
1. **Pemeriksaan Jalur Namespace**: Memastikan semua Controller dan Model berlokasi di subfolder yang benar dengan deklarasi `namespace App\Http\Controllers\...` dan `namespace App\Models\...`.
2. **Pemeriksaan Rute (`php artisan route:list`)**: Memastikan rute terhubung dengan benar ke masing-masing controller modular.
3. **Pemeriksaan `php artisan migrate:fresh --seed`**: Memastikan tabel dan data seeder terbentuk sempurna tanpa bentrokan foreign key.
