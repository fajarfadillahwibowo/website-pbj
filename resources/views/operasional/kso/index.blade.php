@extends('layouts.app')

@section('judul', 'Data KSO & Ongkos Angkut - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaDataKSO('{{ $tabAktif ?? 'kso' }}')" x-init="initKSO()" class="space-y-6">

    @php
        $opsiStatusKso = [
            ['nilai' => 'Aktif', 'label' => 'Kontrak Aktif'],
            ['nilai' => 'Selesai', 'label' => 'Kontrak Selesai'],
            ['nilai' => 'Ditangguhkan', 'label' => 'Ditangguhkan'],
        ];
        $opsiFilterStatusKso = [
            ['nilai' => 'semua', 'label' => 'Semua Status KSO'],
            ['nilai' => 'Aktif', 'label' => 'Kontrak Aktif'],
            ['nilai' => 'Selesai', 'label' => 'Kontrak Selesai'],
            ['nilai' => 'Ditangguhkan', 'label' => 'Ditangguhkan'],
        ];

        $opsiPilihanMitraKso = collect($pilihanMitraKso ?? [])->map(fn($pm) => [
            'nilai' => $pm->kode_kso,
            'label' => $pm->nama_kso . ' (' . $pm->kode_kso . ')',
            'sub'   => 'Mitra: ' . ($pm->pihak_mitra ?? '-')
        ])->values()->toArray();

        $opsiFilterMitraKso = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Mitra KSO']
        ], collect($pilihanMitraKso ?? [])->map(fn($pm) => [
            'nilai' => $pm->kode_kso,
            'label' => '[' . $pm->kode_kso . '] ' . $pm->nama_kso
        ])->values()->toArray());
    @endphp

    <!-- 1. Header Banner Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 font-mono">
                    SPV Operasional · Kemitraan Armada
                </span>
                <span class="text-xs text-slate-400 font-mono">Kerja Sama Operasional (KSO)</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data KSO & Tarif Ongkos Angkut</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Manajemen kontrak kemitraan operasional armada (KSO), dokumen legal kontrak, serta standarisasi tarif ongkos angkut per rute trayek.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Tombol Aksi Sesuai Tab Aktif -->
            <template x-if="tabAktif === 'kso'">
                <button @click="bukaModalTambahKso()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 rounded-xl transition-all shadow-md shadow-blue-600/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Mitra KSO</span>
                </button>
            </template>
            <template x-if="tabAktif === 'ongkos'">
                <button @click="bukaModalTambahOa()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 active:scale-95 rounded-xl transition-all shadow-md shadow-purple-600/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Tarif Ongkos KSO</span>
                </button>
            </template>
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
                <span>Terdapat kesalahan validasi data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. Ringkasan 4 Kartu KPI KSO -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Mitra KSO -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Mitra KSO</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalKso }} <span class="text-xs font-normal text-slate-400 font-sans">Mitra</span></div>
            </div>
        </div>

        <!-- Kontrak Aktif -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Kontrak Aktif</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $ksoAktif }} <span class="text-xs font-normal text-slate-400 font-sans">Mitra</span></div>
            </div>
        </div>

        <!-- Total Rute OA -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Rute Ongkos KSO</div>
                <div class="text-xl font-bold text-purple-600 dark:text-purple-400 font-mono mt-0.5">{{ $totalRuteOa }} <span class="text-xs font-normal text-slate-400 font-sans">Trayek</span></div>
            </div>
        </div>

        <!-- Total Nilai Kontrak Aktif -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Nilai Kontrak KSO</div>
                <div class="text-base font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">Rp {{ number_format($totalNilaiKontrak, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- 4. Tab Navigation (Data Mitra KSO vs Ongkos Angkut KSO) -->
    <div class="flex items-center gap-2 border-b border-[#E2E8F0] dark:border-[#252837] pb-1">
        <button @click="tabAktif = 'kso'"
                :class="tabAktif === 'kso' ? 'text-blue-600 dark:text-blue-400 border-blue-600 dark:border-blue-400 font-bold bg-blue-50/50 dark:bg-blue-500/10' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 border-transparent'"
                class="flex items-center gap-2 px-4 py-2.5 text-xs rounded-xl border-b-2 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Data Mitra KSO (<code>data_kso</code>)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-bold">{{ count($daftarKso) }}</span>
        </button>

        <button @click="tabAktif = 'ongkos'"
                :class="tabAktif === 'ongkos' ? 'text-purple-600 dark:text-purple-400 border-purple-600 dark:border-purple-400 font-bold bg-purple-50/50 dark:bg-purple-500/10' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 border-transparent'"
                class="flex items-center gap-2 px-4 py-2.5 text-xs rounded-xl border-b-2 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span>Tarif Ongkos Angkut KSO (<code>ongkos_kso</code>)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-bold">{{ count($daftarOngkosKso) }}</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. TABEL TAB 1: DATA MITRA KSO (data_kso) -->
    <!-- ========================================================================= -->
    <div x-show="tabAktif === 'kso'" class="space-y-4">
        <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
            
            <!-- Filter Bar Tab 1 -->
            <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
                <form method="GET" action="{{ route('operasional.kso') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                    <input type="hidden" name="tab" value="kso">
                    
                    <div class="relative flex-1">
                        <input type="text" name="cari_kso" value="{{ $cariKso ?? '' }}"
                               placeholder="Cari kode KSO, nama KSO, pihak mitra, keterangan..."
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Filter Status Dropdown Kustom -->
                    <div class="w-full sm:w-56">
                        <x-dropdown-kustom 
                            nama="status_kso"
                            placeholder="-- Semua Status --"
                            :opsi="$opsiFilterStatusKso"
                            :nilaiAwal="$statusKsoFilter ?? 'semua'"
                            :submitOnChange="true"
                            warnaFokus="blue"
                        />
                    </div>

                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                        Cari
                    </button>

                    @if(!empty($cariKso) || ($statusKsoFilter !== 'semua' && !empty($statusKsoFilter)))
                        <a href="{{ route('operasional.kso', ['tab' => 'kso']) }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-[11px] text-slate-400 font-mono">
                    Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarKso) }}</strong> Mitra KSO
                </div>
            </div>

            <!-- Tabel Data KSO -->
            <div class="overflow-x-auto">
                <table class="tabel-bertingkat w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode & Nama Mitra KSO</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Pihak Mitra & Keterangan</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Masa Berlaku Kontrak</th>
                            <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Nilai Kontrak</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Dokumen Kontrak</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                        @forelse($daftarKso as $kso)
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                
                                <!-- Kode & Nama KSO -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-blue-600 dark:text-blue-400 text-sm">
                                        {{ $kso->kode_kso }}
                                    </div>
                                    <div class="font-bold text-slate-900 dark:text-slate-100 text-xs mt-0.5">
                                        {{ $kso->nama_kso }}
                                    </div>
                                    <div class="text-[10px] text-purple-600 dark:text-purple-400 font-mono mt-0.5">
                                        {{ $kso->total_rute_oa }} Rute Ongkos Terdaftar
                                    </div>
                                </td>

                                <!-- Pihak Mitra & Keterangan -->
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $kso->pihak_mitra }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs mt-0.5">
                                        {{ $kso->keterangan ?: 'Kerja sama operasional armada logistik semen.' }}
                                    </div>
                                </td>

                                <!-- Periode Kontrak -->
                                <td class="px-4 py-3.5 whitespace-nowrap font-mono text-xs">
                                    <div>{{ $kso->tanggal_mulai ? $kso->tanggal_mulai->format('d/m/Y') : '-' }} s.d.</div>
                                    <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $kso->tanggal_selesai ? $kso->tanggal_selesai->format('d/m/Y') : '-' }}</div>
                                </td>

                                <!-- Nilai Kontrak -->
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                    {{ $kso->nilai_kontrak_rupiah }}
                                </td>

                                <!-- Dokumen File Kontrak -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    @if($kso->file_kontrak_url)
                                        <a href="{{ $kso->file_kontrak_url }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Lihat Kontrak</span>
                                        </a>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Tanpa File</span>
                                    @endif
                                </td>

                                <!-- Status KSO -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    @php $badge = $kso->status_badge; @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono border {{ $badge['bg'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>

                                <!-- Aksi & Riwayat Diedit Real-Time -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Detail -->
                                        <button @click="bukaModalDetailKso('{{ $kso->kode_kso }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                                title="Lihat Detail KSO & Rute Ongkos">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <!-- Edit -->
                                        <button @click="bukaModalEditKso('{{ $kso->kode_kso }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                title="Ubah Data Mitra KSO">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Hapus -->
                                        <button @click="bukaModalHapusKso('{{ $kso->kode_kso }}', '{{ $kso->nama_kso }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                title="Hapus Data Mitra KSO">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Riwayat Diedit Real-Time -->
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                         title="Terakhir diperbarui: {{ $kso->terakhir_diedit_waktu }}">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $kso->terakhir_diedit_relatif }}</span>
                                    </div>
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
                                        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada data kemitraan KSO</div>
                                        <div class="text-xs text-slate-400 mt-0.5">Daftarkan mitra operasional baru dengan tombol di kanan atas.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. TABEL TAB 2: TARIF ONGKOS ANGKUT KSO (ongkos_kso) -->
    <!-- ========================================================================= -->
    <div x-show="tabAktif === 'ongkos'" class="space-y-4">
        <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
            
            <!-- Filter Bar Tab 2 -->
            <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
                <form method="GET" action="{{ route('operasional.kso') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                    <input type="hidden" name="tab" value="ongkos">

                    <div class="relative flex-1">
                        <input type="text" name="cari_oa" value="{{ $cariOa ?? '' }}"
                               placeholder="Cari kode OA, rute trayek, kapasitas muatan, nama mitra..."
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Filter Mitra KSO Dropdown Kustom -->
                    <div class="w-full sm:w-60">
                        <x-dropdown-kustom 
                            nama="filter_kso"
                            placeholder="-- Semua Mitra KSO --"
                            :opsi="$opsiFilterMitraKso"
                            :nilaiAwal="$filterKso ?? 'semua'"
                            :submitOnChange="true"
                            warnaFokus="purple"
                        />
                    </div>

                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                        Cari
                    </button>

                    @if(!empty($cariOa) || ($filterKso !== 'semua' && !empty($filterKso)))
                        <a href="{{ route('operasional.kso', ['tab' => 'ongkos']) }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-[11px] text-slate-400 font-mono">
                    Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarOngkosKso) }}</strong> Rute Tarif OA KSO
                </div>
            </div>

            <!-- Tabel Ongkos Angkut KSO -->
            <div class="overflow-x-auto">
                <table class="tabel-bertingkat w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode OA & Rute Trayek</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Mitra KSO Penyelenggara</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kapasitas Muatan Armada</th>
                            <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Tarif Ongkos Angkut</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                        @forelse($daftarOngkosKso as $oa)
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                
                                <!-- Kode OA & Rute -->
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-purple-600 dark:text-purple-400 text-sm">
                                        {{ $oa->kode_oa }}
                                    </div>
                                    <div class="font-bold text-slate-900 dark:text-slate-100 text-xs mt-0.5">
                                        {{ $oa->nama_oa }}
                                    </div>
                                </td>

                                <!-- Mitra KSO -->
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $oa->mitraKso->nama_kso ?? $oa->kode_kso }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        Mitra: {{ $oa->mitraKso->pihak_mitra ?? '-' }} ({{ $oa->kode_kso }})
                                    </div>
                                </td>

                                <!-- Muatan -->
                                <td class="px-4 py-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono font-semibold">
                                        {{ $oa->muatan }}
                                    </span>
                                </td>

                                <!-- Tarif OA -->
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm whitespace-nowrap">
                                    {{ $oa->ongkos_angkut_rupiah }}
                                </td>

                                <!-- Aksi & Riwayat Diedit Real-Time -->
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit -->
                                        <button @click="bukaModalEditOa('{{ $oa->kode_oa }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                title="Ubah Tarif Ongkos KSO">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Hapus -->
                                        <button @click="bukaModalHapusOa('{{ $oa->kode_oa }}', '{{ $oa->nama_oa }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                title="Hapus Tarif Ongkos KSO">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Riwayat Diedit Real-Time -->
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                         title="Terakhir diperbarui: {{ $oa->terakhir_diedit_waktu }}">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $oa->terakhir_diedit_relatif }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada data tarif ongkos angkut KSO</div>
                                        <div class="text-xs text-slate-400 mt-0.5">Daftarkan rute ongkos angkut KSO baru dengan tombol di kanan atas.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- ========================================================================= -->
    <!-- 7. MODAL FORM: TAMBAH MITRA KSO (Tab 1) -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahKsoTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahKsoTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Daftarkan Mitra KSO Baru</h3>
                <button @click="modalTambahKsoTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.kso.simpan') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode KSO <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_kso" x-model="formKsoTambah.kode_kso" required placeholder="KSO-001"
                               class="w-full px-3 py-2 rounded-xl bg-blue-50/50 dark:bg-[#1C1E2A] border border-blue-200 dark:border-blue-900/50 text-blue-900 dark:text-blue-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status KSO <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_kso"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusKso"
                            :wajib="true"
                            warnaFokus="blue"
                            modelBind="formKsoTambah.status_kso"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama KSO <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_kso" x-model="formKsoTambah.nama_kso" required placeholder="KSO Armada Semen Sentosa"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pihak Mitra <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_mitra" x-model="formKsoTambah.pihak_mitra" required placeholder="PT Mitra Logistik Sentosa"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai Kontrak <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_mulai" 
                            modelBind="formKsoTambah.tanggal_mulai" 
                            placeholder="Pilih Tanggal Mulai"
                            :wajib="true"
                            warnaFokus="blue"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai Kontrak <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_selesai" 
                            modelBind="formKsoTambah.tanggal_selesai" 
                            placeholder="Pilih Tanggal Selesai"
                            :wajib="true"
                            warnaFokus="blue"
                        />
                    </div>
                </div>

                <div>
                    <x-input-rupiah 
                        nama="nilai_kontrak" 
                        label="Nilai Kontrak KSO (Rp)" 
                        modelBind="formKsoTambah.nilai_kontrak" 
                        placeholder="500.000.000" 
                    />
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Ruang Lingkup <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="keterangan" x-model="formKsoTambah.keterangan" rows="2" placeholder="Catatan kesepakatan armada, wilayah operasional..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Unggah Dokumen Kontrak (PDF/Doc/Gambar) <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <input type="file" name="file_kontrak_kso" accept=".pdf,.doc,.docx,image/*"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahKsoTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">Simpan Mitra KSO</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. MODAL FORM: EDIT MITRA KSO (Tab 1) -->
    <!-- ========================================================================= -->
    <div x-show="modalEditKsoTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditKsoTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Mitra KSO: <span class="font-mono text-blue-600" x-text="formKsoEdit.kode_kso"></span></h3>
                <button @click="modalEditKsoTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/kso') }}/' + formKsoEdit.kode_kso" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode KSO (Terkunci)</label>
                        <input type="text" :value="formKsoEdit.kode_kso" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status KSO <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_kso"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusKso"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formKsoEdit.status_kso"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama KSO <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_kso" x-model="formKsoEdit.nama_kso" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pihak Mitra <span class="text-rose-500">*</span></label>
                        <input type="text" name="pihak_mitra" x-model="formKsoEdit.pihak_mitra" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_mulai" 
                            modelBind="formKsoEdit.tanggal_mulai" 
                            placeholder="Pilih Tanggal Mulai"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_selesai" 
                            modelBind="formKsoEdit.tanggal_selesai" 
                            placeholder="Pilih Tanggal Selesai"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>

                <div>
                    <x-input-rupiah 
                        nama="nilai_kontrak" 
                        label="Nilai Kontrak (Rp)" 
                        modelBind="formKsoEdit.nilai_kontrak" 
                    />
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Ruang Lingkup <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="keterangan" x-model="formKsoEdit.keterangan" rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Ganti File Dokumen (Biarkan kosong jika tidak diubah) <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <input type="file" name="file_kontrak_kso" accept=".pdf,.doc,.docx,image/*"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900/30 dark:file:text-amber-300">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditKsoTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 9. MODAL DETAIL MITRA KSO (Tab 1) -->
    <!-- ========================================================================= -->
    <div x-show="modalDetailKsoTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailKsoTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Detail Mitra Kerja Sama Operasional (KSO)</h3>
                <button @click="modalDetailKsoTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] text-blue-600 dark:text-blue-400 font-mono block">Kode Mitra KSO</span>
                        <strong class="text-base font-mono font-bold text-blue-700 dark:text-blue-300" x-text="detailKso.kode_kso"></strong>
                    </div>
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono border"
                              :class="detailKso.status_badge ? detailKso.status_badge.bg : ''"
                              x-text="detailKso.status_badge ? detailKso.status_badge.label : detailKso.status_kso"></span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Nama KSO:</span>
                        <strong class="text-slate-900 dark:text-slate-100 text-right" x-text="detailKso.nama_kso"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Pihak Mitra:</span>
                        <strong class="text-slate-900 dark:text-slate-100 text-right" x-text="detailKso.pihak_mitra"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Masa Berlaku Kontrak:</span>
                        <strong class="font-mono text-slate-900 dark:text-slate-100" x-text="detailKso.tanggal_mulai + ' s.d. ' + detailKso.tanggal_selesai"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Nilai Kontrak:</span>
                        <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="detailKso.nilai_kontrak_rupiah"></strong>
                    </div>
                    <div class="py-1.5">
                        <span class="text-slate-500 block mb-1">Keterangan:</span>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] text-slate-800 dark:text-slate-200" x-text="detailKso.keterangan || 'Tidak ada catatan tambahan.'"></div>
                    </div>
                </div>

                <!-- Dokumen Kontrak -->
                <template x-if="detailKso.file_kontrak_url">
                    <div class="pt-2 border-t border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                        <span class="text-slate-500">File Dokumen Kontrak:</span>
                        <a :href="detailKso.file_kontrak_url" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Buka Dokumen PDF/Lampiran</span>
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 10. MODAL KONFIRMASI HAPUS MITRA KSO (Tab 1) -->
    <!-- ========================================================================= -->
    <div x-show="modalHapusKsoTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusKsoTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Mitra KSO?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus data kemitraan <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusKsoData.nama"></strong>? Seluruh rute ongkos angkut terkait juga akan terhapus.
            </p>

            <form :action="'{{ url('operasional/kso') }}/' + hapusKsoData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusKsoTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Mitra KSO
                </button>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 11. MODAL FORM: TAMBAH ONGKOS ANGKUT KSO (Tab 2) -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahOaTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahOaTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Tarif Ongkos Angkut KSO</h3>
                <button @click="modalTambahOaTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.kso.ongkos.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode OA KSO <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-purple-600 dark:text-purple-400 font-semibold px-1.5 py-0.5 bg-purple-50 dark:bg-purple-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_oa" x-model="formOaTambah.kode_oa" required placeholder="OAK-001"
                               class="w-full px-3 py-2 rounded-xl bg-purple-50/50 dark:bg-[#1C1E2A] border border-purple-200 dark:border-purple-900/50 text-purple-900 dark:text-purple-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Mitra KSO Penyelenggara <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_kso"
                            placeholder="-- Pilih Mitra KSO --"
                            :opsi="$opsiPilihanMitraKso"
                            :wajib="true"
                            warnaFokus="purple"
                            modelBind="formOaTambah.kode_kso"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Rute Trayek (Nama OA) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_oa" x-model="formOaTambah.nama_oa" required placeholder="Plant Cikarang ➔ Hub Karawang"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="muatan" x-model="formOaTambah.muatan" required placeholder="Tronton 30 Ton (600 Zak)"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="ongkos_angkut" 
                            label="Tarif Ongkos Angkut (Rp)" 
                            modelBind="formOaTambah.ongkos_angkut" 
                            :wajib="true" 
                            placeholder="1.850.000" 
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahOaTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition-all shadow-sm">Simpan Tarif OA</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 12. MODAL FORM: EDIT ONGKOS ANGKUT KSO (Tab 2) -->
    <!-- ========================================================================= -->
    <div x-show="modalEditOaTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditOaTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Tarif Ongkos Angkut: <span class="font-mono text-purple-600" x-text="formOaEdit.kode_oa"></span></h3>
                <button @click="modalEditOaTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/kso/ongkos') }}/' + formOaEdit.kode_oa" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode OA (Terkunci)</label>
                        <input type="text" :value="formOaEdit.kode_oa" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Mitra KSO Penyelenggara <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_kso"
                            placeholder="-- Pilih Mitra KSO --"
                            :opsi="$opsiPilihanMitraKso"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formOaEdit.kode_kso"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Rute Trayek (Nama OA) <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_oa" x-model="formOaEdit.nama_oa" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="muatan" x-model="formOaEdit.muatan" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="ongkos_angkut" 
                            label="Tarif Ongkos Angkut (Rp)" 
                            modelBind="formOaEdit.ongkos_angkut" 
                            :wajib="true" 
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditOaTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 13. MODAL KONFIRMASI HAPUS ONGKOS ANGKUT KSO (Tab 2) -->
    <!-- ========================================================================= -->
    <div x-show="modalHapusOaTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusOaTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Tarif Ongkos Angkut?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus rute <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusOaData.nama"></strong>?
            </p>

            <form :action="'{{ url('operasional/kso/ongkos') }}/' + hapusOaData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusOaTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Tarif OA
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika KSO & Ongkos KSO -->
<script>
    function kelolaDataKSO(tabAwal = 'kso') {
        return {
            tabAktif: tabAwal,

            // State Modal Tab 1 (Mitra KSO)
            modalTambahKsoTerbuka: false,
            modalEditKsoTerbuka: false,
            modalDetailKsoTerbuka: false,
            modalHapusKsoTerbuka: false,
            keteranganKodeKso: 'Mode: Daur Ulang Slot Kosong',

            formKsoTambah: {
                kode_kso: '',
                nama_kso: '',
                pihak_mitra: '',
                tanggal_mulai: new Date().toISOString().slice(0, 10),
                tanggal_selesai: new Date(new Date().setFullYear(new Date().getFullYear() + 1)).toISOString().slice(0, 10),
                nilai_kontrak: '',
                status_kso: 'Aktif',
                keterangan: ''
            },

            formKsoEdit: {
                kode_kso: '',
                nama_kso: '',
                pihak_mitra: '',
                tanggal_mulai: '',
                tanggal_selesai: '',
                nilai_kontrak: '',
                status_kso: 'Aktif',
                keterangan: ''
            },

            detailKso: {},
            hapusKsoData: { kode: '', nama: '' },

            // State Modal Tab 2 (Ongkos Angkut KSO)
            modalTambahOaTerbuka: false,
            modalEditOaTerbuka: false,
            modalHapusOaTerbuka: false,
            keteranganKodeOa: 'Mode: Daur Ulang Slot Kosong',

            formOaTambah: {
                kode_oa: '',
                kode_kso: '{{ $pilihanMitraKso->first()->kode_kso ?? "" }}',
                nama_oa: '',
                muatan: 'Tronton 30 Ton (600 Zak)',
                ongkos_angkut: ''
            },

            formOaEdit: {
                kode_oa: '',
                kode_kso: '',
                nama_oa: '',
                muatan: '',
                ongkos_angkut: ''
            },

            hapusOaData: { kode: '', nama: '' },

            initKSO() {},

            // Handler Tab 1: Mitra KSO
            bukaModalTambahKso() {
                this.buatKodeKSO('gap');
                this.modalTambahKsoTerbuka = true;
            },

            async buatKodeKSO(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.kso.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formKsoTambah.kode_kso = data.kode_otomatis;
                        this.keteranganKodeKso = data.keterangan || (mode === 'acak' ? 'Format Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode KSO:', e);
                }
            },

            async bukaModalEditKso(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/kso') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formKsoEdit = {
                            kode_kso: d.kode_kso,
                            nama_kso: d.nama_kso,
                            pihak_mitra: d.pihak_mitra,
                            tanggal_mulai: d.tanggal_mulai ? String(d.tanggal_mulai).split('T')[0] : '',
                            tanggal_selesai: d.tanggal_selesai ? String(d.tanggal_selesai).split('T')[0] : '',
                            nilai_kontrak: Math.round(parseFloat(d.nilai_kontrak) || 0),
                            status_kso: d.status_kso,
                            keterangan: d.keterangan || ''
                        };
                        this.modalEditKsoTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data Mitra KSO.');
                }
            },

            async bukaModalDetailKso(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/kso') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailKso = data.data;
                        this.modalDetailKsoTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail Mitra KSO.');
                }
            },

            bukaModalHapusKso(kode, nama) {
                this.hapusKsoData = { kode: kode, nama: nama };
                this.modalHapusKsoTerbuka = true;
            },

            // Handler Tab 2: Ongkos Angkut KSO
            bukaModalTambahOa() {
                this.buatKodeOA('gap');
                this.modalTambahOaTerbuka = true;
            },

            async buatKodeOA(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.kso.ongkos.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formOaTambah.kode_oa = data.kode_otomatis;
                        this.keteranganKodeOa = data.keterangan || (mode === 'acak' ? 'Format Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode OA:', e);
                }
            },

            async bukaModalEditOa(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/kso/ongkos') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formOaEdit = {
                            kode_oa: d.kode_oa,
                            kode_kso: d.kode_kso,
                            nama_oa: d.nama_oa,
                            muatan: d.muatan,
                            ongkos_angkut: Math.round(parseFloat(d.ongkos_angkut) || 0)
                        };
                        this.modalEditOaTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data Tarif Ongkos KSO.');
                }
            },

            bukaModalHapusOa(kode, nama) {
                this.hapusOaData = { kode: kode, nama: nama };
                this.modalHapusOaTerbuka = true;
            }
        };
    }
</script>
@endsection
