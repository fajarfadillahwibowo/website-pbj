# 📝 Pelacak Bug, Error, & Progres Terlewati Real-time

## 🔴 Daftar Bug & Error
- **[TERSELESAIKAN] Undefined property: stdClass::$nomor_transaksi_pengeluaran pada view `keuangan/ap/pengeluaran_kas.blade.php`**:
  - *Penyebab:* Nama kolom pada tabel `data_pengeluaran_kas` adalah `nomor_pengeluaran`.
  - *Solusi:* Mengubah referensi `$keluar->nomor_transaksi_pengeluaran` menjadi `$keluar->nomor_pengeluaran`.
- **[TERSELESAIKAN] Nomor bukti transaksi keuangan menggunakan rand(100, 999) acak melompat ratusan dan rentan duplicate entry**:
  - *Penyebab:* Penggunaan fungsi acak bawaan PHP `rand(100, 999)` pada Deposit Customer (`DEP-IN-`), Faktur Penjualan (`INV-` & `DEP-OUT-`), Pembelian SO (`SO-PBJ-`), Rilisan Driver (`RLS-DRV-`), Pengeluaran Kas (`KAS-OUT-`), dan Jurnal Umum (`JU-`).
  - *Solusi:* Membangun metode `GeneratorKodeOtomatis::buatKodeTransaksi()` berbasis tanggal transaksi (`YYYYMMDD`) dan nomor urut sekuensial dengan algoritma gap-filling (`001`, `002`, dst.), serta merapikan data riwayat deposit lama agar tertib sekuensial.
- **[TERSELESAIKAN] Inkonsistensi dan kesalahan struktur kolom pada file master `database/skema_database.sql`**:
  - *Penyebab:* File SQL cadangan tidak di-update saat migrasi lanjutan dibuat. Tabel `opname_gudang` memiliki kolom yang salah total (atribut customer/kendaraan), tabel `data_ongkos_angkut` memakai kolom wilayah lama, tabel `data_toko_bangunan` & `ongkos_kso` belum ada, serta kolom baru (`kode_toko`, `nomor_lo`, dll.) belum tercatat.
  - *Solusi:* Memperbarui seluruh definisi tabel, kolom, constraint, dan seed data RBAC pada `database/skema_database.sql` sehingga hasil verifikasi perbandingan skema dengan database aktual menunjukkan 0 perbedaan (100% sinkron).
- **[TERSELESAIKAN] Tumpang tindih tabel aset akuntansi dengan armada operasional kendaraan & ketiadaan sistem amortisasi penyusutan PSAK 16**:
  - *Penyebab:* Tabel `data_aset` sebelumnya menampung data fisik truk operasional (nomor mesin, nomor rangka, tanggal KIR, tanggal pajak) sekaligus menjadi foreign key transaksi pengiriman dan bengkel, sehingga penginputan aset non-kendaraan (tanah, bangunan, mesin gudang, alat kantor) menjadi rancu dan ketiadaan sistem amortisasi nilai buku.
  - *Solusi:* 
    1. Memisahkan tabel `data_aset` (fokus aktiva tetap finansial & depresiasi) dan `data_kendaraan` (fokus fisik armada truk operasional berelasi ke `kode_aset`).
    2. Menambahkan tabel `riwayat_penyusutan` untuk mencatat beban depresiasi bulanan yang otomatis memposting ayat jurnal umum debit Beban Penyusutan dan kredit Akumulasi Penyusutan.
    3. Menerapkan aturan PSAK 16: Tanah tidak disusutkan (0%), Bangunan 20 tahun (5%), Truk 8 tahun (12.5%), dan Alat Kantor 4 tahun (25%).
    4. Mengalihkan foreign key pada `pengiriman` (Surat Jalan) dan `perbaikan_kendaraan` (SPK Bengkel) ke `data_kendaraan(kode_kendaraan)`.
    5. Menyelaraskan seluruh Controller, Model, View Blade, dan file master `skema_database.sql` (0 perbedaan terverifikasi).
- **[TERSELESAIKAN] Duplikasi blok form modal pada `master/customer/index.blade.php`**:
  - *Penyebab:* Patch ganda saat perapian script.
  - *Solusi:* Mengganti seluruh view dengan struktur bersih, modular, dan kontras teks tajam.
- **[TERSELESAIKAN] Kontras warna teks pada header modal detail 360 derajat**:
  - *Penyebab:* Warna teks `text-slate-900` tersamar di beberapa container putih.
  - *Solusi:* Mempertegas dengan `text-slate-900 font-extrabold dark:text-white` dan `text-slate-600 dark:text-slate-300 font-medium`.
- **[TERSELESAIKAN] Warna angka redup/pucat (faint) pada kartu metrik finansial aset dan kebingungan istilah kolom tabel**:
  - *Penyebab:* Angka pada kartu `Total Harga Perolehan` dan `Beban Susut / Bulan` menggunakan utilitas warna yang redup dan rentan tertimpa varian gelap/terang, serta istilah kolom `RELASI ARMADA` kurang intuitif bagi pengguna.
  - *Solusi:* 
    1. Mengubah angka `Total Harga Perolehan` menjadi `text-slate-900 dark:text-white font-extrabold` (hitam pekat di mode terang, putih bersih di mode gelap).
    2. Mengubah angka `Beban Susut / Bulan` dari amber pucat menjadi `text-orange-600 dark:text-orange-400 font-extrabold` (warna oranye tajam berstandar kontras WCAG AAA).
    3. Mengubah header kolom tabel dari `RELASI ARMADA` menjadi `Armada Fisik Terhubung` lengkap dengan ikon truk dan tooltip penjelas keterkaitan unit fisik kendaraan operasional di lapangan.


