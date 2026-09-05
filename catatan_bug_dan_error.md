# 📝 Pelacak Bug, Error, & Progres Terlewati Real-time

- **[TERSELESAIKAN] Pembenahan Bug Progres 0% Monitoring List SO, Multi-Stage Approval Pengiriman SPV Operasional, & Konfirmasi Fisik SPV Gudang**:
  - *Kebutuhan & Permasalahan:*
    1. Pada modul Monitoring List SO (`/keuangan/ap/list-so`), kolom *Terambil* dan *Progres* selalu bernilai `0 Zak` dan `0%`, padahal transaksi pengiriman Surat Jalan telah dibuat.
    2. Alur penerbitan pengiriman belum memiliki tahapan validasi multi-stage approval: Dispatcher langsung menerbitkan Surat Jalan tanpa persetujuan berjenjang dari staf yang berwenang.
    3. Belum ada mekanisme konfirmasi fisik barang tiba (Good Receipt) oleh SPV Gudang saat armada membongkar muatan di fasilitas gudang PBJ.
    4. Pembagian wewenang role RBAC perlu diperketat: Super Admin hanya mengelola akun pengguna, Direktur/Manager hanya melihat laporan keuangan (Neraca & Laba Rugi), dan wewenang approval pengiriman berada mutlak pada SPV Operasional.
  - *Akar Masalah (Root Cause):*
    1. Tabel `pengiriman` (Surat Jalan) tidak memiliki kolom `jumlah_zak` muatan dan tidak memiliki relasi atau sinkronisasi balik ke tabel `pembelian_so`. Kolom `qty_pengambilan` pada `pembelian_so` tidak pernah di-update oleh pengiriman apapun.
    2. Tidak ada status persetujuan draf, kolom verifikator (`disetujui_oleh`, `disetujui_pada`, `alasan_penolakan`), maupun kolom status penerimaan gudang (`status_penerimaan_gudang`).
    3. Tidak ada guard check kuota SO saat membuat Surat Jalan baru, sehingga muatan berisiko melebihi kuota tebusan pabrik SIG.
  - *Solusi Menyeluruh:*
    1. **Migrasi Database (`2026_09_05_000004_tambah_kolom_approval_dan_muatan_pada_pengiriman.php`):**
       - Menambahkan kolom `jumlah_zak` (integer muatan semen per pengiriman).
       - Menambahkan kolom `disetujui_oleh` (string username SPV), `disetujui_pada` (datetime), dan `alasan_penolakan` (text).
       - Menambahkan kolom `status_penerimaan_gudang` (enum: `menunggu_gudang`, `diterima_gudang`).
       - Memperbarui enum `status_pengiriman` dengan opsi: `menunggu`, `dalam_perjalanan`, `terkirim`, `ditolak`, `retur`.
    2. **Model Eloquent (`SuratJalan.php` & `PembelianSO.php`):**
       - Menambahkan atribut `$fillable`, `$casts`, dan status badge dinamis lengkap dengan warna Tailwind semantic (`menunggu` amber, `dalam_perjalanan` blue, `terkirim` emerald, `ditolak` rose).
       - Menambahkan relasi `daftarPengiriman()` pada model `PembelianSO`.
    3. **Controller Pengiriman (`SuratJalanController.php`):**
       - Form simpan Surat Jalan otomatis berstatus default `menunggu` (Draf) dan memvalidasi `jumlah_zak` tidak boleh melebihi sisa kuota SO yang belum dikirim.
       - Menambahkan method `setujui($id_pengiriman)`: Khusus role `SPV_OPERASIONAL`, memvalidasi sisa kuota SO secara atomik dalam `DB::transaction`, mengubah status ke `dalam_perjalanan`, memotong kuota SO dengan meng-update `pembelian_so.qty_pengambilan`, mengubah `status_so` menjadi `dikirim`/`selesai`, serta mengubah armada truk menjadi `Dalam Pengiriman` dan supir menjadi `Jalan`.
       - Menambahkan method `tolak(Request $request, $id_pengiriman)`: Khusus role `SPV_OPERASIONAL`, mencatat `alasan_penolakan`, mengembalikan status armada & supir ke `Tersedia`, dan merevisi kuota SO jika sebelumnya disetujui.
    4. **Controller Monitoring Kuota (`ListSOController.php`):**
       - Mengintegrasikan eager loading `daftarPengiriman` dan sinkronisasi otomatis kuota pengambilan aktual dari pengiriman disetujui (`whereIn('status_pengiriman', ['dalam_perjalanan', 'terkirim'])`).
       - Progres persentase dan sisa kuota SO ter-render secara real-time dan akurat.
    5. **Controller Gudang (`StokGudangController.php`):**
       - Mengoper daftar pengiriman yang sedang menuju gudang (`$pengirimanMenungguKonfirmasi`) ke view stok gudang.
       - Menambahkan method `konfirmasiPenerimaan(Request $request, $id_pengiriman)`: Khusus role `SPV_GUDANG`, memverifikasi fisik muatan zak yang tiba, menambahkan kuantitas zak ke `list_gudang_so.stok_tersedia`, menandai `status_penerimaan_gudang = 'diterima_gudang'` & `status_pengiriman = 'terkirim'`, serta melepaskan armada dan driver kembali ke status `Tersedia`.
    6. **Antarmuka Pengguna (Blade Views):**
       - `surat_jalan.blade.php`: Menampilkan input `Jumlah Zak Muatan` dengan info kuota SO live, badge status persetujuan, tombol aksi cepat *Setujui* dan *Tolak* khusus SPV Operasional, modal alasan penolakan, penguncian cetak Surat Jalan (hanya dapat dicetak setelah disetujui), serta lembar cetak dokumen resmi berkop dengan 4 kolom tanda tangan pengesahan (Dispatcher, SPV Operasional, Supir, Penerima).
       - `operasional/gudang/stok.blade.php`: Menambahkan seksi khusus *Konfirmasi Penerimaan Fisik Pengiriman Semen* untuk SPV Gudang dengan tombol 1-klik konfirmasi terima fisik.
       - `app.blade.php`: Menyelaraskan pergantian role di header dengan reload sesi otomatis ke backend PHP.
    7. **Dokumentasi Lengkap Alur Hulu-ke-Hilir:**
       - Membuat panduan komprehensif di `docs/08_panduan_alur_pengiriman_dan_pergudangan.md` lengkap dengan visualisasi Mermaid diagram alur, matriks RACI wewenang peran, skema database, dan 5 skenario pengujian tim QA.
  - *Hasil Verifikasi Live Browser Mandiri (Autonomous Subagent):*
    1. Dispatcher menerbitkan SJ-002 sebanyak 200 Zak dari SO-PBJ-20260905-002 (500 Zak) -> Status draf berhasil diterbitkan dengan badge amber `MENUNGGU PERSETUJUAN SPV` dan dokumen cetak terkunci aman.
    2. Login / beralih ke SPV Operasional -> Tombol hijau `Setujui` dan merah `Tolak` tampil responsif. Setelah disetujui, status berubah menjadi `DALAM PERJALANAN`.
    3. Cek Monitoring List SO (`/keuangan/ap/list-so`) -> Kolom *Terambil* terbukti bertambah menjadi `200 Zak` dan progres berhasil naik menjadi `40%` (masalah 0% terselesaikan 100%).
    4. Beralih ke SPV Gudang -> Buka `/operasional/gudang/stok`, seksi konfirmasi fisik menampilkan SJ-002 (+200 Zak). Tombol `Konfirmasi Terima Fisik` ditekan -> Stok fisik Gudang Hub Cikampek otomatis bertambah dari 1.704 Zak menjadi 1.904 Zak, status pengiriman menjadi `Terkirim`, dan armada kembali `Tersedia`.

  - *Kebutuhan:* Menambahkan penanda visual kapan data dibuat atau diperbarui di seluruh tabel sistem secara konsisten menggunakan komponen terstandarisasi, menampilkan ikon jam SVG dan format relatif ramah pengguna (contoh: `🕒 3 hari yang lalu`, `1 jam yang lalu`, atau `Baru`), serta tooltip atribut `title` dengan tanggal dan jam presisi (`d/m/Y H:i:s`).
  - *Solusi:*
    1. Membuat komponen reusable Blade `<x-waktu-relatif :diperbaruiPada="..." :dibuatPada="..." />` (`resources/views/components/waktu-relatif.blade.php`) dengan kalkulasi otomatis `Carbon::locale('id')->diffForHumans()`, fallback ke `dibuatPada` atau `'Baru'`, tooltip presisi, serta ikon jam SVG profesional tanpa emoji teks Unicode.
    2. Menambahkan migrasi database `2026_09_05_000003_tambah_diperbarui_pada_tabel_jurnal_umum.php` untuk kolom `diperbarui_pada` pada tabel `jurnal_umum`, serta pemutakhiran model `JurnalUmum`.
    3. Menerapkan `<x-waktu-relatif>` pada seluruh tabel di kolom AKSI:
       - Modul Keuangan AR: Faktur Penjualan (`faktur_penjualan.blade.php`), List Piutang (`list_piutang.blade.php`), Deposit Customer (`deposit_customer.blade.php`).
       - Modul Keuangan AP: Pengeluaran Kas (`pengeluaran_kas.blade.php`), List Rilisan Uang Jalan (`list_rilisan.blade.php`), Pembelian SO Pabrik (`pembelian_so.blade.php`), List SO (`list_so.blade.php`).
       - Modul Keuangan Akuntansi: Jurnal Umum (`jurnal_umum.blade.php`), Kode Akun COA (`kode_akun.blade.php`), Aset Perusahaan (`aset_perusahaan.blade.php`).
       - Modul Master Data: Wilayah (`master/wilayah/index.blade.php`), Toko Bangunan (`master/toko_bangunan/index.blade.php`), Customer (`master/customer/index.blade.php`), Barang Semen (`master/barang/index.blade.php`), Karyawan (`master/karyawan/index.blade.php`), Jenis Aset (`master/jenis_aset/index.blade.php`).
       - Modul Operasional: Surat Jalan (`operasional/pengiriman/surat_jalan.blade.php`), dan tabel lainnya yang sudah terpasang struktur identik.
       - Modul Superadmin: Kelola Akun (`superadmin/kelola_akun.blade.php`) beserta penambahan field `diperbarui_pada` & `dibuat_pada` pada `KelolaAkunController`.
  - *Hasil Verifikasi:* Seluruh view lolos kompilasi Blade (`php artisan view:cache` lolos dengan 0 error) dan lolos inspeksi live browser mandiri (Autonomous Browser Subagent) pada modul AR & AP. Badge waktu relatif tampil presisi, rapi di bawah tombol popover aksi tanpa merusak tata letak, dan tanpa error konsol.

