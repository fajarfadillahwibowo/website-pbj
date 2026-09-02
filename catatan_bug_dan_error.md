# 📝 Pelacak Bug, Error, & Progres Terlewati Real-time

## 🔴 Daftar Bug & Error
- **[TERSELESAIKAN] Undefined property: stdClass::$nomor_transaksi_pengeluaran pada view `keuangan/ap/pengeluaran_kas.blade.php`**:
  - *Penyebab:* Nama kolom pada tabel `data_pengeluaran_kas` adalah `nomor_pengeluaran`.
  - *Solusi:* Mengubah referensi `$keluar->nomor_transaksi_pengeluaran` menjadi `$keluar->nomor_pengeluaran`.
- **[TERSELESAIKAN] Duplikasi blok form modal pada `master/customer/index.blade.php`**:
  - *Penyebab:* Patch ganda saat perapian script.
  - *Solusi:* Mengganti seluruh view dengan struktur bersih, modular, dan kontras teks tajam.
- **[TERSELESAIKAN] Kontras warna teks pada header modal detail 360 derajat**:
  - *Penyebab:* Warna teks `text-slate-900` tersamar di beberapa container putih.
  - *Solusi:* Mempertegas dengan `text-slate-900 font-extrabold dark:text-white` dan `text-slate-600 dark:text-slate-300 font-medium`.

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