---

## ⏭️ Progres Terlewati/Tertunda
*Tidak ada tugas yang tertunda. Seluruh 24 rute sistem distribusi semen, master data relasi 1:N Customer-Toko Bangunan, dan operasional logistik berjalan 100% lancar.*

---

## ✅ Status Verifikasi & Solusi (24 Rute HTTP 200 OK)

### 1. Modul Master Data & Keuangan
| No | Modul / Fitur | Rute URL | Status HTTP | Keterangan Verifikasi |
|---|---|---|---|---|
| 1 | Super Admin (Kelola Akun) | `/superadmin/kelola-akun` | **200 OK** | CRUD akun, reset sandi, toggle status aktif/nonaktif terhubung DB. |
| 2 | Master Customer (Pemilik Induk) | `/master/customer` | **200 OK** | CRUD pemilik, plafon kredit terpusat, saldo piutang, saldo deposit, Modal Detail 360 Derajat. |
| 3 | Master Toko Bangunan & Proyek | `/master/toko-bangunan` | **200 OK** | CRUD outlet toko retail/proyek/gudang, relasi FK `kode_customer`, Modal Detail 360 Derajat. |
| 4 | Master Produk Semen | `/master/barang` | **200 OK** | CRUD semen zak & curah, estimasi margin jual. |
| 5 | Master Wilayah Distribusi | `/master/wilayah` | **200 OK** | CRUD wilayah, hitung mitra toko terhubung, proteksi relasi data. |
| 6 | Master Karyawan & Driver | `/master/karyawan` | **200 OK** | CRUD karyawan, kode otomatis per jabatan (`ADM-`, `KEU-`, `SAR-`, `SAP-`, `DSP-`, `DRV-`, dll.). |
| 7 | Faktur Penjualan (AR) | `/keuangan/ar/faktur-penjualan` | **200 OK** | Dropdown pilihan Toko Bangunan / Proyek tujuan kirim, auto-detect Customer Induk & limit kredit. |
| 8 | List Piutang (AR) | `/keuangan/ar/list-piutang` | **200 OK** | Monitoring piutang per toko/pemilik, form cicilan pelunasan, mutasi saldo customer. |
| 9 | Deposit Customer (AR) | `/keuangan/ar/deposit-customer` | **200 OK** | Riwayat mutasi deposit, modal top up saldo deposit toko. |
| 10 | Pembelian SO Pabrik (AP) | `/keuangan/ap/pembelian-so` | **200 OK** | Penerbitan SO semen ke pabrik, kalkulasi volume & harga, alokasi gudang. |
| 11 | Pengeluaran Kas (AP) | `/keuangan/ap/pengeluaran-kas` | **200 OK** | Catat kas keluar operasional, BBM & Tol armada, pemotongan rekening sumber & akun COA. |
| 12 | Rilisan Uang Jalan (AP) | `/keuangan/ap/list-rilisan` | **200 OK** | Rilisan uang jalan supir armada truk terintegrasi akun COA 1107. |
| 13 | Bagan Akun COA (Akuntansi) | `/keuangan/akuntansi/kode-akun` | **200 OK** | CRUD klasifikasi akun aktiva, kewajiban, modal, pendapatan, beban. |
| 14 | Jurnal Umum (Akuntansi) | `/keuangan/akuntansi/jurnal-umum` | **200 OK** | Entri double-entry berpasangan debit & kredit otomatis, cek keseimbangan saldo. |
| 15 | Aset Perusahaan (Akuntansi) | `/keuangan/akuntansi/aset-perusahaan` | **200 OK** | Inventaris armada truk & peralatan gudang, total perolehan nilai aktiva tetap. |

### 2. Modul Operasional, Logistik & Bengkel
| No | Modul / Fitur | Rute URL | Status HTTP | Keterangan Verifikasi |
|---|---|---|---|---|
| 16 | Dispatcher & Surat Jalan | `/operasional/pengiriman/surat-jalan` | **200 OK** | Auto-generator nomor SJ, live kuota SO, dropdown kustom SO/Driver/Truk. |
| 17 | Pengawas Kendaraan / Truk | `/operasional/armada/kendaraan` | **200 OK** | Standardisasi form tambah/edit truk, dropdown kustom jenis aset & status. |
| 18 | Pengawas Supir / Driver | `/operasional/armada/driver` | **200 OK** | Standardisasi form tambah/edit supir, dropdown kustom status & wilayah. |
| 19 | SPV Gudang - Stok Semen | `/operasional/gudang/stok` | **200 OK** | Standardisasi modal penyesuaian stok semen, dropdown kustom jenis semen. |
| 20 | SPV Gudang - Stock Opname | `/operasional/gudang/opname` | **200 OK** | Standardisasi modal opname fisik gudang, auto-kalkulasi selisih stok. |
| 21 | Bengkel - SPK Perbaikan | `/operasional/bengkel/perbaikan` | **200 OK** | Generator nomor SPK, dropdown kustom unit armada & prioritas servis. |
| 22 | Bengkel - Master Sparepart | `/operasional/bengkel/sparepart` | **200 OK** | Dropdown kustom kategori suku cadang & modal mutasi stok part. |
| 23 | Bengkel - Pembelian Sparepart | `/operasional/bengkel/pembelian-sparepart` | **200 OK** | Generator nomor faktur beli, dropdown part + watcher harga otomatis. |
| 24 | Kerja Sama Operasional (KSO) | `/operasional/kso` | **200 OK** | Tab Mitra KSO & Tab Tarif OA KSO dengan filter & modal dropdown kustom. |
