# Dokumen Perencanaan & Pembagian Tugas Tim
**Proyek:** Sistem Informasi Akuntansi & Distribusi Semen Terpadu
**Ukuran Tim:** 2 Orang (*Developer A* & *Developer B*)
**Metodologi Pembagian:** Berbasis Domain / Modul (Vertical Slice)

Untuk memastikan tidak ada konflik bentrokan kode (merge conflict) yang besar dan setiap developer fokus pada alur bisnis tertentu, pembagian tugas dilakukan secara modular (berdasarkan fitur fungsional dari *Database* hingga *UI/Frontend*).

---

## 👨‍💻 Developer A (Fokus: Core, Keuangan & Akuntansi)
**Tanggung Jawab Utama:** Membangun fondasi sistem, autentikasi, serta seluruh modul yang berhubungan langsung dengan arus uang dan pembukuan.

### Daftar Tugas (Task List):
1. **Setup & Core System (Tahap 1)**
   - [ ] Konfigurasi Model, Migration, dan Seeder untuk tabel `users` dan `hak_akses_jabatan` (RBAC).
   - [ ] Integrasi middleware autentikasi dan pengecekan role/jabatan.
2. **Modul Piutang / Account Receivable (Tahap 2)**
   - [ ] CRUD Master Customer & Deposit Customer.
   - [ ] Fitur pembuatan Faktur Penjualan berdasarkan Sales Order (SO).
   - [ ] Fitur pencatatan pelunasan faktur & update status otomatis.
3. **Modul Hutang / Account Payable (Tahap 3)**
   - [ ] CRUD Master Supplier.
   - [ ] Fitur penerimaan tagihan (AP) dan pengeluaran kas (pembayaran tagihan).
4. **Modul Akuntansi Dasar (Tahap 4)**
   - [ ] CRUD Master Chart of Accounts (COA).
   - [ ] Fitur Jurnal Umum (Otomatis dari AR/AP dan input Manual).
   - [ ] *View / Report* Laporan Keuangan: Neraca Saldo dan Laba/Rugi.

---

## 👨‍💻 Developer B (Fokus: Operasional, Distribusi & Gudang)
**Tanggung Jawab Utama:** Membangun modul operasional fisik yang meliputi pergerakan barang (semen), penjadwalan armada, dan pemeliharaan kendaraan.

### Daftar Tugas (Task List):
1. **Modul Gudang & Inventory (Tahap 1)**
   - [ ] Konfigurasi Model, Migration, dan Seeder untuk tabel gudang, barang, dan stok.
   - [ ] Fitur *Stock Opname* (Pencocokan stok fisik vs sistem).
   - [ ] Pencatatan barang masuk (Penerimaan SO).
2. **Modul Distribusi & Surat Jalan (Tahap 2)**
   - [ ] CRUD Manajemen Sales Order (SO) tahap operasional.
   - [ ] Fitur pembuatan Surat Jalan Pengiriman.
   - [ ] Perhitungan Ongkos Angkut berdasarkan tujuan dan jenis kendaraan.
3. **Modul Armada & Pengemudi (Tahap 3)**
   - [ ] Master Data Driver dan status kesiapannya.
   - [ ] Master Data Armada / Kendaraan.
   - [ ] *Dashboard / Tracking* status surat jalan (Draft -> Dalam Perjalanan -> Terkirim).
4. **Modul Pemeliharaan / Bengkel (Tahap 4)**
   - [ ] Pencatatan SPK (Surat Perintah Kerja) perbaikan kendaraan.
   - [ ] Manajemen pemakaian sparepart kendaraan.

---

## 🤝 Tugas Kolaboratif (Dikerjakan Bersama)
1. **Integrasi Dashboard Utama:**
   - Developer A menyiapkan *query KPI* Keuangan (Penjualan, Laba, Piutang).
   - Developer B menyiapkan *query KPI* Operasional (Surat Jalan, Armada, Stok Gudang).
   - Digabungkan ke dalam satu file `dashboard.blade.php` menggunakan *Alpine.js template x-if*.
2. **Standarisasi UI/UX:**
   - Keduanya wajib mengikuti aturan `design.md` secara ketat (Menggunakan format Rupiah *tabular-nums*, *badge* warna semantik, dan ukuran *Border Radius* maksimal 12px).
3. **Code Review:**
   - Setiap selesai satu modul (misal: Selesai modul Faktur Penjualan), Developer A melakukan *Pull Request* dan Developer B wajib mereview kodenya sebelum digabungkan ke branch `main` (berlaku sebaliknya).

---

## 📈 Alur Kerja Git (Git Workflow)
1. **Branch Utama:** `main` (Produksi/Stabil)
2. **Penamaan Branch Developer A:** 
   - `feat/keuangan-faktur`
   - `feat/core-rbac`
3. **Penamaan Branch Developer B:**
   - `feat/operasional-surat-jalan`
   - `feat/gudang-opname`
4. Selalu jalankan `git pull origin main` sebelum membuat branch baru agar kode selalu terbarui dengan repositori utama.
