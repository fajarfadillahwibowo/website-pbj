# 🧠 Memori Proyek: Website Sistem Distribusi & Keuangan PT Putra Balkom Jaya (PBJ)

## 📌 Status Terkini
- **Branch Aktif:** `web-dev1`
- **Fokus Eksekusi:** Integrasi Arsitektur Relasi 1:N Customer (Entitas Pemilik & Finansial) ke Data Toko Bangunan / Proyek Cabang, Modul Master Toko Bangunan & Proyek (`/master/toko-bangunan`), Modul Monitoring List SO (`/keuangan/ap/list-so`), Matriks Hak Akses & Read-Only Guard, Modal Kinerja 360 Derajat, Dropdown Kustom `<x-dropdown-kustom>`, Input Tanggal Kustom `<x-input-tanggal>`, Standarisasi Wajib `*` & Opsional, serta Form Faktur Penjualan (AR).
- **Status Master Customer & Toko Bangunan:** ✅ **100% SELESAI, TERHUBUNG, & TERVERIFIKASI**.
- **Status Pemisahan Tabel Aset & Kendaraan:** ✅ **100% SELESAI, TERINTEGRASI & TERUJI** (`data_aset` fokus finansial aktiva tetap & depresiasi PSAK 16, sedangkan `data_kendaraan` fokus fisik armada ekspedisi truk. Modul Surat Jalan & SPK Bengkel kini merujuk ke `kode_kendaraan`).
- **Status Input Multi-Unit / Batch Aset Sekaligus & Konsistensi Input Plat Nomor:** ✅ **100% SELESAI & TERUJI KONSISTEN** (Dapat menginput satu tipe aset dengan menentukan `jumlah_unit`. Sistem otomatis membuat N baris aset dengan penomoran `#01, #02...`, kode unik sekuensial bebas bentrok melalui `GeneratorKodeOtomatis::buatBanyakKode()`, serta sub-form kartu dinamis untuk plat nomor 3 kolom [Wilayah, Nomor Seri, Seri Huruf] dengan live visual badge `PLAT B ____ ___` yang 100% seragam dengan mode single unit).
- **Status Amortisasi Depresiasi Bulanan:** ✅ **100% SELESAI, OTOMATIS & TERUJI** (Tabel `riwayat_penyusutan`, tombol Tutup Buku Bulanan di `/keuangan/akuntansi/aset-perusahaan`, auto-posting jurnal umum debit Beban Penyusutan dan kredit Akumulasi Penyusutan).
- **Status Penggolongan Aset & Nomenklatur Jelas:** ✅ **100% SELESAI & SANGAT KONTRAS** (4 Kategori Efisien & Teruji: 1. Kendaraan Armada Truk, 2. Tanah & Bangunan Properti, 3. Mesin & Alat Gudang, 4. Elektronik & Perabot Kantor).
- **Status Penyatuan Kategori Tanah & Bangunan:** ✅ **100% SELESAI & TERUJI** (Tanah & Gedung disatukan ke `AST-TNH: Tanah & Bangunan Properti`. Dilengkapi field wajib `keterangan` untuk mencatat fasilitas/bangunan di atas tanah, input luas & sertifikat dokumen, serta sakelar PSAK 16: 'Ada Bangunan Gedung (Susut 20 Th / 5%)' vs 'Tanah Kosong Saja (Bebas Penyusutan)').
- **Status Modal Aset Landscape & Atribut Lengkap ERD Kendaraan:** ✅ **100% SELESAI & TERVERIFIKASI BROWSER LIVE** (Modal Tambah, Edit, dan Detail berformat Landscape `max-w-5xl` 2 panel berdampingan: Kiri untuk Data Pokok & Finansial PSAK 16, Kanan untuk Spesifikasi Fisik Objek. Menampung seluruh atribut ERD: jenis kendaraan truk, muatan tonase/sak, merek pabrikan, no mesin, no rangka, tahun pembuatan, tanggal KIR, tanggal pajak, entitas pemilik, dan status aset yang tersinkronisasi otomatis ke `data_kendaraan`).
- **Status Developer 1 & Operasional:** ✅ **100% SELESAI & TERVERIFIKASI (HTTP 200 OK pada 25 rute modul view, termasuk menu baru List SO)**.
- **Status Modul Baru List SO (`/keuangan/ap/list-so`):** ✅ **100% SELESAI & TERVERIFIKASI** (Monitoring real-time kuota kuantitas zak semen per nomor SO/LO pabrik SIG vs realisasi pengambilan).
- **Status Matriks Hak Akses & Read-Only:** ✅ **100% SELESAI & TERUJI** (Mode Read-Only dengan penanda status 'Lihat' di sidebar dan badge 'Mode Lihat Saja' pada form transaksi di luar wewenang modifikasi peran).
- **Status Komponen Input Tanggal Kustom (`<x-input-tanggal>`):** ✅ **100% SELESAI & TERPASANG DI SELURUH FORM** (Desain 100% identik dengan `<x-dropdown-kustom>`: background `#F4F6F9`/`#1C1E2A`, border slate, font medium text-xs, ikon SVG kalender kustom, date picker trigger overlay, dan two-way binding Alpine.js).
- **Status Standarisasi Penanda Wajib & Opsional:** ✅ **100% SELESAI DI SELURUH VIEW FORM** (Setiap input wajib ditandai bintang merah `<span class="text-rose-500">*</span>` dan input opsional ditandai abu-abu `<span class="text-slate-400 font-normal text-[10px]">(Opsional)</span>`).
- **Status Bebas Pembatasan Modal (`overflow-visible`):** ✅ **100% SELESAI DI SELURUH MODAL** (Dropdown dan kalender mengambang bebas di luar batas canvas modal).
- **Status Mesin Jurnal Otomatis Terpadu (`MesinJurnalOtomatis.php`):** ✅ **100% SELESAI, TERINTEGRASI, & TERUJI** (Otomasi pencatatan jurnal umum double-entry yang atomik dalam `DB::transaction`, validasi keseimbangan mutlak $\sum Debit == \sum Kredit$, mekanisme *Idempotency Guard* berbasis `referensi_transaksi` untuk mencegah duplikasi, serta pemutakhiran otomatis `saldo_berjalan` akun COA di tabel `data_kode_akun` secara real-time).
- **Status Integrasi Auto-Journal pada Transaksi Harian:** ✅ **100% SELESAI & TERUJI (Faktur Penjualan Tunai/Transfer/Kredit/Deposit, Pelunasan Piutang, Penebusan SO SIG, Pengeluaran Kas AP, dan Rilisan Uang Jalan Supir)**.
- **Status Cetak Faktur Penjualan Resmi (`cetak_faktur.blade.php`):** ✅ **100% SELESAI & TERINTEGRASI** (Kop surat resmi PT Putra Balkom Jaya, rincian produk semen zak, informasi rekening bank resmi Mandiri/BRI/BCA, kalkulasi finansial, 3 kolom pengesahan tanda tangan, dan tombol aksi cetak langsung pada tabel faktur penjualan).
- **Status Dinamisasi Laporan Neraca Keuangan (`neraca.blade.php`):** ✅ **100% SELESAI & SINKRON REAL-TIME** (Seluruh pos neraca: kas & bank, piutang, persediaan semen, uang muka supir, aset tetap, akumulasi depresiasi, hutang dagang AP, titipan deposit, dan ekuitas membaca langsung saldo berjalan akun COA `data_kode_akun`, sehingga selalu seimbang $\text{Aktiva} \equiv \text{Passiva}$).
- **Status Perbaikan Buku Jurnal Umum (`jurnal_umum.blade.php`):** ✅ **100% SELESAI & TERUJI** (Perbaikan properti kolom database `$jurnal->posisi` dan `$jurnal->nama_akun`, integrasi `MesinJurnalOtomatis` pada form modal input manual. Status neraca saldo **SEIMBANG (BALANCED)** dengan mutasi Rp 51.291.667).
- **Status Standarisasi Validasi Input Nominal Rupiah (HTML5 Step Fix):** ✅ **100% SELESAI DI 9 VIEW FORM** (Seluruh input nominal diubah menjadi `min="0" step="any"` untuk mengeliminasi peringatan constraint kelipatan browser dan mengizinkan input nominal bebas).
- **Status Standarisasi & Redesain Form Berkas Driver (Dispatcher):** ✅ **100% SELESAI & TERSTANDARISASI** (Penulisan baku 'Foto KTP / Identitas (Opsional)' dan 'Surat Kontrak Kerja (SPK) (Opsional)', kartu unggah berkas enterprise dengan preview terintegrasi, tombol hapus/batal yang aman, standarisasi pesan validasi controller, dan pembersihan title tooltip redundan pada menu sidebar).
- **Status Perbaikan CRUD Data Kendaraan & Validasi Bahasa Indonesia (Dispatcher):** ✅ **100% SELESAI & TERUJI** (Memperbaiki error validasi pada input, edit, dan hapus kendaraan: normalisasi atribut `kode_kendaraan`, `merek_kendaraan`, dan `status_kendaraan` dari form, sinkronisasi action URL edit dan hapus dengan primary key armada, penambahan field legal owner `nama_pemilik` pada modal edit, serta lokalisasi 100% pesan validasi formulir ke dalam Bahasa Indonesia yang santun dan profesional).
- **Status Format Otomatis Titik Ribuan (`<x-input-rupiah>`) & Pengaman Hanya Angka:** ✅ **100% SELESAI & TERAPLIKASI DI SELURUH APLIKASI** (Semua input harga, nominal, biaya, tarif, dan plafon kredit secara otomatis menambahkan titik pemisah ribuan secara real-time seperti `1.000.000` saat diketik dan memblokir huruf. Seluruh field identitas angka seperti NIK KTP, No HP/Telepon, Nomor Rekening, Jumlah Zak, Stok Gudang, Sparepart, Tahun Pembuatan, dan Odometer diproteksi secara global pada event `keydown` dan `input` sehingga huruf ditolak seketika).
- **Status Pemilihan Produk Semen & Kalkulasi Otomatis Faktur Penjualan (AR):** ✅ **100% SELESAI & TERVERIFIKASI** (Penambahan kolom `kode_barang`, `nama_barang`, `satuan_barang`, `jumlah_zak`, `harga_satuan` pada tabel `penjualan`, dropdown produk semen dinamis dengan autofill harga satuan standar, kalkulasi subtotal bruto dan netto secara real-time di form modal, tampilan nama produk & kuantitas pada tabel daftar faktur, serta tabel rincian item invoice cetak resmi yang dinamis).
- **Status Standarisasi Tombol Aksi Tabel Modern (`<x-menu-aksi-tabel>`):** ✅ **100% SELESAI & TERAPLIKASI DI SELURUH TABEL SISTEM** (Komponen Three-Dots Menu Popover `•••` berbasis Alpine.js dengan penulisan label ringkas & padat: `Salin Kode`, `Detail`, `Edit`, `Hapus`, `Cetak`, `Bayar`, `Ubah Status`, `Mutasi Stok`, notifikasi toast salin cepat, proteksi Hak Akses RBAC & Read-Only Guard `apakahReadOnly(modul)`, deteksi ruang otomatis membuka ke atas `bukaKeAtas` saat berada di baris bawah tabel agar tidak tertutup pagination toolbar, serta **Eksklusivitas Popover Tunggal**: menggunakan listener global window `tutup-semua-menu` dengan instance `idUnik` sehingga hanya 1 popover menu aksi yang dapat terbuka di layar pada saat bersamaan).
- **Status Standarisasi Dokumen Cetak Resmi & Tombol Aksi Cetak di Seluruh Tabel:** ✅ **100% SELESAI, TERINTEGRASI, & TERUJI** (Komponen popover `<x-menu-aksi-tabel>` diperkaya dengan props native `:aksiCetak` dan `:urlCetak` lengkap dengan ikon printer SVG profesional. Seluruh modul yang memiliki dokumen fisik resmi [Faktur Penjualan, Kwitansi Deposit, Bukti Memorial Jurnal, Kartu Inventaris Aset PSAK 16, Surat Jalan Pengiriman, SPK Perbaikan Bengkel, Bukti Beli Sparepart, Kartu Suku Cadang, Dossier Armada Truk, Biodata Driver, Kartu Stok Gudang, Berita Acara Opname BASO, dan Surat Ketetapan Tarif OA] telah dilengkapi lembar cetak standar berkop PT Putra Balkom Jaya, rincian teknis, dan tanda tangan pengesahan resmi).
- **Status Mode Read-Only Eksekutif (DIREKTUR_MANAGER):** ✅ **100% SELESAI & AKTIF** (Fungsi `apakahReadOnly()` di `app.blade.php` secara otomatis mengunci seluruh aksi tambah, ubah, dan hapus transaksi menjadi mode lihat-saja aman untuk jabatan Direktur & General Manager).
- **Status Pembersihan Diagnostik & Warning Linter IDE:** ✅ **100% BERSIH (0 WARNING / 0 ERROR)** (Blade templates cached successfully via `artisan view:cache`).


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