- **[TERSELESAIKAN] Getaran Layout Shift Saat Refresh/Filter/Navigasi & Transisi Mulus Full SPA**:
  - *Penyebab:*
    1. Pembaruan filter via `<x-dropdown-kustom>` memanggil `form.submit()` native JavaScript yang menurut standar W3C mem-bypass event listener `submit`, sehingga interceptor SPA tidak menangkapnya dan browser melakukan full page reload (layar berkedip putih dan seluruh dokumen dimuat ulang).
    2. Event listener `submit` pada `resources/views/layouts/app.blade.php` sebelumnya hanya menangani `method === 'GET'`, sehingga pengiriman form tambah/edit modal (POST/PUT/DELETE) tetap memicu reload halaman penuh.
    3. Pada `app.css`, kelas animasi bertingkat `.tabel-bertingkat tbody tr:nth-child(1..10)` menerapkan stagger delay dengan `translateY(10px)` yang membuat baris-baris tabel melompat satu per satu saat dimuat, ditambah collapse tinggi kontainer Alpine yang menyebabkan efek "bergetar" (*layout shift*).
  - *Solusi:*
    1. Mengubah pemanggilan submit di `komponenDropdownKustom` menjadi `form.requestSubmit()` agar memicu event submit DOM dan tertangkap secara otomatis oleh engine SPA.
    2. Menghapus animasi bertingkat kasar pada baris tabel (`.tabel-bertingkat tbody tr { animation: none !important; }`) dan menggantinya dengan transisi lembut `fade-masuk-halus` (`translateY(3px)`).
    3. Memperbarui engine SPA `muatKontenDinamis()` dengan penguncian tinggi kontainer (`min-height`) sebelum swap dilakukan agar kontainer tabel tidak loncat, serta menambahkan transisi opacity `0.6` ke `1.0`.
    4. Menambahkan dukungan form mutasi (POST/PUT/DELETE) di latar belakang via Fetch API dengan penutupan modal otomatis (`tutupSemuaModal()`), pembersihan dan inisialisasi ulang Alpine tree, serta ekstraksi pesan alert session ke komponen **Floating Toast Notification** di pojok kanan atas tanpa menggeser tata letak tabel.
    5. Menyelaraskan event select pada `operasional/armada/kendaraan.blade.php` agar menggunakan `requestSubmit()`.
  - *Hasil Verifikasi:* Lulus uji browser live mandiri (Autonomous Browser Subagent). Filter dropdown pada Toko Bangunan beralih secara instan dan mulus tanpa reload, URL tersinkron via History API, total baris tabel ter-update presisi, navigasi menu sidebar berjalan lancar tanpa me-reload frame sidebar, dan modal tambah/edit bekerja rapi tanpa getaran/flicker.

