# Catatan Bug, Error, & Progres Terlewati

## Daftar Bug & Error
- **Error Tabel `sessions` Not Found (Terselesaikan)**:
  - *Deskripsi*: `Table 'laravel1.sessions' doesn't exist` saat membuka URL `http://127.0.0.1:8000`.
  - *Akar Masalah*: Konfigurasi `.env` sebelumnya menggunakan driver database sebelum tabel session bawaan Laravel dibuat oleh migration.
  - *Status & Solusi*:
    1. Driver sesi di `.env` diubah ke `SESSION_DRIVER=file` (lebih cepat dan stabil untuk lokal).
    2. Perintah `php artisan migrate --force` telah dijalankan untuk memastikan tabel `sessions`, `cache`, dan `jobs` terbuat secara resmi di database `laravel1`.
    3. Cache Laravel dibersihkan (`php artisan optimize:clear`).
    4. Pengujian HTTP GET menghasilkan status `200 OK` dan halaman login render dengan sempurna.

- **Error GitHub 403 (Terselesaikan)**:
  - *Deskripsi*: `Permission to fajarfadillahwibowo/website-pbj.git denied (403)`.
  - *Akar Masalah*: Personal Access Token belum memiliki hak akses *Write/Contents*.
  - *Status*: Berhasil diperbaiki setelah izin token diperbarui dan push sukses 100%.

## Status Verifikasi & Solusi
- Halaman login pada `http://127.0.0.1:8000` atau `http://localhost/laravel1/public` sudah aktif, normal, dan terhubung ke database `laravel1`.