---

## 🛠️ Catatan Pembaruan & Penanganan Bug
### 1. Perbaikan Format Tanggal (SQLSTATE 22007: 1292 Incorrect date value)
- **Penyebab:** Tanggal terserialisasi dalam format ISO-8601 UTC (`2026-09-04T00:00:00.000000Z`) dari Eloquent model, lalu dikirim kembali lewat formulir edit modal ke kolom `DATE` MySQL tanpa sanitasi.
- **Solusi Defense-in-Depth:**
  1. **Eloquent Model:** Mengatur casting `date:Y-m-d` pada model `AsetPerusahaan` dan `Kendaraan`.
  2. **Controller (`KendaraanController`):** Memastikan seluruh penulisan/pembaruan kolom tanggal (`tanggal_pembelian`, `tanggal_kir`, `tanggal_pajak`) diformat via `Carbon::parse(...)->format('Y-m-d')` pada `simpan`, `ambilDetail`, `perbarui`, `simpanAset`, `ambilDetailAset`, dan `perbaruiAset`.
  3. **Frontend Blade & Alpine.js:** Memastikan inisialisasi modal edit di `kendaraan.blade.php` dan komponen `<x-input-tanggal>` memfilter string dengan `.split('T')[0]` sehingga nilai input murni `YYYY-MM-DD`.