- **[TERSELESAIKAN] Penghapusan Tombol Filter Statis & Standarisasi UI Filter Master Customer & Toko Bangunan**:
  - *Penyebab:* Pada modul [Master Customer](file:///c:/laragon/www/laravel1/resources/views/master/customer/index.blade.php) dan [Master Toko Bangunan](file:///c:/laragon/www/laravel1/resources/views/master/toko_bangunan/index.blade.php), terdapat tombol abu-abu `<button type="submit">Filter</button>` statis. Tombol ini terkesan tidak berfungsi karena dropdown filter di sampingnya sudah memiliki fungsi auto-submit saat dipilih dan input pencarian teks otomatis submit saat menekan tombol Enter. Selain itu, belum ada badge indikator visual filter aktif maupun tombol Reset cepat yang seragam.
  - *Solusi:*
    1. Menghapus tombol submit statis "Filter" yang membingungkan dan redundan.
    2. Mengganti elemen `<select>` native dengan komponen modern `<x-dropdown-kustom :submitOnChange="true">` untuk filter wilayah domisili dan filter customer pemilik.
    3. Menambahkan lencana dinamis `X Filter Aktif` serta tombol `Reset` cepat yang otomatis muncul saat ada filter atau kata kunci aktif.
    4. Menambahkan `min-h-[260px] pb-12` pada pembungkus tabel agar menu popover aksi baris bawah tidak terpotong.
  - *Hasil Verifikasi:* Kompilasi Blade berhasil (`artisan view:cache` lolos), tampilan antarmuka selaras dengan seluruh modul sistem, dan kontrol reset berfungsi instan.

- **[TERSELESAIKAN] Standardisasi Filter Data Tabel Seluruh Modul SPV Keuangan (AR, AP, Akuntansi) & Perbaikan Inline Handler Cetak Memorial**:
  - *Penyebab:*
    1. Filter tabel pada modul-modul SPV Keuangan (`faktur_penjualan`, `list_piutang`, `deposit_customer`, `pengeluaran_kas`, `list_rilisan`, `pembelian_so`, `jurnal_umum`) sebelumnya tidak seragam: sebagian hanya memiliki kolom pencarian kata kunci teks, belum memiliki filter rentang tanggal terstandar, belum ada filter akun COA / pabrik gudang / sumber dana rekening, serta tidak memiliki indikator visual jumlah filter aktif dan tombol reset cepat.
    2. Pada berkas `jurnal_umum.blade.php`, pemanggilan fungsi `cetakVoucherJurnal` melalui string JSON ter-encode di atribut Blade memicu syntax error Alpine.js (`missing ) after argument list`) akibat bentrokan tanda kutip ganda/karakter enter.
  - *Solusi:*
    1. Membuat helper bersama `app/Helpers/FilterKeuanganHelper.php` yang menyediakan opsi periode terstandar (`hari_ini`, `bulan_ini`, `30_hari`, `kustom`), penanganan query tanggal otomatis, dan penghitung filter aktif `hitungFilterAktif()`.
    2. Menyelaraskan seluruh 7 controller dan view Blade SPV Keuangan:
       - Modul 1: Faktur Penjualan AR (`/keuangan/ar/faktur`) -> filter status bayar, periode, badge aktif, reset, min-h-[260px] pb-12.
       - Modul 2: List Piutang Pelanggan AR (`/keuangan/ar/list-piutang`) -> filter status lunas, jatuh tempo (lewat tempo, bulan ini, 30 hari ke depan, kustom), badge aktif, reset, min-h-[260px] pb-12.
       - Modul 3: List Deposit Pelanggan AR (`/keuangan/ar/deposit`) -> filter tipe mutasi, periode, badge aktif, reset, min-h-[260px] pb-12.
       - Modul 4: Pengeluaran Kas AP (`/keuangan/ap/pengeluaran-kas`) -> filter kategori, sumber rekening/tunai, periode, badge aktif, reset, min-h-[260px] pb-12.
       - Modul 5: Rilisan Uang Jalan Supir AP (`/keuangan/ap/list-rilisan`) -> perbaikan query grouping kategori, filter rekening/tunai, periode, badge aktif, reset, min-h-[260px] pb-12.
       - Modul 6: Pembelian SO Pabrik AP (`/keuangan/ap/pembelian-so`) -> filter status SO, gudang/plant semen, periode, badge aktif, reset, min-h-[260px] pb-12.
       - Modul 7: Buku Jurnal Umum Akuntansi (`/keuangan/akuntansi/jurnal-umum`) -> filter posisi debit/kredit, akun COA, periode, badge aktif, reset, min-h-[260px] pb-12.
    3. Mengubah handler cetak memorial pada `jurnal_umum.blade.php` agar menggunakan atribut dataset HTML `data-*` dan `$el.dataset` yang bebas benturan quote JavaScript.
  - *Hasil Verifikasi:* Seluruh modul lulus pengujian live browser (Autonomous Browser Subagent), filter auto-submit berjalan responsif, badge filter aktif dan tombol reset bekerja presisi, serta modal cetak memorial terbuka lengkap dengan data tanpa error JavaScript.

- **[TERSELESAIKAN] Pemotongan Popover Menu Aksi Baris Atas, Teks Aksi Terlalu Panjang, dan Kerusakan Cetak Kartu Aset**:
  - *Penyebab:*
    1. Logika perhitungan `bukaKeAtas` di [menu-aksi-tabel.blade.php](file:///c:/laragon/www/laravel1/resources/views/components/menu-aksi-tabel.blade.php) memicu `bukaKeAtas = true` jika `ruangBawahKontainer < 180`. Pada tabel berbaris sedikit (1-3 baris), baris pertama berada tepat di bawah header tabel (ruang atas hanya ~40px). Saat dipaksa buka ke atas, menu mencuat melewati batas atas kontainer `overflow-x-auto` sehingga terpotong (*clipped* di ceiling).
    2. Teks label tombol cetak terlalu panjang (`Cetak Bukti Kas Keluar (BKK)`, `Cetak Surat Pesanan (PO)`, `Cetak Lembar SO`) sehingga terpotong elipsis di dalam popover.
    3. Fungsi `cetakDokumenAset()` di modul aset perusahaan menyalin innerHTML mentah ke jendela baru dan memuat stylesheet Tailwind CDN eksternal via CDN yang lambat/tidak ter-reset, sehingga tag SVG armada truk mengembang menjadi grafik hitam raksasa tak terkendali dan tombol 'Tutup' ikut tercetak.
  - *Solusi:*
    1. Menstandarkan formula arah popover: popover HANYA boleh buka ke atas jika `ruangAtasKontainer >= 170px` dan ruang bawah sempit (`< 160px`). Jika di baris atas, menu wajib membuka ke bawah.
    2. Menambahkan `min-h-[260px] pb-12` pada seluruh kontainer `overflow-x-auto` tabel transaksi (`list_piutang`, `pengeluaran_kas`, `pembelian_so`, `list_so`, `aset_perusahaan`) dan memperlebar popover menjadi `w-48 min-w-[180px]`.
    3. Menyingkat teks aksi: `Cetak Bukti Kas Keluar (BKK)` -> `Cetak Bukti Kas`, `Cetak Surat Pesanan (PO)` -> `Cetak PO`, `Cetak Lembar SO` -> `Cetak SO`, dan `Cetak Kartu Aset` -> `Cetak Kartu`.
    4. Mengganti generator cetak aset dengan template dokumen cetak mandiri A4 berstandar resmi PBJ (kop surat resmi, tabel data aktiva tetap PSAK 16, tabel spesifikasi fisik armada logistik, tanda tangan berimbang, CSS inline tanpa dependensi CDN, serta menyembunyikan tombol modal).
  - *Hasil Verifikasi:* Lolos pengujian live browser mandiri (100% Passed) di kelima halaman: popover baris 1 membuka ke bawah tanpa terpotong batas tabel, teks label proporsional, dan lembar cetak dokumen berformat rapi.
- **[TERSELESAIKAN] Audit & Penguatan CRUD Master Data Wilayah & Zonasi**:
  - *Penyebab:*
    1. Facade `DB` tidak di-import di `WilayahController.php` sehingga metode `hapusMassal` berpotensi memicu fatal error `Class "App\Http\Controllers\Master\DB" not found`.
    2. Metode `destroy()` dan `hapusMassal()` hanya memeriksa keterikatan `daftarCustomer`, padahal tabel `data_toko_bangunan` memiliki foreign key aktif `fk_toko_wilayah` yang akan memicu integrity constraint violation MySQL jika ada toko cabang yang terhubung ke wilayah tersebut.
    3. Kolom "Jumlah Mitra Toko" sebelumnya hanya menghitung customer dan belum merinci data fisik toko bangunan / proyek cabang.
  - *Solusi:*
    1. Mengimpor facade `use Illuminate\Support\Facades\DB;` pada `WilayahController.php`.
    2. Menambahkan relasi `daftarToko()` pada model `Wilayah.php` (`hasMany(TokoBangunan::class)`) dan memperkuat validasi penghapusan di controller untuk memeriksa relasi customer dan toko fisik secara bersamaan dengan pesan peringatan yang informatif.
    3. Memperbarui tabel view untuk menampilkan rincian keterikatan Toko Cabang dan Customer Pemilik, menambahkan `min-h-[260px] pb-12`, serta menambahkan field `Kode Wilayah` readonly pada modal edit wilayah.
  - *Hasil Verifikasi:* Siklus penuh Create (tambah data otomatis WLY-004), Read (pencarian responsif), Update (ubah nama wilayah), dan Delete (hapus data) teruji dan lulus 100% di browser live.

    1. Terjadi *double HTML-escaping* tanda kutip JSON (`" -> &quot; -> &amp;quot;`) akibat evaluasi bertingkat `{{ json_encode(...) }}` pada Blade dan `{{ $aksiEdit }}` di dalam `<x-menu-aksi-tabel>`. Saat dirender ke peramban, Alpine.js menerima teks bertanda entitas HTML mentah dan melempar `SyntaxError: Unexpected token '&'` sehingga modal edit tidak pernah terpanggil. Selain itu, mutasi langsung properti dari dalam komponen anak `menu-aksi-tabel` tidak tersinkron ke *root scope* penampung modal.
    2. Dropdown menu aksi pada riwayat rilisan kas bon terpotong karena batas bawah kontainer `overflow-x-auto` pada tabel yang berbaris sedikit, serta rumus `bukaKeAtas` di `menu-aksi-tabel` belum memperhitungkan jarak batas kontainer tabel terdekat.
    3. Teks label tombol aksi terlalu panjang untuk ruang popover.
  - *Solusi:*
    1. Mengubah pencetakan ekspresi JS di [menu-aksi-tabel.blade.php](file:///c:/laragon/www/laravel1/resources/views/components/menu-aksi-tabel.blade.php) menjadi raw tag `{!! $aksiEdit !!}`.
    2. Menstandarkan pembukaan modal edit menggunakan arsitektur event dispatch `$dispatch('buka-edit-...', 'KODE')` yang membaca dataset Javascript ter-lookup aman tanpa interpolasi JSON atribut mentah.
    3. Menambahkan logika deteksi batas kontainer bawah `ruangBawahKontainer < 180` pada fungsi `toggleMenu()` di `menu-aksi-tabel` agar popover otomatis membuka ke atas saat mendekati batas kontainer, serta menambahkan `min-h-[260px]` pada kontainer tabel rilisan.
    4. Menyingkat teks label tombol aksi menjadi ringkas dan padat: `Cetak Voucher` dan `Cetak Lembar SO`.
  - *Hasil Verifikasi:* Lulus inspeksi live browser 100% pada kelima halaman Master Data & Akun, modal edit terbuka responsif dan terisi data, serta dropdown rilisan membuka rapi ke atas tanpa terpotong.
- **[TERSELESAIKAN] Tombol aksi popover dan timestamp melayang di atas tabel pada `operasional/gudang/stok.blade.php:278`**:
  - *Penyebab:* Hilangnya tag pembuka `<td class="px-4 py-3.5 text-center whitespace-nowrap">` yang membungkus komponen `<x-menu-aksi-tabel>`. Sesuai spesifikasi HTML parser browser (*foster parenting rule*), elemen non-tabel yang berada langsung di dalam baris `<tr>` tanpa dibungkus `<td>` atau `<th>` otomatis dikeluarkan dan ditempatkan di atas tabel `<table>`, menyebabkan kolom 'AKSI & MUTASI' kosong dan tombol aksi melayang di atas header tabel.
  - *Solusi:* Menambahkan kembali tag pembuka `<td class="px-4 py-3.5 text-center whitespace-nowrap">` sebelum `<x-menu-aksi-tabel>`. Tampilan tabel kembali presisi, kolom 'Aksi & Mutasi' sejajar rapi di setiap baris gudang, dan popover berfungsi normal.
- **[TERSELESAIKAN] Syntax error, unexpected token "\" pada view `operasional/gudang/opname.blade.php:272`**:
  - *Penyebab:* Penggunaan tanda kutip ganda ter-escape `\"` di dalam ekspresi Blade `aksiEdit="{{ $opn->status_konfirmasi === 'draft' ? \"bukaModalEdit('{$opn->id_opname}')\" : null }}"`. Saat compiler Blade mengompilasinya menjadi kode PHP, karakter backslash terbawa dan menyebabkan `ParseError: syntax error, unexpected token "\"`.
  - *Solusi:* Mengubah binding atribut menjadi native Blade binding dengan prefix titik dua `:aksiEdit="$opn->status_konfirmasi === 'draft' ? 'bukaModalEdit(\'' . $opn->id_opname . '\')' : null"`. View berhasil dirender bersih (HTML 247 KB) dan semua aksi modal berfungsi lancar.
- **[TERSELESAIKAN] Pengecekan & Perbaikan Kolom Aksi Modul Gudang (Stok & Opname)**:
  - *Modul:* Fasilitas Gudang (`/operasional/gudang/stok`) & Stok Opname (`/operasional/gudang/opname`).
  - *Status Aksi:*
    1. Salin Kode / Nomor: Normal (Berfungsi via clipboard & indikator feedback).
    2. Modal Detail: Normal (Endpoint JSON mengembalikan data valid, modal interaktif terbuka rapi).
    3. Cetak Dokumen / Kartu Stok / BASO: Normal (Lembar cetak kartu stok & generator dokumen BASO resmi berfungsi).
    4. Edit / Ubah Data: Normal (Memuat data ke modal edit, submit via PUT).
    5. Mutasi Stok Fisik: Normal (Modal mutasi terbuka, submit via POST dan stok fisik terupdate).
    6. Konfirmasi SPV: Normal (Submit PATCH, status berubah ke dikonfirmasi_spv dan stok gudang tersinkron).
    7. Hapus Data: Normal (Modal konfirmasi hapus terbuka, submit DELETE aman dengan proteksi relasi).
  - *Hasil Pengujian:* 19 pengujian backend & integrasi Blade lulus 100%, verifikasi live browser interaksi menu aksi terkonfirmasi berfungsi normal.
  - *Status:* Terselesaikan.
- **[TERSELESAIKAN] Resolusi Konflik Git Merge origin/main ke web-dev1 (Modul Bengkel & UI)**:
  - *Penyebab:* Terjadi bentrok pada 3 berkas view operasional bengkel (`pembelian_sparepart.blade.php`, `perbaikan.blade.php`, `sparepart.blade.php`) akibat perbedaan desain aksi tombol (inline horizontal dari `origin/main` vs popover `<x-menu-aksi-tabel>` dari cabang lokal `web-dev1`).
  - *Solusi:* Sesuai arahan eksplisit pengguna (*"kodingan lokal mengalah mengikuti data dari main"*), konflik diselesaikan dengan memenangkan versi `origin/main` (`git checkout --theirs`). Seluruh template blade berhasil dikompilasi ulang (`artisan view:cache`) dan suite pengujian QA `test_direktur_manager_and_rbac.php` lulus 100% (22 Lulus, 0 Gagal). Commit merge berhasil diselesaikan (`2496f81`).
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
- **[TERSELESAIKAN] Format formulir input aset tegak sempit serta belum memuat atribut armada ERD dan kategori tanah-bangunan terpisah**:
  - *Penyebab:* Modal input awal berformat vertikal (`max-w-xl`) yang sempit dan belum menampung kolom-kolom spesifikasi fisik armada sesuai diagram ERD (`jenis_kendaraan`, `muatan`, `no_rangka`, `tahun_pembuatan`, `tanggal_kir`, `tanggal_pajak`, `nama_pemilik`), serta kategori Tanah dan Bangunan terpisah padahal di lapangan bangunan selalu berdiri di atas tanah perusahaan.
  - *Solusi:*
    1. Mengubah form modal pendaftaran aset menjadi format **Landscape / Melebar (`max-w-5xl`)** dengan pembagian 2 kolom lapang: Sisi Kiri untuk data pokok akuntansi & finansial PSAK 16, Sisi Kanan untuk spesifikasi fisik objek lapangan.
    2. Menyatukan kategori tanah dan gedung menjadi satu kategori efisien: **Tanah & Bangunan Properti (`AST-TNH`)** dengan penambahan kolom `keterangan` (deskripsi bangunan gedung/fasilitas di atas tanah), luas, nomor dokumen legalitas/sertifikat, serta tombol sakelar penyusutan: "Ada Bangunan Gedung (20 Tahun / 5%)" vs "Tanah Kosong Saja (Bebas Penyusutan)".
    3. Melengkapi seluruh atribut armada fisik kendaraan sesuai ERD pada modal tambah, modal edit, dan modal detail, serta menghubungkannya otomatis ke tabel `data_kendaraan`.
    4. Menjalankan pengujian live browser otomatis (snapshot DOM & verifikasi respons 200 OK) tanpa ada kendala.
- **[TERSELESAIKAN] Undefined property: stdClass::$posisi_debit_kredit pada view `keuangan/akuntansi/jurnal_umum.blade.php:115`**:
  - *Penyebab:* Penulisan nama kolom pada view blade menggunakan `$jurnal->posisi_debit_kredit`, sedangkan nama kolom master di tabel database `jurnal_umum` adalah `posisi` (ENUM 'Debit', 'Kredit'). Selain itu nama akun hasil join diakses melalui relasi model yang belum eager loaded.
  - *Solusi:* Memperbaiki pemanggilan menjadi `$jurnal->posisi` dan `$jurnal->nama_akun`, serta menghubungkan method `JurnalUmumController@store` langsung ke `MesinJurnalOtomatis::catatJurnal` agar saldo akun COA otomatis termutasi saat ada entri manual. Halaman telah lulus inspeksi live browser dengan status **SEIMBANG (BALANCED)**.
- **[TERSELESAIKAN] Peringatan browser 'Please enter a valid value' saat input angka nominal Rupiah (HTML5 Step Constraint Violation)**:
  - *Penyebab:* Elemen `<input type="number">` memiliki atribut `min="1"` yang dikombinasikan dengan `step="100000"`. Sesuai standar W3C HTML5, rumus validasi browser menghitung nilai valid sebagai $\text{min} + (k \times \text{step}) = 1 + (k \times 100.000)$, sehingga nilai yang diizinkan hanya angka yang berakhiran 1 (seperti 1, 100001, 3500001). Saat pengguna mengetik angka bulat normal seperti `3500000`, peramban menolak dan memunculkan pop-up kesalahan kelipatan.
  - *Solusi:* Memperbaiki seluruh input nominal keuangan (Faktur Penjualan, Pelunasan Piutang, Pembelian SO, Rilisan Uang Jalan, Beban Kas, Jurnal Umum, Deposit, Master Barang, dan Customer) menggunakan atribut `min="0" step="any"`. Input kini menerima nilai nominal bebas tanpa hambatan validasi peramban.
- **[TERSELESAIKAN] Pembersihan Keseluruhan Warning & Diagnostic Linter IDE (Intelephense, PHP, CSS Tailwind v4)**:
  - *Penyebab:* 
    1. Warning *"Trying to get property of non-object of type void"* pada 8 view Blade (`list_piutang`, `faktur_penjualan`, `deposit_customer`, `pengeluaran_kas`, `pembelian_so`, `list_rilisan`, `kode_akun`, `jurnal_umum`, `master/barang`, `master/wilayah`) karena Intelephense memerlukan anotasi tipe PHPDoc pada variabel iterasi loop.
    2. Warning *"Use of unknown class: DataKendaraan"* pada `KendaraanController.php` karena kurang deklarasi `use App\Models\Operasional\DataKendaraan;`.
    3. Warning *"Call to unknown method: date::format()"* pada `PerbaikanKendaraan.php` accessor `getTanggalMasukFormatAttribute` dan `getTanggalSelesaiFormatAttribute`.
    4. Warning *"Argument 1 passed to number_format() is expected to be float, decimal|null given"* pada `aset_perusahaan.blade.php`.
    5. Warning *"Unknown at rule @source, @theme"* pada `resources/css/app.css` akibat styling Tailwind CSS v4.
  - *Solusi:*
    1. Menambahkan anotasi tipe PHPDoc `@php /** @var ModelClass $var */ @endphp` pada seluruh template Blade terkait.
    2. Menambahkan `use App\Models\Operasional\DataKendaraan;` pada `KendaraanController.php`.
    3. Memperbarui accessor di `PerbaikanKendaraan.php` menggunakan `\Carbon\Carbon::parse(...)->format('d/m/Y')`.
    4. Melakukan type cast eksplisit `(float) ...` pada parameter `number_format()` di `aset_perusahaan.blade.php`.
    5. Membuat file konfigurasi `.vscode/settings.json` dengan `"css.lint.unknownAtRules": "ignore"` untuk menonaktifkan peringatan at-rules Tailwind CSS v4.
    6. Verifikasi sintaks PHP 8.3 CLI: 0 error / 100% lulus bersih.
- **[TERSELESAIKAN] Kolom Tanggal Jatuh Tempo Tidak Muncul Kembali Saat Memilih Metode Kredit Tempo di Faktur Penjualan**:
  - *Penyebab:* Nilai opsi pada dropdown kustom `$opsiMetodeModal` menggunakan `'nilai' => 'Kredit'`, sedangkan state awal dan pengkondisian Alpine.js di Blade memeriksa `x-show="metode === 'Kredit / Piutang'"`. Saat pengguna beralih ke Tunai lalu kembali ke Kredit Tempo, variabel `metode` bernilai `'Kredit'` sehingga kondisi evaluasi tidak terpenuhi dan input jatuh tempo tetap tersembunyi.
  - *Solusi:*
    1. Menyelaraskan nilai pilihan `$opsiMetodeModal` menjadi `'nilai' => 'Kredit / Piutang'` dan `'nilai' => 'Potong Deposit'`.
    2. Memperbarui kondisi Blade menjadi `x-show="metode === 'Kredit / Piutang' || metode === 'Kredit'"` dan penataan grid dinamis (`:class`).
    3. Menambahkan normalisasi dan validasi ganda pada `FakturPenjualanController@store` agar menerima baik string `'Kredit'` maupun `'Kredit / Piutang'`.
- **[TERSELESAIKAN] Integrity Constraint Violation 1451 Saat Menghapus Data Aset (Terkendala Foreign Key Riwayat Penyusutan)**:
  - *Penyebab:* Foreign key `fk_penyusutan_aset` pada tabel `riwayat_penyusutan` memiliki aturan `ON DELETE RESTRICT`. Ketika aset yang pernah memiliki riwayat penyusutan bulanan dihapus, database menolak operasi *delete* karena masih terdapat data anak (*child rows*).
  - *Solusi:*
    1. Membuat migrasi database `2026_09_04_000002_cascade_delete_riwayat_penyusutan.php` untuk mengubah aturan foreign key menjadi `ON DELETE CASCADE`.
    2. Memperbarui method `AsetPerusahaanController@destroy` dengan transaksi atomik `DB::beginTransaction()`: otomatis menghapus riwayat penyusutan, melepas/menghapus relasi unit fisik di `data_kendaraan` secara aman tanpa merusak riwayat surat jalan logistik, lalu menghapus master aset.
    3. Memperbarui master file `database/skema_database.sql` menjadi `ON DELETE CASCADE`.

- **[TERSELESAIKAN] Penambahan Toolbar Paginasi Terpadu di Seluruh Tabel Aplikasi Sesuai Referensi Desain**:
  - *Kebutuhan:* Pengguna meminta seluruh datatable di sistem memiliki toolbar kontrol paginasi terpadu yang memuat:
    1. Info dinamis: *"Menampilkan X sampai Y dari Z data"*.
    2. Pemilih *"Baris per halaman"* dropdown kustom `[5, 10, 25, 50, 100]` (default 10).
    3. Indikator *"Halaman X dari Y"*.
    4. 4 Tombol navigasi: Pertama (`«`), Sebelumnya (`‹`), Berikutnya (`›`), Terakhir (`»`) dengan status disabled dinamis.
  - *Solusi:*
    1. Membuat helper global Alpine.js `tabelPaginasi(opsi)` pada `resources/views/layouts/app.blade.php`.
    2. Membuat komponen Blade modular `<x-paginasi-tabel>` pada `resources/views/components/paginasi-tabel.blade.php`.
    3. Mengintegrasikan reaktivitas baris `x-show="apakahBarisTampil($loop->index)"` dan `<x-paginasi-tabel>` pada 25 tabel di seluruh modul (Master, Keuangan AR/AP/Akuntansi, Operasional Pengiriman/Gudang/Bengkel/Armada/KSO, dan Superadmin).
    4. Verifikasi `php artisan view:cache` lulus 100% tanpa error Blade.

- **[TERSELESAIKAN] Menu Aksi Popover Terbuka Bersamaan / Tumpuk dan Ketiadaan Fitur Pilih Lebih Dari Satu Baris (Multi-Select Checkbox)**:
  - *Penyebab:* 
    1. Komponen `<x-menu-aksi-tabel>` memiliki state lokal `menuTerbuka` yang terisolasi per baris tanpa listener global, sehingga saat tombol titik tiga pada baris lain diklik, menu sebelumnya tidak tertutup dan menumpuk secara visual di layar.
    2. Belum adanya fitur seleksi multi-baris (checkbox header *select-all* dan checkbox baris) beserta *floating action bar* untuk aksi massal.
  - *Solusi:*
    1. Memberikan identitas unik `idUnik: 'menu-' + Math.random().toString(36).substr(2, 9)` pada setiap instance `<x-menu-aksi-tabel>`.
    2. Menanamkan event listener global `@tutup-semua-menu.window="if ($event.detail !== idUnik) menuTerbuka = false"` dan memancarkan event `window.dispatchEvent(new CustomEvent('tutup-semua-menu', { detail: this.idUnik }))` setiap kali suatu menu dibuka, memastikan hanya 1 popover yang terbuka di layar pada satu waktu.
    3. Memperkaya helper global Alpine.js `tabelPaginasi` dengan array `daftarTerpilih: []`, method `apakahTerpilih(id)`, `togglePilih(id)`, `apakahSemuaTerpilih(semuaId)`, `togglePilihSemua(semuaId)`, `kosongkanPilihan()`, `salinTerpilih(pemisah)`, serta `bukaModalHapusMassal()` dan `tutupModalHapusMassal()`.
    4. Membangun komponen modular `<x-bar-aksi-massal>` (`resources/views/components/bar-aksi-massal.blade.php`) berupa *floating bar* dengan indikator badge jumlah terpilih, tombol Salin Terpilih, tombol Hapus Terpilih (bersyarat izin peran), modal konfirmasi hapus massal, dan tombol Batal Pilih.
    5. Mengintegrasikan kolom checkbox `<th>`/`<td>` yang terlindungi izin read-only (`x-show="!apakahReadOnly(modulIzin)"`), highlight baris terpilih `:class="{ 'bg-primary-50/50 dark:bg-primary-950/20': apakahTerpilih(...) }"`, dan `<x-bar-aksi-massal>` pada seluruh tabel utama aplikasi.
    6. Menyederhanakan penulisan copywriting seluruh label aksi menjadi ringkas & padat (`Salin Kode`, `Detail`, `Edit`, `Hapus`, `Cetak`, `Bayar`, `Ubah Status`, `Mutasi Stok`).

- **[TERSELESAIKAN] Inkonsistensi Aksi Baris Tabel, Format Filter, dan Penutupan Menu Popover pada 5 Role (06, 07, 08, 09, 10)**:
  - *Penyebab:*
    1. Beberapa view (seperti `operasional/armada/driver.blade.php`, `operasional/gudang/stok.blade.php`, `operasional/gudang/opname.blade.php`, `operasional/pengiriman/ongkos_angkut.blade.php`, `operasional/kso/index.blade.php`, `operasional/bengkel/pembelian_sparepart.blade.php`) masih menggunakan tombol aksi horizontal terpisah atau tombol aksi langsung tanpa komponen popover standar.
    2. Pada slot tombol kustom `<x-menu-aksi-tabel>` di `surat_jalan.blade.php`, `sparepart.blade.php`, dan `perbaikan.blade.php`, kode penutupan menu memanggil variabel yang tidak ada (`terbuka = false`), sehingga menu popover tidak otomatis tertutup saat tombol diklik.
    3. Pada `ongkos_angkut.blade.php`, pemeriksaan izin RBAC menggunakan kode `ops_ongkos_angkut` yang tidak sesuai dengan matriks hak akses `layouts/app.blade.php` (`kirim_ongkos`).
    4. Halaman Neraca dan Laba Rugi untuk Direktur & Manager belum memiliki filter periode bulanan dan tahunan yang seragam dengan komponen `<x-dropdown-kustom>`.
  - *Solusi:*
    1. Mengganti seluruh tombol aksi baris di seluruh tabel 5 role terkait dengan komponen `<x-menu-aksi-tabel>` yang rapi, padat, dan eksklusif 1 popover aktif.
    2. Menstandarisasi event klik slot tombol menjadi `@click.stop="menuTerbuka = false; fungsiAksi(...)"` untuk menutup popover seketika sebelum modal ditampilkan.
    3. Memperbaiki kode izin modul pada `ongkos_angkut.blade.php` menjadi `kirim_ongkos`.
    4. Menambahkan bilah filter periode bulan dan tahun pada `neraca.blade.php` dan memodernisasi form filter `laba_rugi.blade.php` menggunakan `<x-dropdown-kustom submitOnChange="true">` beserta badge penanda `Akses Eksekutif: Read-Only`.
    5. Seluruh suite pengujian otomatis (`test_crud_pengawas_driver.php`, `test_crud_spv_gudang.php`, `test_crud_spv_operasional.php`, `test_crud_pengawas_kendaraan.php`) lulus 100% tanpa error.

- **[TERSELESAIKAN] Standardisasi dan Penyelarasan Konsistensi Input Plat Nomor Kendaraan (Single Unit, Multi Unit, & Modal Edit)**:
  - *Penyebab Masalah:* Input plat nomor pada pendaftaran aset single unit menggunakan format 3 kolom (`Wilayah`, `Nomor Seri`, `Seri Huruf`) dan badge plat visual live, sedangkan pada pendaftaran multi-unit kartu daftar truk hanya menggunakan 1 kolom text biasa sehingga bentuk penginputannya tidak konsisten.
  - *Solusi Eksekusi:*
    1. Mengubah seluruh kartu armada unit di modal multi-unit agar memiliki header elegan dengan nomor unit, live badge pratinjau `PLAT B ____ ___`, dan input plat nomor 3 kolom terpisah (`Wilayah`, `Nomor Seri`, `Seri Huruf`) dengan layout dan navigasi fokus otomatis yang seragam.
    2. Menghubungkan otomatis pembentukan string lengkap plat nomor ke input hidden untuk dikirim ke backend.
    3. Memperbarui modal edit aset (`aset_perusahaan.blade.php`) serta modal tambah/edit aset di manajemen armada (`kendaraan.blade.php`) menggunakan `<x-input-plat-nomor>`.
    4. Pengujian live browser menunjukkan format input 100% konsisten dan reaktif memperbarui visual badge secara real-time.

- **[TERSELESAIKAN] Standardisasi dan Penyelarasan Tombol Aksi Cetak Dokumen Resmi pada Seluruh Tabel ERP**:
  - *Kebutuhan:* Seluruh tabel operasional dan keuangan yang memiliki dokumen transaksi fisik memerlukan tombol aksi cetak langsung pada menu aksi tabel (`<x-menu-aksi-tabel>`) dan tombol cetak di modal detail, lengkap dengan template berkop surat resmi PT Putra Balkom Jaya dan pengesahan tanda tangan.
  - *Solusi Eksekusi:*
    1. Memperkaya komponen `<x-menu-aksi-tabel>` dengan props native `:aksiCetak`, `:urlCetak`, dan `:labelCetak` lengkap dengan ikon printer SVG profesional.
    2. Menghubungkan tombol cetak pada 13 modul fisik: Faktur Penjualan AR (`cetak_faktur`), List Piutang (faktur invoice), Kwitansi Deposit Customer, Bukti Memorial Jurnal Umum, Kartu Inventaris Aset PSAK 16, Surat Jalan Pengiriman, SPK Perbaikan Bengkel, Bukti Beli Sparepart, Kartu Suku Cadang, Dossier Armada Truk, Biodata Driver, Kartu Stok Gudang, Berita Acara Opname BASO, dan Surat Ketetapan Tarif OA.
    3. Menerapkan mekanisme cetak terisolasi yang ringan via `window.open` dengan Tailwind CSS dan auto-close pasca-cetak.
    4. Menyelaraskan kode modul RBAC (`kirim_sj`, `bengkel_perbaikan`, `bengkel_sparepart`) serta memastikan hak akses Read-Only untuk peran `DIREKTUR_MANAGER`.
    5. Verifikasi kompilasi template Blade `artisan view:cache` lulus 100% (0 error).

---

## ⏭️ Progres Terlewati/Tertunda
*Tidak ada tugas yang tertunda. Seluruh 25 rute sistem distribusi semen, master data relasi 1:N Customer-Toko Bangunan, dan operasional logistik berjalan 100% lancar.*

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
| 7 | Faktur Penjualan (AR) | `/keuangan/ar/faktur-penjualan` | **200 OK** | Dropdown pilihan Toko Bangunan & Produk Semen, autofill harga standar, live kalkulasi subtotal/netto, dan cetak invoice resmi. |
| 8 | Dokumen Cetak Faktur (Invoice) | `/keuangan/ar/faktur-penjualan/{nomor}/cetak` | **200 OK** | Dokumen invoice resmi kop PT PBJ dengan rincian nama semen, kuantitas zak, satuan, harga, info rekening, & 3 tanda tangan. |
| 9 | List Piutang (AR) | `/keuangan/ar/list-piutang` | **200 OK** | Monitoring piutang per toko/pemilik, form cicilan pelunasan, mutasi saldo customer. |
| 10 | Deposit Customer (AR) | `/keuangan/ar/deposit-customer` | **200 OK** | Riwayat mutasi deposit, modal top up saldo deposit toko. |
| 11 | Pembelian SO Pabrik (AP) | `/keuangan/ap/pembelian-so` | **200 OK** | Penerbitan SO semen ke pabrik, kalkulasi volume & harga, alokasi gudang. |
| 12 | Monitoring List SO (AP) | `/keuangan/ap/list-so` | **200 OK** | Monitoring kuota zak semen per nomor SO/LO pabrik SIG vs realisasi pengambilan. |
| 13 | Pengeluaran Kas (AP) | `/keuangan/ap/pengeluaran-kas` | **200 OK** | Catat kas keluar operasional, BBM & Tol armada, pemotongan rekening sumber & akun COA. |
| 14 | Rilisan Uang Jalan (AP) | `/keuangan/ap/list-rilisan` | **200 OK** | Rilisan uang jalan supir armada truk terintegrasi akun COA 1107. |
| 15 | Bagan Akun COA (Akuntansi) | `/keuangan/akuntansi/kode-akun` | **200 OK** | CRUD klasifikasi akun aktiva, kewajiban, modal, pendapatan, beban. |
| 16 | Jurnal Umum (Akuntansi) | `/keuangan/akuntansi/jurnal-umum` | **200 OK** | Entri double-entry berpasangan debit & kredit otomatis, cek keseimbangan saldo. |
| 17 | Aset Perusahaan (Akuntansi) | `/keuangan/akuntansi/aset-perusahaan` | **200 OK** | Inventaris armada truk & aktiva tetap, depresiasi amortisasi bulanan PSAK 16. |

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
