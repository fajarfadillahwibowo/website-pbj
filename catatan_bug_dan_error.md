# 📝 Pelacak Bug, Error, & Progres Terlewati Real-time

## 🔴 Daftar Bug & Error
- **[TERSELESAIKAN] Undefined property: stdClass::$nomor_transaksi_pengeluaran pada view `keuangan/ap/pengeluaran_kas.blade.php`**:
  - *Penyebab:* Nama kolom pada tabel `data_pengeluaran_kas` adalah `nomor_pengeluaran`.
  - *Solusi:* Mengubah referensi `$keluar->nomor_transaksi_pengeluaran` menjadi `$keluar->nomor_pengeluaran`. Seluruh 18 route sekarang lolos dengan status HTTP 200 OK.

---

## ⏭️ Progres Terlewati/Tertunda
*Tidak ada tugas Developer 1 yang tertunda. Seluruh 6 domain Developer 1 pada dokumen `docs/02_Pembagian_Tugas.md` telah diselesaikan secara penuh.*

---

## ✅ Status Verifikasi & Solusi (Developer 1 & Modul Operasional)

### 1. Modul Keuangan & Master Data (Developer 1)
| No | Modul / Fitur | Rute URL | Status HTTP | Keterangan Verifikasi |
|---|---|---|---|---|
| 1 | Super Admin (Kelola Akun) | `/superadmin/kelola-akun` | **200 OK** | CRUD akun, reset sandi, toggle status aktif/nonaktif terhubung DB. |
| 2 | Master Customer Toko | `/master/customer` | **200 OK** | CRUD mitra toko, kalkulasi plafon, piutang, deposit, filter wilayah. |
| 3 | Master Produk Semen | `/master/barang` | **200 OK** | CRUD semen zak & curah, estimasi margin jual. |
| 4 | Master Wilayah Distribusi | `/master/wilayah` | **200 OK** | CRUD wilayah, hitung mitra toko terhubung, proteksi relasi data. |
| 5 | Master Karyawan & Driver | `/master/karyawan` | **200 OK** | CRUD karyawan, filter kategori staf/driver/gudang/teknisi/manajemen. |
| 6 | Faktur Penjualan (AR) | `/keuangan/ar/faktur-penjualan` | **200 OK** | Terbit faktur `INV-YYYYMMDD-XXX`, potong deposit, validasi limit plafon kredit toko. |
| 7 | List Piutang (AR) | `/keuangan/ar/list-piutang` | **200 OK** | Monitoring piutang per toko, form cicilan pelunasan, mutasi saldo customer. |
| 8 | Deposit Customer (AR) | `/keuangan/ar/deposit-customer` | **200 OK** | Riwayat mutasi deposit, modal top up saldo deposit toko. |
| 9 | Pembelian SO Pabrik (AP) | `/keuangan/ap/pembelian-so` | **200 OK** | Penerbitan SO semen ke pabrik, kalkulasi volume & harga, alokasi gudang. |
| 10 | Pengeluaran Kas (AP) | `/keuangan/ap/pengeluaran-kas` | **200 OK** | Catat kas keluar operasional, BBM & Tol armada, pemotongan rekening sumber & akun COA. |
| 11 | Rilisan Uang Jalan (AP) | `/keuangan/ap/list-rilisan` | **200 OK** | Rilisan uang jalan supir armada truk terintegrasi akun COA 1107. |
| 12 | Bagan Akun COA (Akuntansi) | `/keuangan/akuntansi/kode-akun` | **200 OK** | CRUD klasifikasi akun aktiva, kewajiban, modal, pendapatan, beban. |
| 13 | Jurnal Umum (Akuntansi) | `/keuangan/akuntansi/jurnal-umum` | **200 OK** | Entri double-entry berpasangan debit & kredit otomatis, cek keseimbangan saldo. |
| 14 | Aset Perusahaan (Akuntansi) | `/keuangan/akuntansi/aset-perusahaan` | **200 OK** | Inventaris armada truk & peralatan gudang, total perolehan nilai aktiva tetap. |
| 15 | Laporan Neraca (Eksekutif) | `/laporan/neraca` | **200 OK** | Posisi keuangan aktiva lancar/tetap vs pasiva (liabilitas & ekuitas) real-time. |
| 16 | Laporan Laba Rugi (Eksekutif) | `/laporan/laba-rugi` | **200 OK** | Perhitungan omset penjualan semen, HPP pabrik, biaya operasional, dan laba bersih. |
| 17 | Laporan Arus Kas (Eksekutif) | `/laporan/arus-kas` | **200 OK** | Arus kas masuk customer, kas keluar operasional, dan saldo akhir kas & bank. |

### 2. Modul Operasional, Logistik & Bengkel
| No | Modul / Fitur | Rute URL | Status HTTP | Keterangan Verifikasi |
|---|---|---|---|---|
| 1 | Dispatcher & Surat Jalan | `/operasional/pengiriman/surat-jalan` | **200 OK** | Auto-generator nomor SJ, live kuota SO, dropdown kustom SO/Driver/Truk. |
| 2 | Pengawas Kendaraan / Truk | `/operasional/armada/kendaraan` | **200 OK** | Standardisasi form tambah/edit truk, dropdown kustom jenis aset & status. |
| 3 | Pengawas Supir / Driver | `/operasional/armada/driver` | **200 OK** | Standardisasi form tambah/edit supir, dropdown kustom status & wilayah. |
| 4 | SPV Gudang - Stok Semen | `/operasional/gudang/stok` | **200 OK** | Standardisasi modal penyesuaian stok semen, dropdown kustom jenis semen. |
| 5 | SPV Gudang - Stock Opname | `/operasional/gudang/opname` | **200 OK** | Standardisasi modal opname fisik gudang, auto-kalkulasi selisih stok. |
| 6 | Bengkel - SPK Perbaikan | `/operasional/bengkel/perbaikan` | **200 OK** | Generator nomor SPK, dropdown kustom unit armada & prioritas servis. |
| 7 | Bengkel - Master Sparepart | `/operasional/bengkel/sparepart` | **200 OK** | Dropdown kustom kategori suku cadang & modal mutasi stok part. |
| 8 | Bengkel - Pembelian Sparepart | `/operasional/bengkel/pembelian-sparepart` | **200 OK** | Generator nomor faktur beli, dropdown part + watcher harga otomatis. |
| 9 | Kerja Sama Operasional (KSO) | `/operasional/kso` | **200 OK** | Tab Mitra KSO & Tab Tarif OA KSO dengan filter & modal dropdown kustom. |
| 10 | Master - Jenis Aset Truk | `/master/jenis-aset` | **200 OK** | Generator nomor jenis aset cerdas, modal detail unit truk terpasang. |
