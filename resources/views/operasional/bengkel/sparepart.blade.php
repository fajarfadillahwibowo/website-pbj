@extends('layouts.app')

@section('judul', 'Katalog & Stok Sparepart Truk - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaStokSparepart()" x-init="initSparepart()" class="space-y-6">

    @php
        $opsiKategoriPart = [
            ['nilai' => 'Pelumas & Oli', 'label' => 'Pelumas & Oli'],
            ['nilai' => 'Ban & Roda', 'label' => 'Ban & Roda'],
            ['nilai' => 'Pengereman', 'label' => 'Pengereman (Kampas/Brake)'],
            ['nilai' => 'Filter', 'label' => 'Filter (Oli/Solar/Udara)'],
            ['nilai' => 'Elektrikal & Aki', 'label' => 'Elektrikal & Aki'],
            ['nilai' => 'Mesin & Transmisi', 'label' => 'Mesin & Transmisi'],
            ['nilai' => 'Suspensi & Sasis', 'label' => 'Suspensi & Sasis'],
            ['nilai' => 'Lainnya', 'label' => 'Lainnya / Aksesoris'],
        ];
        $opsiFilterKategori = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Kategori Sparepart']
        ], collect($daftarKategori ?? [])->map(fn($k) => ['nilai' => $k, 'label' => $k])->values()->toArray());

        $opsiTipeMutasiPart = [
            ['nilai' => 'masuk', 'label' => 'Tambah Stok Masuk (Penerimaan Toko)'],
            ['nilai' => 'keluar', 'label' => 'Kurang Stok Keluar (Pemakaian Servis / Rusak)'],
            ['nilai' => 'atur', 'label' => 'Set Kuantitas Fisik Langsung (Hasil Opname)'],
        ];
    @endphp

    <!-- 1. Header Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 font-mono">
                    Pengawas Kendaraan · Bengkel Armada
                </span>
                <span class="text-xs text-slate-400 font-mono">Inventaris Suku Cadang</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Katalog & Stok Sparepart Truk</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Daftar master suku cadang armada truk (ban, oli, kampas rem, filter, aki), batas safety stock, dan penyesuaian kuantitas fisik.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-xl transition-all shadow-md shadow-amber-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Sparepart Baru</span>
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
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. Ringkasan 4 Kartu KPI Inventaris Sparepart -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Jenis Sparepart -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3zm0 5h16"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Jenis Sparepart</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalJenisPart }} <span class="text-xs font-normal text-slate-400 font-sans">Item</span></div>
            </div>
        </div>

        <!-- Total Kuantitas Unit -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Kuantitas Fisik</div>
                <div class="text-xl font-bold text-sky-600 dark:text-sky-400 font-mono mt-0.5">{{ number_format($totalKuantitasFisik, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">Unit/Pcs</span></div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Stok Menipis (≤ 5)</div>
                <div class="text-xl font-bold text-rose-600 dark:text-rose-400 font-mono mt-0.5">{{ $partStokMenipis }} <span class="text-xs font-normal text-slate-400 font-sans">Item</span></div>
            </div>
        </div>

        <!-- Total Valuasi Persediaan -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Valuasi Stok Sparepart</div>
                <div class="text-base font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">Rp {{ number_format($totalValuasiPersediaan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Sparepart & Filter -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarSparepart ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('operasional.bengkel.sparepart') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari kode part, nama suku cadang, kategori, satuan..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Kategori Dropdown Kustom -->
                <div class="w-full sm:w-60">
                    <x-dropdown-kustom 
                        nama="kategori"
                        placeholder="-- Semua Kategori --"
                        :opsi="$opsiFilterKategori"
                        :nilaiAwal="$kategoriFilter ?? 'semua'"
                        :submitOnChange="true"
                        warnaFokus="amber"
                    />
                </div>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Cari
                </button>

                @if(!empty($kataKunci) || ($kategoriFilter !== 'semua' && !empty($kategoriFilter)))
                    <a href="{{ route('operasional.bengkel.sparepart') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono">
                Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarSparepart) }}</strong> Jenis Suku Cadang
            </div>
        </div>

        <!-- Tabel Sparepart -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode & Nama Sparepart</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kategori Suku Cadang</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kuantitas Stok & Satuan</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Harga Beli Satuan</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Total Valuasi Stok</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi & Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarSparepart as $part)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <!-- Kode & Nama Part -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-amber-600 dark:text-amber-400 text-sm">
                                    {{ $part->kode_sparepart }}
                                </div>
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs mt-0.5">
                                    {{ $part->nama_sparepart }}
                                </div>
                            </td>

                            <!-- Kategori Part -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                                    {{ $part->kategori_part }}
                                </span>
                            </td>

                            <!-- Stok & Status -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-sm text-slate-900 dark:text-slate-100">
                                    {{ number_format($part->stok_part, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">{{ $part->satuan }}</span>
                                </div>
                                @php $stokStatus = $part->status_stok; @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono border {{ $stokStatus['bg'] }} mt-0.5 inline-block">
                                    {{ $stokStatus['label'] }}
                                </span>
                            </td>

                            <!-- Harga Beli Satuan -->
                            <td class="px-4 py-3.5 text-right font-mono font-medium text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                {{ $part->harga_satuan_rupiah }}
                            </td>

                            <!-- Total Valuasi Stok -->
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ $part->total_valuasi_rupiah }}
                            </td>

                            <!-- Aksi Popover Modern -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$part->kode_sparepart" 
                                    labelSalin="Salin Kode"
                                    modulIzin="ops_bengkel"
                                    :aksiEdit="'bukaModalEdit(\'' . $part->kode_sparepart . '\')'"
                                    labelEdit="Edit"
                                >
                                    <template x-if="!apakahReadOnly('ops_bengkel')">
                                        <button @click="bukaModalMutasi('{{ $part->kode_sparepart }}', '{{ $part->nama_sparepart }}', {{ $part->stok_part }}, '{{ $part->satuan }}'); terbuka = false" 
                                                type="button" 
                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors text-left border-b border-slate-100 dark:border-[#252837]">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                            </svg>
                                            <span>Mutasi Stok</span>
                                        </button>
                                    </template>

                                    <template x-if="!apakahReadOnly('ops_bengkel')">
                                        <button @click="bukaModalHapus('{{ $part->kode_sparepart }}', '{{ $part->nama_sparepart }}'); terbuka = false" 
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
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3zm0 5h16"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada suku cadang terdaftar</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Daftarkan katalog sparepart baru dengan tombol di kanan atas.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-paginasi-tabel :totalData="count($daftarSparepart ?? [])" />
    </div>

    <!-- Modal Tambah Master Sparepart -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Suku Cadang Baru</h3>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.bengkel.sparepart.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Sparepart <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_sparepart" x-model="formTambah.kode_sparepart" required placeholder="PRT-001"
                               class="w-full px-3 py-2 rounded-xl bg-amber-50/50 dark:bg-[#1C1E2A] border border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Sparepart <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kategori_part"
                            placeholder="-- Pilih Kategori --"
                            :opsi="$opsiKategoriPart"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formTambah.kategori_part"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Suku Cadang / Sparepart <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_sparepart" x-model="formTambah.nama_sparepart" required placeholder="Oli Mesin Diesel Meditran SX 15W-40"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="satuan" x-model="formTambah.satuan" required placeholder="Pcs / Set / Drum / Liter"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Awal Fisik <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_part" x-model="formTambah.stok_part" min="0" required placeholder="10"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                </div>

                <div>
                    <x-input-rupiah 
                        nama="harga_satuan" 
                        label="Harga Beli Standar (Rp)" 
                        modelBind="formTambah.harga_satuan" 
                        :wajib="true" 
                        placeholder="150.000" 
                    />
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Sparepart</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Master Sparepart -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Suku Cadang: <span class="font-mono text-amber-600" x-text="formEdit.kode_sparepart"></span></h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/bengkel/sparepart') }}/' + formEdit.kode_sparepart" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Sparepart (Terkunci)</label>
                        <input type="text" :value="formEdit.kode_sparepart" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Sparepart <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kategori_part"
                            placeholder="-- Pilih Kategori --"
                            :opsi="$opsiKategoriPart"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kategori_part"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Suku Cadang <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_sparepart" x-model="formEdit.nama_sparepart" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="satuan" x-model="formEdit.satuan" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kuantitas Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_part" x-model="formEdit.stok_part" min="0" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                </div>

                <div>
                    <x-input-rupiah 
                        nama="harga_satuan" 
                        label="Harga Beli Satuan (Rp)" 
                        modelBind="formEdit.harga_satuan" 
                        :wajib="true" 
                    />
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Mutasi & Penyesuaian Stok Cepat -->
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
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Mutasi / Penyesuaian Stok Sparepart</h3>
                    <p class="text-[11px] text-slate-400" x-text="mutasiData.nama"></p>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] mb-4 flex justify-between items-center">
                <span class="text-slate-500">Stok Fisik Saat Ini:</span>
                <strong class="font-mono text-slate-900 dark:text-slate-100 text-sm" x-text="mutasiData.stok_sekarang + ' ' + mutasiData.satuan"></strong>
            </div>

            <form :action="'{{ url('operasional/bengkel/sparepart') }}/' + mutasiData.kode + '/mutasi'" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Aksi Mutasi <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="tipe_mutasi"
                        placeholder="-- Pilih Tipe Mutasi --"
                        :opsi="$opsiTipeMutasiPart"
                        :wajib="true"
                        warnaFokus="amber"
                        modelBind="mutasiData.tipe"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Kuantitas <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah" min="1" required placeholder="Contoh: 2"
                           class="w-full px-3 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Referensi SPK / Faktur</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Pemakaian SPK-002 atau Koreksi Opname"
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

    <!-- Modal Konfirmasi Hapus Sparepart -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Suku Cadang Sparepart?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus sparepart <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nama"></strong>?
            </p>

            <form :action="'{{ url('operasional/bengkel/sparepart') }}/' + hapusData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Sparepart
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika Sparepart -->
<script>
    function kelolaStokSparepart() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalMutasiTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodePart: 'Mode: Daur Ulang Slot Kosong',

            formTambah: {
                kode_sparepart: '',
                nama_sparepart: '',
                kategori_part: 'Pelumas & Oli',
                stok_part: 10,
                satuan: 'Pcs',
                harga_satuan: ''
            },

            formEdit: {
                kode_sparepart: '',
                nama_sparepart: '',
                kategori_part: 'Pelumas & Oli',
                stok_part: 0,
                satuan: '',
                harga_satuan: 0
            },

            mutasiData: { kode: '', nama: '', stok_sekarang: 0, satuan: '', tipe: 'masuk' },
            hapusData: { kode: '', nama: '' },

            initSparepart() {},

            bukaModalTambah() {
                this.buatKodeOtomatis('gap');
                this.modalTambahTerbuka = true;
            },

            async buatKodeOtomatis(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.bengkel.sparepart.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambah.kode_sparepart = data.kode_otomatis;
                        this.keteranganKodePart = data.keterangan || (mode === 'acak' ? 'Format Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode sparepart:', e);
                }
            },

            async bukaModalEdit(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/bengkel/sparepart') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEdit = {
                            kode_sparepart: d.kode_sparepart,
                            nama_sparepart: d.nama_sparepart,
                            kategori_part: d.kategori_part,
                            stok_part: d.stok_part,
                            satuan: d.satuan,
                            harga_satuan: Math.round(parseFloat(d.harga_satuan) || 0)
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data sparepart.');
                }
            },

            bukaModalMutasi(kode, nama, stok, satuan) {
                this.mutasiData = {
                    kode: kode,
                    nama: nama,
                    stok_sekarang: stok,
                    satuan: satuan,
                    tipe: 'masuk'
                };
                this.modalMutasiTerbuka = true;
            },

            bukaModalHapus(kode, nama) {
                this.hapusData = { kode: kode, nama: nama };
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
