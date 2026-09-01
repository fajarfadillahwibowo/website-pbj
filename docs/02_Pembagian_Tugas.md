# Panduan Rinci Perencanaan & Pembagian Tugas Tim (2 Developer)
**Proyek:** Sistem Informasi Akuntansi & Distribusi Semen Terpadu  
**Target Rilis:** Laravel 13 · Tailwind CSS v4 · Alpine.js  
**Strategi Kolaborasi:** Pemisahan Vertikal (*Vertical Slice Architecture*) Berbasis Domain Bisnis  

Dokumen ini disusun serinci mungkin sebagai panduan kerja harian 2 pengembang (*Developer 1* dan *Developer 2*). Setiap pengembang memiliki domain penuh mulai dari **Migrasi Basis Data, Model & Relasi, Controller, Validasi Form, hingga Tampilan Blade UI** yang terisolasi untuk meminimalkan potensi konflik kode (*merge conflict*).

---

## 🧭 1. Ringkasan Pembagian Domain

| Domain Kerja | Developer Penanggung Jawab | Peran PRD yang Ditangani |
|---|---|---|
| **Domain A: Core, Keuangan, Master Data & Eksekutif** | **Developer 1** | Super Admin, SPV Keuangan, Staff AR, Staff AP, Direktur & Manager |
| **Domain B: Logistik, Distribusi, Gudang & Armada** | **Developer 2** | Dispatcher, SPV Operasional, SPV Gudang, Pengawas Driver, Pengawas Kendaraan |

---

## 👨‍💻 2. Rincian Tugas Developer 1 (Core, Keuangan & Eksekutif)

### Modul 1.1: Fondasi RBAC, Autentikasi & Akun
- **Nama Branch:** `feat/dev1-auth-rbac`
- **Tabel Terkait:** `pengguna`, `jabatan`, `hak_akses_jabatan`, `riwayat_aktivitas`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Models/Pengguna.php`, `app/Models/Jabatan.php`, `app/Models/HakAksesJabatan.php`
  - [ ] `app/Http/Middleware/PeriksaJabatan.php` (Middleware otorisasi role)
  - [ ] `app/Http/Controllers/AutentikasiController.php` (Login, Logout, Validasi Sesi)
  - [ ] `app/Http/Controllers/SuperAdminController.php` (Kelola akun staf, reset sandi, status aktif/nonaktif)
  - [ ] `resources/views/auth/login.blade.php` (Integrasi form login ke backend)
  - [ ] `resources/views/superadmin/kelola_akun.blade.php`

---

### Modul 1.2: Master Data Sentral
- **Nama Branch:** `feat/dev1-master-data`
- **Tabel Terkait:** `toko_bangunan`, `customer`, `barang`, `wilayah`, `jenis_aset`, `karyawan`
- **Berkas yang Dikerjakan:**
  - [ ] Model & Controller untuk Master:
    - [ ] `MasterCustomerController.php` (Data Customer & Toko Bangunan)
    - [ ] `MasterBarangController.php` (Data Semen Zak, Curah, Harga Jual Dasar)
    - [ ] `MasterWilayahController.php` (Zonasi Wilayah & Area Distribusi)
    - [ ] `MasterKaryawanController.php` (Data Staf Kantor & Administrasi)
  - [ ] Tampilan Blade:
    - [ ] `resources/views/master/customer/index.blade.php`
    - [ ] `resources/views/master/barang/index.blade.php`
    - [ ] `resources/views/master/wilayah/index.blade.php`

---

### Modul 1.3: Staff AR (Piutang & Penjualan)
- **Nama Branch:** `feat/dev1-staff-ar-penjualan`
- **Tabel Terkait:** `faktur_penjualan`, `rincian_faktur_penjualan`, `piutang`, `pelunasan_piutang`, `deposit_customer`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/PenjualanARController.php`
  - [ ] `app/Http/Controllers/DepositCustomerController.php`
  - [ ] Logika Bisnis:
    - [ ] Pembuatan Faktur Penjualan (Tunai, Kredit, atau Potong Deposit).
    - [ ] Pencatatan List Piutang & otomatis hitung tanggal jatuh tempo.
    - [ ] Pencatatan pelunasan cicilan piutang dan sisa saldo.
    - [ ] Riwayat mutasi deposit customer (Topup saldo & penggunaan).
  - [ ] Tampilan Blade:
    - [ ] `resources/views/keuangan/ar/faktur_penjualan.blade.php`
    - [ ] `resources/views/keuangan/ar/list_piutang.blade.php`
    - [ ] `resources/views/keuangan/ar/deposit_customer.blade.php`

