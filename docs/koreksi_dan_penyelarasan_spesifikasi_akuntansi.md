# 📋 Dokumen Koreksi & Penyelarasan Spesifikasi Sistem Akuntansi PBJ

Dokumen ini merupakan hasil audit dan telaah mendalam terhadap berkas [`.agents/panduan_spesifikasi_sistem_akuntansi.md`](file:///c:/laragon/www/laravel1/.agents/panduan_spesifikasi_sistem_akuntansi.md) dibandingkan dengan arsitektur basis data, model Eloquent, controller, alur transaksi, dan antarmuka pada codebase Laravel aktual PT Putra Balkom Jaya (PBJ).

---

## 🔍 1. Ringkasan Hasil Audit

| Kategori Analisis | Status Keselarasan | Keterangan & Catatan Kritis |
| :--- | :---: | :--- |
| **Prinsip Double-Entry & Formula Finansial** | ✅ **100% Sesuai** | Rumus Laba Rugi, Keseimbangan Neraca, Arus Kas, dan Logika Amortisasi Aset Tetap (Garis Lurus & Saldo Menurun) sudah sangat tepat dan memenuhi kaidah standar akuntansi. |
| **Matriks Otomasi Jurnal (Auto-Journal)** | ✅ **90% Sesuai** | Skenario jurnal (Penebusan SO SIG, Penjualan DO, Potong Deposit, Kredit Piutang, Biaya Operasional BBM/Ritase) tepat, namun membutuhkan penyelarasan kode akun COA dan ID rekening aktual. |
| **Arsitektur Relasi Customer & Toko Bangunan** | ⚠️ **Perlu Koreksi** | Dokumen masih menggunakan tabel tunggal `master_pelanggan`. Codebase aktual telah disempurnakan menjadi relasi 1:N antara `data_customer` (Induk Pemilik & Finansial) dan `data_toko_bangunan` (Outlet/Proyek Cabang Fisik). |
| **Nomenklatur & Skema Tabel Database** | ⚠️ **Perlu Koreksi** | Nama tabel di dokumen menggunakan prefix `master_*` / `*_retail`, sedangkan codebase aktual menggunakan penamaan terstruktur Bahasa Indonesia (`data_customer`, `data_toko_bangunan`, `penjualan`, `list_piutang`, `deposit_customer`, `data_kode_akun`, `jurnal_umum`). |
| **Hierarki Peran & Akses Pengguna (RBAC)** | ⚠️ **Perlu Koreksi** | Dokumen hanya mendokumentasikan 4 peran umum (`SUPER_ADMIN`, `ADMIN_KEUANGAN`, `ADMIN_PENEBUSAN`, `ADMIN_PENJUALAN`), sedangkan sistem aktual mengimplementasikan **10 Peran Spesifik** (Role 01 s.d. Role 10) dengan tabel `jabatan`, `modul`, `hak_akses_jabatan`, dan `account`. |
| **Generator Kode Otomatis & Penomoran** | ⚠️ **Perlu Koreksi** | Dokumen belum mendokumentasikan mesin pengisi celah nomor kosong (*Gap-Filling Engine*) pada `GeneratorKodeOtomatis.php` serta format kode per jabatan (`ADM-`, `KEU-`, `SAR-`, `SAP-`, `DSP-`, `DRV-`, `TKB-`, `CUST-`, dll.). |

---

## 🛠️ 2. Rincian Poin Koreksi Struktural

### A. Koreksi 1: Pemisahan Entitas Customer (Pemilik) vs Toko Bangunan & Proyek (1:N)
* **Kondisi pada Dokumen (`Bab 3 & 10`)**:
  * Menggunakan tabel tunggal `master_pelanggan` yang menggabungkan nama toko, nama pemilik, NIK, dan harga jual dalam satu baris.
* **Kondisi Nyata Bisnis & Codebase Aktual**:
  * Satu pemilik (Customer) dapat memiliki lebih dari satu toko bangunan retail, proyek konstruksi, atau gudang transit di berbagai lokasi.
  * **Plafon Kredit dan Deposit** terpusat di entitas Pemilik (`data_customer`).
  * **Titik Pengiriman Fisik Semen** tercatat pada entitas Cabang (`data_toko_bangunan`).
* **Koreksi Skema Relasi**:
  ```mermaid
  erDiagram
      data_customer ||--o{ data_toko_bangunan : "memiliki cabang (1:N)"
      data_customer ||--o{ penjualan : "plafon kredit & deposit (1:N)"
      data_toko_bangunan ||--o{ penjualan : "tujuan kirim barang (1:N)"
  ```

---

### B. Koreksi 2: Penyelarasan Nomenklatur Tabel & Kolom Database

Berikut adalah tabel komparasi nama tabel di dokumen spesifikasi vs nama tabel aktual di database MySQL Laravel:

| No | Modul Domain | Nama Tabel di Dokumen Spesifikasi | Nama Tabel Aktual di Laravel | Keterangan Penyelarasan |
| :---: | :--- | :--- | :--- | :--- |
| 1 | Master Akun | `master_akun` | `data_kode_akun` | Memiliki kolom `kode_akun`, `nama_akun`, `tipe_akun`, `posisi_normal`, `saldo_awal`, `saldo_berjalan`. |
| 2 | Jurnal Umum | `transaksi_jurnal` & `rincian_jurnal` | `jurnal_umum` | Tersimpan per baris debit/kredit dengan `nomor_jurnal`, `tanggal_transaksi`, `kode_akun`, `posisi`, `nominal`. |
| 3 | Customer / Pemilik | `master_pelanggan` | `data_customer` | Menyimpan entitas legal/pemilik, plafon kredit terpusat, saldo piutang, saldo deposit. |
| 4 | Outlet / Cabang | *(Belum ada di dokumen)* | `data_toko_bangunan` | Menyimpan outlet toko retail, proyek kontraktor, gudang transit, PIC, dan alamat fisik. |
| 5 | Produk Semen | `master_barang` | `data_semen` | Menyimpan kode barang (`SMN-xxx`), nama semen, jenis, harga pokok, dan harga jual standar. |
| 6 | Wilayah Distribusi | `master_wilayah_pelanggan` | `data_wilayah` | Menyimpan zonasi logistik dan distribusi. |
| 7 | Penjualan Semen | `penjualan_retail` | `penjualan` | Menyimpan faktur penjualan (`nomor_faktur`), `kode_customer`, `kode_toko`, metode bayar, total netto. |
| 8 | Piutang Usaha | `piutang_pelanggan` | `list_piutang` | Menyimpan data faktur piutang tempo dan saldo sisa piutang. |
| 9 | Deposit Customer | `deposit_pelanggan` | `list_deposit` / `deposit_customer` | Menyimpan mutasi masuk dan keluar saldo deposit toko. |
| 10 | Pengeluaran Kas AP | `pengeluaran_kas` | `pengeluaran` / `data_pengeluaran_kas` | Menyimpan biaya operasional, BBM, dan pemotongan kas/rekening bank. |
| 11 | Master Karyawan | `master_karyawan` | `data_karyawan` | Menyimpan NIK 16 digit, kategori karyawan, tanggal masuk/berhenti, dan relasi `id_jabatan`. |
| 12 | Hak Akses & Akun | `akun_pengguna` | `account`, `super_account`, `jabatan`, `modul`, `hak_akses_jabatan` | Sistem RBAC granular berbasis jabatan dan modul. |

---

### C. Koreksi 3: Penyelarasan Hierarki 10 Peran (Role 01 s.d. Role 10)

Dokumen Bab 13 perlu diperbarui dari 4 peran sederhana menjadi 10 peran operasional nyata:

| Kode Peran | Nama Peran / Jabatan | Prefix Kode Karyawan | Lingkup Modul & Kewenangan |
| :---: | :--- | :---: | :--- |
| **Role 01** | **Super Admin** | `ADM-` | Manajemen akun, reset sandi, RBAC, dan konfigurasi master global. |
| **Role 02** | **SPV Keuangan** | `KEU-` | Otorisasi pengeluaran kas, buku besar, neraca saldo, neraca eksekutif, dan laba rugi. |
| **Role 03** | **Staff AR (Piutang)** | `SAR-` | Penerbitan faktur penjualan semen, monitoring piutang, pelunasan, dan mutasi deposit. |
| **Role 04** | **Staff AP (Hutang)** | `SAP-` | Penebusan SO semen ke pabrik SIG, pengeluaran kas operasional, dan rilisan uang jalan driver. |
| **Role 05** | **Dispatcher** | `DSP-` | Pembuatan surat jalan pengiriman, kuota SO aktif, penugasan armada dan supir. |
| **Role 06** | **Pengawas Driver** | `PDR-` | Monitoring status supir armada, data SIM, dan rekap penugasan pengiriman semen. |
| **Role 07** | **SPV Gudang** | `GDG-` | Monitoring stok semen per gudang/plant, mutasi stok fisik, dan stock opname berkala. |
| **Role 08** | **Direktur / Manager** | `MGR-` | Laporan eksekutif ringkas, monitoring omset penjualan, margin keuntungan, dan aset. |
| **Role 09** | **SPV Operasional** | `OPS-` | Pengelolaan bengkel servis, SPK perbaikan armada, master sparepart, dan KSO. |
| **Role 10** | **Pengawas Kendaraan** | `PKN-` | Monitoring data armada truk, masa berlaku pajak/STNK/KIR, dan riwayat perbaikan. |

---

### D. Koreksi 4: Mesin Generator Kode Otomatis & Algoritma Gap-Filling

Dokumen spesifikasi Bab 3 dan 10 perlu memasukkan dokumentasi helper [`app/Helpers/GeneratorKodeOtomatis.php`](file:///c:/laragon/www/laravel1/app/Helpers/GeneratorKodeOtomatis.php):
* **Algoritma Lowest Missing Positive Integer**: Menjamin nomor sekuensial selalu padat tanpa celah meskipun terdapat data masa lalu yang dihapus.
* **Daftar Prefix Resmi**:
  * `CUST-xxx`: Master Customer Pemilik
  * `TKB-xxx`: Master Toko Bangunan / Proyek Cabang
  * `SMN-xxx`: Master Produk Semen
  * `WLY-xxx`: Master Wilayah Distribusi
  * `AST-xxx`: Master Aset Tetap Perusahaan
  * `SJ-xxx`: Surat Jalan Logistik
  * `SPK-xxx`: Surat Perintah Kerja Bengkel
  * `PRT-xxx`: Master Sparepart Kendaraan
  * `FB-SP-xxx`: Faktur Pembelian Sparepart
  * `KSO-xxx` & `OAK-xxx`: Kontrak KSO & Tarif Ongkos Angkut KSO
  * `JNS-xxx`: Jenis Klasifikasi Aset Truk

---

### E. Koreksi 5: Rekening Bank Perusahaan & Akun COA Terkait

Di dalam dokumen disebutkan Bank BNI (`1123`), sedangkan pada sistem aktual rekening operasional yang digunakan adalah:
1. **Bank BRI Operasional** (Akun COA: `1121` / Rekening ID: `1`)
2. **Bank Mandiri Operasional** (Akun COA: `1122` / Rekening ID: `2`)
3. **Kas Operasional Kantor** (Akun COA: `1111`)
4. **Kas Rilisan Uang Jalan Supir** (Akun COA: `1107`)

---

## 🎯 3. Rekomendasi Langkah Penyelarasan

1. **Pembaruan Berkas `.agents/panduan_spesifikasi_sistem_akuntansi.md`**:
   - Ganti DDL pada Bab 3 dan Bab 10 dengan skema database aktual (termasuk tabel `data_toko_bangunan`).
   - Perbarui Bab 11 (Nilai Domain) untuk mencakup 10 jabatan RBAC dan opsi metode pembayaran yang selaras.
   - Tambahkan dokumentasi relasi 1:N Customer-Toko Bangunan pada Bab 6 (Logika Transaksi Operasional).
2. **Integrasi Mesin Auto-Journal ke Controller Transaksi**:
   - Buat Service Class `App\Services\Keuangan\MesinJurnalOtomatis` yang mengeksekusi matriks jurnal otomatis pada saat:
     - Faktur Penjualan terbit (`FakturPenjualanController@store`)
     - Pengeluaran Kas disetujui (`PengeluaranKasController@approve`)
     - Pembelian SO tersimpan (`PembelianSOController@store`)
     - Rilisan Uang Jalan diterbitkan (`ListRilisanController@store`)
3. **Verifikasi Keseimbangan Buku Besar**:
   - Mengaktifkan pengecekan balance validation $\sum \text{Debit} = \sum \text{Kredit}$ di setiap entri jurnal otomatis.
