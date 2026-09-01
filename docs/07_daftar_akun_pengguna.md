# Daftar Kredensial Akun Pengguna & Hak Akses Sistem
**Sistem Informasi Akuntansi & Distribusi Semen Terpadu**  
**Basis Data:** `laravel1` (MySQL)  
**Kata Sandi Default Semua Akun:** `password123`

---

## 1. Ringkasan Kredensial Masuk (10 Peran RBAC)

Berikut adalah daftar lengkap akun yang telah terdaftar dan aktif di basis data untuk keperluan pengembangan (*development*) dan pengujian sistem:

| # | Nama Pengguna (*Username*) | Kata Sandi (*Password*) | Nama Karyawan / Pemilik | Jabatan / Role | Modul & Wewenang yang Dapat Diakses |
|---|---|---|---|---|---|
| 1 | **`superadmin`** | `password123` | Administrator Kontrol Akun | **Super Admin** | Manajemen Akun Staf, Reset Kata Sandi, Hak Akses RBAC, Log Riwayat Login |
| 2 | **`spv_keuangan`** | `password123` | Siti Rahmawati | **SPV Keuangan** | Faktur Penjualan, List Piutang, Deposit Customer, Master Data, COA, Jurnal Umum, Aset Perusahaan |
| 3 | **`staff_ar`** | `password123` | Dewi Anggraeni | **Staff AR** | Faktur Penjualan, Data Customer, List Piutang, Deposit Customer, Data Semen |
| 4 | **`staff_ap`** | `password123` | Rian Hidayat | **Staff AP** | Pengeluaran Kas, Pembelian SO Pabrik, List Rilisan Biaya, Hutang Supplier |
| 5 | **`dispatcher`** | `password123` | Bambang Wijaya | **Dispatcher** | Surat Jalan Pengiriman, Data Kendaraan Truk, Penugasan Driver Supir |
| 6 | **`pengawas_driver`** | `password123` | Agus Suryanto | **Pengawas Driver** | Data Driver, Status Kesiapan (*Standby / Sedang Kirim / Cuti*) |
| 7 | **`spv_gudang`** | `password123` | Hendra Gunawan | **SPV Gudang** | Stok Gudang Semen Zak & Curah, Penerimaan Barang, Modul Stock Opname |
| 8 | **`direktur`** | `password123` | Ahmad Supriyadi | **Direktur & Manager** | Laporan Neraca, Laporan Laba Rugi, Laporan Arus Kas, Metrik Eksekutif |
| 9 | **`spv_operasional`**| `password123` | Wahyu Pratama | **SPV Operasional** | Pengawasan Distribusi, Tarif Ongkos Angkut, Monitoring Armada, Data KSO |
| 10 | **`pengawas_kendaraan`**| `password123`| Doni Kurniawan | **Pengawas Kendaraan** | SPK Perbaikan Kendaraan Bengkel, Pembelian Sparepart, List Sparepart |

---

## 2. Struktur Tabel Basis Data Akun

Sistem memisahkan akun keamanan tingkat tinggi dan operasional ke dalam 2 tabel terisolasi:

### A. Tabel `super_account` (Khusus Super Admin)
- **Tujuan**: Mengisolasi akun kontrol administrator agar tidak bercampur dengan data pegawai operasional.
- **Kolom Utama**: `id_super_account`, `username`, `password`, `nama_pemilik`, `tanggal_create`, `diperbarui_pada`.

### B. Tabel `account` (Akun Pegawai Staf Operasional)
- **Tujuan**: Menyimpan akun pengguna staf operasional yang terhubung ke relasi karyawan dan jabatan.
- **Kolom Utama**: `id_account`, `username`, `password`, `kode_karyawan`, `id_jabatan`, `status_aktif`, `tanggal_create`, `diperbarui_pada`.
- **Relasi**:
  - `kode_karyawan` $\rightarrow$ merujuk ke tabel `data_karyawan.kode_karyawan`
  - `id_jabatan` $\rightarrow$ merujuk ke tabel `jabatan.id_jabatan`

---

## 3. Cara Pengujian Masuk Sistem

1. Buka peramban di `http://127.0.0.1:8000` atau `http://localhost/laravel1/public`.
2. Masukkan **Nama Pengguna** (misal: `spv_keuangan` atau `superadmin`).
3. Masukkan **Kata Sandi**: `password123`.
4. Klik **Masuk ke Dashboard**.
5. Untuk kemudahan berpindah simulasi peran saat *development*, gunakan fitur **Simulasi Role** pada header bar dashboard di pojok kanan atas.
