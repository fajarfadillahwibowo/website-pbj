---
name: penamaan-bahasa-indonesia
description: Mengarahkan Antigravity untuk selalu menggunakan Bahasa Indonesia dalam penamaan variabel, fungsi, atribut, ID, class, tabel database, kolom, komentar, dan elemen yang dapat dikustomisasi dalam penulisan kode.
---

# Standar Penamaan Kode Bahasa Indonesia (Indonesian Naming Skill)

Skill ini mewajibkan Antigravity untuk menggunakan Bahasa Indonesia dalam semua penamaan yang dapat dikustomisasi (*customizable identifiers*) saat menulis kode, membuat file, merancang basis data, atau menulis dokumentasi.

## Penjelasan & Cakupan Penamaan:

1. **Variabel, Konstanta, dan Fungsi / Method**:
   - Gunakan nama dalam Bahasa Indonesia sesuai konvensi penamaan standar (*camelCase*, *snake_case*, *PascalCase*).
   - *Contoh JS/TS:* `const jumlahTotal`, `function hitungTotalBelanja()`, `let namaPengguna`.
   - *Contoh PHP/Python:* `$jumlah_total`, `def hitung_total_belanja()`, `$nama_pengguna`.

2. **Elemen UI / HTML / CSS (ID, Class, Tag, Label, Props)**:
   - ID elemen: `id="tombol-simpan"`, `id="tabel-transaksi"`.
   - Class CSS: `.kartu-pengguna`, `.teks-utama`, `.wadah-konten`.
   - Label & Teks UI: `<button>Simpan Data</button>`, `<label>Nama Lengkap</label>`.

3. **Database (Tabel & Kolom)**:
   - Nama Tabel: `pengguna`, `transaksi`, `barang`, `laporan_keuangan`.
   - Nama Kolom: `id_pengguna`, `nama_lengkap`, `harga_satuan`, `tanggal_transaksi`, `dibuat_pada`.

4. **Class, Interface, Struct, & State Component**:
   - Class/Model: `PenggunaModel`, `LaporanKeuanganController`, `DaftarBarangComponent`.
   - State React/Vue: `const [daftarBarang, setDaftarBarang] = useState([])`.

5. **Komentar & Dokumen Kode**:
   - Semua komentar penjelasan kode dan docstring wajib ditulis menggunakan Bahasa Indonesia yang jelas dan profesional.

---

## Pengecualian (Keyword & Pustaka bawaan):
- **Sintaks Bawaan Bahasa/Framework (Syntax Keywords)** tetap menggunakan standar asli (seperti `if`, `else`, `return`, `class`, `function`, `public`, `private`, `import`, `export`, `useState`, `useEffect`, dll).
