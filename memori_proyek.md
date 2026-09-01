# Memori Proyek: Pembuatan Template Desain (Design.md, Login, & Dashboard)

## Status Terkini
1. **Design System**: Dibuat file baku `design.md` yang menetapkan standar token warna (Dual Mode: Light & Dark), tipografi finansial tabular, komponen tombol, kartu, tabel, dan prinsip *Anti AI-Slop / Zero Unicode Emoji*.
2. **Template Login**: Dibuat file `resources/views/auth/login.blade.php` dengan tata letak *Split Screen Pro* (kiri: visual & proposisi nilai distribusi semen; kanan: form login kredensial dengan toggle sandi & sakelar tema).
3. **Template Master Layout**: Dibuat file `resources/views/layouts/app.blade.php` dengan *Collapsible Sidebar*, deteksi menu dinamis per jabatan, dan toggle tema.
4. **Template Dashboard Dinamis**: Dibuat file `resources/views/dashboard.blade.php` yang mendukung pergantian simulasi 9 jabatan + Super Admin dengan kartu KPI dan tabel transaksi yang menyesuaikan diri 100% secara real-time.
5. **Rute Web**: Rute `/`, `/login`, dan `/dashboard` telah dihubungkan di `routes/web.php`.
