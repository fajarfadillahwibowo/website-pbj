# 🧠 Memori Proyek: Website Sistem Distribusi & Keuangan PT Putra Balkom Jaya (PBJ)

## 📌 Status Terkini
- **Branch Aktif:** `web-dev1`
- **Fokus Eksekusi:** Integrasi Arsitektur Relasi 1:N Customer (Entitas Pemilik & Finansial) ke Data Toko Bangunan / Proyek Cabang, Modul Master Toko Bangunan & Proyek (`/master/toko-bangunan`), Modul Monitoring List SO (`/keuangan/ap/list-so`), Matriks Hak Akses & Read-Only Guard, Modal Kinerja 360 Derajat, Dropdown Kustom `<x-dropdown-kustom>`, Input Tanggal Kustom `<x-input-tanggal>`, Standarisasi Wajib `*` & Opsional, serta Form Faktur Penjualan (AR).
- **Status Master Customer & Toko Bangunan:** ✅ **100% SELESAI, TERHUBUNG, & TERVERIFIKASI**.
- **Status Pemisahan Tabel Aset & Kendaraan:** ✅ **100% SELESAI, TERINTEGRASI & TERUJI** (`data_aset` fokus finansial aktiva tetap & depresiasi PSAK 16, sedangkan `data_kendaraan` fokus fisik armada ekspedisi truk. Modul Surat Jalan & SPK Bengkel kini merujuk ke `kode_kendaraan`).
- **Status Input Multi-Unit / Batch Aset Sekaligus:** ✅ **100% SELESAI & TERUJI** (Dapat menginput satu tipe aset dengan menentukan `jumlah_unit`. Sistem otomatis membuat N baris aset dengan penomoran `#01, #02...`, kode unik sekuensial bebas bentrok melalui `GeneratorKodeOtomatis::buatBanyakKode()`, serta sub-form dinamis untuk plat nomor dan nomor mesin jika memilih kategori truk).
- **Status Amortisasi Depresiasi Bulanan:** ✅ **100% SELESAI, OTOMATIS & TERUJI** (Tabel `riwayat_penyusutan`, tombol Tutup Buku Bulanan di `/keuangan/akuntansi/aset-perusahaan`, auto-posting jurnal umum debit Beban Penyusutan dan kredit Akumulasi Penyusutan).
- **Status Penggolongan Aset & Nomenklatur Jelas:** ✅ **100% SELESAI & SANGAT KONTRAS** (1. Kendaraan Armada [Truk/Tronton/Mobil], 2. Tanah & Lahan [Lahan Usaha - Bebas Susut], 3. Bangunan & Gedung [Kantor/Gudang Semen/Pos], 4. Mesin & Alat Gudang [Forklift/Genset/Conveyor/Timbangan], 5. Elektronik & Inventaris Kantor [Komputer/Printer/AC/Meja], dilengkapi kotak panduan objek fisik real-time).
- **Status Developer 1 & Operasional:** ✅ **100% SELESAI & TERVERIFIKASI (HTTP 200 OK pada 25 rute modul view, termasuk menu baru List SO)**.
- **Status Modul Baru List SO (`/keuangan/ap/list-so`):** ✅ **100% SELESAI & TERVERIFIKASI** (Monitoring real-time kuota kuantitas zak semen per nomor SO/LO pabrik SIG vs realisasi pengambilan).
- **Status Matriks Hak Akses & Read-Only:** ✅ **100% SELESAI & TERUJI** (Mode Read-Only dengan penanda status 'Lihat' di sidebar dan badge 'Mode Lihat Saja' pada form transaksi di luar wewenang modifikasi peran).
- **Status Komponen Input Tanggal Kustom (`<x-input-tanggal>`):** ✅ **100% SELESAI & TERPASANG DI SELURUH FORM** (Desain 100% identik dengan `<x-dropdown-kustom>`: background `#F4F6F9`/`#1C1E2A`, border slate, font medium text-xs, ikon SVG kalender kustom, date picker trigger overlay, dan two-way binding Alpine.js).
- **Status Standarisasi Penanda Wajib & Opsional:** ✅ **100% SELESAI DI SELURUH VIEW FORM** (Setiap input wajib ditandai bintang merah `<span class="text-rose-500">*</span>` dan input opsional ditandai abu-abu `<span class="text-slate-400 font-normal text-[10px]">(Opsional)</span>`).
- **Status Bebas Pembatasan Modal (`overflow-visible`):** ✅ **100% SELESAI DI SELURUH MODAL** (Dropdown dan kalender mengambang bebas di luar batas canvas modal).
- **Status Sinkronisasi Master SQL (`skema_database.sql`):** ✅ **100% SINKRON & TERVERIFIKASI (0 Perbedaan)** (Tabel `data_aset` akuntansi, `data_kendaraan`, `riwayat_penyusutan`, `pengiriman`, dan `perbaikan_kendaraan` telah selaras penuh dengan database MySQL aktual).


---

## 🏢 Arsitektur Relasi 1:N Customer Pemilik ke Toko Bangunan & Proyek

