<header class="h-14 bg-white dark:bg-[#14161F] border-b border-[#E2E8F0] dark:border-[#252837] px-4 sm:px-5 flex items-center justify-between shrink-0 z-20 select-none">

    <!-- Tombol Toggle Sidebar & Breadcrumb / Judul Ruang Kerja -->
    <div class="flex items-center gap-2.5 min-w-0">
        <button @click="sidebarTerlipat = !sidebarTerlipat"
                class="p-1.5 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-900 dark:hover:text-white transition-colors shrink-0"
                title="Buka/Tutup Sidebar">
            <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="h-4 w-px bg-slate-200 dark:bg-[#252837] hidden sm:block"></div>

        <div class="flex items-center gap-2 min-w-0">
            <span class="text-xs text-slate-400 font-medium hidden md:inline">Portal /</span>
            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                @yield('judul', 'Dashboard Terpadu')
            </span>
            <span class="text-[11px] px-2 py-0.5 rounded-lg font-mono font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 truncate"
                  x-text="labelJabatan">
                SPV Keuangan
            </span>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-2 sm:gap-3">

        <!-- Custom Enterprise Role Selector Dropdown (Menggantikan select bawaan) -->
        <div class="relative" x-data="{ cariRole: '' }">
            
            <!-- Trigger Button -->
            <button @click="dropdownRoleTerbuka = !dropdownRoleTerbuka"
                    type="button"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold bg-[#F4F6F9] dark:bg-[#1C1E2A] hover:bg-slate-200 dark:hover:bg-[#252837] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                <span class="w-2 h-2 rounded-full bg-blue-600 dark:bg-blue-400 animate-pulse"></span>
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-mono hidden lg:inline">Simulasi Role:</span>
                <span class="font-bold text-blue-600 dark:text-blue-400 font-mono" x-text="roleAktifObj.no + '.'"></span>
                <span class="max-w-[130px] sm:max-w-[160px] truncate" x-text="roleAktifObj.nama"></span>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                     :class="dropdownRoleTerbuka ? 'rotate-180 text-blue-600' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <!-- Dropdown Menu Popover -->
            <div x-show="dropdownRoleTerbuka"
                 x-cloak
                 @click.outside="dropdownRoleTerbuka = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-[#181B26] border border-[#E2E8F0] dark:border-[#2A2E3D] dropdown-shadow z-50 overflow-hidden">
                
                <!-- Header Popover -->
                <div class="px-4 py-3 bg-[#F8FAFC] dark:bg-[#141620] border-b border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-900 dark:text-slate-100">Simulasi Hak Akses Pengguna</div>
                        <div class="text-[10px] text-slate-400">Pilih salah satu dari 10 peran sistem RBAC</div>
                    </div>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                        10 Peran
                    </span>
                </div>

                <!-- List 10 Peran -->
                <div class="max-h-80 overflow-y-auto p-1.5 space-y-1">
                    <template x-for="role in daftarRole" :key="role.kode">
                        <button @click="pilihRole(role.kode)"
                                type="button"
                                class="w-full flex items-start gap-3 p-2.5 rounded-xl text-left transition-all group"
                                :class="jabatanAktif === role.kode 
                                    ? 'bg-blue-50 dark:bg-blue-500/15 border-l-4 border-blue-600 dark:border-blue-500 text-blue-900 dark:text-blue-100' 
                                    : 'hover:bg-slate-100/80 dark:hover:bg-[#202434] text-slate-700 dark:text-slate-300'">
                            
                            <!-- Nomor Role Badge -->
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-mono text-[11px] font-bold shrink-0 transition-colors"
                                 :class="jabatanAktif === role.kode 
                                     ? 'bg-blue-600 text-white shadow-sm shadow-blue-600/30' 
                                     : 'bg-slate-100 dark:bg-[#252837] text-slate-500 group-hover:bg-slate-200 dark:group-hover:bg-[#2E3346]'">
                                <span x-text="role.no"></span>
                            </div>

                            <!-- Informasi Role -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold truncate"
                                          :class="jabatanAktif === role.kode ? 'text-blue-700 dark:text-blue-400' : 'text-slate-800 dark:text-slate-200'"
                                          x-text="role.nama">
                                    </span>
                                    
                                    <!-- Checkmark Aktif -->
                                    <template x-if="jabatanAktif === role.kode">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5 leading-snug"
                                   x-text="role.deskripsi"></p>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- Footer Popover -->
                <div class="px-4 py-2.5 bg-[#F8FAFC] dark:bg-[#141620] border-t border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between text-[10px] text-slate-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        RBAC Otomatis Menyaring Sidebar
                    </span>
                    <span class="font-mono text-slate-500" x-text="'Kode: ' + jabatanAktif"></span>
                </div>
            </div>
        </div>

        <!-- Toggle Kunci RBAC / Tampilkan Semua -->
        <button @click="kunciRbac = !kunciRbac"
                class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold border transition-all"
                :class="kunciRbac 
                    ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400' 
                    : 'bg-slate-100 dark:bg-[#1C1E2A] border-slate-200 dark:border-slate-700 text-slate-500'"
                :title="kunciRbac ? 'Hierarki RBAC Aktif: Menampilkan hanya menu yang diizinkan untuk role terpilih' : 'Mode Developer: Menampilkan seluruh menu'">
            <span class="w-2 h-2 rounded-full" :class="kunciRbac ? 'bg-emerald-500' : 'bg-slate-400'"></span>
            <span x-text="kunciRbac ? 'Hierarki RBAC: Terkunci' : 'Mode Developer: Semua Menu'"></span>
        </button>

        <div class="w-px h-5 bg-[#E2E8F0] dark:bg-[#252837] hidden sm:block"></div>

        <!-- Toggle Mode Terang/Gelap -->
        <button @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
                class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
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
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 border border-transparent hover:border-rose-200 dark:hover:border-rose-500/20 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden sm:inline">Keluar</span>
            </button>
        </form>
    </div>
</header>