---

### Modul 1.4: Staff AP (Hutang, Pembelian & Pengeluaran)
- **Nama Branch:** `feat/dev1-staff-ap-pengeluaran`
- **Tabel Terkait:** `supplier`, `pembelian_so`, `pengeluaran_kas`, `kategori_biaya`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/PengeluaranAPController.php`
  - [ ] `app/Http/Controllers/PembelianSOController.php`
  - [ ] Logika Bisnis:
    - [ ] Pencatatan Pembelian SO ke Pabrik Semen / Distributor Utama.
    - [ ] Form Pengeluaran Kas Operasional & Rilisan Biaya Harian.
    - [ ] Rekap tagihan supplier yang belum dibayarkan.
  - [ ] Tampilan Blade:
    - [ ] `resources/views/keuangan/ap/pembelian_so.blade.php`
    - [ ] `resources/views/keuangan/ap/pengeluaran_kas.blade.php`
    - [ ] `resources/views/keuangan/ap/list_rilisan.blade.php`

---

### Modul 1.5: SPV Keuangan, COA & Jurnal Umum
- **Nama Branch:** `feat/dev1-spv-akuntansi`
- **Tabel Terkait:** `kode_akun` (COA), `jurnal_umum`, `rincian_jurnal`, `aset_perusahaan`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/KodeAkunController.php` (CRUD Chart of Accounts)
  - [ ] `app/Http/Controllers/JurnalUmumController.php` (Input manual & posting otomatis dari AR/AP)
  - [ ] `app/Http/Controllers/AsetPerusahaanController.php` (Penyusutan & daftar aset)
  - [ ] Tampilan Blade:
    - [ ] `resources/views/keuangan/akuntansi/kode_akun.blade.php`
    - [ ] `resources/views/keuangan/akuntansi/jurnal_umum.blade.php`
    - [ ] `resources/views/keuangan/akuntansi/aset_perusahaan.blade.php`

---

### Modul 1.6: Direktur & Manager (Laporan Strategis)
- **Nama Branch:** `feat/dev1-laporan-eksekutif`
- **Tabel / View Terkait:** `view_laporan_laba_rugi`, `view_laporan_neraca`, `view_arus_kas`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/LaporanEksekutifController.php`
  - [ ] Fitur Filter Periode (Bulanan, Kuartal, Tahunan).
  - [ ] Fitur Ekspor Laporan ke format PDF dan Excel/CSV.
  - [ ] Tampilan Blade:
    - [ ] `resources/views/laporan/neraca.blade.php`
    - [ ] `resources/views/laporan/laba_rugi.blade.php`

---

## 🚚 3. Rincian Tugas Developer 2 (Logistik, Gudang, Armada & Operasional)

### Modul 2.1: Master Gudang & Manajemen Persediaan
- **Nama Branch:** `feat/dev2-gudang-stok`
- **Tabel Terkait:** `gudang`, `stok_gudang`, `mutasi_stok`, `penerimaan_barang`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/GudangController.php`
  - [ ] `app/Http/Controllers/MutasiStokController.php`
  - [ ] Logika Bisnis:
    - [ ] Pemantauan stok semen zak & curah per gudang secara real-time.
    - [ ] Pencatatan penerimaan semen dari pabrik (Inbound Goods).
    - [ ] Pengurangan stok otomatis saat Surat Jalan dirilis.
  - [ ] Tampilan Blade:
    - [ ] `resources/views/operasional/gudang/index.blade.php`
    - [ ] `resources/views/operasional/gudang/mutasi_stok.blade.php`
    - [ ] `resources/views/operasional/gudang/penerimaan_barang.blade.php`

