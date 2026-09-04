@props([
    'totalData' => 0,
    'pilihanBaris' => [5, 10, 25, 50, 100],
    'defaultBaris' => 10,
    'satuan' => 'data',
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3.5 border-t border-[#E2E8F0] dark:border-[#252837] bg-white dark:bg-[#14161F] text-xs text-slate-600 dark:text-slate-400 font-medium select-none']) }}>
    
    <!-- Sisi Kiri: Informasi Rentang Baris -->
    <div class="flex items-center gap-1">
        <span>Menampilkan</span>
        <strong class="font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="barisAwal">1</strong>
        <span>sampai</span>
        <strong class="font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="barisAkhir">10</strong>
        <span>dari</span>
        <strong class="font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="totalData">{{ $totalData }}</strong>
        <span>{{ $satuan }}</span>
    </div>

    <!-- Sisi Kanan: Pilihan Baris, Indikator Halaman, & Tombol Navigasi 4 Arah -->
    <div class="flex flex-wrap items-center gap-4 sm:gap-6">
        
        <!-- Pilihan Baris per Halaman -->
        <div class="flex items-center gap-2">
            <span class="text-slate-500 dark:text-slate-400 whitespace-nowrap">Baris per halaman</span>
            <div class="relative">
                <select x-model.number="barisPerHalaman" 
                        @change="halamanSekarang = 1"
                        class="appearance-none pl-2.5 pr-6 py-1 text-xs font-semibold rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 cursor-pointer shadow-xs">
                    @foreach($pilihanBaris as $jml)
                        <option value="{{ $jml }}" {{ $jml == $defaultBaris ? 'selected' : '' }}>{{ $jml }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1.5 text-slate-400">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Indikator Halaman -->
        <div class="whitespace-nowrap font-medium text-slate-700 dark:text-slate-300">
            <span>Halaman</span>
            <strong class="font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="halamanSekarang">1</strong>
            <span>dari</span>
            <strong class="font-bold text-slate-900 dark:text-slate-100 font-mono" x-text="totalHalaman">1</strong>
        </div>

        <!-- Tombol Navigasi 4 Tombol (First, Prev, Next, Last) -->
        <div class="inline-flex items-center gap-1">
            
            <!-- Halaman Pertama (<<) -->
            <button @click="keHalamanPertama()" 
                    :disabled="halamanSekarang <= 1"
                    type="button" 
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-[#E2E8F0] dark:border-[#252837] bg-white dark:bg-[#1C1E2A] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-blue-600 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white dark:disabled:hover:bg-[#1C1E2A] disabled:hover:text-slate-600 transition-all shadow-xs"
                    title="Halaman Pertama">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Halaman Sebelumnya (<) -->
            <button @click="keHalamanSebelumnya()" 
                    :disabled="halamanSekarang <= 1"
                    type="button" 
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-[#E2E8F0] dark:border-[#252837] bg-white dark:bg-[#1C1E2A] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-blue-600 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white dark:disabled:hover:bg-[#1C1E2A] disabled:hover:text-slate-600 transition-all shadow-xs"
                    title="Halaman Sebelumnya">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Halaman Selanjutnya (>) -->
            <button @click="keHalamanSelanjutnya()" 
                    :disabled="halamanSekarang >= totalHalaman"
                    type="button" 
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-[#E2E8F0] dark:border-[#252837] bg-white dark:bg-[#1C1E2A] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-blue-600 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white dark:disabled:hover:bg-[#1C1E2A] disabled:hover:text-slate-600 transition-all shadow-xs"
                    title="Halaman Selanjutnya">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Halaman Terakhir (>>) -->
            <button @click="keHalamanTerakhir()" 
                    :disabled="halamanSekarang >= totalHalaman"
                    type="button" 
                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-[#E2E8F0] dark:border-[#252837] bg-white dark:bg-[#1C1E2A] text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#252837] hover:text-blue-600 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-white dark:disabled:hover:bg-[#1C1E2A] disabled:hover:text-slate-600 transition-all shadow-xs"
                    title="Halaman Terakhir">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>

        </div>
    </div>
</div>
