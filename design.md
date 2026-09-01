# Design System Guidelines — Sistem Akuntansi & Distribusi Semen
**Versi 2.0 · Enterprise Compact Density · WCAG AA Compliant · Dual Mode**

> Dokumen ini adalah **satu-satunya sumber kebenaran (single source of truth)** standar visual dan komponen antarmuka proyek. Setiap baris kode UI/UX wajib mengacu ke sini.

---

## 1. Prinsip Utama Desain

| # | Prinsip | Implementasi Wajib |
|---|---|---|
| 1 | **Least-Privilege UI** | Setiap pengguna hanya melihat menu & data sesuai jabatan — tidak ada modul asing terlihat |
| 2 | **Data Legibility First** | Angka nominal wajib `font-mono tabular-nums`, kontras minimum WCAG AA (4.5:1) |
| 3 | **Enterprise Compact Density** | Radius card ≤ 12px, padding container 16–24px, tidak ada dead space > 50% viewport |
| 4 | **Zero AI-Slop** | Dilarang: emoji teks Unicode, placeholder "Acme Corp", tombol yang trigger `alert()` |
| 5 | **SVG Icon Only** | Wajib pakai Heroicons / Lucide SVG inline — dilarang icon font atau emoji |
| 6 | **Dual Mode Native** | Light & Dark mode via Alpine.js + Tailwind `dark:` class, toggle persistent ke `localStorage` |
| 7 | **Copywriting Pro** | Label, judul, CTA: singkat, padat, lugas — dilarang paragraf penjelas bertele-tele di UI |

---

## 2. Token Warna & Semantik Status

### 2.1 Light Mode
| Token | Nilai | Kegunaan |
|---|---|---|
| `bg-base` | `#F4F6F9` | Latar utama halaman |
| `bg-surface` | `#FFFFFF` | Kartu, modal, dropdown |
| `bg-surface-raised` | `#F8FAFC` | Sidebar, header |
| `border-base` | `#E2E8F0` (`slate-200`) | Garis batas kartu, tabel |
| `border-subtle` | `#EEF0F4` | Pemisah dalam form |
| `text-primary` | `#0F172A` (`slate-900`) | Judul, nilai KPI utama |
| `text-secondary` | `#475569` (`slate-600`) | Label, keterangan (WCAG 7:1) |
| `text-muted` | `#64748B` (`slate-500`) | Footer meta text (WCAG 5:1) |

> ⚠️ **DILARANG** menggunakan `#9CA3AF` (`gray-400`) sebagai teks di background putih — kontras hanya 2.8:1, gagal WCAG AA.

### 2.2 Dark Mode
| Token | Nilai | Kegunaan |
|---|---|---|
| `bg-base-dark` | `#0C0E14` | Latar utama halaman |
| `bg-surface-dark` | `#14161F` | Kartu, modal |
| `bg-surface-raised-dark` | `#1C1E2A` | Sidebar, header |
| `bg-elevated-dark` | `#252837` | Hover state, dropdown |
| `border-base-dark` | `#252837` | Garis batas kartu |
| `text-primary-dark` | `#F1F5F9` (`slate-100`) | Teks utama |
| `text-secondary-dark` | `#94A3B8` (`slate-400`) | Label |
| `text-muted-dark` | `#64748B` (`slate-500`) | Meta text |

### 2.3 Warna Semantik Bisnis (Akuntansi)
| Kondisi | Light Bg | Light Text | Dark Bg | Dark Text | Penggunaan |
|---|---|---|---|---|---|
| **Positif / Lunas / Profit** | `#F0FDF4` | `#059669` | `rgba(5,150,105,0.15)` | `#34D399` | Pendapatan, penjualan lunas |
| **Peringatan / Piutang / Draft** | `#FFFBEB` | `#D97706` | `rgba(217,119,6,0.15)` | `#FCD34D` | Tagihan belum lunas, draft SO |
| **Bahaya / Jatuh Tempo / Rugi** | `#FFF1F2` | `#DC2626` | `rgba(220,38,38,0.15)` | `#F87171` | Piutang macet, pengeluaran besar |
| **Info / Proses / Dalam Perjalanan** | `#EFF6FF` | `#2563EB` | `rgba(37,99,235,0.15)` | `#60A5FA` | SO diproses, armada berangkat |
| **Netral / Saldo Kas** | `#F8FAFC` | `#334155` | `rgba(51,65,85,0.3)` | `#CBD5E1` | Saldo bank, data statis |

---

## 3. Tipografi

