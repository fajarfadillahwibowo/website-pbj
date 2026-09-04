@props([
    'labelItem'        => 'data',
    'warna'            => 'emerald',
    'modulIzin'        => '',
    'ruteHapusMassal'  => null,
    'namaInputId'      => 'daftar_id',
    'pesanPeringatan'  => 'Tindakan ini akan menghapus seluruh data yang dipilih secara permanen dari sistem.'
])

<!-- Floating Bar Aksi Massal -->
<div x-show="(daftarTerpilih && daftarTerpilih.length > 0) && (!'{{ $modulIzin }}' || !apakahReadOnly('{{ $modulIzin }}'))" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-4 scale-95"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-slate-900/95 dark:bg-[#14161F]/95 text-white backdrop-blur-md border border-slate-700/60 dark:border-[#2E3348] shadow-2xl text-xs font-medium select-none">
    
    <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span class="text-slate-200">
            <strong x-text="daftarTerpilih.length" class="font-mono text-sm font-bold text-white"></strong> 
            <span>{{ $labelItem }} dipilih</span>
        </span>
    </div>

    <div class="h-4 w-px bg-slate-700 dark:bg-slate-700"></div>

    <div class="flex items-center gap-2">
        <!-- Tombol Salin Kode Terpilih -->
        <button type="button" 
                @click="salinTerpilih(); $dispatch('notifikasi-toast', { pesan: daftarTerpilih.length + ' data berhasil disalin!' })" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <span>Salin Terpilih</span>
        </button>

        @if($ruteHapusMassal)
            <template x-if="{{ $modulIzin ? "!apakahReadOnly('$modulIzin')" : "true" }}">
                <!-- Tombol Hapus Terpilih -->
                <button type="button" 
                        @click="bukaModalHapusMassal()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Hapus Terpilih</span>
                </button>
            </template>
        @endif

        {{ $slot ?? '' }}

        <!-- Tombol Batal Pilih -->
        <button type="button" 
                @click="kosongkanPilihan()" 
                class="p-1.5 rounded-xl hover:bg-slate-800 text-slate-400 hover:text-slate-200 transition-colors"
                title="Batal Pilih Semua">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

@if($ruteHapusMassal)
    <!-- Modal Konfirmasi Hapus Massal Elegan -->
    <template x-if="{{ $modulIzin ? "!apakahReadOnly('$modulIzin')" : "true" }}">
        <div x-show="modalHapusMassalTerbuka" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div @click.away="tutupModalHapusMassal()" 
                 class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl my-8">
                
                <div class="p-6 text-center space-y-4">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center border border-rose-200 dark:border-rose-500/20 shadow-xs">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                            Konfirmasi Hapus Massal
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                            Anda akan menghapus <strong class="text-rose-600 dark:text-rose-400 font-mono font-bold" x-text="daftarTerpilih.length"></strong> {{ $labelItem }} yang dipilih. {{ $pesanPeringatan }}
                        </p>
                    </div>

                    <form action="{{ $ruteHapusMassal }}" method="POST" class="pt-2">
                        @csrf
                        <template x-for="id in daftarTerpilih" :key="id">
                            <input type="hidden" name="{{ $namaInputId }}[]" :value="id">
                        </template>

                        <div class="flex items-center justify-center gap-2.5">
                            <button type="button" 
                                    @click="tutupModalHapusMassal()" 
                                    class="w-1/2 px-4 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="w-1/2 px-4 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:scale-98 rounded-xl transition-all shadow-md shadow-rose-600/20">
                                Ya, Hapus Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
@endif