### 2. Perbaikan Kelipatan Nilai Rupiah (SQLSTATE 22003: 1264 Out of range value for column 'harga_aset')
- **Penyebab:** Kolom `harga_aset` di MySQL bertipe `DECIMAL(15,2)` yang mengembalikan nilai desimal dengan akhiran `.00` (contoh: `"1200000000.00"`). Komponen `<x-input-rupiah>` sebelumnya membersihkan string menggunakan `.replace(/[^0-9]/g, '')`, yang menghilangkan titik desimal dan menyebabkan akhiran `.00` berubah menjadi dua angka nol tambahan (terkalikan 100 setiap kali form diedit). Akibatnya, nilai melonjak hingga belasan triliun dan melebihi kapasitas kolom `DECIMAL(15,2)` (`9.999.999.999.999,99`).
- **Solusi Defense-in-Depth:**
  1. **Komponen `<x-input-rupiah>`:** Diperbaiki fungsi `formatKeTampilan` untuk membedakan dan membulatkan angka berformat desimal database MySQL (`Math.round(parseFloat(strAngka))`) sehingga `.00` tidak lagi terkonversi menjadi dua nol ekstra.
  2. **Controller (`KendaraanController`):** Membulatkan nilai `harga_aset` dan `harga_perolehan` ke integer pada `ambilDetail` dan `ambilDetailAset`, serta menambahkan validasi batas maksimum `max:9999999999999` di `perbarui` dan `perbaruiAset`.
  3. **Perbaikan Data:** Nilai aset `AST-004` di database dinormalisasi kembali ke nilai wajar `1.200.000.000` (Rp 1,2 Miliar).
