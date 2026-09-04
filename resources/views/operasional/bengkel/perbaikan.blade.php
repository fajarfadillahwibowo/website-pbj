@extends('layouts.app')

@section('judul', 'SPK Perbaikan & Servis Kendaraan - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaPerbaikanKendaraan()" x-init="initPerbaikan()" class="space-y-6">

    @php
        $opsiStatusPerbaikan = [
            ['nilai' => 'Dalam Proses', 'label' => 'Dalam Proses Pengerjaan'],
            ['nilai' => 'Menunggu Sparepart', 'label' => 'Menunggu Suku Cadang'],
            ['nilai' => 'Selesai', 'label' => 'Selesai Servis'],
            ['nilai' => 'Dibatalkan', 'label' => 'Dibatalkan'],
        ];
        $opsiStatusFilterPerbaikan = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Status SPK']
        ], $opsiStatusPerbaikan);

        $opsiKendaraan = ($daftarKendaraan ?? collect())->map(fn($knd) => [
            'nilai' => $knd->kode_kendaraan ?? $knd->kode_aset,
            'label' => ($knd->no_polisi ?? '-') . ' — ' . ($knd->merek_kendaraan ?? $knd->nama_aset ?? 'Truk') . ' [' . ($knd->kode_kendaraan ?? $knd->kode_aset) . ']',
            'sub'   => 'Plat/Kode: ' . ($knd->kode_kendaraan ?? $knd->kode_aset)
        ])->toArray();
    @endphp

    <!-- 1. Header Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/20 font-mono">
                    Pengawas Kendaraan · SPK Bengkel
                </span>
                <span class="text-xs text-slate-400 font-mono">Surat Perintah Kerja (SPK)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Surat Perintah Kerja (SPK) Servis Armada</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Penerbitan SPK perbaikan truk semen, penggantian suku cadang, pelaporan bengkel rekanan/internal, serta estimasi rincian biaya perawatan.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 active:scale-95 rounded-xl transition-all shadow-md shadow-red-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Terbitkan SPK Baru</span>
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
                <span>Terdapat kesalahan pengisian data SPK:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. Ringkasan 4 Kartu KPI SPK Perbaikan -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total SPK -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total SPK Terbit</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalSpk }} <span class="text-xs font-normal text-slate-400 font-sans">SPK</span></div>
            </div>
        </div>

        <!-- Dalam Proses Pengerjaan -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Sedang Dikerjakan</div>
                <div class="text-xl font-bold text-blue-600 dark:text-blue-400 font-mono mt-0.5">{{ $dalamPengerjaan }} <span class="text-xs font-normal text-slate-400 font-sans">Armada</span></div>
            </div>
        </div>

        <!-- Servis Selesai -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Servis Selesai</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $servisSelesai }} <span class="text-xs font-normal text-slate-400 font-sans">Armada</span></div>
            </div>
        </div>

        <!-- Total Biaya Servis -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Biaya Perawatan</div>
                <div class="text-base font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">Rp {{ number_format($totalBiayaServis, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel SPK Perbaikan & Filter -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarPerbaikan ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('operasional.bengkel.perbaikan') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari no SPK, armada truk, keluhan, bengkel pelaksana..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Status Dropdown Kustom -->
                <div class="w-full sm:w-60">
                    <x-dropdown-kustom 
                        nama="status"
                        placeholder="-- Semua Status SPK --"
                        :opsi="$opsiStatusFilterPerbaikan"
                        :nilaiAwal="$statusFilter ?? 'semua'"
                        :submitOnChange="true"
                        warnaFokus="red"
                    />
                </div>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Cari
                </button>

                @if(!empty($kataKunci) || ($statusFilter !== 'semua' && !empty($statusFilter)))
                    <a href="{{ route('operasional.bengkel.perbaikan') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono">
                Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarPerbaikan) }}</strong> SPK Servis
            </div>
        </div>
        <!-- Tabel SPK Perbaikan -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. SPK & Tanggal</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Armada Truk & Plat</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Keluhan / Tindakan Servis</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Rincian & Total Biaya</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status Servis</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi & Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarPerbaikan as $spk)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            
                            <!-- No SPK & Tanggal Masuk -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-red-600 dark:text-red-400 text-sm">
                                    {{ $spk->nomor_spk_perbaikan }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Masuk: {{ $spk->tanggal_masuk_format }}</span>
                                </div>
                                @if($spk->tanggal_selesai)
                                    <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">
                                        ✓ Selesai: {{ $spk->tanggal_selesai_format }}
                                    </div>
                                @endif
                            </td>

                            <!-- Plat & Nama Truk -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-900 dark:text-slate-100 text-sm">
                                    {{ $spk->kendaraan->no_polisi ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                    {{ $spk->kendaraan->merek_kendaraan ?? $spk->kendaraan->nama_aset ?? 'Truk Semen' }} ({{ $spk->kode_kendaraan ?? $spk->kode_aset }})
                                </div>
                            </td>

                            <!-- Keluhan & Bengkel Pelaksana -->
                            <td class="px-4 py-3.5 max-w-xs">
                                <div class="font-medium text-slate-900 dark:text-slate-100 line-clamp-2">
                                    {{ $spk->keluhan_kerusakan }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-1 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    <span>{{ $spk->bengkel_pelaksana }}</span>
                                </div>
                            </td>

                            <!-- Rincian Biaya -->
                            <td class="px-4 py-3.5 text-right font-mono whitespace-nowrap">
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-sm">
                                    {{ $spk->total_biaya_rupiah }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Jasa: {{ $spk->biaya_jasa_rupiah }} | Part: {{ $spk->biaya_sparepart_rupiah }}
                                </div>
                            </td>

                            <!-- Status Servis & Quick Toggle -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @php $badge = $spk->status_badge; @endphp
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold font-mono border {{ $badge['bg'] }} inline-block">
                                    {{ $badge['label'] }}
                                </span>
                                @if($spk->status_perbaikan === 'Dalam Proses')
                                    <div class="mt-1">
                                        <button @click="ubahStatusCepat('{{ $spk->id_perbaikan }}', 'Selesai')"
                                                class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold hover:underline">
                                            ✓ Tandai Selesai
                                        </button>
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi Popover Modern -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$spk->nomor_spk_perbaikan" 
                                    labelSalin="Salin No"
                                    modulIzin="ops_perbaikan"
                                    :aksiDetail="'bukaModalDetail(\'' . $spk->id_perbaikan . '\')'"
                                    labelDetail="Detail"
                                    :aksiEdit="'bukaModalEdit(\'' . $spk->id_perbaikan . '\')'"
                                    labelEdit="Edit"
                                >
                                    <!-- Cetak SPK -->
                                    <button @click="cetakSPK('{{ $spk->id_perbaikan }}'); terbuka = false" 
                                            type="button" 
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400 transition-colors text-left border-b border-slate-100 dark:border-[#252837]">
                                        <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span>Cetak</span>
                                    </button>

                                    @if($spk->status_perbaikan === 'Dalam Proses')
                                        <template x-if="!apakahReadOnly('ops_perbaikan')">
                                            <button @click="ubahStatusCepat('{{ $spk->id_perbaikan }}', 'Selesai'); terbuka = false" 
                                                    type="button" 
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors text-left border-b border-slate-100 dark:border-[#252837]">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Tandai Selesai</span>
                                            </button>
                                        </template>
                                    @endif

                                    <template x-if="!apakahReadOnly('ops_perbaikan')">
                                        <button @click="bukaModalHapus('{{ $spk->id_perbaikan }}', '{{ $spk->nomor_spk_perbaikan }}'); terbuka = false" 
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada data SPK Perbaikan</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Terbitkan SPK perbaikan armada baru dengan tombol di kanan atas.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-paginasi-tabel :totalData="count($daftarPerbaikan ?? [])" />
    </div>

    <!-- Modal Tambah SPK Perbaikan -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Terbitkan Surat Perintah Kerja (SPK)</h3>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.bengkel.perbaikan.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Nomor SPK Perbaikan <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-red-600 dark:text-red-400 font-semibold px-1.5 py-0.5 bg-red-50 dark:bg-red-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="nomor_spk_perbaikan" x-model="formTambah.nomor_spk_perbaikan" required placeholder="SPK-001"
                               class="w-full px-3 py-2 rounded-xl bg-red-50/50 dark:bg-[#1C1E2A] border border-red-200 dark:border-red-900/50 text-red-900 dark:text-red-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-red-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Armada Truk <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_aset"
                            placeholder="-- Pilih Truk Armada --"
                            :opsi="$opsiKendaraan"
                            :wajib="true"
                            warnaFokus="red"
                            modelBind="formTambah.kode_aset"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Masuk Servis <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_masuk" 
                            modelBind="formTambah.tanggal_masuk" 
                            placeholder="Pilih Tanggal Masuk"
                            :wajib="true"
                            warnaFokus="red"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                        <x-input-tanggal 
                            nama="tanggal_selesai" 
                            modelBind="formTambah.tanggal_selesai" 
                            placeholder="Pilih Tanggal Selesai"
                            :wajib="false"
                            warnaFokus="red"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bengkel Pelaksana <span class="text-rose-500">*</span></label>
                        <input type="text" name="bengkel_pelaksana" x-model="formTambah.bengkel_pelaksana" required placeholder="Bengkel Internal PBJ"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Pengerjaan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_perbaikan"
                            placeholder="-- Status Pengerjaan --"
                            :opsi="$opsiStatusPerbaikan"
                            :wajib="true"
                            warnaFokus="red"
                            modelBind="formTambah.status_perbaikan"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keluhan / Indikasi Kerusakan <span class="text-rose-500">*</span></label>
                    <textarea name="keluhan_kerusakan" x-model="formTambah.keluhan_kerusakan" rows="2" required placeholder="Deskripsikan keluhan pengemudi / kerusakan armada..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/30"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tindakan Perbaikan / Suku Cadang <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="tindakan_perbaikan" x-model="formTambah.tindakan_perbaikan" rows="2" placeholder="Rincian tindakan montir dan sparepart yang diganti..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/30"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-rupiah 
                            nama="biaya_jasa" 
                            label="Biaya Ongkos Jasa (Rp)" 
                            modelBind="formTambah.biaya_jasa" 
                            placeholder="0" 
                        />
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="biaya_sparepart" 
                            label="Biaya Sparepart (Rp)" 
                            modelBind="formTambah.biaya_sparepart" 
                            placeholder="0" 
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pengawas Kendaraan Penanggung Jawab <span class="text-rose-500">*</span></label>
                    <input type="text" name="pengawas_kendaraan" x-model="formTambah.pengawas_kendaraan" required placeholder="Nama pengawas armada"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-sm">Terbitkan SPK</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit SPK Perbaikan -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data SPK Perbaikan: <span class="font-mono text-red-600" x-text="formEdit.nomor_spk_perbaikan"></span></h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/bengkel/perbaikan') }}/' + formEdit.id_perbaikan" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor SPK (Terkunci)</label>
                        <input type="text" :value="formEdit.nomor_spk_perbaikan" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Armada Truk <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_aset"
                            placeholder="-- Pilih Truk Armada --"
                            :opsi="$opsiKendaraan"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_aset"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Masuk Servis <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_masuk" 
                            modelBind="formEdit.tanggal_masuk" 
                            placeholder="Pilih Tanggal Masuk"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                        <x-input-tanggal 
                            nama="tanggal_selesai" 
                            modelBind="formEdit.tanggal_selesai" 
                            placeholder="Pilih Tanggal Selesai"
                            :wajib="false"
                            warnaFokus="amber"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bengkel Pelaksana <span class="text-rose-500">*</span></label>
                        <input type="text" name="bengkel_pelaksana" x-model="formEdit.bengkel_pelaksana" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Pengerjaan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_perbaikan"
                            placeholder="-- Status Pengerjaan --"
                            :opsi="$opsiStatusPerbaikan"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.status_perbaikan"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keluhan / Kerusakan <span class="text-rose-500">*</span></label>
                    <textarea name="keluhan_kerusakan" x-model="formEdit.keluhan_kerusakan" rows="2" required
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tindakan Perbaikan <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="tindakan_perbaikan" x-model="formEdit.tindakan_perbaikan" rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-rupiah 
                            nama="biaya_jasa" 
                            label="Biaya Jasa (Rp)" 
                            modelBind="formEdit.biaya_jasa" 
                            placeholder="0" 
                        />
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="biaya_sparepart" 
                            label="Biaya Sparepart (Rp)" 
                            modelBind="formEdit.biaya_sparepart" 
                            placeholder="0" 
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pengawas Kendaraan <span class="text-rose-500">*</span></label>
                    <input type="text" name="pengawas_kendaraan" x-model="formEdit.pengawas_kendaraan" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail & Cetak SPK -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Surat Perintah Kerja (SPK) Servis Armada</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="cetakDokumenSPK()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak SPK</span>
                    </button>
                    <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Area Lembar SPK Cetak -->
            <div id="areaCetakSPK" class="p-6 space-y-4 text-xs">
                <div class="flex items-center justify-center gap-3 pb-3 border-b border-[#E2E8F0] dark:border-[#252837]">
                    <img src="{{ asset('images/logo-pbj.png') }}" alt="Logo PT Putra Balkom Jaya" class="w-12 h-12 object-contain shrink-0">
                    <div class="text-center">
                        <div class="font-bold text-base text-slate-900 dark:text-slate-100">PT PUTRA BALKOM JAYA</div>
                        <div class="text-[11px] text-slate-500">Divisi Operasional & Pemeliharaan Armada Truk Semen</div>
                        <div class="text-sm font-mono font-bold text-red-600 dark:text-red-400 mt-1 uppercase tracking-wider" x-text="'SURAT PERINTAH KERJA (SPK): ' + detailSPK.nomor_spk_perbaikan"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-mono block">Armada Truk</span>
                        <strong class="text-sm font-bold text-slate-900 dark:text-slate-100" x-text="detailSPK.kendaraan ? detailSPK.kendaraan.nama_aset : detailSPK.kode_aset"></strong>
                        <div class="text-[11px] text-slate-500 font-mono" x-text="'Kode: ' + detailSPK.kode_aset"></div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 uppercase font-mono block">Bengkel Pelaksana</span>
                        <strong class="text-xs font-bold text-slate-900 dark:text-slate-100" x-text="detailSPK.bengkel_pelaksana"></strong>
                        <div class="text-[11px] text-slate-500" x-text="'Tgl Masuk: ' + (detailSPK.tanggal_masuk_format || '-')"></div>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="p-3 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Keluhan / Indikasi Kerusakan:</span>
                        <p class="text-slate-800 dark:text-slate-200" x-text="detailSPK.keluhan_kerusakan"></p>
                    </div>

                    <div class="p-3 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block mb-1">Tindakan Perbaikan / Suku Cadang:</span>
                        <p class="text-slate-800 dark:text-slate-200" x-text="detailSPK.tindakan_perbaikan || 'Belum ada catatan tindakan teknisi.'"></p>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] space-y-1.5 font-mono">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Biaya Jasa Montir:</span>
                        <span class="text-slate-900 dark:text-slate-100 font-bold" x-text="detailSPK.biaya_jasa_rupiah"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Biaya Sparepart / Suku Cadang:</span>
                        <span class="text-slate-900 dark:text-slate-100 font-bold" x-text="detailSPK.biaya_sparepart_rupiah"></span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-[#E2E8F0] dark:border-[#252837] text-sm">
                        <strong class="text-slate-800 dark:text-slate-200">Total Biaya Perbaikan:</strong>
                        <strong class="text-emerald-600 dark:text-emerald-400" x-text="detailSPK.total_biaya_rupiah"></strong>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 text-center">
                    <div class="p-2 border border-[#E2E8F0] dark:border-[#252837] rounded-xl">
                        <div class="text-[10px] text-slate-400 mb-10">Pengawas Kendaraan:</div>
                        <div class="font-bold text-slate-900 dark:text-slate-100 border-t border-slate-300 dark:border-slate-700 pt-1" x-text="detailSPK.pengawas_kendaraan"></div>
                    </div>
                    <div class="p-2 border border-[#E2E8F0] dark:border-[#252837] rounded-xl">
                        <div class="text-[10px] text-slate-400 mb-10">Kepala Bengkel / Teknisi:</div>
                        <div class="font-bold text-slate-900 dark:text-slate-100 border-t border-slate-300 dark:border-slate-700 pt-1">( ........................................ )</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 8. MODAL KONFIRMASI HAPUS SPK PERBAIKAN -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus SPK Perbaikan?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus data SPK <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nomor"></strong>?
            </p>

            <form :action="'{{ url('operasional/bengkel/perbaikan') }}/' + hapusData.id" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus SPK
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika SPK Perbaikan Kendaraan -->
<script>
    function kelolaPerbaikanKendaraan() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalDetailTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodeSPK: 'Mode: Daur Ulang Slot Kosong',

            formTambah: {
                nomor_spk_perbaikan: '',
                kode_aset: '{{ $daftarKendaraan->first()->kode_aset ?? "" }}',
                tanggal_masuk: new Date().toISOString().slice(0, 10),
                tanggal_selesai: '',
                bengkel_pelaksana: 'Bengkel Internal PBJ Karawang',
                status_perbaikan: 'Dalam Proses',
                keluhan_kerusakan: '',
                tindakan_perbaikan: '',
                biaya_jasa: 0,
                biaya_sparepart: 0,
                pengawas_kendaraan: 'Bambang Supriyanto (Pengawas Kendaraan)'
            },

            formEdit: {
                id_perbaikan: '',
                nomor_spk_perbaikan: '',
                kode_aset: '',
                tanggal_masuk: '',
                tanggal_selesai: '',
                bengkel_pelaksana: '',
                status_perbaikan: '',
                keluhan_kerusakan: '',
                tindakan_perbaikan: '',
                biaya_jasa: 0,
                biaya_sparepart: 0,
                pengawas_kendaraan: ''
            },

            detailSPK: {},
            hapusData: { id: '', nomor: '' },

            get totalBiayaTambah() {
                return (parseFloat(this.formTambah.biaya_jasa) || 0) + (parseFloat(this.formTambah.biaya_sparepart) || 0);
            },

            initPerbaikan() {},

            bukaModalTambah() {
                this.buatNomorSPK('gap');
                this.modalTambahTerbuka = true;
            },

            async buatNomorSPK(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.bengkel.perbaikan.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambah.nomor_spk_perbaikan = data.kode_otomatis;
                        this.keteranganKodeSPK = data.keterangan || (mode === 'acak' ? 'Format Tanggal & Acak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat nomor SPK:', e);
                }
            },

            async bukaModalEdit(id) {
                try {
                    const res = await fetch(`{{ url('operasional/bengkel/perbaikan') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEdit = {
                            id_perbaikan: d.id_perbaikan,
                            nomor_spk_perbaikan: d.nomor_spk_perbaikan,
                            kode_aset: d.kode_kendaraan || d.kode_aset,
                            tanggal_masuk: d.tanggal_masuk ? d.tanggal_masuk.split('T')[0] : '',
                            tanggal_selesai: d.tanggal_selesai ? d.tanggal_selesai.split('T')[0] : '',
                            bengkel_pelaksana: d.bengkel_pelaksana,
                            status_perbaikan: d.status_perbaikan,
                            keluhan_kerusakan: d.keluhan_kerusakan,
                            tindakan_perbaikan: d.tindakan_perbaikan,
                            biaya_jasa: Math.round(parseFloat(d.biaya_jasa) || 0),
                            biaya_sparepart: Math.round(parseFloat(d.biaya_sparepart) || 0),
                            pengawas_kendaraan: d.pengawas_kendaraan
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data SPK perbaikan.');
                }
            },

            async bukaModalDetail(id) {
                try {
                    const res = await fetch(`{{ url('operasional/bengkel/perbaikan') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailSPK = data.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail SPK perbaikan.');
                }
            },

            async cetakSPK(id) {
                await this.bukaModalDetail(id);
                setTimeout(() => {
                    this.cetakDokumenSPK();
                }, 400);
            },

            cetakDokumenSPK() {
                const printContents = document.getElementById('areaCetakSPK').innerHTML;
                const originalContents = document.body.innerHTML;
                const win = window.open('', '', 'height=700,width=900');
                win.document.write('<html><head><title>Cetak SPK ' + (this.detailSPK.nomor_spk_perbaikan || '') + '</title>');
                win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">');
                win.document.write('</head><body class="p-8">');
                win.document.write(printContents);
                win.document.write('</body></html>');
                win.document.close();
                win.focus();
                setTimeout(() => { win.print(); win.close(); }, 500);
            },

            async ubahStatusCepat(id, statusBaru) {
                if (!confirm(`Ubah status pengerjaan SPK menjadi '${statusBaru}'?`)) return;
                try {
                    const res = await fetch(`{{ url('operasional/bengkel/perbaikan') }}/${id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status_perbaikan: statusBaru })
                    });
                    location.reload();
                } catch (e) {
                    alert('Gagal mengubah status SPK.');
                }
            },

            bukaModalHapus(id, nomor) {
                this.hapusData = { id: id, nomor: nomor };
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
