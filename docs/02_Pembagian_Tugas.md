# Panduan Kerja & Pembagian Tugas Tim (2 Pengembang)
**Proyek:** Sistem Informasi Akuntansi & Distribusi Semen Terpadu  
**Stack Teknologi:** Laravel 13 · PHP 8.3 · Tailwind CSS v4 · Alpine.js · MySQL Laragon  
**Status Fondasi:** 100% Selesai & Terverifikasi (25 Tampilan View, 33 Tabel Fisik + 2 SQL View Selaras Penuh)

---

## 1. Kesiapan Fondasi Proyek & Status Terkini

Seluruh fondasi antarmuka, arsitektur data, dan relasi bisnis telah selesai dibangun dan diverifikasi dengan kode status **`200 OK`**:
- [x] **Autentikasi & RBAC Multi-Tabel:** Multi-tabel (`super_account` dan `account`) dengan 10 peran pengguna dan kata sandi default `password123`.
- [x] **Integritas Database (33 Tabel & 2 SQL View):** Seluruh 33 tabel fisik, 351 kolom, dan 28 relasi *Foreign Key* telah diaudit secara empiris dan selaras 100% dengan berkas master [`database/skema_database.sql`](file:///c:/laragon/www/laravel1/database/skema_database.sql).
- [x] **Relasi 1:N Customer ke Toko Bangunan & Proyek:** Pemisahan entitas pemilik legal dan plafon kredit terpusat (`data_customer`) dengan titik pengiriman fisik drop point (`data_toko_bangunan`).
- [x] **Pemisahan Aset Finansial & Armada Kendaraan Lapangan:**
  - `data_aset`: Fokus pada aktiva tetap finansial dan amortisasi penyusutan PSAK 16.
  - `data_kendaraan`: Fokus pada fisik operasional armada truk ekspedisi logistik semen.
- [x] **Penyatuan Kategori Properti (`AST-TNH`):** Penggabungan kategori tanah dan bangunan gedung ke dalam satu kategori terpadu *Tanah & Bangunan Properti*, dilengkapi field wajib `keterangan` fasilitas di atas tanah dan opsi penyusutan PSAK 16.
- [x] **Formulir Pendaftaran Aset Format Landscape:** Modal pendaftaran aset telah diubah menjadi canvas melebar (`max-w-5xl`) dengan 2 kolom terpisah (Data Pokok/Finansial di kiri, Spesifikasi Objek Fisik di kanan) yang menampung seluruh atribut armada ERD kendaraan secara lengkap.
- [x] **Navigasi Dinamis SPA (Single Page Application):** Navigasi sidebar parsial yang persisten tanpa *full page reload*, tanpa bug *jump-to-top*, serta dilengkapi proteksi status *Read-Only* bagi peran di luar wewenang modifikasi data.

---

## 2. Aturan Isolasi Berkas & Protokol Anti-Bentrok (Zero-Conflict Protocol)

Agar kedua pengembang dapat bekerja secara simultan tanpa risiko konflik penggabungan (*merge conflict*), diterapkan pembagian domain berkas yang terisolasi secara ketat:

### A. Matriks Kepemilikan Domain Berkas

| Area Kerja | Developer 1 (`web-dev1`) | Developer 2 (`web-dev2`) |
|---|---|---|
| **Branch Git** | `web-dev1` | `web-dev2` |
| **Domain Bisnis** | Core, Keuangan, AR, AP, Akuntansi & Laporan | Logistik, Dispatcher, Gudang, Armada, Bengkel |
| **Controllers** | `app/Http/Controllers/Keuangan/**`<br>`app/Http/Controllers/Master/**`<br>`app/Http/Controllers/Laporan/**`<br>`app/Http/Controllers/Autentikasi/**` | `app/Http/Controllers/Operasional/**` |
| **Services** | `app/Services/Keuangan/**` | `app/Services/Operasional/**` *(jika ada)* |
| **Views (Blade)** | `resources/views/keuangan/**`<br>`resources/views/master/**`<br>`resources/views/laporan/**`<br>`resources/views/superadmin/**` | `resources/views/operasional/**` |
| **Models** | `app/Models/Keuangan/**`<br>`app/Models/Master/**` | `app/Models/Operasional/**` |

### B. Berkas Bersama (Shared Files) & Aturan Modifikasi
- **Berkas `routes/web.php`:**
  - *Aturan:* Pengembang tidak boleh mengubah baris milik pengembang lain. Tambahkan rute baru hanya di dalam blok komentar masing-masing:
    - Blok `/* --- RUTE DEVELOPER 1: KEUANGAN & MASTER --- */`
    - Blok `/* --- RUTE DEVELOPER 2: OPERASIONAL & LOGISTIK --- */`
- **Berkas `database/skema_database.sql`:**
  - *Aturan:* Menjadi berkas referensi utama (*source of truth*). Jika ada penambahan kolom/tabel baru, komunikasikan sebelum commit.
- **Berkas Layout Utama (`layouts/app.blade.php`, `sidebar.blade.php`, `header.blade.php`):**
  - *Aturan:* Sudah terkunci (*locked*) dan tidak boleh diubah kecuali ada kesepakatan bersama.

---

## 3. Checklist Tugas Rinci DEVELOPER 1 (Rencana Kerja Besok)

**Branch Kerja:** `web-dev1`  
**Peran PRD:** Super Admin, SPV Keuangan, Staff AR, Staff AP, Direktur & Manager  
**Fokus Utama:** Implementasi Mesin Jurnal Otomatis Terpadu, Auto-Journal pada seluruh transaksi harian, pemutakhiran saldo COA real-time, dan cetak invoice faktur penjualan resmi.

### 3.1. Mesin Otomasi Jurnal Terpadu (Auto-Journal Engine)
- [ ] **Class Terpusat `App\Services\Keuangan\MesinJurnalOtomatis.php`:**
  - [ ] Method `catatJurnal($nomorReferensi, $tanggal, array $barisJurnal, $keterangan, $pembuat)`
  - [ ] Pembungkus transaksi database atomik (`DB::transaction`) untuk menjamin konsistensi data.
  - [ ] Validasi keseimbangan otomatis ($\sum \text{Debit} == \sum \text{Kredit}$) sebelum data tersimpan ke tabel `jurnal_umum`. Jika tidak seimbang, transaksi dibatalkan (*rollback*) dengan notifikasi error.
  - [ ] Mekanisme *Idempotency Guard*: Mencegah duplikasi jurnal jika transaksi dengan `referensi_transaksi` yang sama diproses ulang.
  - [ ] Pemutakhiran otomatis nilai `saldo_berjalan` pada tabel `data_kode_akun` berdasarkan posisi normal akun (Debit/Kredit).

### 3.2. Integrasi Transaksi Penjualan & Piutang (AR)
- [ ] **Faktur Penjualan Baru (`FakturPenjualanController@store`):**
  - [ ] **Penjualan Tunai / Transfer:**
    - Debit: Kas Operasional (`1111`) atau Bank Mandiri/BRI (`1121`/`1122`)
    - Kredit: Pendapatan Penjualan Semen (`4101`)
  - [ ] **Penjualan Kredit / Piutang Tempo:**
    - Debit: Piutang Usaha (`1131`)
    - Kredit: Pendapatan Penjualan Semen (`4101`)
    - Otomasi pemotongan sisa plafon kredit customer pada `data_customer`.
  - [ ] **Penjualan Potong Deposit Customer:**
    - Debit: Hutang Deposit Customer (`2131`)
    - Kredit: Pendapatan Penjualan Semen (`4101`)
    - Otomasi pemotongan `saldo_deposit` customer dan pencatatan mutasi di `list_deposit`.
- [ ] **Pelunasan & Cicilan Piutang (`PiutangController@bayarCicilan`):**
  - [ ] Debit: Kas/Bank (`1111`/`1121`/`1122`)
  - [ ] Kredit: Piutang Usaha (`1131`)
  - [ ] Pemutakhiran `sisa_piutang` pada tabel `list_piutang` dan `data_customer`.

### 3.3. Integrasi Transaksi Pembelian & Kas Keluar (AP)
- [ ] **Penebusan SO ke Pabrik SIG (`PembelianSOController@store`):**
  - [ ] Debit: Persediaan Semen Dalam Perjalanan / Uang Muka Pembelian (`1142`)
  - [ ] Kredit: Kas/Bank atau Hutang Usaha SIG (`2111`)
- [ ] **Pengeluaran Kas Operasional (`PengeluaranKasController@store`):**
  - [ ] Debit: Akun Beban sesuai pilihan COA (Beban BBM `5101`, Beban Tol `5102`, Beban Kantor `6101`, dll.)
  - [ ] Kredit: Rekening Sumber Kas/Bank terpilih (`1111`/`1121`/`1122`)
- [ ] **Rilisan Uang Jalan Supir (`HutangSupplierController@store`):**
  - [ ] Debit: Kas Bon Uang Jalan Driver (`1107`)
  - [ ] Kredit: Rekening Kas/Bank Operasional

### 3.4. Cetak Dokumen Resmi Penjualan
- [ ] **Template Faktur Penjualan / Invoice PBJ (`resources/views/keuangan/ar/cetak_faktur.blade.php`):**
  - [ ] Header resmi PT Putra Balkom Jaya (logo, alamat, kontak).
  - [ ] Rincian barang semen, kuantitas zak/ton, harga satuan, PPN/diskon, dan total pembayaran.
  - [ ] Rincian tujuan kirim toko bangunan/proyek cabang dan tanda tangan penerima/pengirim.

---

## 4. Checklist Tugas Rinci DEVELOPER 2 (Rencana Kerja Besok)

**Branch Kerja:** `web-dev2`  
**Peran PRD:** Dispatcher, SPV Operasional, SPV Gudang, Pengawas Driver, Pengawas Kendaraan  
**Fokus Utama:** Otomasi pengurangan kuota SO pada Surat Jalan, cetak dokumen fisik resmi (Surat Jalan & SPK Bengkel), pemotongan stok sparepart servis, dan rekap ritase supir.

### 4.1. Modul Dispatcher & Surat Jalan (Pengiriman)
- [ ] **Sinkronisasi Kuota SO Real-Time (`SuratJalanController@store`):**
  - [ ] Saat Surat Jalan diterbitkan dengan kuantitas tertentu, otomatis menambahkan kolom `qty_pengambilan` pada tabel `pembelian_so` terkait.
  - [ ] Jika `qty_pengambilan` mencapai atau melebihi total kuota SO, ubah status SO menjadi `Selesai`.
  - [ ] Validasi guard: Mencegah penerbitan Surat Jalan jika kuantitas yang diminta melebihi sisa kuota SO yang tersedia.
- [ ] **Sinkronisasi Status Armada & Supir:**
  - [ ] Mengubah status armada di `data_kendaraan` menjadi `Dalam Pengiriman`.
  - [ ] Mengubah status supir di `data_karyawan` menjadi `Jalan`.
- [ ] **Cetak Dokumen Surat Jalan Resmi (`resources/views/operasional/pengiriman/cetak_surat_jalan.blade.php`):**
  - [ ] Format standar pengiriman logistik pabrik semen SIG/PBJ (3 rangkap: Driver, Gudang/Pabrik, Customer).
  - [ ] Memuat nomor Surat Jalan (`SJ-xxx`), nomor SO/LO, nama driver, plat nomor truk, jenis muatan, dan paraf petugas.

### 4.2. Modul Bengkel & Perawatan Armada
- [ ] **Otomasi Pemotongan Suku Cadang Servis (`PerbaikanKendaraanController@store`):**
  - [ ] Saat SPK servis mencantumkan penggunaan sparepart, sistem otomatis memotong jumlah stok fisik pada tabel `list_sparepart`.
  - [ ] Peringatan stok menipis jika kuantitas sparepart di gudang bengkel mendekati batas minimum.
- [ ] **Cetak Lembar SPK Perbaikan Bengkel (`resources/views/operasional/bengkel/cetak_spk.blade.php`):**
  - [ ] Formulir tugas montir/mekanik: nomor SPK (`SPK-xxx`), unit kendaraan, keluhan kerusakan, daftar penggantian komponen, dan estimasi waktu selesai.

### 4.3. Modul Gudang & Stock Opname
- [ ] **Eksekusi Penyesuaian Fisik Opname (`StockOpnameController@setujui`):**
  - [ ] Tombol persetujuan SPV Gudang memutakhirkan kuantitas fisik pada tabel `list_gudang_so` berdasarkan hasil hitung fisik di `opname_gudang`.
  - [ ] Pencatatan riwayat mutasi stok dengan tipe `Penyesuaian Opname`.

### 4.4. Rekap Ritase Supir & Estimasi Upah Jalan
- [ ] **Agregasi Ritase Pengiriman:**
  - [ ] Menampilkan ringkasan total ritase perjalanan sukses per supir dalam rentang tanggal tertentu pada modul Pengawas Driver sebagai dasar pemberian premi/upah jalan.

---

<<<<<<< HEAD
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
  - [x] **Integrasi & Penyelarasan Penuh Kode Gudang dengan SPV Gudang:** Sinkronisasi master fasilitas gudang (`list_gudang_so`) ke master tarif OA (`data_ongkos_angkut`), validasi integritas data referensial `exists:list_gudang_so,kode_gudang`, pencarian multi-kolom menembus atribut gudang (nama gudang, plant, distrik), live sync card info gudang terpilih pada modal tambah/edit, serta kartu detail terintegrasi dengan stok fisik real-time.
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
  - [x] **Revisi Sub-Teks Identitas Perusahaan:** Penyesuaian keterangan di bawah nama PT menjadi 'Distribusi & Logistik' di seluruh antarmuka (sidebar, form login, lembar cetak surat jalan).
  - [x] **Mesin SPA Dynamic Content Swapping (Sidebar Tidak Kerefresh):** Navigasi sidebar berjalan secara parsial tanpa full page reload pada seluruh 29 menu dan seluruh 10 role/aktor.
  - [x] **Eliminasi Tuntas Bug Layar Melompat ke Atas (Jump-to-Top):** Menghilangkan scroll jump saat mengklik menu bawah (seperti *Laporan Laba Rugi* atau *Laporan Neraca* pada role SPV Keuangan), posisi scroll sidebar tersimpan dan dipulihkan 100% presisi.
  - [x] **Indikator Loading Bar Halus:** Progres bar gradien modern (YouTube/GitHub style) di bagian paling atas layar saat transisi konten.
  - [x] **Sinkronisasi State Role Instan:** State role sinkron secara real-time antara frontend (`localStorage`) dan sesi backend Laravel via `/api/sinkronisasi-role`.
  - [x] **Dukungan Penuh Browser History (`popstate`) & Form Filter GET:** Tombol Back/Forward browser dan form pencarian filter berjalan mulus tanpa reload sidebar.
  - [x] **Restrukturisasi & Penyelarasan Menu Laporan Laba dan Rugi:** Memindahkan seksi Laporan Eksekutif Finansial ke posisi teratas (tepat di bawah Dashboard) khusus untuk peran SPV Keuangan dan Direktur & Manager, merevisi penulisan menjadi "Laporan Laba dan Rugi" dengan micro-badge `P&L` serta penempatan prioritas di atas Laporan Neraca untuk keterbacaan dan interaktivitas maksimal.

---

## 7. Timeline Jadwal Kerja Harian Besok

| Waktu | Sesi Kerja | Fokus Developer 1 (`web-dev1`) | Fokus Developer 2 (`web-dev2`) |
|---|---|---|---|
| **08.30 - 09.00** | Sinkronisasi Awal | `git checkout web-dev1` & `git pull origin main` | `git checkout web-dev2` & `git pull origin main` |
| **09.00 - 12.00** | Sesi Pagi (Inti Otomasi) | Membangun `MesinJurnalOtomatis.php` & validasi debit-kredit | Integrasi pengurangan kuota SO pada Surat Jalan & validasi kuota |
| **12.00 - 13.00** | Istirahat Siang | - | - |
| **13.00 - 15.30** | Sesi Siang (Integrasi & Cetak) | Integrasi auto-journal pada Faktur Penjualan, SO & Kas AP | Pembuatan template cetak Surat Jalan & cetak SPK Bengkel |
| **15.30 - 16.30** | Pengujian & Verifikasi | Pengujian entri jurnal seimbang & saldo berjalan COA | Pengujian alur pengiriman semen & pemotongan stok part bengkel |
| **16.30 - 17.00** | Push & Penggabungan | Push branch `web-dev1` & persiapan Pull Request ke `main` | Push branch `web-dev2` & persiapan Pull Request ke `main` |

---

## 6. Alur Kerja Git & Standar Commit (Branching Protocol)

```text
[origin/main] ─────────────────────────────────────────────────────────────► [origin/main]
     │                                                                 ▲
     ├──► [web-dev1] ─── (Commit Mandiri Dev 1) ───► (PR ke main) ────┤
     │                                                                 │
     └──► [web-dev2] ─── (Commit Mandiri Dev 2) ───► (PR ke main) ────┘
```

### Panduan Sinkronisasi:
1. **Sebelum Mulai Kerja:**
   ```powershell
   git checkout web-dev1 # atau web-dev2
   git pull origin main
   ```
2. **Format Pesan Commit Terstruktur (Bahasa Indonesia):**
   - `feat(keuangan): implementasi service mesin jurnal otomatis`
   - `feat(ar): integrasi auto journal pada faktur penjualan`
   - `feat(operasional): integrasi pengurangan kuota so pada surat jalan`
   - `feat(cetak): template cetak surat jalan resmi format supir`
3. **Penyelesaian Akhir Hari:**
   Lakukan pengujian lokal (`php artisan test` atau uji coba web di browser), pastikan respons HTTP 200 OK tanpa error sintaks, kemudian lakukan push ke branch masing-masing sebelum proses merge ke branch `main`.