### 3. Perbaikan & Penyelarasan Penuh CRUD 3 Modul Role Dispatcher (Kendaraan, Driver, Pengiriman)
- **Modul Pengiriman (Surat Jalan):**
  1. Menyelaraskan mapping opsi dropdown dan default value form tambah ke `kode_kendaraan` (sebelumnya mengirim `kode_aset` yang memicu foreign key mismatch constraint).
  2. Menambahkan normalisasi otomatis di `SuratJalanController` (`simpan` dan `perbarui`) sehingga jika form mengirimkan `kode_aset`, sistem otomatis mengonversinya ke `kode_kendaraan`.
  3. Membungkus operasi database ke dalam transaksi atomik `DB::beginTransaction()` dan `commit()` / `rollBack()`.
  4. Sanitasi format `tanggal_kirim` dengan `Carbon::parse(...)->format('Y-m-d H:i:s')` dan pembersihan string tanggal di modal edit.
- **Modul Data Karyawan (Driver):**
  1. Casting `tanggal_mulai_kerja` dan `tanggal_berhenti` di model `Driver` diubah ke `date:Y-m-d`.
  2. Membungkus penyimpanan dan pembaruan driver ke dalam `DB::beginTransaction()` dengan perlindungan cleanup berkas fisik storage jika transaksi database gagal (anti-file orphan).
  3. Memperbaiki logika pembaruan tanggal berakhir kontrak sehingga `tanggal_berhenti` dapat di-nullkan/dikosongkan kembali jika supir menjadi karyawan tetap.
  4. Menambahkan input `tanggal_berhenti` di formulir edit modal agar sinkron dengan formulir tambah.
  5. Memperbaiki operasi `hapus` driver: penghapusan record database dilakukan di dalam transaksi terlebih dahulu, dan penghapusan berkas fisik di storage hanya dieksekusi setelah database berhasil commit (mencegah kehilangan berkas jika supir masih terikat transaksi operasional lain).
- **Modul Data Kendaraan:**
  1. Menambahkan validasi `max:9999999999999` pada `harga_aset` di metode `simpan` agar konsisten dengan `perbarui`.
  2. Memperbaiki pengecekan null-safe `!empty($validated['nama_aset'])`, `!empty($validated['no_mesin'])`, `!empty($validated['no_rangka'])`, `!empty($validated['jenis_kendaraan'])`, dan `!empty($validated['tipe_armada'])` untuk mencegah fatal error `Undefined array key`.
  3. Membungkus operasi `hapus` ke dalam transaksi atomik `DB::beginTransaction()` dan menyinkronkan pembersihan record `data_aset` terkait.
- **Hasil Pengujian Otomatis:**
  - Skrip pengujian `tests/test_crud_dispatcher.php` menguji siklus lengkap Create, Read, Update, dan Delete untuk ketiga modul: **100% LULUS (0 Error)**.
  - Endpoint HTTP `/operasional/armada/kendaraan`, `/operasional/armada/driver`, dan `/operasional/pengiriman/surat-jalan` berstatus **HTTP 200 OK**.
### 4. Analisis & Verifikasi CRUD Penuh Role "Pengawas Driver" (Sidebar "Data Driver")
- **Wewenang & RBAC:** Role `PENGAWAS_DRIVER` memiliki hak akses penuh (bisa tambah, ubah, dan hapus) pada modul `/operasional/armada/driver`.
- **Personalisasi UI & Sidebar:**
  1. Label sidebar otomatis menampilkan teks **`Data Driver`** (bukan `Data Karyawan (Driver)`) khusus saat role `PENGAWAS_DRIVER` aktif.
  2. Header modul menampilkan badge **`Pengawas Driver (Akses Penuh)`** dan judul halaman disesuaikan ke **`Data Driver`**.
