# Product Requirement Document (PRD)
**Proyek:** Sistem Informasi Akuntansi & Distribusi Semen Terpadu
**Versi:** 1.1
**Status:** Draf / Perencanaan

---

## 1. Ringkasan Eksekutif
Sistem Informasi Akuntansi & Distribusi Semen Terpadu adalah platform enterprise berbasis web yang dirancang untuk mengelola seluruh siklus operasional bisnis distribusi semen. Sistem ini mengintegrasikan pencatatan pesanan (Sales Order), manajemen armada pengiriman, kontrol stok gudang, hingga pembukuan akuntansi (AR/AP, Jurnal, dan Laporan Keuangan) secara *real-time*. 

Tujuan utama sistem ini adalah mencegah kebocoran data, meningkatkan akurasi laporan keuangan, dan memastikan setiap pegawai hanya mengakses data yang relevan dengan tugasnya (*Strict Least Privilege*).

---

## 2. Arsitektur & Teknologi
- **Backend:** Laravel (v13) dengan PHP 8.2+
- **Frontend:** Blade Templating, Alpine.js (reaktivitas ringan), Tailwind CSS v4 (styling)
- **Database:** PostgreSQL / MySQL (sesuai konfigurasi)
- **Pendekatan UI/UX:** Enterprise Compact Density (bersih, minim distraksi, *data-legibility first*, mendukung *Dark/Light Mode*).

---

## 3. Hierarki Pengguna dan Hak Akses (Role-Based Access)
Sistem ini menggunakan otentikasi login terpusat dengan parameter username, password, dan jabatan (*role*) untuk menentukan hak akses modul. Berikut adalah hierarki peran beserta modul yang dapat diakses:

1. **Direktur & Manager (Top Level Management)**
   - Akses penuh ke laporan strategis: Laporan Neraca & Laporan Laba Rugi.

2. **SPV Keuangan (Supervisor Finance)**
   - Manajemen Penjualan dan List Piutang.
   - Manajemen List Deposit dan Data Customer.
   - Master Data: Data Toko Bangunan, Data Barang, Data Kode Akun (Chart of Accounts), Data Wilayah, Data Jenis Aset.
   - Manajemen Internal: Aset Perusahaan, List SO (Sales Order), Data Karyawan.

3. **Staff AR (Account Receivable)**
   - Operasional harian piutang: Penjualan, Data Customer, List Piutang, List Deposit, dan Data Barang.

4. **Staff AP (Account Payable)**
   - Operasional harian hutang dan pengeluaran: Pengeluaran, Rilisan, Pembelian SO, List SO, List Gudang SO.

5. **SPV Operasional**
   - Pengawasan distribusi: Data Ongkos Angkut, Pengiriman, Data Kendaraan, Data Karyawan (Driver), Data KSO, dan Opname Gudang.

6. **Modul Logistik & Armada (Dispatcher, Pengawas, SPV Gudang)**
   - **Dispatcher:** Data Kendaraan, Pengiriman, Data Karyawan (Driver).
   - **Pengawas Driver:** Akses khusus Data Driver.
   - **Pengawas Kendaraan:** Perbaikan Kendaraan, Pembelian Sparepart, List Sparepart.
   - **SPV Gudang:** Data Gudang, Opname Gudang.

---

## 4. Daftar Modul & Fitur Utama

### 4.1 Modul Autentikasi & Keamanan
- Halaman Login (Centered Card Form) dengan perlindungan *Brute Force*.
- Manajemen Sesi (Ingat Saya selama 30 hari).
- Pencatatan aktivitas (*Audit Trail*) untuk setiap aksi krusial.

### 4.2 Modul Dashboard (Dinamis)
- Tampilan matriks KPI (*Key Performance Indicator*) yang berubah sesuai dengan jabatan yang masuk.
- Tabel *feed* aktivitas atau transaksi terbaru (*real-time*).

### 4.3 Modul Keuangan & Akuntansi (Finance)
- **Manajemen Piutang (AR):** Pembuatan Faktur, Daftar Piutang, Deposit Customer, dan pencatatan pelunasan otomatis pembentukan jurnal.
- **Manajemen Hutang (AP):** Pencatatan Faktur Pembelian (Supplier), Daftar Hutang, dan Pengeluaran Kas.
- **Buku Besar & Jurnal:** Daftar Kode Akun (COA), Jurnal Umum otomatis & manual.
- **Laporan Keuangan:** Laba/Rugi, Neraca Saldo, Arus Kas (dapat diekspor ke PDF/Excel).

### 4.4 Modul Operasional & Distribusi (Logistik)
- **Surat Jalan:** Penerbitan surat jalan terkait dengan Sales Order.
- **Manajemen Armada:** Penugasan *driver* ke truk, status perjalanan armada.
- **Manajemen Gudang:** Kartu Stok, Surat Penerimaan Barang, dan modul *Stock Opname* berkala.
- **Manajemen Pemeliharaan:** Penjadwalan servis kendaraan, daftar komponen/sparepart.
- **Ongkos Angkut:** Perhitungan otomatis biaya pengiriman berdasarkan rute dan tonase.

---

## 5. Standar Desain & Implementasi (Design System v2.0)
- **Warna Status:** Hijau (Positif/Lunas), Amber (Peringatan/Piutang), Merah (Bahaya/Jatuh Tempo), Biru (Proses).
- **Tipografi Data:** Semua angka nominal keuangan wajib rata kanan (*right-aligned*) dan menggunakan font khusus (`font-mono tabular-nums`).
- **Komponen UI:** 
  - Maksimal *Border Radius* untuk kartu adalah 12px, tombol 8px.
  - Header tabel *sticky* saat di-*scroll*.
  - Menampilkan *Empty State* ketika data kosong (dilarang *blank screen*).
- **Penamaan Kode:** Semua penamaan fungsi, variabel, ID/Class HTML menggunakan **Bahasa Indonesia**.

---

## 6. Kriteria Penerimaan (Acceptance Criteria)
- Seluruh modul harus bisa diuji menggunakan perangkat *mobile* (responsif).
- Tidak ada data lintas departemen yang bocor (*Data Isolation* sukses).
- Transaksi keuangan otomatis menghasilkan 2 entri jurnal yang seimbang (*Double-entry accounting*).
- Transisi antara *Dark Mode* dan *Light Mode* berjalan lancar tanpa *flicker*.