---

### Modul 2.2: SPV Gudang (Stock Opname)
- **Nama Branch:** `feat/dev2-stock-opname`
- **Tabel Terkait:** `opname_gudang`, `rincian_opname_gudang`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/StockOpnameController.php`
  - [ ] Logika Bisnis:
    - [ ] Form pencatatan hitung fisik semen di lapangan.
    - [ ] Kalkulasi otomatis selisih (*stok sistem vs stok fisik*).
    - [ ] Validasi SPV Gudang dan penyesuaian (*adjustment*) stok.
  - [ ] Tampilan Blade:
    - [ ] `resources/views/operasional/gudang/opname.blade.php`
    - [ ] `resources/views/operasional/gudang/form_opname.blade.php`

---

### Modul 2.3: Master Armada, Kendaraan & Driver
- **Nama Branch:** `feat/dev2-armada-driver`
- **Tabel Terkait:** `kendaraan`, `driver`, `kategori_kendaraan`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/KendaraanController.php`
  - [ ] `app/Http/Controllers/DriverController.php`
  - [ ] Logika Bisnis:
    - [ ] Data spesifikasi armada (Plat nomor, tipe truk: Colt Diesel/Tronton, kapasitas tonase).
    - [ ] Data profil supir, nomor kontak, SIM, dan status kesiapan (Standby / Sedang Jalan / Cuti).
  - [ ] Tampilan Blade:
    - [ ] `resources/views/operasional/armada/kendaraan.blade.php`
    - [ ] `resources/views/operasional/armada/driver.blade.php`

---

### Modul 2.4: Dispatcher & Surat Jalan Pengiriman
- **Nama Branch:** `feat/dev2-surat-jalan-dispatcher`
- **Tabel Terkait:** `sales_order`, `surat_jalan`, `rincian_surat_jalan`, `ongkos_angkut`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/SuratJalanController.php`
  - [ ] `app/Http/Controllers/OngkosAngkutController.php`
  - [ ] Logika Bisnis:
    - [ ] Konversi Sales Order (SO) menjadi Surat Jalan (SJ).
    - [ ] Pemilihan armada truk dan penugasan driver yang berstatus *Standby*.
    - [ ] Perhitungan ongkos angkut berdasarkan jarak/wilayah dan tarif per zak/ton.
    - [ ] Pembaruan status pengiriman (*Draft -> Muat -> Dalam Perjalanan -> Terkirim -> Selesai*).
  - [ ] Tampilan Blade:
    - [ ] `resources/views/operasional/pengiriman/surat_jalan.blade.php`
    - [ ] `resources/views/operasional/pengiriman/buat_surat_jalan.blade.php`
    - [ ] `resources/views/operasional/pengiriman/ongkos_angkut.blade.php`

---

### Modul 2.5: Pengawas Kendaraan & Bengkel (Sparepart & Servis)
- **Nama Branch:** `feat/dev2-bengkel-sparepart`
- **Tabel Terkait:** `sparepart`, `pembelian_sparepart`, `perbaikan_kendaraan`, `rincian_servis`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/PerbaikanKendaraanController.php`
  - [ ] `app/Http/Controllers/SparepartController.php`
  - [ ] Logika Bisnis:
    - [ ] Pembuatan SPK (Surat Perintah Kerja) perbaikan kendaraan.
    - [ ] Pengurangan stok sparepart gudang bengkel saat dilakukan servis.
    - [ ] Riwayat pemeliharaan berkala per nomor plat kendaraan.
  - [ ] Tampilan Blade:
    - [ ] `resources/views/operasional/bengkel/perbaikan.blade.php`
    - [ ] `resources/views/operasional/bengkel/sparepart.blade.php`
    - [ ] `resources/views/operasional/bengkel/pembelian_sparepart.blade.php`

---