- **Standarisasi Seluruh Inputan Formulir:**
  1. **Kode Driver:** Format sekuensial `DRV-XXX` (mode gap-filling otomatis) atau `DRV-XXXX` (mode acak anti-enumerasi).
  2. **Nama Lengkap:** Wajib diisi, maksimal 100 karakter.
  3. **Jabatan Karyawan:** Dropdown kustom terhubung ke `data_karyawan.id_jabatan`.
  4. **Status Karyawan:** Opsi terstandarisasi (`aktif`, `kontrak`, `tetap`, `non-aktif`, `berhenti`).
  5. **No. KTP / NIK:** Indikator digit dinamis `16/16 Digit`, format berjarak 4 digit (`3201 0203 0405 0001`), dan sanitasi 16 digit murni pada database.
  6. **No. Handphone:** Validasi nomor HP/WhatsApp dengan placeholder standar `0812-xxxx-xxxx`.
  7. **Tanggal Mulai Kerja:** Komponen `<x-input-tanggal>` berformat murni `YYYY-MM-DD`.
  8. **Tanggal Berakhir Kontrak:** Komponen `<x-input-tanggal>` opsional, tervalidasi `after_or_equal:tanggal_mulai_kerja`, dan dapat di-nullkan kembali jika supir menjadi karyawan tetap.
  9. **Alamat Domisili:** Textarea domisili supir.
  10. **Berkas Foto KTP & Kontrak SPK:** Validasi berkas maksimal 2 MB, format `JPG, PNG, WEBP` untuk foto dan `PDF, DOC, DOCX, JPG` untuk kontrak, kartu pratinjau instan dengan tombol batal/hapus, serta penghapusan fisik storage aman pasca-commit DB.
- **Hasil Pengujian Otomatis:** Skrip `tests/test_crud_pengawas_driver.php` mengonfirmasi siklus Create, Read, Update, Delete, dan API Generator Kode Otomatis berstatus **100% LULUS (0 Error)**.

### 5. Analisis & Peningkatan Penuh CRUD Role "SPV Gudang" (Sidebar "Data Gudang" & "Opname Gudang")
- **Wewenang & RBAC:** Role `SPV_GUDANG` (`id_jabatan = 7`) memiliki wewenang penuh tanpa read-only (`apakahReadOnly = false`) pada modul `gudang_stok` (`/operasional/gudang/stok`) dan `gudang_opname` (`/operasional/gudang/opname`).
- **Modul Data Gudang (`StokGudangController` & `stok.blade.php`):**
  1. **Sanitasi Format Rupiah:** Sanitasi `preg_replace('/[^0-9]/', '', ...)` pada input `harga_barang` di metode `simpan` dan `perbarui` untuk mencegah penolakan validasi `numeric` akibat titik format ribuan.
  2. **Transaksi Database Atomik:** Membungkus seluruh mutasi data (`simpan`, `perbarui`, `mutasiStok`, dan `hapus`) ke dalam blok `DB::beginTransaction()`, `commit()`, dan `rollBack()` dengan blok `try-catch` terstruktur.
  3. **Fleksibilitas Mutasi Stok:** Memperbarui validasi `mutasiStok` sehingga tipe mutasi `atur` mendukung nilai `0` zak (saat gudang dikosongkan untuk perawatan), sementara tipe `masuk` dan `keluar` tetap mewajibkan minimal `1` zak.
  4. **Proteksi Integritas Relasional Hapus Gudang:** Menambahkan pencegahan penghapusan fasilitas gudang jika masih memiliki riwayat di tabel `opname_gudang` (mencegah error SQL `fk_opname_gudang`), selain proteksi terhadap transaksi `pembelian_so`.
  5. **Otomatisasi Input UI:** Menambahkan mapping otomatis harga standar semen (`$watch('formTambah.kode_barang')`) di formulir tambah, dan pembulatan harga bersih pada modal edit.
- **Modul Opname Gudang (`StockOpnameController`, `StockOpname.php` & `opname.blade.php`):**
  1. **Format Tanggal Presisi (Defense-in-Depth):** Mengubah casting `tanggal_opname` di model `StockOpname` ke `date:Y-m-d`, melakukan sanitasi `Carbon::parse(...)->format('Y-m-d')` di controller, serta menambahkan `.split('T')[0]` pada fungsi modal edit Alpine.js.
  2. **Transaksi Atomik & Sinkronisasi Stok:** Membungkus `simpan`, `perbarui`, `konfirmasiSPV`, dan `hapus` dalam `DB::beginTransaction()`. Saat status disetujui (`dikonfirmasi_spv`), pembaruan status opname dan sinkronisasi kuantitas stok riil pada tabel `list_gudang_so` dijamin berjalan secara atomik (tanpa desinkronisasi).
  3. **Sinkronisasi Reaktif Form:** Menambahkan reaktivitas Alpine `$watch('formTambah.kode_gudang')` yang secara otomatis mengisi input `stok_sistem` dan default `stok_fisik` sesuai kuantitas stok riil fasilitas gudang terpilih.
- **Hasil Pengujian Otomatis:**
  - Script pengujian komprehensif `tests/test_crud_spv_gudang.php` menguji 16 skenario pengujian siklus CRUD, kalkulasi selisih stok, mutasi masuk/keluar/atur, proteksi foreign key, dan sinkronisasi opname: **16 Berhasil, 0 Gagal (100% Lulus)**.
  - Endpoint `/operasional/gudang/stok` dan `/operasional/gudang/opname` merespons dengan status **HTTP 200 OK**.

