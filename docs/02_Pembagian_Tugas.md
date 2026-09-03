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
- [x] **Restrukturisasi Menu Laporan Laba dan Rugi:** Penempatan seksi Laporan Eksekutif Finansial di posisi teratas (tepat di bawah Dashboard) dengan micro-badge `P&L` untuk peran SPV Keuangan dan Direktur & Manager.

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

## 3. Checklist Tugas Rinci DEVELOPER 1

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

## 4. Checklist Tugas Rinci DEVELOPER 2

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

## 5. Alur Kerja Git & Standar Commit (Branching Protocol)

```text
[origin/main] ─────────────────────────────────────────────────────────────► [origin/main]
     │                                                                 ▲
     ├──► [web-dev1] ─── (Commit Mandiri Dev 1) ───► (PR ke main) ────┤
     │                                                                 │
     └──► [web-dev2] ─── (Commit Mandiri Dev 2) ───► (PR ke main) ────┘
```

### Panduan Sinkronisasi:
1. **Sebelum Mulai Bekerja:**
   ```powershell
   git checkout web-dev1 # atau web-dev2
   git pull origin main
   ```
2. **Format Pesan Commit Terstruktur (Bahasa Indonesia):**
   - `feat(keuangan): implementasi service mesin jurnal otomatis`
   - `feat(ar): integrasi auto journal pada faktur penjualan`
   - `feat(operasional): integrasi pengurangan kuota so pada surat jalan`
   - `feat(cetak): template cetak surat jalan resmi format supir`
3. **Penyelesaian Tugas & Penggabungan:**
   Lakukan pengujian lokal (`php artisan test` atau uji coba web di browser), pastikan seluruh respons HTTP 200 OK tanpa error, kemudian lakukan push ke branch masing-masing sebelum proses merge ke branch `main`.
