<header class="h-14 bg-white dark:bg-[#14161F] border-b border-[#E2E8F0] dark:border-[#252837] px-5 flex items-center justify-between shrink-0 z-20">

    <!-- Breadcrumb / Judul Ruang Kerja -->
    <div class="flex items-center gap-2 min-w-0">
        <span class="text-xs text-slate-400 hidden sm:inline">Portal /</span>
        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate">
            @yield('judul', 'Dashboard Terpadu')
        </span>
        <span class="text-[11px] px-2 py-0.5 rounded font-mono font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20"
              x-text="labelJabatan">
            SPV Keuangan
        </span>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-3">

        <!-- Role Simulator Selector (Demo & Development) -->
        <div class="hidden md:flex items-center gap-1.5 bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] rounded-lg px-2 py-1">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Simulasi Role:</span>
            <select x-model="jabatanAktif"
                    class="text-xs font-semibold bg-transparent text-slate-700 dark:text-slate-200 focus:outline-none cursor-pointer">
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

        <div class="w-px h-5 bg-[#E2E8F0] dark:bg-[#252837] hidden sm:block"></div>

        <!-- Toggle Mode Terang/Gelap -->
        <button @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
                class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
                :title="modeGelap ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'">
            <svg x-show="modeGelap" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg x-show="!modeGelap" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        <!-- Tombol Logout -->
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden sm:inline">Keluar</span>
            </button>
        </form>
    </div>
</header>
