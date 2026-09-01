---
name: enterprise-ui-designer
description: >-
  Use this skill whenever designing, building, auditing, or refactoring UI components, dashboards, tables, forms, or web layouts.
  Enforces compact enterprise aesthetic, WCAG accessibility, financial data alignment, multi-agent parallel execution protocol, real-time browser inspection protocol (via Chrome DevTools MCP), anti-AI-slop rules, and integrates shadcn, mui, context7, and chrome-devtools MCP tools.
---

# Mega Enterprise UI/UX Designer & Multi-Agent Skill

Panduan standar ini memandu AI Agent dalam merancang, merapikan, dan mengimplementasikan antarmuka (UI/UX) berkelas dunia untuk aplikasi web Enterprise / ERP, menetapkan **Protokol Eksekusi Paralel Multi-Agent**, serta **Protokol Inspeksi Browser Real-Time**.

---

## 1. Multi-Agent Parallel Execution Protocol (Wajib)

Setiap kali menerima tugas pembuatan atau modifikasi antarmuka yang melibatkan lebih dari **2 file** atau **2 domain teknis** (misal: CSS + React, atau Frontend + Database), AI **WAJIB** secara mandiri membelah jalur eksekusi dengan spawn subagent paralel (`invoke_subagent`):

### Skema Pembelahan Agent:
1. **Subagent Design System & CSS (`self`)**:
   * Bertanggung jawab atas `index.css`, design tokens, variabel warna, CSS Grid/Flex layout, dan responsivitas.
2. **Subagent Component Builder (`self`)**:
   * Bertanggung jawab atas pembuatan/editing komponen React TSX (`Header.tsx`, `Sidebar.tsx`, `Modal.tsx`, `Table.tsx`).
3. **Subagent Backend & Data Integration (`self`)**:
   * Bertanggung jawab atas skema data, API calls, integrasi PostgreSQL / Laravel Artisan, dan state context (`AccountingContext.tsx`).
4. **Subagent QA & Visual Verifier (`self`)**:
   * Bertanggung jawab atas verifikasi build (`npm run build`), type check (`tsc`), dan visual audit via `chrome-devtools-mcp`.

---

## 2. Real-Time Browser Inspection Protocol (`chrome-devtools-mcp`)

AI tidak perlu bergantung hanya pada perintah manual user atau screenshot lambat untuk "melihat". AI **WAJIB** memanfaatkan perkakas Chrome DevTools MCP secara real-time:

### 1. Instant DOM & Text Verification (`take_snapshot`)
* Gunakan `take_snapshot` untuk mengambil pohon DOM & elemen interaktif secara instan (ringan & cepat tanpa overhead rendering gambar).

### 2. Live Runtime Inspection (`evaluate_script`)
* Jalankan `evaluate_script` untuk mengecek state komponen React, posisi scroll, nilai form, atau layout rect secara real-time di dalam browser:
  ```javascript
  () => ({
    title: document.title,
    activeElement: document.activeElement?.tagName,
    tableRows: document.querySelectorAll('.data-table tbody tr').length,
    errors: window.__lastErrors || []
  })
  ```

### 3. Live Console & Network Error Monitoring
* Setelah navigasi atau klik tombol, jalankan `list_console_messages` untuk mendeteksi unhandled JavaScript errors/warnings secara real-time.
* Gunakan `list_network_requests` untuk memastikan API HTTP call mengembalikan status `200 OK` (bukan `404` atau `500`).

### 4. Automated Visual Screenshot Verification (`take_screenshot`)
* Ambil `take_screenshot` (viewport / fullPage) secara otomatis di akhir perubahan UI untuk memastikan tidak ada layout shift, teks terpotong, atau dead space.

---

## 3. Visual Hierarchy & Spacing Tokens (Enterprise Compact Density)

* **Card Border Radius**: Maksimal `12px` (`--radius-card: 12px`). *Dilarang menggunakan radius > 16px.*
* **Button Border Radius**: Maksimal `8px` (`--radius-btn: 8px`).
* **Badge Border Radius**: Maksimal `6px` (`--radius-badge: 6px`).
* **Padding Kontainer**: 16px–24px. *Dilarang padding boros vertikal (> 32px).*
* **Gap Antar Input**: 4px–6px antara label dan input.

---

## 4. Standar Tabel Keuangan & Data ERP

