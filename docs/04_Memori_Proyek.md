# Memori Proyek: Sistem Akuntansi & Distribusi Semen Terpadu

## Status Repositori Git
- **URL Repositori**: `https://github.com/fajarfadillahwibowo/website-pbj.git`
- **Branch Utama**: `main`
- **Status Push**: Berhasil terunggah 100% (Commit: `40f72f2`).

## Ringkasan Struktur Berkas yang Terunggah
1. **Skema Basis Data**:
   - `database/skema_database.sql` (Skema InnoDB lengkap dengan relasi Foreign Key, indeks, sistem RBAC 9 Jabatan, COA, Jurnal, dan Views Laporan Neraca & Laba Rugi).
2. **Panduan Desain**:
   - `design.md` (Design System v2.0 Enterprise Compact, palet warna dual-mode, spesifikasi tipografi ringkas, dan checklist audit).
3. **Frontend & Antarmuka**:
   - `resources/views/layouts/app.blade.php` (Master layout Blade dengan sidebar dinamis per jabatan & sakelar tema).
   - `resources/views/auth/login.blade.php` (Halaman login Split-Screen Pro).
   - `resources/views/dashboard.blade.php` (Dashboard operasional bergaya Kravio dengan KPI dinamis dan tabel transaksi).
   - `routes/web.php` (Rute web terhubung ke controller/view).
4. **Dokumentasi & Pelacak Bug**:
   - `memori_proyek.md`
   - `catatan_bug_dan_error.md`
