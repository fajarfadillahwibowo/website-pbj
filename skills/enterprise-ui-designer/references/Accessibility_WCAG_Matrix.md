# Accessibility & Color Matrix Guide (WCAG 2.1 AA)

Dokumen ini mendefinisikan matriks kontras warna dan aturan aksesibilitas untuk aplikasi enterprise.

---

## 1. Color Contrast Matrix (Standard WCAG AA)

| Penggunaan | Hex Code | Background | Ratio | Status |
|---|---|---|---|---|
| Primary Text (Judul & Nominal) | `#0F172A` / `#111827` | `#FFFFFF` | 15.6:1 | Lulus AA/AAA |
| Body Text / Label | `#374151` | `#FFFFFF` | 9.2:1 | Lulus AA/AAA |
| Secondary Text (Subtitle & TH) | `#475569` | `#FFFFFF` | 7.1:1 | Lulus AA/AAA |
| Footer Meta / Helper Text | `#64748B` | `#FFFFFF` | 5.0:1 | Lulus AA |
| Muted Icon / Border | `#94A3B8` / `#CBD5E1` | `#FFFFFF` | 2.9:1 | Hanya untuk grafik/border |
| Success Badge Text | `#059669` | `#ECFDF5` | 4.8:1 | Lulus AA |
| Error Badge Text | `#DC2626` | `#FEF2F2` | 4.6:1 | Lulus AA |
| Warning Badge Text | `#D97706` | `#FFFBEB` | 4.7:1 | Lulus AA |

> **Dilarang**: Menggunakan warna `#9CA3AF` atau `#D1D5DB` untuk teks informasi karena kontrasnya < 3:1 (gagal WCAG).

---

## 2. Monospaced Tabular Numbers Rule

Dalam aplikasi ERP & Akuntansi, keterbacaan data finansial sangat krusial.

### CSS Implementation:
```css
.text-right {
  text-align: right !important;
  font-variant-numeric: tabular-nums;
}
```

### Manfaat Tabular Nums:
- Setiap digit angka (0-9) memiliki lebar karakter (character width) yang persis sama.
- Angka `1.000.000` dan `8.888.888` akan sejajar lurus secara vertikal dalam satu kolom tabel.
- Mencegah kesalahan baca bagi staf keuangan saat scanning cepat ribuan baris transaksi.

---

## 3. Keyboard Navigation Checklist

1. **Tab Traversal**: Seluruh form input, select, dan button dapat dijangkau menggunakan tombol `Tab` dengan urutan yang logis.
2. **Modal Escape Hotkey**: Menekan `Esc` wajib menutup modal/drawer yang sedang terbuka.
3. **Global Search Hotkey**: Menekan `Ctrl + K` (atau `Cmd + K`) memicu modal pencarian global.
4. **Focus Rings**: Wajib ada visual focus ring (`outline` atau `box-shadow`) saat elemen di-focus menggunakan keyboard.