1. **Alignment Data Uang & Angka**:
   * Seluruh nilai nominal uang, stok, dan persentase **wajib rata kanan** (`text-right`).
   * Gunakan `font-variant-numeric: tabular-nums` atau `font-family: monospace` pada angka.
2. **Format Uang**:
   * Gunakan pemisah ribuan titik: `Rp 1.085.330.000`.
3. **Table Header**:
   * Header tabel wajib sticky saat scroll (`position: sticky; top: 0; z-index: 10;`).
4. **Empty State Pattern**:
   * Wajib sertakan `empty-row` saat data kosong.

Lihat template kode lengkap di: [EnterpriseDataTable.tsx](./examples/EnterpriseDataTable.tsx)

---

## 5. Accessibility & Kontras Warna (WCAG AA 4.5:1+)

* **Primary Text**: `#0F172A` / `#111827` (kontras 14+:1).
* **Secondary / Subtitle**: `#475569` (kontras 7+:1). *Dilarang menggunakan `#9CA3AF` pada background putih.*
* **Footer / Meta Text**: `#64748B` (kontras 5+:1).
* **Semantik Warna**:
  * **Hijau** (`#059669`): Positif, Revenue, Profit, Approved, In Stock.
  * **Merah** (`#DC2626`): Negatif, Expense, Loss, Overdue, Out of Stock.
  * **Kuning/Orange** (`#D97706`): Peringatan, Restock Kritis.
  * **Slate/Dark** (`#0F172A`): Netral, Saldo Kas/Bank, SKU ID.

Lihat matriks kontras lengkap di: [Accessibility_WCAG_Matrix.md](./references/Accessibility_WCAG_Matrix.md)

---

## 6. Workflow Integrasi MCP Server

1. **`shadcn` & `shadcn-space`**: Cari komponen & UI layout blocks dari registry resmi sebelum membangun dari nol.
2. **`context7`**: Tarik dokumentasi resmi terbaru (React, Tailwind, Next.js) untuk memastikan tidak ada sintaks API yang usang.
3. **`chrome-devtools-mcp`**: Manfaatkan `take_snapshot`, `evaluate_script`, `list_console_messages`, `list_network_requests`, dan `take_screenshot` untuk inspeksi real-time.

Lihat spesifikasi teknis komponen di: [Component_Specifications.md](./references/Component_Specifications.md)

---

## 7. Aturan "Anti-AI-Slop"

* **Dilarang** menggunakan teks placeholder generik (`Acme Corp`, `Uxeflow`, `Lorem Ipsum`).
* **Wajib** menggunakan identitas & data bisnis lokal nyata (`PT Nusantara Solusi Industri`, produk riil).
* **Dilarang** membiarkan dashboard memiliki >50% dead space. Tampilkan widget aktivitas terbaru & ringkasan operasional.
* **Dilarang** menyajikan tombol/link dummy yang memicu `alert()`. Gunakan Toast / Modal Drawer.

---

## 8. Checklist Audit UI (20 Poin Mandatory)

- [ ] 1. Border radius card max 12px, btn max 8px
- [ ] 2. Padding modal max 20-24px, form gap 4-6px
- [ ] 3. Teks sekunder minimal `#475569` (WCAG 4.5:1+)
- [ ] 4. Tabel nominal rata kanan (`text-right`)
- [ ] 5. Numbers monospace / `tabular-nums`
- [ ] 6. Format Rupiah `Rp X.XXX.XXX`
- [ ] 7. Sticky table header (`position: sticky; top: 0`)
- [ ] 8. Empty state row tersedia
- [ ] 9. Pagination fungsional (bukan hardcoded)
- [ ] 10. Sidebar active state jelas terbedakan
- [ ] 11. Tidak ada duplikasi role di header & sidebar
- [ ] 12. Placeholder search ringkas
- [ ] 13. Modal mendukung keyboard `Escape` hotkey
- [ ] 14. Auto-focus pada field pertama saat modal dibuka
- [ ] 15. Tidak ada `alert()` native browser
- [ ] 16. Tidak ada nama placeholder AI (`Acme Corp`, dll)
- [ ] 17. Zero dead space di dashboard
- [ ] 18. Warna KPI sesuai makna bisnis semantik
- [ ] 19. Type check `tsc` 0 error
- [ ] 20. Real-time browser inspection (`take_snapshot` / `evaluate_script` / `list_console_messages`) & visual verification (`take_screenshot`) lulus 100%
