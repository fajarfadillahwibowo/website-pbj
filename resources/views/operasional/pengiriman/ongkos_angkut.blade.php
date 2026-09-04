@extends('layouts.app')

@section('judul', 'Data Ongkos Angkut - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaOngkosAngkut()" x-init="initOngkosAngkut()" class="space-y-6">

    @php
        $opsiMuatan = [
            ['nilai' => 'Semen Zak 50kg', 'label' => 'Semen Zak 50kg (Kemasan Zak)', 'sub' => 'Standar Konstruksi'],
            ['nilai' => 'Semen Zak 40kg', 'label' => 'Semen Zak 40kg (Kemasan Praktis)', 'sub' => 'Retail / Toko'],
            ['nilai' => 'Curah Semen (Ton)', 'label' => 'Curah Semen / Tonase', 'sub' => 'Truk Tangki Bulk / Batching Plant'],
            ['nilai' => 'Semen Big Bag (1 Ton)', 'label' => 'Semen Big Bag (1.000 kg)', 'sub' => 'Proyek Industri'],
            ['nilai' => 'Klinker / Bahan Mentah', 'label' => 'Klinker Curah Semen', 'sub' => 'Antar Pabrik'],
        ];

        $opsiFilterMuatan = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Jenis Muatan', 'sub' => null]
        ], $opsiMuatan);

        $opsiGudang = ($daftarGudang ?? collect())->map(fn($g) => [
            'nilai' => $g->kode_gudang,
            'label' => $g->kode_gudang . ' — ' . $g->nama_gudang,
            'sub'   => 'Plant: ' . ($g->plant ?? 'Utama') . ' · ' . ($g->distrik ?? 'Pusat') . ' · Stok: ' . number_format($g->stok_tersedia ?? 0, 0, ',', '.') . ' Zak (' . ($g->jenis_gudang ?? 'Gudang') . ')'
        ])->toArray();

        $opsiFilterGudang = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Fasilitas Gudang (SPV Gudang)', 'sub' => null]
        ], $opsiGudang);

        $opsiWilayah = ($daftarWilayah ?? collect())->map(fn($w) => [
            'nilai' => $w->nama_wilayah,
            'label' => $w->nama_wilayah . ' (' . $w->kode_wilayah . ')',
            'sub'   => 'Kode Zonasi: ' . $w->kode_wilayah
        ])->toArray();
    @endphp

    <!-- 1. Header Modul & Tombol Aksi -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 font-mono">
                    SPV Operasional · Distribusi Semen
                </span>
                <span class="text-xs text-slate-400 font-mono">Modul Logistik & Pengiriman</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data Ongkos Angkut (Master Tarif OA)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola master tarif ongkos angkut distribusi, kontrak tarif pengiriman, perbandingan harga standar vs tarif KSO armada.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 rounded-xl transition-all shadow-md shadow-blue-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Tarif OA Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Flash Message / Notifikasi Sukses & Error -->
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
                <span>Terdapat kesalahan pada isian form:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. Ringkasan Kartu KPI / Statistik -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Rute OA -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Trayek OA</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalRute }} <span class="text-xs font-normal text-slate-400 font-sans">Rute</span></div>
            </div>
        </div>

        <!-- Rata-rata Harga OA -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Rata-rata Harga OA</div>
                <div class="text-base font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">Rp {{ number_format($rataHargaOa, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Rata-rata Harga KSO -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Rata-rata Tarif KSO</div>
                <div class="text-base font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">Rp {{ number_format($rataHargaKso, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Rata-rata Harga KSO Khusus -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Tarif KSO Khusus</div>
                <div class="text-base font-bold text-violet-600 dark:text-violet-400 font-mono mt-0.5">Rp {{ number_format($rataHargaKsoKhusus, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- 4. Bar Pencarian & Filter Data -->
    <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm space-y-3">
        <form method="GET" action="{{ route('operasional.pengiriman.ongkos_angkut') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            
            <!-- Input Cari -->
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari kode OA, nama rute, kontrak, wilayah, gudang..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
            </div>

            <!-- Filter Muatan Dropdown -->
            <div class="sm:col-span-3">
                <x-dropdown-kustom 
                    nama="muatan"
                    placeholder="-- Semua Muatan --"
                    :opsi="$opsiFilterMuatan"
                    :nilaiAwal="$filterMuatan ?? 'semua'"
                    :submitOnChange="true"
                    warnaFokus="blue"
                />
            </div>

            <!-- Filter Gudang Dropdown -->
            <div class="sm:col-span-3">
                <x-dropdown-kustom 
                    nama="gudang"
                    placeholder="-- Semua Gudang --"
                    :opsi="$opsiFilterGudang"
                    :nilaiAwal="$filterGudang ?? 'semua'"
                    :submitOnChange="true"
                    warnaFokus="blue"
                />
            </div>

        </form>
    </div>

    <!-- 5. Tabel Data Master Ongkos Angkut -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarOngkosAngkut ?? []) }}, defaultBaris: 10 })" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode OA</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Nama Rute / Trayek OA</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Gudang Asal (SPV Gudang)</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Kontrak</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Jenis Muatan</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Harga OA</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Harga KSO</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">KSO Khusus</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Wilayah OA</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarOngkosAngkut as $oa)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors group">
                            <!-- 1. Kode OA -->
                            <td class="px-4 py-3.5 font-mono font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                <span class="px-2 py-1 rounded bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                                    {{ $oa->kode_oa }}
                                </span>
                            </td>

                            <!-- 2. Nama OA -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs flex items-center gap-1.5">
                                    {{ $oa->nama_oa }}
                                </div>
                                @if($oa->keterangan)
                                    <div class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5" title="{{ $oa->keterangan }}">
                                        {{ $oa->keterangan }}
                                    </div>
                                @endif
                            </td>

                            <!-- 3. Kode Gudang (Tersinkronisasi dengan SPV Gudang) -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if($oa->kode_gudang && $oa->gudang)
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ $oa->kode_gudang }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                            ({{ $oa->gudang->nama_gudang }})
                                        </span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">-</span>
                                @endif
                            </td>

                            <!-- 4. No. Kontrak -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-mono text-slate-600 dark:text-slate-400">
                                {{ $oa->no_kontrak ?: '-' }}
                            </td>

                            <!-- 5. Jenis Muatan -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $oa->muatan === 'KLINKER' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                    {{ $oa->muatan ?: 'SEMEN ZAK' }}
                                </span>
                            </td>

                            <!-- 6. Harga OA -->
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                {{ $oa->harga_oa_rupiah }}
                            </td>

                            <!-- 7. Harga KSO -->
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-amber-600 dark:text-amber-400 whitespace-nowrap">
                                {{ $oa->harga_kso_rupiah }}
                            </td>

                            <!-- 8. Harga KSO Khusus -->
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-violet-600 dark:text-violet-400 whitespace-nowrap">
                                {{ $oa->harga_kso_khusus_rupiah }}
                            </td>

                            <!-- 9. Wilayah OA -->
                            <td class="px-4 py-3.5 whitespace-nowrap font-medium text-slate-700 dark:text-slate-300">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 text-[11px]">
                                    {{ $oa->wilayah_oa }}
                                </span>
                            </td>

                            <!-- 10. Aksi Popover Modern -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$oa->kode_oa" 
                                    labelSalin="Salin Kode"
                                    modulIzin="ops_ongkos_angkut"
                                    :aksiDetail="'bukaModalDetail(\'' . $oa->kode_oa . '\')'"
                                    labelDetail="Detail"
                                    :aksiEdit="'bukaModalEdit(\'' . $oa->kode_oa . '\')'"
                                    labelEdit="Edit"
                                >
                                    <template x-if="!apakahReadOnly('ops_ongkos_angkut')">
                                        <button @click="bukaModalHapus('{{ $oa->kode_oa }}', '{{ addslashes($oa->nama_oa) }}'); terbuka = false" 
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
                            <td colspan="10" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Tidak ada data ongkos angkut ditemukan</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci filter pencarian atau klik tombol Tambah Tarif OA Baru.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-paginasi-tabel :totalData="count($daftarOngkosAngkut ?? [])" />
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL TAMBAH DATA ONGKOS ANGKUT -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Tarif Ongkos Angkut (OA)</h3>
                    <p class="text-[11px] text-slate-400">Lengkapi 9 atribut master data ongkos angkut distribusi semen.</p>
                </div>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>
            
            <form action="{{ route('operasional.pengiriman.ongkos_angkut.simpan') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                
                <!-- Baris 1: kode_oa & nama_oa -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode OA <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_oa" x-model="formTambah.kode_oa" required placeholder="OA-001"
                               class="w-full px-3 py-2 rounded-xl bg-blue-50/50 dark:bg-[#1C1E2A] border border-blue-200 dark:border-blue-900/50 text-blue-900 dark:text-blue-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Rute / Trayek OA <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_oa" x-model="formTambah.nama_oa" required placeholder="Contoh: Rute Pabrik Cirebon - Gudang Bekasi"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>

                <!-- Baris 2: kode_gudang & kontrak_oa -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Gudang Asal Semen <span class="text-slate-400 font-normal">(kode_gudang)</span></label>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Sinkron SPV Gudang</span>
                        </div>
                        <x-dropdown-kustom 
                            nama="kode_gudang"
                            placeholder="-- Pilih Gudang Asal --"
                            :opsi="$opsiGudang"
                            :wajib="false"
                            warnaFokus="blue"
                            modelBind="formTambah.kode_gudang"
                        />
                        <!-- Kartu Info Real-Time Gudang Terpilih (Sinkronisasi SPV Gudang) -->
                        <template x-if="ambilDetailGudang(formTambah.kode_gudang)">
                            <div class="mt-2 p-2.5 rounded-xl bg-amber-50/70 dark:bg-amber-500/10 border border-amber-200/80 dark:border-amber-500/20 text-xs">
                                <div class="flex items-center justify-between font-semibold">
                                    <span class="text-amber-900 dark:text-amber-300 truncate" x-text="ambilDetailGudang(formTambah.kode_gudang).nama_gudang"></span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 shrink-0 ml-1" x-text="ambilDetailGudang(formTambah.kode_gudang).kode_gudang"></span>
                                </div>
                                <div class="mt-1.5 text-[11px] text-slate-600 dark:text-slate-400 grid grid-cols-2 gap-x-2 gap-y-1">
                                    <div>Plant: <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="ambilDetailGudang(formTambah.kode_gudang).plant || '-'"></span></div>
                                    <div>Wilayah: <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="ambilDetailGudang(formTambah.kode_gudang).distrik || '-'"></span></div>
                                    <div>Fasilitas: <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="ambilDetailGudang(formTambah.kode_gudang).jenis_gudang || '-'"></span></div>
                                    <div>Stok Fisik: <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="(new Intl.NumberFormat('id-ID').format(ambilDetailGudang(formTambah.kode_gudang).stok_tersedia || 0)) + ' Zak'"></span></div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Kontrak OA <span class="text-slate-400 font-normal">(kontrak_oa)</span></label>
                        <input type="text" name="kontrak_oa" x-model="formTambah.kontrak_oa" placeholder="Contoh: KTR/PBJ-OA/2026/01"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 font-mono">
                    </div>
                </div>

                <!-- Baris 3: muatan_oa & wilayah_oa -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Muatan <span class="text-rose-500">*</span> <span class="text-slate-400 font-normal">(muatan_oa)</span></label>
                        <x-dropdown-kustom 
                            nama="muatan_oa"
                            placeholder="-- Pilih Jenis Muatan --"
                            :opsi="$opsiMuatan"
                            :wajib="true"
                            warnaFokus="blue"
                            modelBind="formTambah.muatan_oa"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah OA / Zonasi <span class="text-rose-500">*</span> <span class="text-slate-400 font-normal">(wilayah_oa)</span></label>
                        <input type="text" name="wilayah_oa" x-model="formTambah.wilayah_oa" required placeholder="Contoh: Bekasi & Sekitarnya"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>

                <!-- Baris 4: 3 Kolom Nominal Tarif (harga_oa, harga_kso, harga_kso_khusus) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <label class="block font-semibold text-emerald-700 dark:text-emerald-400 mb-1">Harga OA Standar (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_oa" 
                            modelBind="formTambah.harga_oa" 
                            placeholder="0" 
                            :wajib="true"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-amber-700 dark:text-amber-400 mb-1">Harga KSO Standar (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_kso" 
                            modelBind="formTambah.harga_kso" 
                            placeholder="0" 
                            :wajib="true"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-violet-700 dark:text-violet-400 mb-1">Harga KSO Khusus (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_kso_khusus" 
                            modelBind="formTambah.harga_kso_khusus" 
                            placeholder="0" 
                            :wajib="true"
                        />
                    </div>
                </div>

                <!-- Baris 5: Keterangan -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Catatan Tambahan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <textarea name="keterangan" x-model="formTambah.keterangan" rows="2" placeholder="Catatan ketentuan tarif, jenis armada yang berlaku, dll."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="modalTambahTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 rounded-xl transition-all shadow-md shadow-blue-600/20">Simpan Tarif OA</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL EDIT DATA ONGKOS ANGKUT -->
    <!-- ========================================================================= -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Tarif OA: <span class="font-mono text-amber-600" x-text="formEdit.kode_oa"></span></h3>
                    <p class="text-[11px] text-slate-400">Perbarui spesifikasi rute dan tarif ongkos angkut.</p>
                </div>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>
            
            <form :action="'{{ url('operasional/pengiriman/ongkos-angkut') }}/' + formEdit.kode_oa" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                
                <!-- Baris 1: kode_oa (Read-only) & nama_oa -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode OA</label>
                        <input type="text" x-model="formEdit.kode_oa" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-[#1C1E2A] border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-mono font-bold cursor-not-allowed">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Rute / Trayek OA <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_oa" x-model="formEdit.nama_oa" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <!-- Baris 2: kode_gudang & kontrak_oa -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Gudang Asal Semen <span class="text-slate-400 font-normal">(kode_gudang)</span></label>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-medium">Sinkron SPV Gudang</span>
                        </div>
                        <x-dropdown-kustom 
                            nama="kode_gudang"
                            placeholder="-- Pilih Gudang Asal --"
                            :opsi="$opsiGudang"
                            :wajib="false"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_gudang"
                        />
                        <!-- Kartu Info Real-Time Gudang Terpilih (Sinkronisasi SPV Gudang) -->
                        <template x-if="ambilDetailGudang(formEdit.kode_gudang)">
                            <div class="mt-2 p-2.5 rounded-xl bg-amber-50/70 dark:bg-amber-500/10 border border-amber-200/80 dark:border-amber-500/20 text-xs">
                                <div class="flex items-center justify-between font-semibold">
                                    <span class="text-amber-900 dark:text-amber-300 truncate" x-text="ambilDetailGudang(formEdit.kode_gudang).nama_gudang"></span>
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 shrink-0 ml-1" x-text="ambilDetailGudang(formEdit.kode_gudang).kode_gudang"></span>
                                </div>
                                <div class="mt-1.5 text-[11px] text-slate-600 dark:text-slate-400 grid grid-cols-2 gap-x-2 gap-y-1">
                                    <div>Plant: <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="ambilDetailGudang(formEdit.kode_gudang).plant || '-'"></span></div>
                                    <div>Wilayah: <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="ambilDetailGudang(formEdit.kode_gudang).distrik || '-'"></span></div>
                                    <div>Fasilitas: <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="ambilDetailGudang(formEdit.kode_gudang).jenis_gudang || '-'"></span></div>
                                    <div>Stok Fisik: <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="(new Intl.NumberFormat('id-ID').format(ambilDetailGudang(formEdit.kode_gudang).stok_tersedia || 0)) + ' Zak'"></span></div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Kontrak OA <span class="text-slate-400 font-normal">(kontrak_oa)</span></label>
                        <input type="text" name="kontrak_oa" x-model="formEdit.kontrak_oa"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                </div>

                <!-- Baris 3: muatan_oa & wilayah_oa -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Muatan <span class="text-rose-500">*</span> <span class="text-slate-400 font-normal">(muatan_oa)</span></label>
                        <x-dropdown-kustom 
                            nama="muatan_oa"
                            placeholder="-- Pilih Jenis Muatan --"
                            :opsi="$opsiMuatan"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.muatan_oa"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah OA / Zonasi <span class="text-rose-500">*</span> <span class="text-slate-400 font-normal">(wilayah_oa)</span></label>
                        <input type="text" name="wilayah_oa" x-model="formEdit.wilayah_oa" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <!-- Baris 4: 3 Kolom Nominal Tarif (harga_oa, harga_kso, harga_kso_khusus) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <label class="block font-semibold text-emerald-700 dark:text-emerald-400 mb-1">Harga OA Standar (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_oa" 
                            modelBind="formEdit.harga_oa" 
                            placeholder="0" 
                            :wajib="true"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-amber-700 dark:text-amber-400 mb-1">Harga KSO Standar (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_kso" 
                            modelBind="formEdit.harga_kso" 
                            placeholder="0" 
                            :wajib="true"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-violet-700 dark:text-violet-400 mb-1">Harga KSO Khusus (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_kso_khusus" 
                            modelBind="formEdit.harga_kso_khusus" 
                            placeholder="0" 
                            :wajib="true"
                        />
                    </div>
                </div>

                <!-- Baris 5: Keterangan -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Catatan Tambahan</label>
                    <textarea name="keterangan" x-model="formEdit.keterangan" rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="modalEditTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-xl transition-all shadow-md shadow-amber-600/20">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. MODAL DETAIL DATA ONGKOS ANGKUT -->
    <!-- ========================================================================= -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl my-8">
            
            <!-- Header Modal Detail -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold font-mono text-sm shadow-md shadow-blue-600/30">
                        <span>OA</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100" x-text="detailOa.nama_oa"></h2>
                        <p class="text-[11px] text-blue-600 dark:text-blue-400 font-mono font-semibold" x-text="detailOa.kode_oa"></p>
                    </div>
                </div>
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Konten Detail 9 Atribut -->
            <div class="p-6 space-y-4 text-xs">
                
                <!-- Grid Informasi Rute & Kontrak -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 p-4 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Wilayah Cakupan OA</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailOa.wilayah_oa || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">No. Kontrak OA</div>
                        <div class="font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailOa.kontrak_oa || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Kategori Muatan</div>
                        <div class="font-bold text-blue-600 dark:text-blue-400 mt-0.5" x-text="detailOa.muatan_oa || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Kode Rute OA</div>
                        <div class="font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailOa.kode_oa"></div>
                    </div>

                    <!-- Panel Detail Gudang Asal (Sinkron SPV Gudang) -->
                    <div class="sm:col-span-2 border-t border-slate-200/60 dark:border-slate-800/80 pt-3 mt-1">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Fasilitas Gudang Asal (Tersinkronisasi SPV Gudang)
                            </span>
                            <span class="font-mono text-amber-600 dark:text-amber-400 font-bold" x-text="detailOa.kode_gudang || 'Pusat / Pabrik'"></span>
                        </div>
                        <template x-if="detailOa.gudang">
                            <div class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-500/10 border border-amber-200/80 dark:border-amber-500/20">
                                <div class="flex items-center justify-between font-bold text-slate-900 dark:text-slate-100">
                                    <span class="text-amber-900 dark:text-amber-300 text-xs" x-text="detailOa.gudang.nama_gudang"></span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-amber-200/60 dark:border-slate-700" x-text="detailOa.gudang.jenis_gudang || 'Fasilitas Utama'"></span>
                                </div>
                                <div class="mt-2 grid grid-cols-3 gap-2 text-[11px] text-slate-600 dark:text-slate-400">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Plant Produksi:</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="detailOa.gudang.plant || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Wilayah / Distrik:</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="detailOa.gudang.distrik || '-'"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Stok Fisik SPV Gudang:</span>
                                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="(new Intl.NumberFormat('id-ID').format(detailOa.gudang.stok_tersedia || 0)) + ' Zak'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template x-if="!detailOa.gudang">
                            <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-[#1C1E2A] text-slate-500 italic text-[11px] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Rute ini tidak terikat gudang transit/buffer (distribusi langsung dari Silo / Pabrik Utama PBJ).
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Grid Komparasi Tarif -->
                <div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Struktur Komparasi Tarif</div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-xl bg-emerald-50/80 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-center">
                            <div class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">Harga OA</div>
                            <div class="text-sm font-bold font-mono text-emerald-800 dark:text-emerald-300 mt-1" x-text="detailOa.harga_oa_rupiah"></div>
                            <div class="text-[9px] text-emerald-600/80 mt-0.5">Tarif Standar</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-amber-50/80 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-center">
                            <div class="text-[10px] font-bold text-amber-700 dark:text-amber-400 uppercase">Harga KSO</div>
                            <div class="text-sm font-bold font-mono text-amber-800 dark:text-amber-300 mt-1" x-text="detailOa.harga_kso_rupiah"></div>
                            <div class="text-[9px] text-amber-600/80 mt-0.5">Mitra KSO</div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-violet-50/80 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20 text-center">
                            <div class="text-[10px] font-bold text-violet-700 dark:text-violet-400 uppercase">KSO Khusus</div>
                            <div class="text-sm font-bold font-mono text-violet-800 dark:text-violet-300 mt-1" x-text="detailOa.harga_kso_khusus_rupiah"></div>
                            <div class="text-[9px] text-violet-600/80 mt-0.5">Proyek Khusus</div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Catatan & Keterangan Rute</div>
                    <div class="p-3 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 leading-relaxed"
                         x-text="detailOa.keterangan || 'Tidak ada catatan khusus untuk rute ini.'"></div>
                </div>

                <!-- Timestamp -->
                <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono pt-1">
                    <span>Terakhir diperbarui:</span>
                    <span x-text="detailOa.terakhir_diedit_waktu || '-'"></span>
                </div>

            </div>

            <!-- Footer Modal Detail -->
            <div class="flex items-center justify-end px-6 py-3.5 border-t border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <button @click="modalDetailTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 9. MODAL KONFIRMASI HAPUS ONGKOS ANGKUT -->
    <!-- ========================================================================= -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Data Ongkos Angkut?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus data tarif OA <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nama"></strong> (<span class="font-mono" x-text="hapusData.kode"></span>)?
            </p>

            <form :action="'{{ url('operasional/pengiriman/ongkos-angkut') }}/' + hapusData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika CRUD Ongkos Angkut -->
<script>
    function kelolaOngkosAngkut() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalDetailTerbuka: false,
            modalHapusTerbuka: false,

            formTambah: {
                kode_oa: '',
                nama_oa: '',
                kode_gudang: '{{ $daftarGudang->first()->kode_gudang ?? "" }}',
                kontrak_oa: '',
                muatan_oa: 'Semen Zak 50kg',
                harga_oa: 0,
                harga_kso: 0,
                harga_kso_khusus: 0,
                wilayah_oa: '{{ $daftarWilayah->first()->nama_wilayah ?? "" }}',
                keterangan: ''
            },

            formEdit: {
                kode_oa: '',
                nama_oa: '',
                kode_gudang: '',
                kontrak_oa: '',
                muatan_oa: '',
                harga_oa: 0,
                harga_kso: 0,
                harga_kso_khusus: 0,
                wilayah_oa: '',
                keterangan: ''
            },

            detailOa: {},
            hapusData: {
                kode: '',
                nama: ''
            },

            // Master data gudang sinkron dari SPV Gudang
            daftarGudangLengkap: @json($daftarGudang),

            ambilDetailGudang(kode) {
                if (!kode) return null;
                return this.daftarGudangLengkap.find(g => String(g.kode_gudang) === String(kode)) || null;
            },

            initOngkosAngkut() {
                // Inisialisasi awal modul
            },

            bukaModalTambah() {
                this.formTambah.nama_oa = '';
                this.formTambah.kontrak_oa = '';
                this.formTambah.harga_oa = 0;
                this.formTambah.harga_kso = 0;
                this.formTambah.harga_kso_khusus = 0;
                this.formTambah.keterangan = '';
                this.buatKodeOtomatis();
                this.modalTambahTerbuka = true;
            },

            async buatKodeOtomatis() {
                try {
                    const response = await fetch(`{{ route("operasional.pengiriman.ongkos_angkut.buat_kode") }}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        this.formTambah.kode_oa = hasil.kode_otomatis;
                    }
                } catch (e) {
                    console.error('Gagal membuat kode otomatis:', e);
                }
            },

            async bukaModalDetail(kode) {
                try {
                    const response = await fetch(`{{ url('operasional/pengiriman/ongkos-angkut') }}/${kode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        this.detailOa = hasil.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data detail ongkos angkut.');
                }
            },

            async bukaModalEdit(kode) {
                try {
                    const response = await fetch(`{{ url('operasional/pengiriman/ongkos-angkut') }}/${kode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        const d = hasil.data;
                        this.formEdit = {
                            kode_oa: d.kode_oa,
                            nama_oa: d.nama_oa,
                            kode_gudang: d.kode_gudang || '',
                            kontrak_oa: d.kontrak_oa || '',
                            muatan_oa: d.muatan_oa || 'Semen Zak 50kg',
                            harga_oa: Number(d.harga_oa) || 0,
                            harga_kso: Number(d.harga_kso) || 0,
                            harga_kso_khusus: Number(d.harga_kso_khusus) || 0,
                            wilayah_oa: d.wilayah_oa || '',
                            keterangan: d.keterangan || ''
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data ongkos angkut untuk diedit.');
                }
            },

            bukaModalHapus(kode, nama) {
                this.hapusData.kode = kode;
                this.hapusData.nama = nama;
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