- **Font Utama**: `Inter` / `Plus Jakarta Sans`
- **Font Angka**: `JetBrains Mono` — wajib di semua nominal, kode transaksi, stok

| Level | Kelas Tailwind | Kegunaan |
|---|---|---|
| Judul Halaman | `text-xl font-bold` | H1 tiap halaman |
| Label Seksi | `text-xs font-semibold uppercase tracking-wider text-slate-500` | Grup menu, header tabel |
| Nilai KPI | `text-2xl font-bold font-mono tabular-nums` | Angka kartu metrik |
| Body | `text-sm text-slate-700 dark:text-slate-300` | Isi tabel & form |
| Meta / Keterangan | `text-xs text-slate-500` | Tanggal, sublabel |
| Badge | `text-xs font-semibold` | Status transaksi |

---

## 4. Spesifikasi Radius & Spacing (Enterprise Compact)

| Elemen | Border Radius | Padding |
|---|---|---|
| Kartu / Widget | `rounded-xl` (12px) — **MAKS** | `p-4` atau `p-5` (16–20px) |
| Tombol | `rounded-lg` (8px) — **MAKS** | `px-3 py-1.5` atau `px-4 py-2` |
| Badge / Tag | `rounded` (6px) — **MAKS** | `px-2 py-0.5` |
| Input Form | `rounded-lg` (8px) | `px-3 py-2` |
| Modal / Drawer | `rounded-xl` (12px) | Padding dalam: 20–24px |
| Sidebar | `rounded-none` untuk container; `rounded-lg` untuk item aktif | `px-3 py-2` per item |

---

## 5. Standar Komponen

### 5.1 Tombol
```html
<!-- PRIMARY -->
<button class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium
               text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98]
               rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-blue-500/40">
  <svg class="w-4 h-4">...</svg>
  Simpan Data
</button>

<!-- SECONDARY / OUTLINED -->
<button class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium
               text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800
               border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700
               rounded-lg transition-all">
  Batal
</button>

<!-- DANGER -->
<button class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium
               text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all">
  Hapus
</button>
```

### 5.2 Kartu KPI Metrik
```html
<div class="bg-white dark:bg-[#14161F] border border-slate-200 dark:border-[#252837] rounded-xl p-5">
  <div class="flex items-center justify-between mb-3">
    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penjualan</span>
    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
      <svg class="w-4 h-4 text-blue-600 dark:text-blue-400">...</svg>
    </div>
  </div>
  <div class="text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">
    Rp 842.500.000
  </div>
  <div class="mt-1.5 flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
    <svg class="w-3.5 h-3.5">...</svg>
    +12.4% vs bulan lalu
  </div>
</div>
```

### 5.3 Badge Status
```html
<!-- Lunas / Sukses -->
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
             bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
  Lunas
</span>
<!-- Piutang / Warning -->
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
             bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">
  Piutang
</span>
<!-- Jatuh Tempo / Danger -->
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
             bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400">
  Jatuh Tempo
</span>
```

### 5.4 Tabel Keuangan (Enterprise Financial Table)
```html
<table class="w-full text-sm">
  <!-- HEADER: sticky, tidak scroll hilang -->
  <thead class="sticky top-0 z-10 bg-slate-50 dark:bg-[#1C1E2A] border-b border-slate-200 dark:border-[#252837]">
    <tr>
      <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
        No. Faktur
      </th>
      <!-- Angka: WAJIB text-right -->
      <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
        Total Netto
      </th>
    </tr>
  </thead>
  <tbody class="divide-y divide-slate-100 dark:divide-[#252837]">
    <tr class="hover:bg-slate-50/70 dark:hover:bg-[#252837]/60 transition-colors">
      <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 text-xs">INV-20260901-001</td>
      <!-- Angka nominal: text-right + font-mono + tabular-nums -->
      <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">
        Rp 42.500.000
      </td>
    </tr>
    <!-- EMPTY STATE -->
    <tr class="empty-row" x-show="dataTabel.length === 0">
      <td colspan="99" class="px-4 py-10 text-center text-sm text-slate-400">
        Belum ada data transaksi.
      </td>
    </tr>
  </tbody>
</table>
```

### 5.5 Sidebar Item
```html
<!-- Aktif -->
<a class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold
          bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400
          border-r-2 border-blue-600 dark:border-blue-500">
  <svg class="w-4 h-4 shrink-0">...</svg>
  Faktur Penjualan
</a>
<!-- Tidak Aktif -->
<a class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium
          text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837]
          hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
  <svg class="w-4 h-4 shrink-0">...</svg>
  List Piutang
</a>
```

