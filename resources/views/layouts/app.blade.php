<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        sidebarTerlipat: false,
        jabatanAktif: localStorage.getItem('jabatan_aktif') || 'SPV_KEUANGAN',
        menuAktif: 'dashboard',
        get labelJabatan() {
          const peta = {
            SUPER_ADMIN: 'Super Admin',
            SPV_KEUANGAN: 'SPV Keuangan',
            STAFF_AR: 'Staff AR',
            STAFF_AP: 'Staff AP',
            DISPATCHER: 'Dispatcher',
            PENGAWAS_DRIVER: 'Pengawas Driver',
            SPV_GUDANG: 'SPV Gudang',
            DIREKTUR_MANAGER: 'Direktur & Manager',
            SPV_OPERASIONAL: 'SPV Operasional',
            PENGAWAS_KENDARAAN: 'Pengawas Kendaraan'
          };
          return peta[this.jabatanAktif] || this.jabatanAktif;
        }
      }"
      x-init="$watch('jabatanAktif', v => localStorage.setItem('jabatan_aktif', v))"
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'Dashboard') — Sistem Akuntansi & Distribusi Semen</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['"JetBrains Mono"', 'monospace'] }
          }
        }
      }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      [x-cloak] { display: none !important; }
      body { font-family: 'Inter', sans-serif; }
      .nav-item { @apply flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors cursor-pointer; }
      .nav-item-aktif { @apply flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-semibold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10; }
      ::-webkit-scrollbar { width: 4px; height: 4px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
      .dark ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
    @stack('gaya_tambahan')
</head>
<body class="bg-[#F4F6F9] dark:bg-[#0C0E14] text-slate-900 dark:text-slate-100 antialiased transition-colors duration-200">
<div class="flex h-screen overflow-hidden">

  <!-- ================================================================
       SIDEBAR KIRI — Navigasi Berbasis Jabatan
       w-60 normal | w-14 terlipat
  ================================================================ -->
  <aside :class="sidebarTerlipat ? 'w-14' : 'w-60'"
         class="flex flex-col bg-white dark:bg-[#14161F] border-r border-[#E2E8F0] dark:border-[#252837] shrink-0 transition-all duration-200 z-30 overflow-hidden">

    <!-- Logo & Toggle Lipat -->
    <div class="h-14 flex items-center justify-between px-3 border-b border-[#E2E8F0] dark:border-[#252837] shrink-0">
      <div class="flex items-center gap-2.5 overflow-hidden">
        <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <div x-show="!sidebarTerlipat" x-transition class="whitespace-nowrap overflow-hidden">
          <div class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight">Semen Indo</div>
          <div class="text-[10px] text-slate-400">Akuntansi Terpadu</div>
        </div>
      </div>
      <button @click="sidebarTerlipat = !sidebarTerlipat"
              :class="sidebarTerlipat ? 'ml-auto' : ''"
              class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-600 dark:hover:text-slate-300 transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    <!-- Navigasi Utama -->
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

      <!-- Beranda (Semua Jabatan) -->
      <a href="{{ route('dashboard') }}"
         :class="menuAktif === 'dashboard' ? 'nav-item-aktif' : 'nav-item'"
         @click="menuAktif = 'dashboard'">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
        </svg>
        <span x-show="!sidebarTerlipat" class="truncate">Ringkasan Dashboard</span>
      </a>

      <!-- ── SUPER ADMIN (Hanya Kontrol Akun) ── -->
      <template x-if="jabatanAktif === 'SUPER_ADMIN'">
        <div class="space-y-0.5">
          <div x-show="!sidebarTerlipat" class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Kontrol Sistem</div>
          <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg><span x-show="!sidebarTerlipat" class="truncate">Manajemen Akun Staf</span></a>
          <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg><span x-show="!sidebarTerlipat" class="truncate">Pengaturan Hak Akses</span></a>
          <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg><span x-show="!sidebarTerlipat" class="truncate">Log Riwayat Login</span></a>
        </div>
      </template>

      <!-- ── MODUL KEUANGAN ── -->
      <template x-if="['SPV_KEUANGAN', 'STAFF_AR', 'STAFF_AP'].includes(jabatanAktif)">
        <div class="space-y-0.5">
          <div x-show="!sidebarTerlipat" class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Keuangan</div>
          <template x-if="['SPV_KEUANGAN', 'STAFF_AR'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg><span x-show="!sidebarTerlipat" class="truncate">Faktur Penjualan</span></a>
          </template>
          <template x-if="['SPV_KEUANGAN', 'STAFF_AR'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span x-show="!sidebarTerlipat" class="truncate">List Piutang (AR)</span></a>
          </template>
          <template x-if="['SPV_KEUANGAN', 'STAFF_AR'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg><span x-show="!sidebarTerlipat" class="truncate">List Deposit Customer</span></a>
          </template>
          <template x-if="['SPV_KEUANGAN', 'STAFF_AP'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span x-show="!sidebarTerlipat" class="truncate">Pengeluaran (AP)</span></a>
          </template>
          <template x-if="jabatanAktif === 'SPV_KEUANGAN'">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><span x-show="!sidebarTerlipat" class="truncate">Data Kode Akun (COA)</span></a>
          </template>
        </div>
      </template>

      <!-- ── MODUL OPERASIONAL ── -->
      <template x-if="['SPV_KEUANGAN', 'STAFF_AP', 'DISPATCHER', 'SPV_GUDANG', 'SPV_OPERASIONAL', 'PENGAWAS_KENDARAAN', 'PENGAWAS_DRIVER'].includes(jabatanAktif)">
        <div class="space-y-0.5">
          <div x-show="!sidebarTerlipat" class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Operasional</div>
          <template x-if="['SPV_KEUANGAN', 'STAFF_AP'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg><span x-show="!sidebarTerlipat" class="truncate">List Pembelian SO</span></a>
          </template>
          <template x-if="['DISPATCHER', 'SPV_OPERASIONAL'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg><span x-show="!sidebarTerlipat" class="truncate">Surat Jalan Pengiriman</span></a>
          </template>
          <template x-if="['SPV_GUDANG', 'STAFF_AP'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg><span x-show="!sidebarTerlipat" class="truncate">List Gudang SO</span></a>
          </template>
          <template x-if="['SPV_GUDANG', 'SPV_OPERASIONAL'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg><span x-show="!sidebarTerlipat" class="truncate">Opname Gudang</span></a>
          </template>
          <template x-if="['PENGAWAS_DRIVER', 'DISPATCHER', 'SPV_OPERASIONAL'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg><span x-show="!sidebarTerlipat" class="truncate">Data Driver</span></a>
          </template>
          <template x-if="['PENGAWAS_KENDARAAN', 'SPV_OPERASIONAL'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span x-show="!sidebarTerlipat" class="truncate">Perbaikan Kendaraan</span></a>
          </template>
          <template x-if="['PENGAWAS_KENDARAAN'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg><span x-show="!sidebarTerlipat" class="truncate">List Sparepart</span></a>
          </template>
          <template x-if="['SPV_OPERASIONAL'].includes(jabatanAktif)">
            <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg><span x-show="!sidebarTerlipat" class="truncate">Data Ongkos Angkut</span></a>
          </template>
        </div>
      </template>

      <!-- ── LAPORAN EKSEKUTIF ── -->
      <template x-if="['DIREKTUR_MANAGER', 'SPV_KEUANGAN'].includes(jabatanAktif)">
        <div class="space-y-0.5">
          <div x-show="!sidebarTerlipat" class="px-3 pt-4 pb-1 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Laporan</div>
          <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg><span x-show="!sidebarTerlipat" class="truncate">Laporan Neraca</span></a>
          <a href="#" class="nav-item"><svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg><span x-show="!sidebarTerlipat" class="truncate">Laporan Laba Rugi</span></a>
        </div>
      </template>

    </nav>

    <!-- Profil Singkat Footer Sidebar -->
    <div class="shrink-0 p-2 border-t border-[#E2E8F0] dark:border-[#252837]">
      <div class="flex items-center gap-2.5 px-1 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-[#252837] transition-colors cursor-pointer">
        <div class="w-7 h-7 rounded-lg bg-blue-600/10 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 flex items-center justify-center text-blue-700 dark:text-blue-400 font-bold text-xs shrink-0">
          <span x-text="labelJabatan.substring(0,2)">SP</span>
        </div>
        <div x-show="!sidebarTerlipat" class="overflow-hidden">
          <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">Budi Santoso</div>
          <div class="text-[10px] text-slate-400 truncate" x-text="labelJabatan"></div>
        </div>
      </div>
    </div>
  </aside>

  <!-- ================================================================
       AREA KONTEN UTAMA
  ================================================================ -->
  <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    <!-- Topbar h-14 -->
    <header class="h-14 flex items-center justify-between px-5 bg-white dark:bg-[#14161F] border-b border-[#E2E8F0] dark:border-[#252837] shrink-0 z-20">

      <!-- Breadcrumb / Judul Halaman -->
      <div class="flex items-center gap-2 min-w-0">
        <span class="text-xs text-slate-400 hidden sm:inline">Beranda /</span>
        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">@yield('judul', 'Dashboard')</span>
      </div>

      <!-- Topbar Actions -->
      <div class="flex items-center gap-2">

        <!-- Simulasi Jabatan (Demo) -->
        <div class="hidden sm:flex items-center gap-1.5">
          <span class="text-[10px] text-slate-400 font-medium">Jabatan:</span>
          <select x-model="jabatanAktif"
                  class="text-xs font-medium bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] rounded-lg px-2 py-1 text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer">
            <option value="SUPER_ADMIN">Super Admin</option>
            <option value="SPV_KEUANGAN">SPV Keuangan</option>
            <option value="STAFF_AR">Staff AR</option>
            <option value="STAFF_AP">Staff AP</option>
            <option value="DISPATCHER">Dispatcher</option>
            <option value="PENGAWAS_DRIVER">Pengawas Driver</option>
            <option value="SPV_GUDANG">SPV Gudang</option>
            <option value="DIREKTUR_MANAGER">Direktur & Manager</option>
            <option value="SPV_OPERASIONAL">SPV Operasional</option>
            <option value="PENGAWAS_KENDARAAN">Pengawas Kendaraan</option>
          </select>
        </div>

        <div class="w-px h-5 bg-[#E2E8F0] dark:bg-[#252837]"></div>

        <!-- Toggle Tema -->
        <button @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
                class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                :title="modeGelap ? 'Mode Terang' : 'Mode Gelap'">
          <svg x-show="modeGelap" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <svg x-show="!modeGelap" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
          </svg>
        </button>

        <!-- Logout -->
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span>Keluar</span>
        </a>
      </div>
    </header>

    <!-- Konten Halaman -->
    <main class="flex-1 overflow-y-auto p-5">
      @yield('konten')
    </main>

  </div><!-- /area konten -->

</div><!-- /flex container -->

@stack('skrip_tambahan')
</body>
</html>