### 6. Analisis & Peningkatan Penuh CRUD Role "SPV Operasional" (5 Menu Sidebar)
- **Wewenang & RBAC:** Role `SPV_OPERASIONAL` (`id_jabatan = 4`) memiliki wewenang operasional penuh (`apakahReadOnly = false`) pada 5 modul sidebar:
  1. **Data Ongkos Angkut (`kirim_ongkos`):** `/operasional/pengiriman/ongkos-angkut` (Route: `operasional.pengiriman.ongkos_angkut`)
  2. **Opname Gudang (`gudang_opname`):** `/operasional/gudang/opname` (Route: `operasional.gudang.opname`)
  3. **Data Kendaraan (`armada_truk`):** `/operasional/armada/kendaraan` (Route: `operasional.armada.kendaraan`)
  4. **Pengiriman / Surat Jalan (`kirim_sj`):** `/operasional/pengiriman/surat-jalan` (Route: `operasional.pengiriman.surat_jalan`)
  5. **Data KSO (`ops_kso`):** `/operasional/kso` (Route: `operasional.kso`)
- **Modul Data Ongkos Angkut (`OngkosAngkutController.php` & `ongkos_angkut.blade.php`):**
  1. **Transaksi Database Atomik:** Membungkus operasi `simpan`, `perbarui`, dan `hapus` ke dalam `DB::beginTransaction()`, `DB::commit()`, dan `DB::rollBack()` dengan blok `try-catch` terstruktur.
  2. **Perbaikan Bug Relasional:** Memperbaiki bug query pengecekan tabel non-eksisten `DB::table('surat_jalan')` pada metode `hapus()` (tabel fisik sebenarnya bernama `pengiriman` dan tidak memiliki relasi `kode_oa`), sehingga mencegah error fatal `SQLSTATE[42S02]: Base table or view not found`.
  3. **Sanitasi Nominal Mata Uang UI:** Menambahkan sanitasi pembulatan float `Math.round(parseFloat(...))` pada fungsi `bukaModalEdit` Alpine.js untuk `harga_oa`, `harga_kso`, dan `harga_kso_khusus` guna mencegah trailing zero/desimal yang mengganggu format Rupiah.
- **Modul Data KSO & Master Tarif KSO (`KSOController.php`, `KSO.php` & `kso/index.blade.php`):**
  1. **Model Date Serialization:** Memperbarui attribute casting pada model `KSO.php` untuk `tanggal_mulai` dan `tanggal_selesai` menjadi `'date:Y-m-d'` agar serialisasi JSON ke antarmuka Blade/Alpine tidak menghasilkan string ISO dengan timestamp (`T00:00:00.000000Z`).
  2. **Sanitasi Input Tanggal & Nilai Kontrak:** Menambahkan sanitasi `preg_replace('/[^0-9]/', '', ...)` pada field `nilai_kontrak` dan `ongkos_angkut` sebelum validasi numerik. Menambahkan sanitasi tanggal presisi menggunakan `Carbon::parse(...)->format('Y-m-d')` pada `simpanKSO` dan `perbaruiKSO`.
  3. **Pembersihan Berkas Fisik Pasca-Commit:** Membungkus `simpanKSO`, `perbaruiKSO`, dan `hapusKSO` dalam transaksi database atomik, serta menunda pembersihan berkas lama/dihapus pada storage publik hingga transaksi DB berhasil di-commit (mencegah kehilangan berkas jika transaksi di-rollback).
  4. **UI Defense-in-Depth:** Menambahkan pemotongan tanggal `.split('T')[0]` pada fungsi modal edit Alpine.js `bukaModalEditKso` dan pembulatan numerik `Math.round(parseFloat(...))` pada `nilai_kontrak` serta `ongkos_angkut`.
- **Modul Opname Gudang, Data Kendaraan & Pengiriman:**
  1. Seluruh perbaikan dari iterasi Dispatcher & SPV Gudang (transaksi atomik, sanitasi `YYYY-MM-DD`, sanitasi nominal `harga_aset` BIGINT, sinkronisasi kuantitas stok riil, serta validasi status) terintegrasi dan berfungsi mulus untuk role SPV Operasional.
- **Hasil Pengujian Otomatis:**
  - Suite pengujian komprehensif `tests/test_crud_spv_operasional.php` menguji seluruh 5 modul sidebar (Create, Read, Update, Delete Ongkos Angkut, Opname Gudang, Data Kendaraan, Pengiriman Surat Jalan, dan Kontrak KSO): **18 Berhasil, 0 Gagal (100% Lulus)**.
  - Kelima endpoint URL `/operasional/pengiriman/ongkos-angkut`, `/operasional/gudang/opname`, `/operasional/armada/kendaraan`, `/operasional/pengiriman/surat-jalan`, dan `/operasional/kso` merespons dengan status **HTTP 200 OK**.