### Modul 2.6: SPV Operasional (Monitoring & KSO)
- **Nama Branch:** `feat/dev2-spv-operasional`
- **Tabel Terkait:** `kso` (Kerja Sama Operasional), `monitoring_distribusi`
- **Berkas yang Dikerjakan:**
  - [ ] `app/Http/Controllers/SPVOperasionalController.php`
  - [ ] `app/Http/Controllers/KSOController.php`
  - [ ] Dashboard pengawasan utilitas armada dan realisasi kirim harian.
  - [ ] Tampilan Blade:
    - [ ] `resources/views/operasional/kso/index.blade.php`
    - [ ] `resources/views/operasional/monitoring/index.blade.php`

---

## 🔗 4. Titik Sinkronisasi Antar-Developer (Data Contract)

Agar kedua domain terintegrasi mulus tanpa salah paham struktur data, berikut kontrak antarmuka (*Interface / Data Contract*) yang disepakati:

1. **Relasi Penjualan (Dev 1) & Surat Jalan (Dev 2):**
   - Developer 1 menyediakan data tabel `sales_order` dengan status `SIAP_KIRIM`.
   - Developer 2 membaca `sales_order` untuk menerbitkan `surat_jalan`, lalu mengubah status SO menjadi `PROSES_KIRIM` atau `SELESAI`.
2. **Relasi Pembelian Sparepart (Dev 2) & Pengeluaran Kas AP (Dev 1):**
   - Developer 2 membuat data di `pembelian_sparepart`.
   - Developer 1 mengambil total tagihan pembelian sparepart untuk dibukukan ke modul `pengeluaran_kas` (AP).
3. **Penyatuan Dashboard Utama (`resources/views/dashboard.blade.php`):**
   - Developer 1 menyuplai data controller untuk variabel `$metrikKeuangan` (Penjualan, Piutang, Laba).
   - Developer 2 menyuplai data controller untuk variabel `$metrikOperasional` (Armada Jalan, Surat Jalan Rilis, Stok Semen).

---

## 🛠️ 5. Standar Eksekusi & Git Workflow

### 5.1 Siklus Branching & Penggabungan
```text
[main] ─────────────────────────────────────────────────────────────► [main (Produksi)]
  │                                                            ▲
  ├──► [feat/dev1-auth-rbac] ────────► (Pull Request + Review) ┤
  │                                                            │
  └──► [feat/dev2-gudang-stok] ──────► (Pull Request + Review) ┘
```

1. **Branch Utama:** `main` (Wajib selalu dalam keadaan stabil dan bisa dijalankan).
2. **Aturan Membuat Branch:**
   ```powershell
   git checkout main
   git pull origin main
   git checkout -b <nama-branch-sesuai-modul>
   ```
3. **Aturan Migrasi Database:**
   - Dilarang mengedit file migrasi yang sudah pernah di-merge ke `main`.
   - Buat migrasi baru jika ada perubahan kolom: `php artisan make:migration tambah_kolom_x_ke_tabel_y`.
4. **Prosedur Pull Request:**
   - Sebelum membuat PR, jalankan pengujian lokal: `php artisan test` (atau cek route list `php artisan route:list`).
   - Developer A wajib meminta persetujuan (*review*) Developer B sebelum merge ke `main` (berlaku sebaliknya).

---

## 📐 6. Kepatuhan Standar UI/UX (Wajib Diikuti Kedua Developer)
Seluruh halaman Blade yang dibuat oleh Developer 1 dan Developer 2 **wajib mematuhi dokumen [docs/03_Design_System.md](file:///c:/laragon/www/laravel1/docs/03_Design_System.md)**:
- [ ] Angka uang wajib rata kanan (`text-right font-mono tabular-nums`) format `Rp X.XXX.XXX`.
- [ ] Card radius maksimal `rounded-xl` (12px), tombol `rounded-lg` (8px), badge `rounded` (6px).
- [ ] Header tabel wajib `sticky top-0 z-10`.
- [ ] Seluruh variabel, fungsi controller, ID/Class HTML ditulis dalam **Bahasa Indonesia**.
- [ ] Dilarang menggunakan emoji teks pada UI; gunakan SVG Icon.
