# Component Specifications & Architecture Guide

Dokumen ini berisi spesifikasi teknis UX dan arsitektur komponen UI enterprise.

---

## 1. Data Table Specification

### Structure
- **Header (`<thead>`)**:
  - Background: `#FFFFFF`
  - Font: 12px, font-weight 600, color `#475569`
  - Border bottom: `1px solid #E8ECEF`
  - Behavior: `position: sticky; top: 0; z-index: 10`
- **Body (`<tbody>`)**:
  - Row height: `44px`–`48px` (compact density)
  - Border bottom: `1px solid #F0F2F5`
  - Hover state: `background-color: #F1F5F9`
  - Selected state: `background-color: #EFF6FF`
- **Cell Alignment**:
  - Teks / Nama / SKU: Rata Kiri (`text-left`)
  - Status Badge / Aksi: Center (`text-center`)
  - Harga Beli / Harga Jual / Omset / Stok: **Rata Kanan (`text-right` + `tabular-nums`)**

### Floating Action Bar
- Ditampilkan otomatis jika `selectedCount > 0`
- Position: `fixed; bottom: 24px; left: 50%; transform: translateX(-50%)`
- Tampilan: Floating pill dengan background putih, shadow large, berisi jumlah terpilih & tombol batch action (Export, Hapus, Tagih).

---

## 2. Modal & Drawer Form Specification

### Spacing & Bounds
- Max width: `640px`–`680px`
- Max height: `90vh` dengan `overflow-y: auto`
- Padding: `20px 24px`
- Modal Overlay: `background: rgba(17, 24, 39, 0.4)` dengan `backdrop-filter: blur(4px)`

### Form Controls
- Label: 12px, font-weight 600, color `#0F172A`, gap 4px ke input.
- Input / Select:
  - Height: `36px`–`38px`
  - Border: `1px solid #E8ECEF`, radius `8px`
  - Focus state: `border-color: #FF6B00`, `box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1)`
- Keyboard Accessibility:
  - Event listener `keydown` untuk tombol `Escape` memicu `onClose()`.
  - Auto-focus pada elemen input pertama.

---

## 3. KPI Summary Cards Specification

### Density & Layout
- Grid: 3 atau 4 kolom (`repeat(4, 1fr)`) dengan gap `16px`.
- Card Height: `100px`–`110px`.
- Card Padding: `16px 20px`.
- Border radius: `12px`.

### Content Elements
- **Title (Atas)**: 12px, font-weight 600, color `#475569`.
- **Value (Tengah)**: 22px–24px, font-weight 700, font-variant `tabular-nums`.
  - Color Rules: Hijau `#059669` (revenue/profit), Merah `#DC2626` (expense), Dark `#0F172A` (balance).
- **Footer (Bawah)**: 11px, color `#64748B`. Wajib mencantumkan konteks bermanfaat (mis. "Periode Agt 2026", "Target: Rp 700jt", "↑ 14.2% YoY").
