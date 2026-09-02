# Memori Proyek: Sistem Akuntansi & Distribusi Semen Terpadu

## Status Repositori Git
- **URL Repositori**: `https://github.com/fajarfadillahwibowo/website-pbj.git`
- **Branch Utama**: `main`
- **Status Integrasi**: 100% Selesai & Terverifikasi (24+ Modul Tampilan View, RBAC Matriks 10 Aktor).

---

## Ringkasan Progres & Milestone Terakhir

1. **Standardisasi Logika Kode Otomatis (*Universal Gap-Filling* & *Acak*)**:
   - Diterapkan pada 46 endpoint API di seluruh role dan sub-modul (Master Data, Operasional Gudang, Armada, Driver, KSO, Pengiriman, Bengkel, AR, AP, Akuntansi COA, Jurnal Umum, dan Aset).
   - Logika daur ulang nomor terkecil (*gap-filling*) otomatis mendeteksi dan menggunakan kembali nomor urut yang pernah terhapus.
   - Dual-mode generator tombol (🔵 Daur Ulang vs 🟣 Acak) terintegrasi pada seluruh modal tambah data.

2. **Indikator Riwayat Terakhir Diedit Real-Time**:
   - Kolom **Aksi & Riwayat** di setiap tabel menampilkan waktu relatif (`🕒 x menit yang lalu` / `diffForHumans` Bahasa Indonesia) dengan tooltip waktu presisi `d/m/Y H:i:s`.

3. **Penyelarasan Sidebar "Data Karyawan"**:
   - Seluruh sidebar diarahkan ke modul Driver Operasional (`operasional.armada.driver`) dengan label standar **`Data Karyawan (Driver)`**.

4. **CRUD Penuh & Manajemen Aging Sidebar "List Piutang" (Role: SPV Keuangan & Staff AR)**:
   - **Create**: Catat piutang baru terhubung master customer & faktur penjualan.
   - **Read**: Modal detail profil toko, visual progress bar pelunasan (% & nominal), sisa piutang, dan status *Aging* jatuh tempo.
   - **Update**: Edit tanggal jatuh tempo, nominal piutang, dan status piutang.
   - **Payment**: Form cicilan/pelunasan cepat (25%, 50%, 75%, 100% Lunas) yang otomatis memotong saldo toko.
   - **Delete**: Hapus piutang dengan sinkronisasi saldo piutang customer di database.