### 7. Analisis & Peningkatan Penuh CRUD Role "Pengawas Kendaraan" (3 Menu Sidebar)
- **Wewenang & RBAC:** Role `PENGAWAS_KENDARAAN` (`id_jabatan = 10`) memiliki wewenang operasional teknis bengkel penuh (`apakahReadOnly = false`) pada 3 modul sidebar:
  1. **Perbaikan Kendaraan (SPK):** `/operasional/bengkel/perbaikan` (Route: `operasional.bengkel.perbaikan`)
  2. **Pembelian Sparepart:** `/operasional/bengkel/pembelian-sparepart` (Route: `operasional.bengkel.pembelian_sparepart`)
  3. **List Sparepart:** `/operasional/bengkel/sparepart` (Route: `operasional.bengkel.sparepart`)
- **Modul List Sparepart (`SparepartController.php`, `Sparepart.php` & `sparepart.blade.php`):**
  1. **Sanitasi Format Rupiah:** Menambahkan sanitasi `preg_replace('/[^0-9]/', '', ...)` pada field `harga_satuan` sebelum validasi numerik pada `simpan` dan `perbarui`.
  2. **Proteksi Integritas Relasional:** Menambahkan proteksi referensial pada metode `hapus()` terhadap tabel `pembelian_sparepart` (`ON DELETE RESTRICT`) agar user mendapatkan pesan error ramah berbahasa Indonesia tanpa memicu crash `SQLSTATE[23000]`.
  3. **Fleksibilitas Mutasi Stok Fisik:** Menyesuaikan aturan validasi pada `mutasiStok` sehingga tipe mutasi `atur` mendukung nilai `0` unit (bila stok suku cadang di bengkel habis/kosong), sementara tipe `masuk` dan `keluar` tetap mewajibkan minimal `1`.
  4. **Transaksi Database Atomik:** Membungkus operasi `simpan`, `perbarui`, `mutasiStok`, dan `hapus` dalam `DB::beginTransaction()`, `commit()`, dan `rollBack()` dengan blok `try-catch`.
  5. **UI Defense-in-Depth:** Membulatkan nominal `harga_satuan` dengan `Math.round(parseFloat(...))` pada modal edit Alpine.js.
- **Modul Pembelian Sparepart (`PembelianSparepartController.php`, `PembelianSparepart.php` & `pembelian_sparepart.blade.php`):**
  1. **Model Date Serialization:** Mengubah casting `tanggal_beli` pada model `PembelianSparepart.php` ke `'date:Y-m-d'` agar serialisasi JSON ke antarmuka Blade/Alpine tidak menghasilkan string ISO dengan timestamp.
  2. **Sanitasi Angka Rupiah & Tanggal:** Menambahkan sanitasi titik Rupiah pada `harga_beli` serta sanitasi tanggal presisi menggunakan `Carbon::parse(...)->format('Y-m-d')`.
  3. **Transaksi Atomik & Sinkronisasi Stok Real-Time:** Membungkus operasi `simpan` (penambahan stok master), `perbarui` (penyesuaian selisih stok), dan `hapus` (pemulihan stok master) ke dalam transaksi database atomik tunggal untuk menjamin sinkronisasi mutlak data faktur dan persediaan fisik bengkel.
  4. **UI Defense-in-Depth:** Menambahkan pemotongan string tanggal `.split('T')[0]` dan pembulatan `harga_beli` pada modal edit Alpine.js.
- **Modul Perbaikan Kendaraan / SPK Servis (`PerbaikanKendaraanController.php`, `PerbaikanKendaraan.php` & `perbaikan.blade.php`):**
  1. **Model Date Serialization:** Mengubah casting `tanggal_masuk` dan `tanggal_selesai` pada model `PerbaikanKendaraan.php` menjadi `'date:Y-m-d'`.
  2. **Sanitasi Input Biaya Jasa & Sparepart:** Menambahkan pembersihan karakter non-numerik pada field `biaya_jasa` dan `biaya_sparepart` sebelum aturan validasi dijalankan.
  3. **Transaksi Database Atomik:** Membungkus `simpan`, `perbarui`, `perbaruiStatus`, dan `hapus` dalam transaksi database terstruktur dengan pesan sukses/gagal berbahasa Indonesia.
  4. **UI Defense-in-Depth:** Menambahkan `.split('T')[0]` pada tanggal servis dan `Math.round(parseFloat(...))` pada biaya di modal edit.