### 1. Model & Tabel `data_customer` (Entitas Legal, Pemilik, & Finansial Terpusat)
- **Tujuan:** Menyimpan data pemilik modal / direktur usaha, NIK KTP, No HP, Plafon Kredit terpusat, Saldo Piutang Berjalan, dan Saldo Deposit aktif.
- **Relasi:** `hasMany(TokoBangunan::class, 'kode_customer', 'kode_customer')`.
- **Fitur 360 Derajat:** Modal Kinerja Customer menampilkan agregat sisa plafon kredit, daftar seluruh cabang toko bangunan dan proyek yang dimiliki, serta status keaktifannya.

### 2. Model & Tabel `data_toko_bangunan` (Outlet Fisik, Proyek Konstruksi, & Titik Drop Point)
- **Tujuan:** Menyimpan titik fisik pengiriman semen, nama toko/proyek cabang, tipe lokasi (`toko_retail`, `proyek_kontraktor`, `gudang_transit`), wilayah zonasi (`kode_wilayah`), PIC lapangan & nomor telepon toko, alamat lengkap pengiriman, dan titik koordinat maps.
- **Relasi:** `belongsTo(Customer::class)` dan `belongsTo(Wilayah::class)`.
- **Fitur 360 Derajat:** Modal Kinerja Toko menampilkan profil pemilik induk, plafon kredit customer induk, total akumulasi nilai pembelian toko, dan jumlah transaksi yang selesai.

### 3. Integrasi Transaksi Penjualan (`penjualan` / Faktur Penjualan AR)
- Form Tambah Faktur Penjualan memiliki opsi memilih **Toko Bangunan / Proyek Cabang Tujuan Kirim**.
- Sistem otomatis mendeteksi entitas **Customer Pemilik Induk** untuk validasi plafon kredit piutang tempo atau pemotongan saldo deposit secara akurat.

---

## 🔢 Generator Kode Otomatis & Algoritma Gap-Filling (Sequence Gap Reuse)

### 1. Helper Baru: `app/Helpers/GeneratorKodeOtomatis.php`
- **Algoritma "Gap-Filling" (Lowest Missing Positive Integer):**
  1. Mengambil seluruh nilai kode yang ada di database berdasarkan tabel, kolom, dan prefix.
  2. Mengekstrak bilangan angka integer di akhir kode ke dalam array daftar nomor terpakai.
  3. Mencari angka terkecil pertama yang belum digunakan dan memformatnya dengan leading zero (`001`, `002`, dst.).

### 2. Daftar Prefix Kode:
- **Master Customer:** `CUST-` (contoh: `CUST-001`)
- **Master Toko Bangunan / Proyek:** `TKB-` (contoh: `TKB-001`, `TKB-002`)
- **Master Produk Semen:** `SMN-` (contoh: `SMN-001`)
- **Master Wilayah:** `WLY-` (contoh: `WLY-001`)
- **Master Karyawan:** `ADM-`, `KEU-`, `SAR-`, `SAP-`, `DSP-`, `PDR-`, `GDG-`, `MGR-`, `OPS-`, `PKN-`, `DRV-`
- **Aset & Truk:** `AST-`
- **Surat Jalan:** `SJ-`
- **SPK Bengkel:** `SPK-`
- **Master Sparepart:** `PRT-`
- **Pembelian Sparepart:** `FB-SP-`
- **KSO:** `KSO-`, `OAK-`
- **Master Jenis Aset:** `JNS-`

### 3. Generator Transaksi Keuangan Sekuensial Berbasis Tanggal (`buatKodeTransaksi`):
- **Logika:** Menggabungkan awalan prefix modul keuangan + tanggal transaksi (`YYYYMMDD`) + nomor urut sekuensial dengan algoritma gap-filling (`001`, `002`, dst.). Reset harian secara otomatis.
- **Daftar Modul Keuangan Terintegrasi:**
  - **Deposit Customer (Masuk):** `DEP-IN-YYYYMMDD-001` (di [`DepositCustomerController`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/DepositCustomerController.php))
  - **Faktur Penjualan (AR):** `INV-YYYYMMDD-001` (di [`FakturPenjualanController`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/FakturPenjualanController.php))
  - **Potong Deposit Penjualan (Keluar):** `DEP-OUT-YYYYMMDD-001` (di [`FakturPenjualanController`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AR/FakturPenjualanController.php))
  - **Pembelian SO Semen (AP):** `SO-PBJ-YYYYMMDD-001` (di [`PembelianSOController`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/PembelianSOController.php))
  - **Pengeluaran Kas Operasional (AP):** `KAS-OUT-YYYYMMDD-001` (di [`PengeluaranKasController`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/PengeluaranKasController.php))
  - **Rilisan Uang Jalan Driver (AP):** `RLS-DRV-YYYYMMDD-001` (di [`HutangSupplierController`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/AP/HutangSupplierController.php))
  - **Jurnal Umum Akuntansi:** `JU-YYYYMMDD-001` (di [`JurnalUmumController`](file:///c:/laragon/www/laravel1/app/Http/Controllers/Keuangan/Akuntansi/JurnalUmumController.php))

---

## 🌐 Status Server & Endpoint (25 Rute 100% OK)
- **Laravel Artisan Dev Server:** `http://127.0.0.1:8000` (25/25 Routes HTTP 200 OK)
- **Laragon MySQL:** `127.0.0.1:3306` (200 OK)
- **Laragon Apache:** `http://localhost/laravel1/public` (200 OK)
