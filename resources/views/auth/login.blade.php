<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        tampilkanSandi: false,
        inputUsername: '{{ old('nama_pengguna') }}',
        inputPassword: '',
        roleTerpilih: '',
        pesanNotif: '',
        sedangMasuk: false,
        get infoRoleTerpilih() {
          const usr = (this.inputUsername || '').toLowerCase().trim();
          const peta = {
            'spv_operasional': {
              role: 'SPV Operasional',
              kode: 'SPV_OPERASIONAL',
              portal: 'Pusat Komando Operasional, Logistik & KSO',
              badgeClass: 'bg-blue-50 dark:bg-blue-500/10 border-blue-200/70 dark:border-blue-500/20 text-blue-700 dark:text-blue-400'
            },
            'spv_keuangan': {
              role: 'SPV Keuangan',
              kode: 'SPV_KEUANGAN',
              portal: 'Ruang Kendali Finansial & Akuntansi Terpadu',
              badgeClass: 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200/70 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400'
            },
            'staff_ar': {
              role: 'Staff AR',
              kode: 'STAFF_AR',
              portal: 'Portal Penjualan & Piutang (Account Receivable)',
              badgeClass: 'bg-sky-50 dark:bg-sky-500/10 border-sky-200/70 dark:border-sky-500/20 text-sky-700 dark:text-sky-400'
            },
            'staff_ap': {
              role: 'Staff AP',
              kode: 'STAFF_AP',
              portal: 'Portal Pembelian & Pengeluaran (Account Payable)',
              badgeClass: 'bg-amber-50 dark:bg-amber-500/10 border-amber-200/70 dark:border-amber-500/20 text-amber-700 dark:text-amber-400'
            },
            'dispatcher': {
              role: 'Dispatcher',
              kode: 'DISPATCHER',
              portal: 'Pusat Penugasan Distribusi & Surat Jalan',
              badgeClass: 'bg-blue-50 dark:bg-blue-500/10 border-blue-200/70 dark:border-blue-500/20 text-blue-700 dark:text-blue-400'
            },
            'pengawas_driver': {
              role: 'Pengawas Driver',
              kode: 'PENGAWAS_DRIVER',
              portal: 'Portal Monitoring Supir & Personil Armada',
              badgeClass: 'bg-indigo-50 dark:bg-indigo-500/10 border-indigo-200/70 dark:border-indigo-500/20 text-indigo-700 dark:text-indigo-400'
            },
            'spv_gudang': {
              role: 'SPV Gudang',
              kode: 'SPV_GUDANG',
              portal: 'Pusat Manajemen Stok Semen & Opname Gudang',
              badgeClass: 'bg-teal-50 dark:bg-teal-500/10 border-teal-200/70 dark:border-teal-500/20 text-teal-700 dark:text-teal-400'
            },
            'pengawas_kendaraan': {
              role: 'Pengawas Kendaraan',
              kode: 'PENGAWAS_KENDARAAN',
              portal: 'Pusat Bengkel Pemeliharaan & Suku Cadang Truk',
              badgeClass: 'bg-rose-50 dark:bg-rose-500/10 border-rose-200/70 dark:border-rose-500/20 text-rose-700 dark:text-rose-400'
            },
            'direktur': {
              role: 'Direktur & Manager',
              kode: 'DIREKTUR_MANAGER',
              portal: 'Ringkasan Eksekutif Kinerja & Laporan Finansial',
              badgeClass: 'bg-purple-50 dark:bg-purple-500/10 border-purple-200/70 dark:border-purple-500/20 text-purple-700 dark:text-purple-400'
            },
            'superadmin': {
              role: 'Super Admin',
              kode: 'SUPER_ADMIN',
              portal: 'Pusat Kontrol Akun & Matriks Hak Akses RBAC',
              badgeClass: 'bg-violet-50 dark:bg-violet-500/10 border-violet-200/70 dark:border-violet-500/20 text-violet-700 dark:text-violet-400'
            }
          };
          for (const k in peta) {
            if (usr === k || usr.startsWith(k)) return peta[k];
          }
          return null;
        },
        isiDemo(usr, label) {
          this.inputUsername = usr;
          this.inputPassword = 'password123';
          this.roleTerpilih = usr;
          const info = this.infoRoleTerpilih;
          this.pesanNotif = 'Akun ' + label + ' siap — klik Masuk untuk mengakses ' + (info ? info.role : label) + '.';
          try {
            if (info) {
              localStorage.setItem('jabatan_aktif', info.kode);
            }
          } catch(e) {}
          setTimeout(() => { this.pesanNotif = ''; }, 3500);
        }
      }"
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Portal Masuk — PT Putra Balkom Jaya</title>
    <meta name="description" content="Sistem Informasi Akuntansi & Distribusi Semen — PT Putra Balkom Jaya">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-pbj.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'system-ui', 'sans-serif'],
              display: ['"Plus Jakarta Sans"', 'sans-serif'],
              mono: ['"JetBrains Mono"', 'monospace']
            }
          }
        }
      }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      /* ============================================================
         DESIGN SYSTEM v2.0 — Login Centered Card
         Mengacu: docs/03_Design_System.md §2, §3, §4, §5, §6.1
      ============================================================ */
      [x-cloak] { display: none !important; }
      *, *::before, *::after { box-sizing: border-box; }
      body {
        font-family: 'Inter', system-ui, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        -webkit-text-size-adjust: 100%;
      }

      /* === TOKEN LATAR (DS §2.1 Light / §2.2 Dark) === */
      /* Light: bg-base = #F4F6F9 */
      body { background-color: #F4F6F9; }
      .dark body { background-color: #0C0E14; }
      /* Tailwind tidak bisa menjangkau body dari dark class di html, pakai override berikut: */
      html.dark { background-color: #0C0E14; }
      html:not(.dark) { background-color: #F4F6F9; }

      /* === CARD SURFACE (DS §2.1 bg-surface = #FFFFFF, §4 rounded-xl = 12px) === */
      .kartu-login {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 12px; /* MAKS sesuai DS */
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06), 0 1px 4px rgba(15, 23, 42, 0.04);
      }
      html.dark .kartu-login {
        background: #14161F;
        border-color: #252837;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
      }

      /* === INPUT FORM (DS §4 rounded-lg = 8px, px-3 py-2) === */
      .input-form {
        width: 100%;
        padding: 9px 12px 9px 40px;
        font-size: 14px;
        line-height: 1.5;
        border-radius: 8px; /* MAKS sesuai DS */
        border: 1.5px solid #E2E8F0;
        background: #F8FAFC;
        color: #0F172A;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        -webkit-appearance: none;
        font-family: 'Inter', sans-serif;
      }
      .input-form::placeholder { color: #94A3B8; }
      .input-form:focus {
        border-color: #2563EB;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
      }
      html.dark .input-form {
        background: #1C1E2A;
        border-color: #252837;
        color: #F1F5F9;
      }
      html.dark .input-form::placeholder { color: #475569; }
      html.dark .input-form:focus {
        border-color: #3B82F6;
        background: #252837;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
      }
      @media (max-width: 767px) { .input-form { font-size: 16px !important; } }

      /* === TOMBOL PRIMARY (DS §5.1, rounded-lg = 8px MAKS) === */
      .tombol-masuk {
        width: 100%; padding: 10px 16px;
        border-radius: 8px; border: none; cursor: pointer;
        font-weight: 600; font-size: 14px; color: #ffffff;
        background: #2563EB;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        transition: background 0.15s, box-shadow 0.15s, transform 0.1s;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        font-family: 'Inter', sans-serif;
      }
      .tombol-masuk:hover:not(:disabled) {
        background: #1D4ED8;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
      }
      .tombol-masuk:active:not(:disabled) { transform: scale(0.98); }
      .tombol-masuk:disabled { opacity: 0.65; cursor: not-allowed; }

      /* === DEMO CHIP BUTTON (DS §5.1 Secondary, rounded-lg = 8px MAKS) === */
      .chip-demo {
        padding: 4px 10px; border-radius: 6px; /* badge max 6px */
        font-size: 11px; font-family: 'JetBrains Mono', monospace;
        font-weight: 500; cursor: pointer; border: 1px solid #E2E8F0;
        background: #F8FAFC; color: #475569;
        transition: all 0.15s ease;
        white-space: nowrap;
      }
      .chip-demo:hover { background: #EFF6FF; border-color: #BFDBFE; color: #1D4ED8; }
      html.dark .chip-demo { background: #1C1E2A; border-color: #252837; color: #94A3B8; }
      html.dark .chip-demo:hover { background: #252837; border-color: #3B82F6; color: #60A5FA; }

      /* Active states sesuai divisi */
      .chip-emerald-aktif { background: #D1FAE5 !important; border-color: #059669 !important; color: #065F46 !important; font-weight: 700 !important; }
      html.dark .chip-emerald-aktif { background: rgba(5,150,105,0.2) !important; border-color: #34D399 !important; color: #34D399 !important; }
      .chip-blue-aktif { background: #DBEAFE !important; border-color: #2563EB !important; color: #1E40AF !important; font-weight: 700 !important; }
      html.dark .chip-blue-aktif { background: rgba(37,99,235,0.2) !important; border-color: #60A5FA !important; color: #60A5FA !important; }
      .chip-purple-aktif { background: #EDE9FE !important; border-color: #7C3AED !important; color: #4C1D95 !important; font-weight: 700 !important; }
      html.dark .chip-purple-aktif { background: rgba(124,58,237,0.2) !important; border-color: #A78BFA !important; color: #A78BFA !important; }

      /* === SCROLLBAR === */
      ::-webkit-scrollbar { width: 5px; height: 5px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
      html.dark ::-webkit-scrollbar-thumb { background: #334155; }

      /* === SPIN ANIMASI === */
      @keyframes loginSpin { to { transform: rotate(360deg); } }
      .anim-spin { animation: loginSpin 0.75s linear infinite; }

      /* === FOCUS VISIBLE (Aksesibilitas) === */
      :focus-visible { outline: 2px solid #2563EB; outline-offset: 2px; border-radius: 4px; }
    </style>
</head>

{{-- Latar halaman: Light = #F4F6F9, Dark = #0C0E14 (DS §2.1 bg-base) --}}
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-[#F4F6F9] dark:bg-[#0C0E14] transition-colors duration-200"
      style="min-height: 100dvh;">

  {{-- ============================================================
       SAKELAR MODE TEMA — Pojok kanan atas (DS §1 Prinsip 6)
  ============================================================ --}}
  <button type="button"
          id="tombol-toggle-tema"
          @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
          class="fixed top-4 right-4 z-50 w-9 h-9 rounded-lg flex items-center justify-center
                 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837]
                 text-slate-500 dark:text-slate-400
                 hover:bg-slate-50 dark:hover:bg-[#252837]
                 hover:text-slate-700 dark:hover:text-slate-200
                 transition-all shadow-sm"
          :title="modeGelap ? 'Mode Terang' : 'Mode Gelap'">
    <svg x-show="modeGelap" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    <svg x-show="!modeGelap" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
  </button>

  {{-- ============================================================
       KARTU LOGIN TERPUSAT — DS §6.1: max-w-md, rounded-2xl
       Header: Logo + Nama + Badge Sistem
       Form: Input + Sandi Toggle + Ingat Sesi + Tombol Masuk
       Footer: Demo Akun + Hak Cipta
  ============================================================ --}}
  <div class="w-full max-w-md mx-auto kartu-login overflow-hidden" id="kartu-login-utama">

    {{-- ===== HEADER KARTU ===== --}}
    <div class="px-6 pt-6 pb-5 border-b border-[#EEF0F4] dark:border-[#252837]">

      {{-- Logo + Identitas --}}
      <div class="flex items-center gap-3.5 mb-5">
        <div class="w-12 h-12 rounded-xl bg-white dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center justify-center shrink-0 p-1.5">
          <img src="{{ asset('images/logo-pbj.png') }}"
               alt="Logo PT Putra Balkom Jaya"
               class="w-full h-full object-contain"
               loading="eager">
        </div>
        <div>
          <div class="text-sm font-bold font-display text-[#0F172A] dark:text-[#F1F5F9] leading-tight">
            PT Putra Balkom Jaya
          </div>
          {{-- Badge sistem adaptif sesuai role (DS §5.3) --}}
          <div class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded border text-[11px] font-semibold transition-all duration-150"
               :class="infoRoleTerpilih ? infoRoleTerpilih.badgeClass : 'bg-[#EFF6FF] dark:bg-blue-500/10 border-blue-200/70 dark:border-blue-500/20 text-blue-700 dark:text-blue-400'">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span x-text="infoRoleTerpilih ? 'Role: ' + infoRoleTerpilih.role : 'Sistem Distribusi & Akuntansi'">Sistem Distribusi &amp; Akuntansi</span>
          </div>
        </div>
      </div>

      {{-- Judul & Instruksi Dinamis Adaptif Sesuai Role/Aktor --}}
      <h1 class="text-xl font-bold text-[#0F172A] dark:text-[#F1F5F9] mb-1 font-display transition-all duration-150"
          x-text="infoRoleTerpilih ? 'Portal ' + infoRoleTerpilih.role : 'Portal Masuk Petugas'">
        Portal Masuk Petugas
      </h1>
      <p class="text-sm text-[#475569] dark:text-[#94A3B8] transition-all duration-150"
         x-text="infoRoleTerpilih ? infoRoleTerpilih.portal : 'Masukkan kredensial akun untuk mengakses sistem.'">
        Masukkan kredensial akun untuk mengakses sistem.
      </p>
    </div>

    {{-- ===== BODY FORM ===== --}}
    <div class="px-6 py-5">

      {{-- Toast Notifikasi (DS §1 Prinsip 4: no alert()) --}}
      <div x-show="pesanNotif"
           x-cloak
           x-transition:enter="transition ease-out duration-150"
           x-transition:enter-start="opacity-0 -translate-y-1"
           x-transition:enter-end="opacity-100 translate-y-0"
           x-transition:leave="transition ease-in duration-100"
           x-transition:leave-end="opacity-0"
           class="mb-4 flex items-center gap-2 px-3 py-2.5 rounded-lg
                  bg-[#F0FDF4] dark:bg-emerald-500/10
                  border border-emerald-200 dark:border-emerald-500/20
                  text-xs font-medium text-[#059669] dark:text-[#34D399]">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <span x-text="pesanNotif"></span>
      </div>

      {{-- Pesan Error Validasi Backend --}}
      @if ($errors->any())
      <div class="mb-4 flex items-start gap-2 px-3 py-2.5 rounded-lg
                  bg-[#FFF1F2] dark:bg-red-500/10
                  border border-red-200 dark:border-red-500/20
                  text-xs font-medium text-[#DC2626] dark:text-[#F87171]">
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="space-y-0.5">
          @foreach ($errors->all() as $pesan)
            <div>{{ $pesan }}</div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Form Autentikasi --}}
      <form id="form-login" action="{{ route('auth.proses_login') }}" method="POST" class="space-y-4"
            @submit="
              sedangMasuk = true;
              if (infoRoleTerpilih) {
                try { localStorage.setItem('jabatan_aktif', infoRoleTerpilih.kode); } catch(e) {}
              }
            ">
        @csrf

        {{-- Input Nama Pengguna --}}
        <div>
          <label for="nama_pengguna"
                 class="block text-xs font-semibold text-[#475569] dark:text-[#94A3B8] mb-1.5">
            Nama Pengguna
          </label>
          <div class="relative group">
            {{-- Ikon user --}}
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94A3B8] group-focus-within:text-[#2563EB] dark:group-focus-within:text-[#60A5FA] transition-colors">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <input type="text"
                   id="nama_pengguna"
                   name="nama_pengguna"
                   x-model="inputUsername"
                   autocomplete="username"
                   required
                   placeholder="Contoh: spv_keuangan"
                   class="input-form"
                   autofocus>
          </div>
        </div>

        {{-- Input Kata Sandi --}}
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label for="kata_sandi"
                   class="text-xs font-semibold text-[#475569] dark:text-[#94A3B8]">
              Kata Sandi
            </label>
            <a href="#"
               class="text-xs font-medium text-[#2563EB] dark:text-[#60A5FA] hover:underline">
              Lupa sandi?
            </a>
          </div>
          <div class="relative group">
            {{-- Ikon kunci --}}
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#94A3B8] group-focus-within:text-[#2563EB] dark:group-focus-within:text-[#60A5FA] transition-colors">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <input :type="tampilkanSandi ? 'text' : 'password'"
                   id="kata_sandi"
                   name="kata_sandi"
                   x-model="inputPassword"
                   autocomplete="current-password"
                   required
                   placeholder="Masukkan kata sandi..."
                   class="input-form"
                   style="padding-right: 40px;">
            {{-- Toggle intip sandi --}}
            <button type="button"
                    id="tombol-intip-sandi"
                    @click="tampilkanSandi = !tampilkanSandi"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#94A3B8] hover:text-[#475569] dark:hover:text-[#94A3B8] transition-colors focus:outline-none"
                    :title="tampilkanSandi ? 'Sembunyikan sandi' : 'Tampilkan sandi'">
              <svg x-show="!tampilkanSandi" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              <svg x-show="tampilkanSandi" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
              </svg>
            </button>
          </div>
        </div>

        {{-- Ingat Sesi --}}
        <div class="flex items-center gap-2">
          <input type="checkbox"
                 id="ingat_saya"
                 name="ingat_saya"
                 class="w-4 h-4 rounded border-[#E2E8F0] dark:border-[#252837] accent-blue-600 cursor-pointer">
          <label for="ingat_saya"
                 class="text-xs text-[#475569] dark:text-[#94A3B8] cursor-pointer select-none font-medium">
            Ingat sesi login saya
          </label>
        </div>

        {{-- Tombol Masuk (DS §5.1 Primary Button) --}}
        <button type="submit"
                id="tombol-masuk"
                :disabled="sedangMasuk"
                class="tombol-masuk group">
          <template x-if="!sedangMasuk">
            <span class="flex items-center gap-2">
              <span x-text="infoRoleTerpilih ? 'Masuk ke Portal ' + infoRoleTerpilih.role : 'Masuk ke Dashboard'">Masuk ke Dashboard</span>
              <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </span>
          </template>
          <template x-if="sedangMasuk">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4 anim-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              <span>Memverifikasi...</span>
            </span>
          </template>
        </button>
      </form>
    </div>

    {{-- ===== FOOTER KARTU: AKUN DEMO ===== --}}
    <div class="px-6 pb-5 pt-4 border-t border-[#EEF0F4] dark:border-[#252837]">

      <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold uppercase tracking-widest text-[#64748B] dark:text-[#475569]">
          Akses Cepat Demo
        </span>
        <span class="text-[10px] font-mono font-semibold text-[#2563EB] dark:text-[#60A5FA]">
          Sandi: password123
        </span>
      </div>

      {{-- Grup 1: Keuangan & Eksekutif --}}
      <div class="mb-3">
        <div class="flex items-center gap-1.5 mb-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-[#059669]"></span>
          <span class="text-[10px] font-semibold uppercase tracking-wider text-[#059669] dark:text-[#34D399]">
            Keuangan &amp; Eksekutif
          </span>
        </div>
        <div class="flex flex-wrap gap-1.5">
          <button type="button" id="demo-spv-keuangan"
                  @click="isiDemo('spv_keuangan', 'SPV Keuangan')"
                  class="chip-demo"
                  :class="roleTerpilih === 'spv_keuangan' ? 'chip-emerald-aktif' : ''">
            spv_keuangan
          </button>
          <button type="button" id="demo-staff-ar"
                  @click="isiDemo('staff_ar', 'Staff AR')"
                  class="chip-demo"
                  :class="roleTerpilih === 'staff_ar' ? 'chip-emerald-aktif' : ''">
            staff_ar
          </button>
          <button type="button" id="demo-staff-ap"
                  @click="isiDemo('staff_ap', 'Staff AP')"
                  class="chip-demo"
                  :class="roleTerpilih === 'staff_ap' ? 'chip-emerald-aktif' : ''">
            staff_ap
          </button>
          <button type="button" id="demo-direktur"
                  @click="isiDemo('direktur', 'Direktur & Manager')"
                  class="chip-demo"
                  :class="roleTerpilih === 'direktur' ? 'chip-emerald-aktif' : ''">
            direktur
          </button>
        </div>
      </div>

      {{-- Grup 2: Operasional & Lapangan --}}
      <div class="mb-3">
        <div class="flex items-center gap-1.5 mb-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-[#2563EB]"></span>
          <span class="text-[10px] font-semibold uppercase tracking-wider text-[#2563EB] dark:text-[#60A5FA]">
            Operasional &amp; Lapangan
          </span>
        </div>
        <div class="flex flex-wrap gap-1.5">
          <button type="button" id="demo-dispatcher"
                  @click="isiDemo('dispatcher', 'Dispatcher')"
                  class="chip-demo"
                  :class="roleTerpilih === 'dispatcher' ? 'chip-blue-aktif' : ''">
            dispatcher
          </button>
          <button type="button" id="demo-spv-operasional"
                  @click="isiDemo('spv_operasional', 'SPV Operasional')"
                  class="chip-demo"
                  :class="roleTerpilih === 'spv_operasional' ? 'chip-blue-aktif' : ''">
            spv_operasional
          </button>
          <button type="button" id="demo-spv-gudang"
                  @click="isiDemo('spv_gudang', 'SPV Gudang')"
                  class="chip-demo"
                  :class="roleTerpilih === 'spv_gudang' ? 'chip-blue-aktif' : ''">
            spv_gudang
          </button>
          <button type="button" id="demo-pengawas-driver"
                  @click="isiDemo('pengawas_driver', 'Pengawas Driver')"
                  class="chip-demo"
                  :class="roleTerpilih === 'pengawas_driver' ? 'chip-blue-aktif' : ''">
            pengawas_driver
          </button>
          <button type="button" id="demo-pengawas-kendaraan"
                  @click="isiDemo('pengawas_kendaraan', 'Pengawas Kendaraan')"
                  class="chip-demo"
                  :class="roleTerpilih === 'pengawas_kendaraan' ? 'chip-blue-aktif' : ''">
            pengawas_kendaraan
          </button>
        </div>
      </div>

      {{-- Grup 3: Administrator Sistem --}}
      <div>
        <div class="flex items-center gap-1.5 mb-1.5">
          <span class="w-1.5 h-1.5 rounded-full bg-violet-600"></span>
          <span class="text-[10px] font-semibold uppercase tracking-wider text-violet-700 dark:text-violet-400">
            Administrator Sistem
          </span>
        </div>
        <div class="flex flex-wrap gap-1.5">
          <button type="button" id="demo-superadmin"
                  @click="isiDemo('superadmin', 'Super Admin')"
                  class="chip-demo"
                  :class="roleTerpilih === 'superadmin' ? 'chip-purple-aktif' : ''">
            superadmin
          </button>
        </div>
      </div>
    </div>

    {{-- ===== FOOTER TERBAWAH: Hak Cipta & Versi ===== --}}
    <div class="px-6 py-3 border-t border-[#EEF0F4] dark:border-[#252837] flex items-center justify-between">
      <span class="text-[11px] text-[#64748B] dark:text-[#475569]">
        &copy; {{ date('Y') }} <span class="font-medium text-[#475569] dark:text-[#64748B]">PT Putra Balkom Jaya</span>
      </span>
      <span class="text-[10px] font-mono font-semibold px-1.5 py-0.5 rounded bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[#64748B] dark:text-[#475569]">
        v2.5 ERP
      </span>
    </div>

  </div>
  {{-- /kartu-login-utama --}}

</body>
</html>
