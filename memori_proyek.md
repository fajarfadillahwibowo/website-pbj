# Memori Proyek: Website PBJ (PT Pura Balkom Jaya)

## 📌 Aturan Kerja Tambahan dari Pengguna
- **Protokol Git Push**: **DILARANG KERAS** melakukan `git push` secara otomatis. `git push` hanya boleh dieksekusi setelah pengguna memberikan instruksi atau konfirmasi eksplisit (misal: *"push ke main"*, *"push git"*).
- **Penamaan Identifier**: Wajib menggunakan Bahasa Indonesia untuk variabel, fungsi, tabel, kolom, ID, dan class.
- **RBAC & Matrix**: 10 Peran Pengguna sesuai hierarki PRD 1.1 dan diagram alur.
- **Model Eloquent**: Terhubung langsung ke 28 tabel MySQL `laravel1`.

---

## 🚀 Status Modul & Fitur Terkini
1. **Autentikasi & RBAC Multi-Role**:
   - 10 Peran Pengguna aktif (Super Admin, SPV Keuangan, Staff AR, Staff AP, Dispatcher, Pengawas Driver, SPV Gudang, Direktur & Manager, SPV Operasional, Pengawas Kendaraan).
   - Simulator Role pada header & filter dinamis sidebar.

2. **Master Data & Karyawan**:
   - Master Karyawan terhubung penuh ke tabel `data_karyawan` (semua kategori: staf, driver, gudang, teknisi, manajemen).
   - Data Driver pada menu Operasional otomatis menyaring `kategori_karyawan = 'driver'`.

3. **Tampilan & Navigasi**:
   - Responsive Collapsible Sidebar (`w-64` <-> `w-16`) dengan logo terpusat saat diciutkan dan toggle hamburger pada header bar.

---

## 🛠️ Pembagian Tugas Tim
- Lihat panduan lengkap di `docs/02_Pembagian_Tugas.md`.
- **Developer 1**: Modul Keuangan (AR, AP, Akuntansi, Laporan) & Master Data Sentral.
- **Developer 2**: Modul Operasional (Gudang, Logistik, Pengiriman, Armada, Driver, Bengkel, KSO).
