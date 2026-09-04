@props([
    'kodeSalin'   => null,
    'labelSalin'  => 'Salin Kode',
    'modulIzin'   => null,
    'bisaEdit'    => true,
    'bisaHapus'   => true,
    'aksiDetail'  => null,
    'urlDetail'   => null,
    'labelDetail' => 'Detail',
    'aksiEdit'    => null,
    'urlEdit'     => null,
    'labelEdit'   => 'Edit',
    'aksiHapus'   => null,
    'labelHapus'  => 'Hapus',
    'pesanHapus'  => 'Apakah Anda yakin ingin menghapus data ini?',
    'posisi'      => 'kanan', // 'kanan' (right-0) atau 'kiri' (left-0)
])

@php
    $kelasPosisiX = ($posisi === 'kiri') ? 'left-0' : 'right-0';
@endphp

<div x-data="{ 
    idUnik: 'menu-' + Math.random().toString(36).substr(2, 9),
    menuTerbuka: false,
    bukaKeAtas: false,
    tersalin: false,
    toggleMenu() {
        if (!this.menuTerbuka) {
            window.dispatchEvent(new CustomEvent('tutup-semua-menu', { detail: this.idUnik }));
            this.menuTerbuka = true;
            this.$nextTick(() => {
                const rect = this.$el.getBoundingClientRect();
                const ruangBawah = window.innerHeight - rect.bottom;
                this.bukaKeAtas = ruangBawah < 220;
            });
        } else {
            this.menuTerbuka = false;
        }
    },
    salinTeks(teks) {
        if (!teks) return;
        navigator.clipboard.writeText(teks).then(() => {
            this.tersalin = true;
            setTimeout(() => { this.tersalin = false; this.menuTerbuka = false; }, 1200);
        });
    }
}" 
@tutup-semua-menu.window="if ($event.detail !== idUnik) menuTerbuka = false"
@click.away="menuTerbuka = false" 
class="relative inline-block text-left">

    <!-- Tombol Pemicu Tiga Titik (Three-Dots Trigger) -->
    <button @click.stop="toggleMenu()" 
            type="button" 
            title="Pilihan Aksi"
            class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-[#1C1E2A] dark:hover:bg-[#252837] text-slate-600 dark:text-slate-300 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="1.5" fill="currentColor"></circle>
            <circle cx="19" cy="12" r="1.5" fill="currentColor"></circle>
            <circle cx="5" cy="12" r="1.5" fill="currentColor"></circle>
        </svg>
    </button>

    <!-- Menu Popover Mengambang (Floating Action Menu) -->
    <div x-show="menuTerbuka" 
         x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         :class="bukaKeAtas ? 'bottom-full mb-1 origin-bottom-right' : 'top-full mt-1 origin-top-right'"
         class="absolute {{ $kelasPosisiX }} z-50 w-44 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] shadow-2xl p-1 text-xs space-y-0.5 select-none font-medium">

        <!-- Header Aksi -->
        <div class="px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase text-slate-400 border-b border-slate-100 dark:border-[#252837] mb-1 flex items-center justify-between">
            <span>Aksi</span>
            @if($kodeSalin)
                <span class="font-mono text-[9px] text-slate-500 truncate max-w-[80px]">{{ $kodeSalin }}</span>
            @endif
        </div>

        <!-- 1. Opsi Salin ID / Kode -->
        @if($kodeSalin)
            <button @click.stop="salinTeks('{{ $kodeSalin }}')" 
                    type="button" 
                    class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 hover:bg-[#F8FAFC] dark:hover:bg-[#1C1E2A] transition-colors text-left group">
                <svg x-show="!tersalin" class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <svg x-show="tersalin" x-cloak class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="tersalin ? 'Tersalin!' : '{{ $labelSalin }}'" :class="tersalin ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : ''"></span>
            </button>
        @endif

        <!-- 2. Opsi Lihat Detail -->
        @if($aksiDetail)
            <button @click.stop="menuTerbuka = false; {{ $aksiDetail }}" 
                    type="button" 
                    class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 hover:bg-[#F8FAFC] dark:hover:bg-[#1C1E2A] transition-colors text-left group">
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>{{ $labelDetail }}</span>
            </button>
        @elseif($urlDetail)
            <a href="{{ $urlDetail }}" 
               @click="menuTerbuka = false"
               class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 hover:bg-[#F8FAFC] dark:hover:bg-[#1C1E2A] transition-colors text-left group">
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>{{ $labelDetail }}</span>
            </a>
        @endif

        <!-- 3. Slot Item Aksi Khusus Modul -->
        @if(isset($slot) && trim($slot) !== '')
            {{ $slot }}
        @endif

        <!-- 4. Opsi Edit Data (Dengan Proteksi RBAC) -->
        @if($aksiEdit || $urlEdit)
            @php
                $kondisiRbacEdit = $modulIzin ? "!apakahReadOnly('{$modulIzin}')" : 'true';
            @endphp
            <div x-show="{{ $kondisiRbacEdit }}">
                @if($aksiEdit)
                    <button @click.stop="menuTerbuka = false; {{ $aksiEdit }}" 
                            type="button" 
                            class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 hover:bg-[#F8FAFC] dark:hover:bg-[#1C1E2A] transition-colors text-left group">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>{{ $labelEdit }}</span>
                    </button>
                @elseif($urlEdit)
                    <a href="{{ $urlEdit }}" 
                       @click="menuTerbuka = false"
                       class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 hover:bg-[#F8FAFC] dark:hover:bg-[#1C1E2A] transition-colors text-left group">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>{{ $labelEdit }}</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- 5. Opsi Hapus Data (Dengan Proteksi RBAC & Konfirmasi) -->
        @if($aksiHapus)
            @php
                $kondisiRbacHapus = $modulIzin ? "!apakahReadOnly('{$modulIzin}')" : 'true';
            @endphp
            <div x-show="{{ $kondisiRbacHapus }}" class="border-t border-slate-100 dark:border-[#252837] pt-1 mt-1">
                <form method="POST" action="{{ $aksiHapus }}" onsubmit="return confirm('{{ $pesanHapus }}');" class="block w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            @click="menuTerbuka = false"
                            class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors text-left group font-medium">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>{{ $labelHapus }}</span>
                    </button>
                </form>
            </div>
        @endif

    </div>
</div>