---

## 6. Arsitektur Layout

### 6.1 Login — Split Screen Pro
- **Kiri (38%)**: Panel gelap `bg-[#0C0E14]`, logo, tagline, 4 kartu statistik sistem
- **Kanan (62%)**: Background `#F4F6F9` / `white dark`, form login dengan input ringkas enterprise
- Breakpoint: di bawah `md:` tampil satu kolom (form di atas, branding disembunyikan)

### 6.2 Dashboard — Enterprise Left Sidebar
```
┌─────────────────────────────────────────────────────────┐
│  [Logo + Nama Sistem]  Sidebar 240px / 56px (collapsed) │
│  ─────────────────────────────────────────────────────  │
│  [Breadcrumb / Judul]     [Pencarian Cepat]  [Toggle]   │  ← Topbar h-14
│  ─────────────────────────────────────────────────────  │
│  [KPI Grid 2–4 kartu]                                   │
│  ─────────────────────────────────────────────────────  │
│  [Tabel Transaksi Utama — sticky header]                │
│  [Feed Aktivitas Terbaru — Right Panel opsional]        │
└─────────────────────────────────────────────────────────┘
```
- Sidebar lebar: `w-60` (240px), collapsed: `w-14` (56px)
- Topbar: `h-14` (56px), `border-b`, `bg-white dark:bg-[#14161F]`
- Konten: `p-5` atau `p-6`, max-width tidak dibatasi (full fluid)

---

## 7. Standar Format Angka Keuangan
| Tipe | Format | Contoh |
|---|---|---|
| Nilai Rupiah | `Rp X.XXX.XXX` | `Rp 1.085.330.000` |
| Persentase | `XX.X%` | `12.4%` |
| Stok Barang | `X.XXX Zak` / `XX Ton` | `14.250 Zak` |
| Kode Transaksi | `font-mono text-xs` | `INV-20260901-001` |
| Tanggal | `DD Mon YYYY` | `01 Sep 2026` |

---

## 8. Penamaan Kode (Bahasa Indonesia)
| Tipe | Contoh |
|---|---|
| State Alpine.js | `modeGelap`, `sidebarTerlipat`, `jabatanAktif`, `tampilkanSandi` |
| ID / Class HTML | `#kartu-kpi-penjualan`, `.baris-tabel-piutang`, `#tombol-simpan` |
| Label UI | *Penjualan*, *Pengeluaran*, *List Piutang*, *Surat Jalan*, *Opname Gudang* |
| PHP Variable | `$jumlahPiutang`, `$statusPengiriman`, `$kodeSO` |

---

## 9. Checklist Audit UI (20 Poin Wajib Sebelum Deploy)
- [ ] Border radius card ≤ 12px, tombol ≤ 8px, badge ≤ 6px
- [ ] Padding modal 20–24px, gap antar input 4–6px
- [ ] Teks sekunder minimal `#475569` (WCAG 4.5:1+) — tidak pakai `#9CA3AF` di bg putih
- [ ] Semua kolom angka nominal rata kanan (`text-right`)
- [ ] Font angka `font-mono tabular-nums` aktif di seluruh tabel & KPI
- [ ] Format Rupiah menggunakan pemisah titik: `Rp X.XXX.XXX`
- [ ] Header tabel sticky (`sticky top-0 z-10`)
- [ ] Empty state row tersedia untuk setiap tabel
- [ ] Pagination fungsional, bukan angka hardcoded
- [ ] Sidebar: item aktif secara visual jelas berbeda dari item biasa
- [ ] Tidak ada duplikasi label jabatan di header & sidebar bersamaan
- [ ] Placeholder search ringkas dan relevan
- [ ] Modal menutup saat tekan `Escape`
- [ ] Auto-focus ke field pertama saat modal terbuka
- [ ] Tidak ada `alert()` native — gunakan toast atau inline error
- [ ] Tidak ada nama placeholder fiktif generik (`Acme Corp`, `Lorem Ipsum`)
- [ ] Zero dead space > 50% viewport di halaman utama dashboard
- [ ] Warna KPI sesuai makna semantik bisnis (hijau=positif, merah=bahaya, kuning=peringatan)
- [ ] Tidak ada emoji Unicode teks di label, judul, tombol, kartu, atau konten tabel
- [ ] Dark mode: semua elemen lolos kontras WCAG AA minimal 4.5:1
