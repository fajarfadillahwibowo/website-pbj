# 📝 Pelacak Bug, Error, & Progres Terlewati Real-time

## 🔴 Daftar Bug & Error
*Tidak ada bug aktif saat ini. Seluruh 17 endpoint Developer 1 lolos pengujian dengan status HTTP 200 OK.*

---

## ⏭️ Progres Terlewati/Tertunda
*Tidak ada tugas Developer 1 yang tertunda. Seluruh 6 domain Developer 1 pada dokumen `docs/02_Pembagian_Tugas.md` telah diselesaikan secara penuh.*

---

## ✅ Status Verifikasi & Solusi (Developer 1)

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