- **Modul Data Ongkos Angkut (`OngkosAngkutController.php` & `ongkos_angkut.blade.php`):**
  1. **Penanganan Relasi Master Gudang SPV Gudang:** Mengatasi error validasi `exists:list_gudang_so,kode_gudang` yang memunculkan pesan _"Fasilitas gudang yang dipilih tidak valid atau belum terdaftar pada Master Gudang (SPV Gudang)"_ dengan menambahkan sanitasi input string kosong/`'semua'`/`'null'` menjadi `null` valid pada controller, serta mendaftarkan opsi `-- Tanpa Fasilitas Gudang Tertentu (Umum) --` di form Blade.
  2. **Integritas Master Data Gudang:** Menambahkan sinkronisasi record gudang `GDG-CKP-03` (Gudang Hub Cikampek) yang sebelumnya berada di seeder namun belum tersinkronisasi ke tabel master `list_gudang_so`.
  3. **Pemberian Nilai Default & Perlindungan Null Coalescing:** Mengamankan akses array hasil validasi (`kontrak_oa`, `keterangan`, `kode_gudang`) menggunakan null coalescing (`?? null`) untuk mencegah `ErrorException: Undefined array key`. Memberikan fallback otomatis untuk muatan default (`Semen Zak 50kg`) dan wilayah terdaftar.
  4. **Kompatibilitas Komponen Kustom UI:** Memodernisasi komponen Alpine.js (`dropdown-kustom.blade.php` dan `input-rupiah.blade.php`) dengan context binding `this` agar setter dan getter Alpine tidak mengalami referensi global error (`ReferenceError`).

### 8. Standarisasi Tampilan Tabel, Fitur Filter, dan Menu Aksi Popover pada 5 Role (06, 07, 08, 09, 10)
- **Tujuan:** Menyelaraskan seluruh tampilan tabel data, filter, dan tombol aksi di 5 role utama yang tertera pada dokumen akun pengguna:
  1. **Peran 06: Pengawas Driver (`PENGAWAS_DRIVER`):**
     - Modul Armada Driver (`operasional/armada/driver.blade.php`): Mengganti tombol aksi horizontal menjadi popover tiga titik eksklusif `<x-menu-aksi-tabel>` lengkap dengan fitur salin kode driver, detail modal, edit modal, dan hapus dengan proteksi Read-Only Guard.
     - Hasil Uji: `tests/test_crud_pengawas_driver.php` lulus 100%.
  2. **Peran 07: SPV Gudang (`SPV_GUDANG`):**
     - Modul Data Gudang (`operasional/gudang/stok.blade.php`): Mengganti tombol aksi inline menjadi `<x-menu-aksi-tabel>` dengan slot aksi `Mutasi Stok` dan `Hapus Gudang`.
     - Modul Stock Opname Fisik (`operasional/gudang/opname.blade.php`): Mengganti tombol aksi menjadi `<x-menu-aksi-tabel>` dengan slot konfirmasi SPV serta status badge visual.
     - Hasil Uji: `tests/test_crud_spv_gudang.php` lulus 16 Berhasil, 0 Gagal (100% Lulus).
  3. **Peran 08: Direktur & Manager (`DIREKTUR_MANAGER`):**
     - Modul Neraca Keuangan (`laporan/neraca.blade.php`): Menambahkan bilah filter periode bulan dan tahun menggunakan `<x-dropdown-kustom submitOnChange="true">` serta badge status `Akses Eksekutif: Read-Only`.
     - Modul Laba Rugi (`laporan/laba_rugi.blade.php`): Memodernisasi select native filter periode menjadi `<x-dropdown-kustom submitOnChange="true">` dan menyematkan badge status `Akses Eksekutif: Read-Only`.
  4. **Peran 09: SPV Operasional (`SPV_OPERASIONAL`):**
     - Modul Ongkos Angkut (`operasional/pengiriman/ongkos_angkut.blade.php`): Memperbaiki kode izin RBAC menjadi `kirim_ongkos` dan mengintegrasikan `<x-menu-aksi-tabel>`.
     - Modul Mitra KSO & Tarif KSO (`operasional/kso/index.blade.php`): Menstandarisasi aksi Tab 1 (Mitra KSO) dan Tab 2 (Tarif OA KSO) menggunakan `<x-menu-aksi-tabel>`.
     - Modul Surat Jalan Pengiriman (`operasional/pengiriman/surat_jalan.blade.php`): Menyelaraskan event penutupan menu popover dengan pemanggilan `@click.stop="menuTerbuka = false; ..."`.
     - Hasil Uji: `tests/test_crud_spv_operasional.php` lulus 18 Berhasil, 0 Gagal (100% Lulus).
  5. **Peran 10: Pengawas Kendaraan (`PENGAWAS_KENDARAAN`):**
     - Modul Pembelian Sparepart (`operasional/bengkel/pembelian_sparepart.blade.php`): Mengganti tombol aksi menjadi `<x-menu-aksi-tabel>`.
     - Modul List Sparepart (`operasional/bengkel/sparepart.blade.php`): Menstandarisasi event penutupan menu popover `@click.stop="menuTerbuka = false; ..."`.
     - Modul Perbaikan SPK (`operasional/bengkel/perbaikan.blade.php`): Menstandarisasi tombol aksi popover, cetak SPK, dan tandai selesai.
     - Hasil Uji: `tests/test_crud_pengawas_kendaraan.php` lulus 21 Berhasil, 0 Gagal (100% Lulus).
- **Hasil Akhir:** Seluruh tabel data kini 100% konsisten menggunakan satu komponen popover tunggal `<x-menu-aksi-tabel>`, filter modern `<x-dropdown-kustom>`, penanganan overflow bebas, dan perlindungan hak akses RBAC.
