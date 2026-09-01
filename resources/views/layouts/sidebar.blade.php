<!-- ================================================================
     SIDEBAR NAVIGASI DINAMIS BERDASARKAN HAK AKSES JABATAN (RBAC)
================================================================ -->
<aside :class="sidebarTerlipat ? 'w-16' : 'w-64'"
       class="flex flex-col bg-white dark:bg-[#14161F] border-r border-[#E2E8F0] dark:border-[#252837] shrink-0 transition-all duration-200 z-30 overflow-hidden">

    <!-- Header Sidebar: Logo & Toggle Lipat -->
    <div class="h-14 flex items-center justify-between px-3.5 border-b border-[#E2E8F0] dark:border-[#252837] shrink-0">
        <div class="flex items-center gap-2.5 overflow-hidden">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shadow-sm shadow-blue-600/30 shrink-0">
                <svg class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div x-show="!sidebarTerlipat" x-transition class="whitespace-nowrap overflow-hidden">
                <div class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight">PT Semen Indo</div>
                <div class="text-[10px] text-slate-400 font-medium">Akuntansi & Distribusi</div>
            </div>
        </div>
        <button @click="sidebarTerlipat = !sidebarTerlipat"
                class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-600 dark:hover:text-slate-300 transition-colors shrink-0"
                title="Ciutkan Sidebar">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Navigasi Menu Dinamis Sesuai RBAC -->
    <nav class="flex-1 overflow-y-auto py-3 px-2.5 space-y-4 text-xs">

        <!-- 1. Menu Utama -->
        <div>
            <div x-show="!sidebarTerlipat" class="px-2 mb-1.5 text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Utama</div>
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg font-semibold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 transition-colors">
                <svg class="w-4 h-4 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/>
                </svg>
                <span x-show="!sidebarTerlipat" class="truncate">Ringkasan Dashboard</span>
            </a>
        </div>

        <!-- 2. Kontrol Sistem (Super Admin) -->
        <div x-show="bisaAkses('admin_akun')">
            <div x-show="!sidebarTerlipat" class="px-2 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-widest">Sistem Admin</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-mono">Super Admin</span>
            </div>
            <div class="space-y-0.5">
                <a href="{{ route('superadmin.kelola_akun') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Kelola Akun & RBAC</span>
                </a>
            </div>
        </div>

        <!-- 3. Master Data Sentral -->
        <div x-show="bisaAkses('master_customer') || bisaAkses('master_barang') || bisaAkses('master_wilayah') || bisaAkses('master_karyawan')">
            <div x-show="!sidebarTerlipat" class="px-2 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Master Data</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('master_customer')" href="{{ route('master.customer.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Customer & Toko</span>
                </a>
                <a x-show="bisaAkses('master_barang')" href="{{ route('master.barang.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Semen / Barang</span>
                </a>
                <a x-show="bisaAkses('master_wilayah')" href="{{ route('master.wilayah.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Wilayah & Zonasi</span>
                </a>
                <a x-show="bisaAkses('master_karyawan')" href="{{ route('master.karyawan.index') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2H9.17A3.001 3.001 0 0112 14z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Karyawan</span>
                </a>
            </div>
        </div>

        <!-- 4. Keuangan & AR/AP -->
        <div x-show="bisaAkses('ar_faktur') || bisaAkses('ar_piutang') || bisaAkses('ar_deposit') || bisaAkses('ap_pembelian') || bisaAkses('ap_pengeluaran') || bisaAkses('akun_coa') || bisaAkses('akun_jurnal')">
            <div x-show="!sidebarTerlipat" class="px-2 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Keuangan & AR/AP</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('ar_faktur')" href="{{ route('keuangan.ar.faktur') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Faktur Penjualan (AR)</span>
                </a>
                <a x-show="bisaAkses('ar_piutang')" href="{{ route('keuangan.ar.piutang') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">List Piutang Pelanggan</span>
                </a>
                <a x-show="bisaAkses('ar_deposit')" href="{{ route('keuangan.ar.deposit') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Deposit Customer</span>
                </a>
                <a x-show="bisaAkses('ap_pembelian')" href="{{ route('keuangan.ap.pembelian_so') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Pembelian SO Pabrik (AP)</span>
                </a>
                <a x-show="bisaAkses('ap_pengeluaran')" href="{{ route('keuangan.ap.pengeluaran') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Pengeluaran Kas Operasional</span>
                </a>
                <a x-show="bisaAkses('ap_rilisan')" href="{{ route('keuangan.ap.rilisan') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">List Rilisan Biaya</span>
                </a>
                <a x-show="bisaAkses('akun_coa')" href="{{ route('keuangan.akuntansi.kode_akun') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Kode Akun (COA)</span>
                </a>
                <a x-show="bisaAkses('akun_jurnal')" href="{{ route('keuangan.akuntansi.jurnal') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Jurnal Umum</span>
                </a>
                <a x-show="bisaAkses('akun_aset')" href="{{ route('keuangan.akuntansi.aset') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Aset Perusahaan</span>
                </a>
            </div>
        </div>

        <!-- 5. Operasional, Logistik & Armada -->
        <div x-show="bisaAkses('kirim_sj') || bisaAkses('kirim_ongkos') || bisaAkses('gudang_stok') || bisaAkses('gudang_opname') || bisaAkses('armada_truk') || bisaAkses('armada_driver') || bisaAkses('bengkel_perbaikan') || bisaAkses('bengkel_pembelian_sparepart') || bisaAkses('bengkel_sparepart')">
            <div x-show="!sidebarTerlipat" class="px-2 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Operasional & Logistik</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('kirim_sj')" href="{{ route('operasional.pengiriman.surat_jalan') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Surat Jalan Dispatcher</span>
                </a>
                <a x-show="bisaAkses('kirim_ongkos')" href="{{ route('operasional.pengiriman.ongkos_angkut') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Tarif Ongkos Angkut</span>
                </a>
                <a x-show="bisaAkses('gudang_stok')" href="{{ route('operasional.gudang.stok') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Gudang & Stok Semen</span>
                </a>
                <a x-show="bisaAkses('gudang_opname')" href="{{ route('operasional.gudang.opname') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Modul Stock Opname</span>
                </a>
                <a x-show="bisaAkses('armada_truk')" href="{{ route('operasional.armada.kendaraan') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Armada Truk Kendaraan</span>
                </a>
                <a x-show="bisaAkses('armada_driver')" href="{{ route('operasional.armada.driver') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Data Driver & Kesiapan</span>
                </a>

                <!-- Sub-Modul Khusus Pengawas Kendaraan & Bengkel -->
                <a x-show="bisaAkses('bengkel_perbaikan')" href="{{ route('operasional.bengkel.perbaikan') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Perbaikan Kendaraan (SPK)</span>
                </a>
                <a x-show="bisaAkses('bengkel_pembelian_sparepart')" href="{{ route('operasional.bengkel.pembelian_sparepart') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Pembelian Sparepart</span>
                </a>
                <a x-show="bisaAkses('bengkel_sparepart')" href="{{ route('operasional.bengkel.sparepart') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3zm0 5h16"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">List Sparepart</span>
                </a>
            </div>
        </div>

        <!-- 6. Laporan Eksekutif -->
        <div x-show="bisaAkses('laporan_neraca') || bisaAkses('laporan_laba_rugi')">
            <div x-show="!sidebarTerlipat" class="px-2 mb-1.5 flex items-center justify-between">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Laporan Eksekutif</span>
            </div>
            <div class="space-y-0.5">
                <a x-show="bisaAkses('laporan_neraca')" href="{{ route('laporan.neraca') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Laporan Neraca</span>
                </a>
                <a x-show="bisaAkses('laporan_laba_rugi')" href="{{ route('laporan.laba_rugi') }}" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-slate-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span x-show="!sidebarTerlipat" class="truncate">Laporan Laba Rugi</span>
                </a>
            </div>
        </div>

    </nav>

    <!-- Profil Pengguna Footer Sidebar -->
    <div class="shrink-0 p-2.5 border-t border-[#E2E8F0] dark:border-[#252837]">
        <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
            <div class="w-7 h-7 rounded-lg bg-blue-600/10 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 flex items-center justify-center text-blue-700 dark:text-blue-400 font-bold text-xs shrink-0">
                <span x-text="labelJabatan.substring(0,2)">SP</span>
            </div>
            <div x-show="!sidebarTerlipat" class="overflow-hidden">
                <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                    {{ session('nama_lengkap', 'Pengguna Sistem') }}
                </div>
                <div class="text-[10px] text-slate-400 truncate" x-text="labelJabatan"></div>
            </div>
        </div>
    </div>
</aside>
