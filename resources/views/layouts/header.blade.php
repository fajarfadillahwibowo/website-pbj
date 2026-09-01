<header class="h-14 bg-white dark:bg-[#14161F] border-b border-[#E2E8F0] dark:border-[#252837] px-4 sm:px-5 flex items-center justify-between shrink-0 z-20">

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
    <div class="flex items-center gap-2.5 sm:gap-3">

        <!-- Role Simulator Selector (Demo & Development) -->
        <div class="flex items-center gap-1.5 bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] rounded-xl px-2.5 py-1">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider hidden md:inline">Simulasi Role:</span>
            <select x-model="jabatanAktif"
                    class="text-xs font-semibold bg-transparent text-slate-800 dark:text-slate-200 focus:outline-none cursor-pointer">
                <option value="SUPER_ADMIN">1. Super Admin</option>
                <option value="SPV_KEUANGAN">2. SPV Keuangan</option>
                <option value="STAFF_AR">3. Staff AR</option>
                <option value="STAFF_AP">4. Staff AP</option>
                <option value="DISPATCHER">5. Dispatcher</option>
                <option value="PENGAWAS_DRIVER">6. Pengawas Driver</option>
                <option value="SPV_GUDANG">7. SPV Gudang</option>
                <option value="DIREKTUR_MANAGER">8. Direktur & Manager</option>
                <option value="SPV_OPERASIONAL">9. SPV Operasional</option>
                <option value="PENGAWAS_KENDARAAN">10. Pengawas Kendaraan</option>
            </select>
        </div>

        <!-- Toggle Kunci RBAC / Tampilkan Semua -->
        <button @click="kunciRbac = !kunciRbac"
                class="hidden lg:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[11px] font-medium border transition-colors"
                :class="kunciRbac ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-[#1C1E2A] border-slate-200 dark:border-slate-700 text-slate-500'"
                :title="kunciRbac ? 'Hierarki RBAC Aktif: Menampilkan hanya menu yang diizinkan untuk role terpilih' : 'Mode Developer: Menampilkan seluruh menu'">
            <span class="w-2 h-2 rounded-full" :class="kunciRbac ? 'bg-emerald-500' : 'bg-slate-400'"></span>
            <span x-text="kunciRbac ? 'Hierarki RBAC: Terkunci' : 'Mode Developer: Semua Menu'"></span>
        </button>

        <div class="w-px h-5 bg-[#E2E8F0] dark:bg-[#252837] hidden sm:block"></div>

        <!-- Toggle Mode Terang/Gelap -->
        <button @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
                class="p-1.5 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
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
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden sm:inline">Keluar</span>
            </button>
        </form>
    </div>
</header>
