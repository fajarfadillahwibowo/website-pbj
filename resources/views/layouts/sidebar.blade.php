<!-- ================================================================
     SIDEBAR NAVIGASI ENTERPRISE BERDASARKAN HAK AKSES JABATAN (RBAC)
================================================================ -->
<aside :class="sidebarTerlipat ? 'w-16' : 'w-64'"
       class="flex flex-col bg-white dark:bg-[#14161F] border-r border-[#E2E8F0] dark:border-[#252837] shrink-0 transition-all duration-200 z-30 overflow-hidden select-none">

    <!-- Header Sidebar: Logo & Nama Perusahaan -->
    <div class="h-14 flex items-center border-b border-[#E2E8F0] dark:border-[#252837] shrink-0 transition-all duration-200"
         :class="sidebarTerlipat ? 'justify-center px-2' : 'justify-between px-3.5'">
        
        <!-- Kondisi 1: Sidebar Terbuka (Expanded) -->
        <template x-if="!sidebarTerlipat">
            <div class="flex items-center justify-between w-full min-w-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0 overflow-hidden group">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-700 to-blue-500 flex items-center justify-center shadow-md shadow-blue-600/30 shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="whitespace-nowrap overflow-hidden">
                        <div class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight truncate">PT Pura Balkom Jaya</div>
                        <div class="text-[10px] text-slate-400 font-medium font-mono">Akuntansi & Logistik Semen</div>
                    </div>
                </a>
                <button @click="sidebarTerlipat = true"
                        class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-600 dark:hover:text-slate-300 transition-colors shrink-0"
                        title="Ciutkan Sidebar">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>
        </template>

        <!-- Kondisi 2: Sidebar Terciut (Collapsed) - Logo Utama Sekaligus Tombol Buka -->
        <template x-if="sidebarTerlipat">
            <button @click="sidebarTerlipat = false"
                    class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-700 to-blue-500 hover:from-blue-600 hover:to-blue-400 flex items-center justify-center shadow-md shadow-blue-600/30 transition-transform active:scale-95 group relative"
                    title="Klik untuk membuka Sidebar (PT Pura Balkom Jaya)">
                <svg class="w-5 h-5 text-white transition-opacity group-hover:opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <svg class="w-4 h-4 text-white absolute inset-0 m-auto opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </template>
    </div>

    <!-- Navigasi Menu Dinamis Sesuai RBAC -->
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-4 text-xs">

        <!-- 1. Menu Utama -->
        <div>
            <div x-show="!sidebarTerlipat" class="px-2.5 mb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Utama</div>
            <a href="{{ route('dashboard') }}"
               :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
               class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
               :title="sidebarTerlipat ? 'Ringkasan Dashboard' : ''">
                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 group-hover:text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                </svg>
                <span x-show="!sidebarTerlipat" class="truncate">Ringkasan Dashboard</span>
            </a>
        </div>

        <!-- 2. Kontrol Sistem (Super Admin) -->
        <div x-show="bisaAkses('admin_akun')">
            <div x-show="!sidebarTerlipat" class="px-2.5 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 uppercase tracking-widest">Sistem Admin</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-mono font-semibold">Super Admin</span>
            </div>
            <div class="space-y-0.5">
                <a href="{{ route('superadmin.kelola_akun') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('superadmin.kelola_akun') ? 'font-bold text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 border-l-2 border-purple-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Kelola Akun & RBAC' : ''">
                    <svg class="w-4 h-4 shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Kelola Akun & RBAC</span>
                </a>
            </div>
        </div>

        <!-- 3. Master Data Sentral -->
        <div x-show="bisaAkses('master_customer') || bisaAkses('master_barang') || bisaAkses('master_wilayah') || bisaAkses('master_karyawan')">
            <div x-show="!sidebarTerlipat" class="px-2.5 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Master Data</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('master_customer')" href="{{ route('master.customer.index') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('master.customer.*') ? 'font-bold text-cyan-700 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-500/10 border-l-2 border-cyan-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Customer (Toko Bangunan)' : ''">
                    <svg class="w-4 h-4 shrink-0 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Customer</span>
                </a>
                <a x-show="bisaAkses('master_barang')" href="{{ route('master.barang.index') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('master.barang.*') ? 'font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border-l-2 border-amber-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Barang / Semen' : ''">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Barang</span>
                </a>
                <a x-show="bisaAkses('master_wilayah')" href="{{ route('master.wilayah.index') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('master.wilayah.*') ? 'font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border-l-2 border-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Wilayah & Zonasi' : ''">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Wilayah</span>
                </a>
                <a x-show="bisaAkses('master_karyawan')" href="{{ route('master.karyawan.index') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('master.karyawan.*') ? 'font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 border-l-2 border-indigo-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Karyawan & Seluruh Pegawai' : ''">
                    <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2H9.17A3.001 3.001 0 0112 14z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Karyawan</span>
                </a>
            </div>
        </div>

        <!-- 4. Keuangan & AR/AP -->
        <div x-show="bisaAkses('ar_faktur') || bisaAkses('ar_piutang') || bisaAkses('ar_deposit') || bisaAkses('ap_pembelian') || bisaAkses('list_so') || bisaAkses('ap_pengeluaran') || bisaAkses('akun_coa') || bisaAkses('akun_jurnal') || bisaAkses('akun_aset') || bisaAkses('jenis_aset')">
            <div x-show="!sidebarTerlipat" class="px-2.5 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Keuangan & AR/AP</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('ar_faktur')" href="{{ route('keuangan.ar.faktur') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.ar.faktur') ? 'font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border-l-2 border-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Penjualan (Faktur AR)' : ''">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Penjualan</span>
                </a>
                <a x-show="bisaAkses('ar_piutang')" href="{{ route('keuangan.ar.piutang') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.ar.piutang') ? 'font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border-l-2 border-amber-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'List Piutang Pelanggan' : ''">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">List Piutang</span>
                </a>
                <a x-show="bisaAkses('ar_deposit')" href="{{ route('keuangan.ar.deposit') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.ar.deposit') ? 'font-bold text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-500/10 border-l-2 border-sky-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'List Deposit Pelanggan' : ''">
                    <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">List Deposit</span>
                </a>
                <a x-show="bisaAkses('ap_pengeluaran')" href="{{ route('keuangan.ap.pengeluaran') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.ap.pengeluaran') ? 'font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border-l-2 border-rose-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Pengeluaran Kas/Bank' : ''">
                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Pengeluaran</span>
                </a>
                <a x-show="bisaAkses('ap_rilisan')" href="{{ route('keuangan.ap.rilisan') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.ap.rilisan') ? 'font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border-l-2 border-amber-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Rilisan / Berita Acara Penerimaan' : ''">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Rilisan</span>
                </a>
                <a x-show="bisaAkses('ap_pembelian')" href="{{ route('keuangan.ap.pembelian_so') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.ap.pembelian_so') ? 'font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Pembelian SO Pabrik' : ''">
                    <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Pembelian SO</span>
                </a>
                <a x-show="bisaAkses('list_so')" href="{{ route('keuangan.ap.pembelian_so') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium"
                   :title="sidebarTerlipat ? 'List Sales Order (SO)' : ''">
                    <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">List SO</span>
                </a>
                <a x-show="bisaAkses('akun_coa')" href="{{ route('keuangan.akuntansi.kode_akun') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.akuntansi.kode_akun') ? 'font-bold text-violet-700 dark:text-violet-400 bg-violet-50 dark:bg-violet-500/10 border-l-2 border-violet-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Kode Akun (COA)' : ''">
                    <svg class="w-4 h-4 shrink-0 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Kode Akun</span>
                </a>
                <a x-show="bisaAkses('akun_jurnal')" href="{{ route('keuangan.akuntansi.jurnal') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.akuntansi.jurnal') ? 'font-bold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-500/10 border-l-2 border-teal-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Jurnal Umum Akuntansi' : ''">
                    <svg class="w-4 h-4 shrink-0 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Jurnal Umum</span>
                </a>
                <a x-show="bisaAkses('akun_aset')" href="{{ route('keuangan.akuntansi.aset') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('keuangan.akuntansi.aset') ? 'font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 border-l-2 border-indigo-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Aset Perusahaan' : ''">
                    <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Aset Perusahaan</span>
                </a>
            </div>
        </div>

        <!-- 5. Operasional, Logistik & Armada -->
        <div x-show="bisaAkses('kirim_sj') || bisaAkses('kirim_ongkos') || bisaAkses('gudang_stok') || bisaAkses('gudang_opname') || bisaAkses('armada_truk') || bisaAkses('armada_driver') || bisaAkses('ops_kso') || bisaAkses('bengkel_perbaikan') || bisaAkses('bengkel_pembelian_sparepart') || bisaAkses('bengkel_sparepart')">
            <div x-show="!sidebarTerlipat" class="px-2.5 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Operasional & Logistik</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('kirim_ongkos')" href="{{ route('operasional.pengiriman.ongkos_angkut') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.pengiriman.ongkos_angkut') ? 'font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-[#1E212E] border-l-2 border-slate-700' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Ongkos Angkut' : ''">
                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Ongkos Angkut</span>
                </a>
                <a x-show="bisaAkses('gudang_opname')" href="{{ route('operasional.gudang.opname') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.gudang.opname') ? 'font-bold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-500/10 border-l-2 border-teal-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Opname Gudang' : ''">
                    <svg class="w-4 h-4 shrink-0 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Stok Opname Gudang</span>
                </a>
                <a x-show="bisaAkses('gudang_stok')" href="{{ route('operasional.gudang.stok') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.gudang.stok') ? 'font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border-l-2 border-amber-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? (jabatanAktif === 'STAFF_AP' ? 'List Gudang SO' : 'Gudang & Stok Semen') : ''">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate" x-text="jabatanAktif === 'STAFF_AP' ? 'List Gudang SO' : 'Data Gudang'">Data Gudang</span>
                </a>
                <a x-show="bisaAkses('armada_driver')" href="{{ route('operasional.armada.driver') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.armada.driver') ? 'font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 border-l-2 border-indigo-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Karyawan (Driver Supir)' : ''">
                    <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Karyawan (Driver)</span>
                </a>
                <a x-show="bisaAkses('armada_truk')" href="{{ route('operasional.armada.kendaraan') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.armada.kendaraan') ? 'font-bold text-orange-700 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 border-l-2 border-orange-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data Kendaraan Truk' : ''">
                    <svg class="w-4 h-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Kendaraan</span>
                </a>
                <a x-show="bisaAkses('kirim_sj')" href="{{ route('operasional.pengiriman.surat_jalan') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.pengiriman.surat_jalan') ? 'font-bold text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-500/10 border-l-2 border-sky-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Pengiriman & Surat Jalan' : ''">
                    <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Pengiriman</span>
                </a>
                <a x-show="bisaAkses('ops_kso')" href="{{ route('operasional.kso') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.kso') ? 'font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Data KSO (Kerja Sama Operasional)' : ''">
                    <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data KSO</span>
                </a>

                <!-- Sub-Modul Khusus Bengkel -->
                <a x-show="bisaAkses('bengkel_perbaikan')" href="{{ route('operasional.bengkel.perbaikan') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.bengkel.perbaikan') ? 'font-bold text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border-l-2 border-red-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Perbaikan Kendaraan (SPK)' : ''">
                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Perbaikan Kendaraan (SPK)</span>
                </a>
                <a x-show="bisaAkses('bengkel_pembelian_sparepart')" href="{{ route('operasional.bengkel.pembelian_sparepart') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.bengkel.pembelian_sparepart') ? 'font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border-l-2 border-rose-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Pembelian Sparepart' : ''">
                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Pembelian Sparepart</span>
                </a>
                <a x-show="bisaAkses('bengkel_sparepart')" href="{{ route('operasional.bengkel.sparepart') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('operasional.bengkel.sparepart') ? 'font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border-l-2 border-amber-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'List Sparepart' : ''">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3zm0 5h16"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">List Sparepart</span>
                </a>
            </div>
        </div>

        <!-- 6. Laporan Eksekutif -->
        <div x-show="bisaAkses('laporan_neraca') || bisaAkses('laporan_laba_rugi')">
            <div x-show="!sidebarTerlipat" class="px-2.5 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Laporan Eksekutif</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('laporan_neraca')" href="{{ route('laporan.neraca') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('laporan.neraca') ? 'font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border-l-2 border-emerald-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Laporan Neraca' : ''">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Laporan Neraca</span>
                </a>
                <a x-show="bisaAkses('laporan_laba_rugi')" href="{{ route('laporan.laba_rugi') }}"
                   :class="sidebarTerlipat ? 'justify-center px-0' : 'px-2.5'"
                   class="flex items-center gap-2.5 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('laporan.laba_rugi') ? 'font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border-l-2 border-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white font-medium' }}"
                   :title="sidebarTerlipat ? 'Laporan Laba Rugi' : ''">
                    <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Laporan Laba Rugi</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- Profil Pengguna Footer Sidebar -->
    <div class="shrink-0 p-2 border-t border-[#E2E8F0] dark:border-[#252837]">
        <div class="flex items-center rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] transition-all"
             :class="sidebarTerlipat ? 'justify-center p-1.5' : 'gap-2.5 px-2.5 py-2'">
            <div class="w-7 h-7 rounded-lg bg-blue-600/10 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 flex items-center justify-center text-blue-700 dark:text-blue-400 font-bold text-xs shrink-0"
                 :title="labelJabatan">
                <span x-text="labelJabatan.substring(0,2)">SP</span>
            </div>
            <div x-show="!sidebarTerlipat" class="overflow-hidden min-w-0">
                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                    {{ session('nama_lengkap', 'Pengguna Sistem') }}
                </div>
                <div class="text-[10px] text-slate-400 truncate font-medium" x-text="labelJabatan"></div>
            </div>
        </div>
    </div>
</aside>
