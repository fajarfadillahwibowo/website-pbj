@extends('layouts.app')

@section('judul', 'Data Fasilitas Gudang & Stok Semen - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaStokGudang()" x-init="initGudang()" class="space-y-6">

    @php
        $opsiJenisGudang = [
            ['nilai' => 'Utama', 'label' => 'Gudang Utama (Pusat)'],
            ['nilai' => 'Distribusi', 'label' => 'Gudang Distribusi / Hub'],
            ['nilai' => 'Buffer', 'label' => 'Gudang Buffer / Cadangan'],
            ['nilai' => 'Transit', 'label' => 'Gudang Transit'],
        ];
        $opsiJenisFilterGudang = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Tipe Gudang']
        ], $opsiJenisGudang);

        $opsiBarangSemen = ($daftarBarang ?? collect())->map(fn($brg) => [
            'nilai' => $brg->kode_barang,
            'label' => $brg->nama_barang . ' (' . $brg->jenis_barang . ')',
            'sub'   => 'Harga: Rp ' . number_format($brg->harga_jual_standar ?? $brg->harga_pokok ?? 0, 0, ',', '.')
        ])->toArray();
    @endphp

    <!-- 1. Header Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 font-mono">
                    SPV Gudang · Manajemen Persediaan
                </span>
                <span class="text-xs text-slate-400 font-mono">Inventori Semen</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data Fasilitas Gudang & Stok Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pemantauan kuantitas fisik semen zak (PCC/OPC) per fasilitas gudang penyimpanan, valuasi persediaan, dan mutasi stok.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-xl transition-all shadow-md shadow-amber-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Fasilitas Gudang</span>
            </button>
        </div>
    </div>

    <!-- 2. Flash Messages -->
    @if(session('sukses'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('sukses') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:text-rose-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm space-y-1">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Terdapat kesalahan validasi formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. Ringkasan 4 Kartu KPI Persediaan -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Gudang -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Fasilitas Gudang</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalGudang }} <span class="text-xs font-normal text-slate-400 font-sans">Lokasi</span></div>
            </div>
        </div>

        <!-- Total Kuantitas Zak -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Stok Fisik</div>
                <div class="text-xl font-bold text-sky-600 dark:text-sky-400 font-mono mt-0.5">{{ number_format($totalStokZak, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">Zak</span></div>
            </div>
        </div>

        <!-- Stok Kritis -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Stok Menipis (≤ 1.000)</div>
                <div class="text-xl font-bold text-rose-600 dark:text-rose-400 font-mono mt-0.5">{{ $stokKritis }} <span class="text-xs font-normal text-slate-400 font-sans">Gudang</span></div>
            </div>
        </div>

        <!-- Valuasi Persediaan -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Valuasi Persediaan</div>
                <div class="text-base font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">Rp {{ number_format($totalValuasiStok, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Fasilitas Gudang & Filter -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarGudang ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('operasional.gudang.stok') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari kode gudang, nama fasilitas, plant, distrik, tipe semen..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Tipe Gudang Dropdown Kustom -->
                <div class="w-full sm:w-56">
                    <x-dropdown-kustom 
                        nama="jenis"
                        placeholder="-- Tipe Gudang --"
                        :opsi="$opsiJenisFilterGudang"
                        :nilaiAwal="$jenisFilter ?? 'semua'"
                        :submitOnChange="true"
                        warnaFokus="amber"
                    />
                </div>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Cari
                </button>

                @if(!empty($kataKunci) || ($jenisFilter !== 'semua' && !empty($jenisFilter)))
                    <a href="{{ route('operasional.gudang.stok') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono">
                Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarGudang) }}</strong> Fasilitas Gudang
            </div>
        </div>

        <!-- Tabel Gudang & Stok -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th x-show="!apakahReadOnly('ops_gudang')" class="w-10 px-3 py-3 text-center">
                            <input type="checkbox" 
                                   @change="togglePilihSemua({{ json_encode(($daftarGudang ?? collect())->pluck('kode_gudang')->toArray()) }})"
                                   :checked="apakahSemuaTerpilih({{ json_encode(($daftarGudang ?? collect())->pluck('kode_gudang')->toArray()) }})"
                                   class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-amber-600 focus:ring-amber-500/30 cursor-pointer">
                        </th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode & Nama Gudang</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Komoditas Semen & Plant</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kuantitas Stok & Status</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Lokasi Wilayah</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Valuasi Persediaan</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi & Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarGudang as $gdg)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            :class="{ 'bg-amber-50/50 dark:bg-amber-950/20': apakahTerpilih('{{ $gdg->kode_gudang }}') }"
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td x-show="!apakahReadOnly('ops_gudang')" class="w-10 px-3 py-3.5 text-center">
                                <input type="checkbox" 
                                       :checked="apakahTerpilih('{{ $gdg->kode_gudang }}')"
                                       @change="togglePilih('{{ $gdg->kode_gudang }}')"
                                       class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-amber-600 focus:ring-amber-500/30 cursor-pointer">
                            </td>
                            
                            <!-- Kode & Nama -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-amber-600 dark:text-amber-400 text-sm">
                                    {{ $gdg->kode_gudang }}
                                </div>
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs mt-0.5">
                                    {{ $gdg->nama_gudang }}
                                </div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                    {{ $gdg->jenis_gudang }}
                                </span>
                            </td>

                            <!-- Komoditas & Plant -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $gdg->barang->nama_barang ?? $gdg->kode_barang }}
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono font-semibold text-amber-600 dark:text-amber-400">Plant: {{ $gdg->plant }}</span>
                                    <span>·</span>
                                    <span>Rp {{ number_format($gdg->harga_barang, 0, ',', '.') }}/zak</span>
                                </div>
                            </td>

                            <!-- Stok & Status -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-base font-bold font-mono {{ $gdg->stok_tersedia <= 1000 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-slate-100' }}">
                                        {{ number_format($gdg->stok_tersedia, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-slate-400">Zak</span>
                                </div>
                                <div class="mt-1">
                                    @if($gdg->stok_tersedia > 5000)
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                            Stok Melimpah
                                        </span>
                                    @elseif($gdg->stok_tersedia > 1000)
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20">
                                            Normal Aman
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 animate-pulse">
                                            Kritis (Perlu Pasok)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Lokasi -->
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-slate-800 dark:text-slate-200">
                                    {{ $gdg->distrik }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $gdg->sub_distrik }}
                                </div>
                            </td>

                            <!-- Valuasi -->
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                Rp {{ number_format($gdg->stok_tersedia * $gdg->harga_barang, 0, ',', '.') }}
                            </td>

                            <!-- Aksi Popover Modern -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$gdg->kode_gudang" 
                                    labelSalin="Salin Kode"
                                    modulIzin="ops_gudang"
                                    :aksiEdit="'bukaModalEdit(\'' . $gdg->kode_gudang . '\')'"
                                    labelEdit="Edit"
                                >
                                    <template x-if="!apakahReadOnly('ops_gudang')">
                                        <button @click="bukaModalMutasi('{{ $gdg->kode_gudang }}', '{{ $gdg->nama_gudang }}', {{ $gdg->stok_tersedia }}); terbuka = false" 
                                                type="button" 
                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors text-left border-b border-slate-100 dark:border-[#252837]">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                            </svg>
                                            <span>Mutasi Stok</span>
                                        </button>
                                    </template>

                                    <template x-if="!apakahReadOnly('ops_gudang')">
                                        <button @click="bukaModalHapus('{{ $gdg->kode_gudang }}', '{{ $gdg->nama_gudang }}'); terbuka = false" 
                                                type="button" 
                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors text-left border-t border-slate-100 dark:border-[#252837]">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    </template>
                                </x-menu-aksi-tabel>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada fasilitas gudang terdaftar</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Daftarkan fasilitas gudang penyimpanan semen baru.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-paginasi-tabel :totalData="count($daftarGudang ?? [])" />

        <!-- Bar Aksi Massal (Multi-Select Floating Bar) -->
        <x-bar-aksi-massal 
            labelItem="gudang" 
            warna="amber" 
            modulIzin="ops_gudang" 
            ruteHapusMassal="{{ route('operasional.gudang.stok.hapus_massal') }}" 
            namaInputId="daftar_kode_gudang" 
            pesanPeringatan="Gudang yang memiliki stok semen aktif tidak akan terhapus demi keamanan inventaris." 
        />
    </div>

    <!-- Modal Tambah Fasilitas Gudang -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Fasilitas Gudang Baru</h3>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.gudang.stok.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Gudang <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_gudang" x-model="formTambah.kode_gudang" required placeholder="GDG-001"
                               class="w-full px-3 py-2 rounded-xl bg-amber-50/50 dark:bg-[#1C1E2A] border border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Fasilitas Gudang <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_gudang" x-model="formTambah.nama_gudang" required placeholder="Gudang Utama Karawang"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe / Jenis Gudang <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="jenis_gudang"
                            placeholder="-- Pilih Tipe Gudang --"
                            :opsi="$opsiJenisGudang"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formTambah.jenis_gudang"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Komoditas Semen <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_barang"
                            placeholder="-- Pilih Semen --"
                            :opsi="$opsiBarangSemen"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formTambah.kode_barang"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Asal Plant Pabrik <span class="text-rose-500">*</span></label>
                        <input type="text" name="plant" x-model="formTambah.plant" required placeholder="Plant Cikarang / Plant Narogong"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="harga_barang" 
                            label="Harga Standar per Zak (Rp)" 
                            modelBind="formTambah.harga_barang" 
                            :wajib="true" 
                            placeholder="64.000" 
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kuantitas Stok Awal (Zak) <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_tersedia" x-model="formTambah.stok_tersedia" min="0" required placeholder="10000"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Distrik / Kabupaten <span class="text-rose-500">*</span></label>
                        <input type="text" name="distrik" x-model="formTambah.distrik" required placeholder="Kabupaten Bekasi"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sub-Distrik / Kecamatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="sub_distrik" x-model="formTambah.sub_distrik" required placeholder="Cikarang Pusat"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Fasilitas Gudang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Fasilitas Gudang -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Gudang: <span class="font-mono text-amber-600" x-text="formEdit.kode_gudang"></span></h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/gudang/stok') }}/' + formEdit.kode_gudang" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Gudang (Terkunci)</label>
                        <input type="text" :value="formEdit.kode_gudang" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Fasilitas Gudang <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_gudang" x-model="formEdit.nama_gudang" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe / Jenis Gudang <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="jenis_gudang"
                            placeholder="-- Pilih Tipe Gudang --"
                            :opsi="$opsiJenisGudang"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.jenis_gudang"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Komoditas Semen <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_barang"
                            placeholder="-- Pilih Semen --"
                            :opsi="$opsiBarangSemen"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_barang"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Asal Plant Pabrik <span class="text-rose-500">*</span></label>
                        <input type="text" name="plant" x-model="formEdit.plant" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="harga_barang" 
                            label="Harga Semen per Zak (Rp)" 
                            modelBind="formEdit.harga_barang" 
                            :wajib="true" 
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kuantitas Stok Fisik (Zak) <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_tersedia" x-model="formEdit.stok_tersedia" min="0" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Distrik / Kabupaten <span class="text-rose-500">*</span></label>
                        <input type="text" name="distrik" x-model="formEdit.distrik" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sub-Distrik / Kecamatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="sub_distrik" x-model="formEdit.sub_distrik" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Mutasi Stok Cepat -->
    <div x-show="modalMutasiTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalMutasiTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-xs">
            
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Mutasi / Penyesuaian Stok Fisik</h3>
                    <p class="text-[11px] text-slate-400" x-text="mutasiData.nama_gudang"></p>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] mb-4 flex justify-between items-center">
                <span class="text-slate-500">Stok Fisik Saat Ini:</span>
                <strong class="font-mono text-slate-900 dark:text-slate-100 text-sm" x-text="mutasiData.stok_sekarang + ' Zak'"></strong>
            </div>

            @php
                $opsiTipeMutasi = [
                    ['nilai' => 'masuk', 'label' => 'Tambah Stok Masuk (Penerimaan Pabrik)'],
                    ['nilai' => 'keluar', 'label' => 'Kurang Stok Keluar (Distribusi / Rusak)'],
                    ['nilai' => 'atur', 'label' => 'Set Kuantitas Fisik Langsung (Hasil Opname)'],
                ];
            @endphp

            <form :action="'{{ url('operasional/gudang/stok') }}/' + mutasiData.kode_gudang + '/mutasi'" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Aksi Mutasi <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="tipe_mutasi"
                        placeholder="-- Pilih Tipe Mutasi --"
                        :opsi="$opsiTipeMutasi"
                        :wajib="true"
                        warnaFokus="amber"
                        modelBind="mutasiData.tipe"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Kuantitas (Zak) <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_zak" min="1" required placeholder="Contoh: 500"
                           class="w-full px-3 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Referensi Surat Jalan</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Penerimaan Pabrik PO-881 atau Koreksi Opname"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalMutasiTerbuka = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-xl transition-all shadow-md shadow-amber-600/20">
                        Proses Mutasi Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Gudang -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Fasilitas Gudang?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus gudang <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nama_gudang"></strong>?
            </p>

            <form :action="'{{ url('operasional/gudang/stok') }}/' + hapusData.kode_gudang" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Gudang
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika Stok Gudang -->
<script>
    function kelolaStokGudang() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalMutasiTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodeGdg: 'Mode: Daur Ulang Slot Kosong',

            formTambah: {
                kode_gudang: '',
                nama_gudang: '',
                jenis_gudang: 'Utama',
                kode_barang: '{{ $daftarBarang->first()->kode_barang ?? "" }}',
                plant: 'Plant Cikarang',
                harga_barang: '64000',
                stok_tersedia: 10000,
                distrik: 'Kabupaten Bekasi',
                sub_distrik: 'Cikarang Pusat'
            },

            formEdit: {
                kode_gudang: '',
                nama_gudang: '',
                jenis_gudang: 'Utama',
                kode_barang: '',
                plant: '',
                harga_barang: 0,
                stok_tersedia: 0,
                distrik: '',
                sub_distrik: ''
            },

            mutasiData: { kode_gudang: '', nama_gudang: '', stok_sekarang: 0, tipe: 'masuk' },
            hapusData: { kode_gudang: '', nama_gudang: '' },

            initGudang() {},

            bukaModalTambah() {
                this.buatKodeOtomatis('gap');
                this.modalTambahTerbuka = true;
            },

            async buatKodeOtomatis(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.gudang.stok.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambah.kode_gudang = data.kode_otomatis;
                        this.keteranganKodeGdg = data.keterangan || (mode === 'acak' ? 'Format Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode gudang:', e);
                }
            },

            async bukaModalEdit(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/gudang/stok') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEdit = {
                            kode_gudang: d.kode_gudang,
                            nama_gudang: d.nama_gudang,
                            jenis_gudang: d.jenis_gudang,
                            kode_barang: d.kode_barang,
                            plant: d.plant,
                            harga_barang: d.harga_barang,
                            stok_tersedia: d.stok_tersedia,
                            distrik: d.distrik,
                            sub_distrik: d.sub_distrik
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data gudang.');
                }
            },

            bukaModalMutasi(kode, nama, stok) {
                this.mutasiData = {
                    kode_gudang: kode,
                    nama_gudang: nama,
                    stok_sekarang: stok,
                    tipe: 'masuk'
                };
                this.modalMutasiTerbuka = true;
            },

            bukaModalHapus(kode, nama) {
                this.hapusData = {
                    kode_gudang: kode,
                    nama_gudang: nama
                };
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
