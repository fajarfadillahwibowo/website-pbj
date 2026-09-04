@extends('layouts.app')

@section('judul', 'Data Kendaraan & Jenis Aset - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaArmadaTerpadu('{{ $tabAktif ?? 'kendaraan' }}')" x-init="initArmada()" class="space-y-6">

    @php
        $opsiStatusKendaraan = [
            ['nilai' => 'aktif', 'label' => 'Aktif (Siap Jalan)'],
            ['nilai' => 'dalam_perbaikan', 'label' => 'Dalam Perbaikan (Bengkel)'],
            ['nilai' => 'rusak', 'label' => 'Rusak'],
            ['nilai' => 'non-aktif', 'label' => 'Non-Aktif'],
        ];
        $opsiStatusFilterKendaraan = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Status']
        ], $opsiStatusKendaraan);

        $opsiJenisAset = ($daftarJenisAset ?? collect())->map(fn($j) => [
            'nilai' => $j->kode_jenis_aset,
            'label' => $j->jenis_aset,
            'sub'   => 'Kode: ' . $j->kode_jenis_aset
        ])->toArray();
        $opsiJenisFilter = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Jenis Aset']
        ], $opsiJenisAset);
    @endphp

    <!-- 1. Header Modul & Navigasi Tab Terpadu -->
    <div class="animasi-masuk bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-500/20 font-mono">
                        Dispatcher & Pengawas Kendaraan
                    </span>
                    <span class="text-xs text-slate-400 font-mono">Modul Logistik Armada</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data Kendaraan & Jenis Aset</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Kelola spesifikasi unit armada truk logistik, perizinan KIR & Pajak STNK, serta klasifikasi kategori jenis aset armada.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <!-- Tombol Tambah Kendaraan (Ketika Tab Kendaraan Aktif) -->
                <button x-show="tabAktif === 'kendaraan'" @click="bukaModalTambahKendaraan()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-orange-600 hover:bg-orange-700 active:scale-95 rounded-xl transition-all shadow-md shadow-orange-600/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Kendaraan Baru</span>
                </button>

                <!-- Tombol Tambah Jenis Aset (Ketika Tab Jenis Aset Aktif) -->
                <template x-if="tabAktif === 'jenis_aset'">
                    <div class="flex items-center gap-2">
                        <button x-show="subTabJenisAset === 'aset'" @click="bukaModalTambahAset()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 rounded-xl transition-all shadow-md shadow-indigo-600/20">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Aset Perusahaan</span>
                        </button>
                        <button x-show="subTabJenisAset === 'kategori'" @click="bukaModalTambahJenisAset()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 active:scale-95 rounded-xl transition-all shadow-md shadow-violet-600/20">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Kategori</span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Tab Switcher Navigation -->
        <div class="flex items-center gap-2 border-t border-[#E2E8F0] dark:border-[#252837] pt-4">
            <button @click="gantiTab('kendaraan')"
                    :class="tabAktif === 'kendaraan' ? 'bg-orange-500 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300'"
                    class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                </svg>
                <span>Daftar Unit Kendaraan (Armada)</span>
                <span class="px-2 py-0.5 text-[10px] font-mono rounded-md font-bold"
                      :class="tabAktif === 'kendaraan' ? 'bg-orange-700/40 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'">
                    {{ $totalKendaraan }}
                </span>
            </button>

            <button @click="gantiTab('jenis_aset')"
                    :class="tabAktif === 'jenis_aset' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300'"
                    class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Data Jenis Aset (Aset Perusahaan)</span>
                <span class="px-2 py-0.5 text-[10px] font-mono rounded-md font-bold"
                      :class="tabAktif === 'jenis_aset' ? 'bg-indigo-800/40 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'">
                    {{ $totalAset }}
                </span>
            </button>
        </div>
    </div>

    <!-- 2. Flash Message / Notifikasi -->
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

    <!-- ========================================================================= -->
    <!-- TAB 1: KONTEN KENDARAAN (ARMADA TRUK) -->
    <!-- ========================================================================= -->
    <div x-show="tabAktif === 'kendaraan'" class="space-y-6">

        <!-- 3. Ringkasan Kartu KPI Armada -->
        <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Unit Armada -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Armada Truk</div>
                    <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalKendaraan }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
                </div>
            </div>

            <!-- Armada Aktif Siap Jalan -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Aktif (Siap Jalan)</div>
                    <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $kendaraanAktif }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
                </div>
            </div>

            <!-- Dalam Perbaikan Bengkel -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Servis / Bengkel</div>
                    <div class="text-xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">{{ $kendaraanServis }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
                </div>
            </div>

            <!-- Perhatian Pajak / KIR Hampir Habis -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Perlu Cek KIR/Pajak</div>
                    <div class="text-xl font-bold text-rose-600 dark:text-rose-400 font-mono mt-0.5">{{ $kendaraanPerhatianPajakKir }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
                </div>
            </div>
        </div>

        <!-- 4. Tabel Data Kendaraan & Bar Pencarian -->
        <div x-data="tabelPaginasi({ totalData: {{ count($daftarKendaraan ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
            <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
                <form method="GET" action="{{ route('operasional.armada.kendaraan') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-3xl">
                    <input type="hidden" name="tab" value="kendaraan">
                    <div class="relative flex-1">
                        <input type="text" name="cari" value="{{ ($tabAktif === 'kendaraan') ? ($kataKunci ?? '') : '' }}"
                               placeholder="Cari plat nomor, model truk, kode aset, merek, no mesin/rangka..."
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Jenis Aset Filter Dropdown Kustom -->
                    <div class="w-full sm:w-48">
                        <x-dropdown-kustom 
                            nama="jenis"
                            placeholder="-- Jenis Aset --"
                            :opsi="$opsiJenisFilter"
                            :nilaiAwal="$jenisFilter ?? 'semua'"
                            :submitOnChange="true"
                            warnaFokus="orange"
                        />
                    </div>

                    <!-- Status Filter Dropdown Kustom -->
                    <div class="w-full sm:w-48">
                        <x-dropdown-kustom 
                            nama="status"
                            placeholder="-- Status Armada --"
                            :opsi="$opsiStatusFilterKendaraan"
                            :nilaiAwal="$statusFilter ?? 'semua'"
                            :submitOnChange="true"
                            warnaFokus="orange"
                        />
                    </div>

                    @if(!empty($kataKunci) || ($statusFilter !== 'semua' && !empty($statusFilter)) || ($jenisFilter !== 'semua' && !empty($jenisFilter)))
                        <a href="{{ route('operasional.armada.kendaraan', ['tab' => 'kendaraan']) }}"
                           class="px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-[11px] text-slate-400 font-mono shrink-0">
                    Menampilkan <strong class="text-slate-700 dark:text-slate-300 font-bold">{{ count($daftarKendaraan) }}</strong> Armada
                </div>
            </div>

            <!-- Tabel Data Kendaraan -->
            <div class="overflow-x-auto">
                <table class="tabel-bertingkat w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Plat Nomor & Kode</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Nama Truk & Jenis Aset</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Merek & Muatan</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Mesin / Rangka</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Masa KIR & Pajak</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status Operasi</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                        @forelse($daftarKendaraan as $k)
                            <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors group">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900 dark:text-slate-100 text-sm px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 inline-block">
                                        {{ $k->no_polisi ?: '-' }}
                                    </div>
                                    <div class="text-[11px] font-mono text-orange-600 dark:text-orange-400 font-semibold mt-1">
                                        {{ $k->kode_kendaraan ?: $k->kode_aset }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-slate-100 text-sm">
                                        {{ $k->nama_aset }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                                        <span class="px-1.5 py-0.2 rounded bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 font-medium">
                                            {{ $k->jenisAset->jenis_aset ?? $k->kode_jenis_aset }}
                                        </span>
                                        <span>•</span>
                                        <span class="font-mono">Thn: {{ $k->tahun_pembuatan ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $k->merek_kendaraan ?: $k->merek_aset }}</div>
                                    <div class="font-mono text-slate-500 dark:text-slate-400 text-[11px] mt-0.5">{{ $k->muatan }}</div>
                                </td>

                                <td class="px-4 py-3.5 font-mono text-[11px] text-slate-600 dark:text-slate-400">
                                    <div><span class="text-slate-400">Msn:</span> {{ $k->no_mesin }}</div>
                                    <div class="mt-0.5"><span class="text-slate-400">Rkg:</span> {{ $k->no_rangka }}</div>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-center gap-1 text-[11px] font-mono">
                                            <span class="text-slate-400 text-[10px]">KIR:</span>
                                             @php $kir = $k->status_kir_info; @endphp
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $kir['warna'] === 'emerald' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : ($kir['warna'] === 'amber' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 font-bold animate-pulse' : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 font-bold') }}">
                                                {{ $kir['label'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-center gap-1 text-[11px] font-mono">
                                            <span class="text-slate-400 text-[10px]">Pjk:</span>
                                             @php $pjk = $k->status_pajak_info; @endphp
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $pjk['warna'] === 'emerald' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : ($pjk['warna'] === 'amber' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 font-bold animate-pulse' : 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 font-bold') }}">
                                                {{ $pjk['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    @php
                                        $statusK = $k->status_kendaraan ?: $k->status_aset;
                                        $warnaStatus = match($statusK) {
                                            'aktif' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                                            'dalam_perbaikan' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                                            'rusak' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                                        };
                                        $labelStatus = match($statusK) {
                                            'aktif' => 'Aktif (Siap Jalan)',
                                            'dalam_perbaikan' => 'Servis Bengkel',
                                            'rusak' => 'Rusak',
                                            default => 'Non-Aktif',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono border {{ $warnaStatus }}">
                                        {{ $labelStatus }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button @click="bukaModalDetailKendaraan('{{ $k->kode_kendaraan ?: $k->kode_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                                title="Lihat Spesifikasi & Detail Truk">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalEditKendaraan('{{ $k->kode_kendaraan ?: $k->kode_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                title="Ubah Data Kendaraan">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalHapusKendaraan('{{ $k->kode_kendaraan ?: $k->kode_aset }}', '{{ addslashes($k->nama_aset) }}', '{{ $k->no_polisi }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                title="Hapus Data Kendaraan">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Riwayat Diedit Real-Time -->
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                         title="Terakhir diperbarui: {{ $k->diperbarui_pada ? \Carbon\Carbon::parse($k->diperbarui_pada)->format('d/m/Y H:i:s') : '-' }}">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $k->diperbarui_pada ? \Carbon\Carbon::parse($k->diperbarui_pada)->locale('id')->diffForHumans() : 'Baru' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Tidak ada data kendaraan ditemukan</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginasi Terpadu -->
            <x-paginasi-tabel :totalData="count($daftarKendaraan ?? [])" />
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: KONTEN DATA JENIS ASET (ASET PERUSAHAAN) -->
    <!-- ========================================================================= -->
    <div x-show="tabAktif === 'jenis_aset'" class="space-y-6">
        
        <!-- Ringkasan Kartu KPI Aset Perusahaan (Selaras dengan Modul Aset Perusahaan SPV Keuangan) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] sm:text-[11px] font-medium text-slate-400 uppercase tracking-wider truncate">Total Nilai Perolehan</div>
                    <div class="text-sm sm:text-base lg:text-lg font-bold text-indigo-600 dark:text-indigo-400 font-mono mt-0.5 truncate">
                        Rp {{ number_format($totalNilaiAset ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] sm:text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Unit Aset</div>
                    <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">
                        {{ $totalAset ?? 0 }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] sm:text-[11px] font-medium text-slate-400 uppercase tracking-wider">Armada Truk Aktif</div>
                    <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">
                        {{ $totalTrukAktif ?? 0 }} <span class="text-xs font-normal text-slate-400 font-sans">Kendaraan</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[10px] sm:text-[11px] font-medium text-slate-400 uppercase tracking-wider">Kategori Jenis Aset</div>
                    <div class="text-xl font-bold text-violet-600 dark:text-violet-400 font-mono mt-0.5">
                        {{ count($daftarJenisAset) }} <span class="text-xs font-normal text-slate-400 font-sans">Kategori</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub-Navigasi View & Filter Bar -->
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
            <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                
                <!-- Sub-Tab Switcher (Inventaris Aset vs Master Kategori) -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-[#1C1E2A] rounded-xl shrink-0">
                    <button type="button" @click="subTabJenisAset = 'aset'"
                            :class="subTabJenisAset === 'aset' ? 'bg-white dark:bg-[#14161F] text-indigo-600 dark:text-indigo-400 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-medium'"
                            class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-2 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Inventaris Aset Perusahaan</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300">{{ $totalAset }}</span>
                    </button>

                    <button type="button" @click="subTabJenisAset = 'kategori'"
                            :class="subTabJenisAset === 'kategori' ? 'bg-white dark:bg-[#14161F] text-violet-600 dark:text-violet-400 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-medium'"
                            class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-2 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <span>Master Kategori (Tipe Truk)</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono bg-violet-50 dark:bg-violet-950/50 text-violet-700 dark:text-violet-300">{{ count($daftarJenisAset) }}</span>
                    </button>
                </div>

                <!-- Form Filter & Pencarian -->
                <form method="GET" action="{{ route('operasional.armada.kendaraan') }}" class="flex flex-wrap items-center gap-2 flex-1 lg:justify-end">
                    <input type="hidden" name="tab" value="jenis_aset">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <input type="text" name="cari" value="{{ ($tabAktif === 'jenis_aset') ? ($kataKunci ?? '') : '' }}"
                               placeholder="Cari nama aset, kode, plat nomor..."
                               class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="w-44">
                        <select name="jenis" onchange="this.form.submit()"
                                class="w-full px-3 py-1.5 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            <option value="semua">-- Semua Jenis Aset --</option>
                            @foreach($daftarSemuaJenis ?? [] as $j)
                                <option value="{{ $j->kode_jenis_aset }}" {{ ($jenisFilter === $j->kode_jenis_aset) ? 'selected' : '' }}>
                                    {{ $j->jenis_aset }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                        Cari
                    </button>

                    @if(!empty($kataKunci) || ($jenisFilter !== 'semua' && !empty($jenisFilter)))
                        <a href="{{ route('operasional.armada.kendaraan', ['tab' => 'jenis_aset']) }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- TAMPILAN 1: TABEL INVENTARIS ASET PERUSAHAAN (SESUAI FITUR SPV KEUANGAN) -->
            <div x-show="subTabJenisAset === 'aset'" class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode Aset</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Nama Aset & Spesifikasi</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kategori Jenis</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Plat / No. Polisi</th>
                            <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Harga Perolehan</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Tanggal Beli</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                        @forelse($daftarAsetPerusahaan ?? [] as $aset)
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg font-mono font-bold text-xs bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20">
                                        {{ $aset->kode_aset }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-slate-100 text-sm">
                                    {{ $aset->nama_aset }}
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $aset->jenis_aset ?? $aset->kode_jenis_aset }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap font-mono font-semibold">
                                    @if(!empty($aset->no_polisi) && $aset->no_polisi !== '-')
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                            {{ $aset->no_polisi }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 text-right font-mono tabular-nums font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                    Rp {{ number_format($aset->harga_aset, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3.5 text-center font-mono text-slate-500 whitespace-nowrap">
                                    {{ $aset->tanggal_pembelian ? date('d/m/Y', strtotime($aset->tanggal_pembelian)) : '-' }}
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button @click="bukaModalDetailAset('{{ $aset->kode_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                                title="Lihat Detail Aset">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalEditAset('{{ $aset->kode_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                title="Ubah Data Aset">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalHapusAset('{{ $aset->kode_aset }}', '{{ addslashes($aset->nama_aset) }}', '{{ $aset->no_polisi }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                title="Hapus Aset">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Riwayat Diedit Real-Time -->
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                         title="Terakhir diperbarui: {{ $aset->diperbarui_pada ? \Carbon\Carbon::parse($aset->diperbarui_pada)->format('d/m/Y H:i:s') : ($aset->dibuat_pada ? \Carbon\Carbon::parse($aset->dibuat_pada)->format('d/m/Y H:i:s') : '-') }}">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $aset->diperbarui_pada ? \Carbon\Carbon::parse($aset->diperbarui_pada)->locale('id')->diffForHumans() : ($aset->dibuat_pada ? \Carbon\Carbon::parse($aset->dibuat_pada)->locale('id')->diffForHumans() : 'Baru') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada aset tetap perusahaan tercatat</div>
                                        <p class="text-xs text-slate-400 mt-0.5">Klik tombol "Tambah Aset Perusahaan" untuk mendaftarkan aktiva tetap baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TAMPILAN 2: TABEL MASTER KATEGORI JENIS ASET -->
            <div x-show="subTabJenisAset === 'kategori'" class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode Jenis Aset</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Nama Kategori Jenis Aset</th>
                            <th class="px-4 py-3 font-semibold uppercase tracking-wider">Deskripsi & Spesifikasi Muatan</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Unit Armada Terdaftar</th>
                            <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                        @forelse($daftarJenisAset as $j)
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg font-mono font-bold text-xs bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-500/20">
                                        {{ $j->kode_jenis_aset }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-slate-100 text-sm whitespace-nowrap">
                                    {{ $j->jenis_aset }}
                                </td>

                                <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 max-w-md">
                                    {{ $j->keterangan ?: '-' }}
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-bold {{ $j->kendaraan_count > 0 ? 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
                                        <span>{{ $j->kendaraan_count }} Unit</span>
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button @click="bukaModalDetailJenisAset('{{ $j->kode_jenis_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                                title="Lihat Detail & Daftar Armada">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalEditJenisAset('{{ $j->kode_jenis_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                title="Ubah Kategori Jenis Aset">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalHapusJenisAset('{{ $j->kode_jenis_aset }}', '{{ addslashes($j->jenis_aset) }}', {{ $j->kendaraan_count }})"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                title="Hapus Kategori">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Riwayat Diedit Real-Time -->
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                         title="Terakhir diperbarui: {{ $j->diperbarui_pada ? \Carbon\Carbon::parse($j->diperbarui_pada)->format('d/m/Y H:i:s') : '-' }}">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $j->diperbarui_pada ? \Carbon\Carbon::parse($j->diperbarui_pada)->locale('id')->diffForHumans() : 'Baru' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Tidak ada jenis aset ditemukan</div>
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
    <!-- MODAL-MODAL KENDARAAN (TAMBAH, EDIT, DETAIL, HAPUS) -->
    <!-- ========================================================================= -->
    <!-- Modal Tambah Kendaraan -->
    <div x-show="modalTambahKendaraanTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahKendaraanTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Unit Kendaraan Baru</h3>
                <button @click="modalTambahKendaraanTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.armada.kendaraan.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Kendaraan</label>
                            <span class="text-[10px] text-orange-600 dark:text-orange-400 font-semibold px-1.5 py-0.5 bg-orange-50 dark:bg-orange-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_kendaraan" x-model="formTambahKendaraan.kode_kendaraan" required placeholder="KND-001"
                               class="w-full px-3 py-2 rounded-xl bg-orange-50/50 dark:bg-[#1C1E2A] border border-orange-200 dark:border-orange-900/50 text-orange-900 dark:text-orange-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Aset Truk</label>
                        <x-dropdown-kustom 
                            nama="kode_jenis_aset"
                            placeholder="-- Pilih Jenis Aset --"
                            :opsi="$opsiJenisAset"
                            :wajib="true"
                            warnaFokus="orange"
                            modelBind="formTambahKendaraan.kode_jenis_aset"
                        />
                    </div>
                </div>

                <div class="col-span-2">
                    <x-input-plat-nomor 
                        nama="no_polisi" 
                        modelBind="formTambahKendaraan.no_polisi" 
                        :wajib="true" 
                        label="Nomor Plat Polisi Kendaraan"
                    />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Model Truk</label>
                        <input type="text" name="nama_aset" x-model="formTambahKendaraan.nama_aset" required placeholder="Hino 500 Tronton Wingbox"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek Truk</label>
                        <input type="text" name="merek_kendaraan" x-model="formTambahKendaraan.merek_kendaraan" required placeholder="Hino / Mitsubishi / Isuzu"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan</label>
                        <input type="text" name="muatan" x-model="formTambahKendaraan.muatan" required placeholder="25 Ton (500 Zak)"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="harga_aset" 
                            label="Harga Perolehan Unit (Rp)" 
                            modelBind="formTambahKendaraan.harga_aset" 
                            :wajib="true" 
                            placeholder="850.000.000"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_pembelian" 
                            modelBind="formTambahKendaraan.tanggal_pembelian" 
                            placeholder="Pilih Tanggal Beli"
                            :wajib="true"
                            warnaFokus="orange"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pembuatan <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_pembuatan" x-model="formTambahKendaraan.tahun_pembuatan" required min="1990" max="2099" placeholder="2022"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Mesin <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_mesin" x-model="formTambahKendaraan.no_mesin" required placeholder="J08E-UG-12948"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-orange-500/30 font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rangka (VIN) <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_rangka" x-model="formTambahKendaraan.no_rangka" required placeholder="MJECFL8J7N-039182"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-orange-500/30 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Legal <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_pemilik" x-model="formTambahKendaraan.nama_pemilik" required placeholder="PT Putra Balkom Jaya"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_kendaraan"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusKendaraan"
                            :wajib="true"
                            warnaFokus="orange"
                            modelBind="formTambahKendaraan.status_kendaraan"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahKendaraanTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-xl transition-all shadow-sm">Simpan Kendaraan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kendaraan -->
    <div x-show="modalEditKendaraanTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditKendaraanTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Kendaraan: <span class="font-mono text-amber-600" x-text="formEditKendaraan.kode_kendaraan || formEditKendaraan.kode_aset"></span></h3>
                <button @click="modalEditKendaraanTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/armada/kendaraan') }}/' + (formEditKendaraan.kode_kendaraan || formEditKendaraan.kode_aset)" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="col-span-2">
                    <x-input-plat-nomor 
                        nama="no_polisi" 
                        modelBind="formEditKendaraan.no_polisi" 
                        :wajib="true" 
                        label="Nomor Plat Polisi Kendaraan"
                    />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Model Truk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_aset" x-model="formEditKendaraan.nama_aset" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Aset Truk <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_jenis_aset"
                            placeholder="-- Pilih Jenis Aset --"
                            :opsi="$opsiJenisAset"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEditKendaraan.kode_jenis_aset"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek Truk <span class="text-rose-500">*</span></label>
                        <input type="text" name="merek_kendaraan" x-model="formEditKendaraan.merek_kendaraan" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="muatan" x-model="formEditKendaraan.muatan" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-rupiah 
                            nama="harga_aset" 
                            label="Harga Perolehan Unit (Rp)" 
                            modelBind="formEditKendaraan.harga_aset" 
                            :wajib="true" 
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_pembelian" 
                            modelBind="formEditKendaraan.tanggal_pembelian" 
                            placeholder="Pilih Tanggal Beli"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pembuatan <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_pembuatan" x-model="formEditKendaraan.tahun_pembuatan" required min="1990" max="2099"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_kendaraan"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusKendaraan"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEditKendaraan.status_kendaraan"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Mesin <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_mesin" x-model="formEditKendaraan.no_mesin" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rangka (VIN) <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_rangka" x-model="formEditKendaraan.no_rangka" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Legal <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pemilik" x-model="formEditKendaraan.nama_pemilik" required placeholder="PT Putra Balkom Jaya"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditKendaraanTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Kendaraan -->
    <div x-show="modalDetailKendaraanTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailKendaraanTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-bold font-mono text-sm">
                        <span x-text="(detailKendaraan.kode_kendaraan || detailKendaraan.kode_aset) ? (detailKendaraan.kode_kendaraan || detailKendaraan.kode_aset).substring(0,3) : 'KND'"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100" x-text="detailKendaraan.nama_aset"></h2>
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-xs bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-slate-100"
                                  x-text="detailKendaraan.no_polisi"></span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-mono" x-text="detailKendaraan.kode_kendaraan || detailKendaraan.kode_aset"></p>
                    </div>
                </div>
                <button @click="modalDetailKendaraanTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 p-4 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Jenis Aset</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailKendaraan.jenis_aset ? detailKendaraan.jenis_aset.jenis_aset : (detailKendaraan.kode_jenis_aset || '-')"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Merek & Tahun</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="(detailKendaraan.merek_kendaraan || detailKendaraan.merek_aset || '-') + ' (' + (detailKendaraan.tahun_pembuatan || '-') + ')'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Kapasitas Muatan</div>
                        <div class="font-mono font-bold text-orange-600 dark:text-orange-400 mt-0.5" x-text="detailKendaraan.muatan || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Nilai Perolehan</div>
                        <div class="font-mono font-bold text-emerald-600 dark:text-emerald-400 mt-0.5" x-text="detailKendaraan.harga_aset_rupiah || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Tanggal Beli</div>
                        <div class="font-mono text-slate-700 dark:text-slate-300 mt-0.5" x-text="detailKendaraan.tanggal_pembelian || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Status Operasi</div>
                        <div class="mt-0.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20"
                                  x-text="detailKendaraan.status_kendaraan || detailKendaraan.status_aset"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="p-3.5 rounded-xl border border-[#E2E8F0] dark:border-[#252837] space-y-2">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Identifikasi Mesin</div>
                        <div class="font-mono text-[11px] space-y-1">
                            <div><span class="text-slate-400">Nomor Mesin:</span> <strong class="text-slate-800 dark:text-slate-200" x-text="detailKendaraan.no_mesin"></strong></div>
                            <div><span class="text-slate-400">Nomor Rangka:</span> <strong class="text-slate-800 dark:text-slate-200" x-text="detailKendaraan.no_rangka"></strong></div>
                            <div><span class="text-slate-400">Pemilik:</span> <span class="text-slate-700 dark:text-slate-300" x-text="detailKendaraan.nama_pemilik"></span></div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-[#E2E8F0] dark:border-[#252837] space-y-2">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Perizinan & Legalitas</div>
                        <div class="font-mono text-[11px] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Uji KIR Dishub:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="detailKendaraan.tanggal_kir || 'Belum Diatur'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Pajak STNK:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200" x-text="detailKendaraan.tanggal_pajak || 'Belum Diatur'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end px-6 py-3.5 border-t border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <button @click="modalDetailKendaraanTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Kendaraan -->
    <div x-show="modalHapusKendaraanTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusKendaraanTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Unit Kendaraan?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus armada <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusKendaraanData.nama"></strong> (<span class="font-mono font-bold" x-text="hapusKendaraanData.plat"></span>)?
            </p>

            <form :action="'{{ url('operasional/armada/kendaraan') }}/' + hapusKendaraanData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')
                <input type="hidden" name="kode_kendaraan" :value="hapusKendaraanData.kode">
                <button type="button" @click="modalHapusKendaraanTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Armada
                </button>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL-MODAL JENIS ASET (TAMBAH, EDIT, DETAIL, HAPUS) -->
    <!-- ========================================================================= -->

    <!-- Modal Tambah Jenis Aset -->
    <div x-show="modalTambahJenisAsetTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalTambahJenisAsetTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Tambah Jenis Aset Baru</h2>
                        <p class="text-[11px] text-slate-400">Buat klasifikasi tipe armada truk baru untuk sistem.</p>
                    </div>
                </div>
                <button @click="modalTambahJenisAsetTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('operasional.armada.kendaraan.jenis_aset.simpan') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div>
                            <label class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                Kode Jenis Aset <span class="text-rose-500">*</span>
                            </label>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="keteranganKodeJenis"></div>
                        </div>
                        
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="buatKodeJenisOtomatis('gap')"
                                    class="px-2 py-1 text-[10px] font-semibold text-violet-700 dark:text-violet-300 bg-violet-100 dark:bg-violet-900/30 hover:bg-violet-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Daur Ulang</span>
                            </button>
                            <button type="button" @click="buatKodeJenisOtomatis('acak')"
                                    class="px-2 py-1 text-[10px] font-semibold text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Kode Acak</span>
                            </button>
                        </div>
                    </div>

                    <input type="text" name="kode_jenis_aset" x-model="formTambahJenisAset.kode_jenis_aset" required placeholder="Contoh: KND-TRN atau JNS-001"
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-violet-600 dark:text-violet-400 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Jenis / Kategori Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="jenis_aset" x-model="formTambahJenisAset.jenis_aset" required placeholder="Contoh: Truk Tronton Wingbox"
                           class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan & Spesifikasi Muatan <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" x-model="formTambahJenisAset.keterangan" rows="3" required placeholder="Contoh: Kapasitas 25 - 30 Ton (500 - 600 Zak Semen)"
                              class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalTambahJenisAsetTerbuka = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 active:scale-95 rounded-xl transition-all shadow-md shadow-violet-600/20">
                        Simpan Jenis Aset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jenis Aset -->
    <div x-show="modalEditJenisAsetTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalEditJenisAsetTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Ubah Data Jenis Aset</h2>
                        <p class="text-[11px] text-slate-400">Kode: <span class="font-mono font-bold text-violet-600 dark:text-violet-400" x-text="formEditJenisAset.kode_jenis_aset"></span></p>
                    </div>
                </div>
                <button @click="modalEditJenisAsetTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('operasional/armada/kendaraan-jenis-aset') }}/' + formEditJenisAset.kode_jenis_aset" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Jenis Aset (Terkunci)</label>
                    <input type="text" :value="formEditJenisAset.kode_jenis_aset" disabled
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Jenis / Kategori Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="jenis_aset" x-model="formEditJenisAset.jenis_aset" required
                           class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan & Spesifikasi Muatan <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" x-model="formEditJenisAset.keterangan" rows="3" required
                              class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalEditJenisAsetTerbuka = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-xl transition-all shadow-md shadow-amber-600/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Jenis Aset -->
    <div x-show="modalDetailJenisAsetTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailJenisAsetTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center font-bold font-mono text-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100" x-text="detailJenisAset.jenis_aset"></h2>
                        <p class="text-[11px] text-slate-400 font-mono" x-text="detailJenisAset.kode_jenis_aset"></p>
                    </div>
                </div>
                <button @click="modalDetailJenisAsetTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <div class="p-4 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Deskripsi & Spesifikasi</div>
                    <p class="text-slate-800 dark:text-slate-200" x-text="detailJenisAset.keterangan || '-'"></p>
                </div>

                <div>
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mb-2 flex items-center justify-between">
                        <span>Daftar Unit Truk Terhubung:</span>
                        <span class="text-[11px] text-orange-600 dark:text-orange-400 font-mono font-bold"
                              x-text="(detailJenisAset.kendaraan ? detailJenisAset.kendaraan.length : 0) + ' Unit'"></span>
                    </div>

                    <div class="max-h-56 overflow-y-auto border border-[#E2E8F0] dark:border-[#252837] rounded-xl">
                        <template x-if="detailJenisAset.kendaraan && detailJenisAset.kendaraan.length > 0">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] text-slate-500 font-semibold border-b border-[#E2E8F0] dark:border-[#252837]">
                                    <tr>
                                        <th class="px-3 py-2">Plat Nomor</th>
                                        <th class="px-3 py-2">Nama Truk</th>
                                        <th class="px-3 py-2">Merek</th>
                                        <th class="px-3 py-2">Muatan</th>
                                        <th class="px-3 py-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837]">
                                    <template x-for="truk in detailJenisAset.kendaraan" :key="truk.kode_aset">
                                        <tr class="hover:bg-slate-50 dark:hover:bg-[#1C1E2A]/50">
                                            <td class="px-3 py-2 font-mono font-bold text-slate-900 dark:text-slate-100" x-text="truk.no_polisi"></td>
                                            <td class="px-3 py-2 font-medium text-slate-800 dark:text-slate-200" x-text="truk.nama_aset"></td>
                                            <td class="px-3 py-2 text-slate-600 dark:text-slate-400" x-text="truk.merek_aset"></td>
                                            <td class="px-3 py-2 font-mono text-slate-600 dark:text-slate-400" x-text="truk.muatan"></td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400"
                                                      x-text="truk.status_aset"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </template>
                        <template x-if="!detailJenisAset.kendaraan || detailJenisAset.kendaraan.length === 0">
                            <div class="p-6 text-center text-slate-400 text-xs">
                                Belum ada armada truk yang terdaftar menggunakan kategori jenis aset ini.
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end px-6 py-3.5 border-t border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <button @click="modalDetailJenisAsetTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Jenis Aset -->
    <div x-show="modalHapusJenisAsetTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusJenisAsetTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Jenis Aset?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus kategori <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusJenisAsetData.nama"></strong> (<span class="font-mono font-bold" x-text="hapusJenisAsetData.kode"></span>)?
            </p>

            <template x-if="hapusJenisAsetData.jumlahTruk > 0">
                <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-300 text-[11px] text-left">
                    ⚠️ <strong>Perhatian:</strong> Kategori ini memiliki <span class="font-bold font-mono" x-text="hapusJenisAsetData.jumlahTruk"></span> unit truk yang terhubung di Data Kendaraan.
                </div>
            </template>

            <form :action="'{{ url('operasional/armada/kendaraan-jenis-aset') }}/' + hapusJenisAsetData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')
                <button type="button" @click="modalHapusJenisAsetTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Kategori
                </button>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL-MODAL ASET PERUSAHAAN (TAMBAH, DETAIL, EDIT, HAPUS) -->
    <!-- Sesuai dan Selaras dengan Sidebar Aset Perusahaan SPV Keuangan -->
    <!-- ========================================================================= -->

    <!-- Modal Tambah Aset Perusahaan -->
    <div x-show="modalTambahAsetTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahAsetTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Aset Perusahaan Baru</h2>
                        <p class="text-[11px] text-slate-400">Sinkronisasi Database Aktiva Tetap (data_aset)</p>
                    </div>
                </div>
                <button @click="modalTambahAsetTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('operasional.armada.kendaraan.aset.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Aset <span class="text-rose-500">*</span></label>
                            <button type="button" @click="buatKodeAsetOtomatis('acak')" class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline">Acak</button>
                        </div>
                        <input type="text" name="kode_aset" x-model="formTambahAset.kode_aset" required placeholder="AST-001"
                               class="w-full px-3 py-2 rounded-xl bg-indigo-50/50 dark:bg-[#1C1E2A] border border-indigo-200 dark:border-indigo-900/50 text-indigo-900 dark:text-indigo-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Aset <span class="text-rose-500">*</span></label>
                        <select name="kode_jenis_aset" x-model="formTambahAset.kode_jenis_aset" required
                                class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            @foreach($daftarSemuaJenis ?? [] as $j)
                                <option value="{{ $j->kode_jenis_aset }}">{{ $j->jenis_aset }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Aset & Spesifikasi <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_aset" x-model="formTambahAset.nama_aset" required placeholder="Contoh: Hino Dutro 130 HD / Forklift Toyota"
                           class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>

                <div class="space-y-3">
                    <x-input-plat-nomor 
                        nama="no_polisi" 
                        modelBind="formTambahAset.no_polisi" 
                        :wajib="false" 
                        label="Plat / No. Polisi Kendaraan" 
                    />

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Aset</label>
                        <select name="status_aset" x-model="formTambahAset.status_aset"
                                class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            <option value="aktif">Aktif</option>
                            <option value="dalam_perbaikan">Dalam Perbaikan</option>
                            <option value="rusak">Rusak</option>
                            <option value="non-aktif">Non-Aktif</option>
                            <option value="dijual">Dijual</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_pembelian" x-model="formTambahAset.tanggal_pembelian" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Perolehan (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_aset"
                            modelBind="formTambahAset.harga_aset"
                            placeholder="385.000.000"
                            :wajib="true"
                            warnaFokus="indigo"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalTambahAsetTerbuka = false"
                            class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-md shadow-indigo-600/20">
                        Simpan Aset Perusahaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Aset Perusahaan -->
    <div x-show="modalDetailAsetTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailAsetTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100" x-text="detailAset.nama_aset"></h2>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-mono font-semibold" x-text="detailAset.kode_aset"></p>
                    </div>
                </div>
                <button @click="modalDetailAsetTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Kategori Jenis</div>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.jenis_aset || detailAset.kode_jenis_aset"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Nomor Polisi</div>
                        <div class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.no_polisi || '-'"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Harga Perolehan</div>
                        <div class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 mt-0.5" x-text="'Rp ' + Number(detailAset.harga_aset || 0).toLocaleString('id-ID')"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Tanggal Pembelian</div>
                        <div class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.tanggal_pembelian || '-'"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Status Operasional</div>
                        <div class="text-xs font-bold uppercase mt-0.5 text-emerald-600 dark:text-emerald-400" x-text="detailAset.status_aset || 'aktif'"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Entitas Pemilik</div>
                        <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.nama_pemilik || 'PT Putra Balkom Jaya'"></div>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button @click="modalDetailAsetTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Aset Perusahaan -->
    <div x-show="modalEditAsetTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditAsetTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Aset Perusahaan</h3>
                <button @click="modalEditAsetTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('operasional/armada/kendaraan-aset') }}/' + formEditAset.kode_aset" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Aset (Terkunci)</label>
                        <input type="text" :value="formEditAset.kode_aset" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Jenis <span class="text-rose-500">*</span></label>
                        <select name="kode_jenis_aset" x-model="formEditAset.kode_jenis_aset" required
                                class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            @foreach($daftarSemuaJenis ?? [] as $j)
                                <option value="{{ $j->kode_jenis_aset }}">{{ $j->jenis_aset }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_aset" x-model="formEditAset.nama_aset" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
                <div class="space-y-3">
                    <x-input-plat-nomor 
                        nama="no_polisi" 
                        modelBind="formEditAset.no_polisi" 
                        :wajib="false" 
                        label="Plat / No. Polisi Kendaraan" 
                    />

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Aset</label>
                        <select name="status_aset" x-model="formEditAset.status_aset"
                                class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            <option value="aktif">Aktif</option>
                            <option value="dalam_perbaikan">Dalam Perbaikan</option>
                            <option value="rusak">Rusak</option>
                            <option value="non-aktif">Non-Aktif</option>
                            <option value="dijual">Dijual</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_pembelian" x-model="formEditAset.tanggal_pembelian" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Perolehan (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_aset"
                            modelBind="formEditAset.harga_aset"
                            placeholder="385.000.000"
                            :wajib="true"
                            warnaFokus="indigo"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="modalEditAsetTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Aset Perusahaan -->
    <div x-show="modalHapusAsetTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusAsetTerbuka = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 text-center mb-1">Hapus Aset Perusahaan?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 text-center mb-4 leading-relaxed">
                Anda akan menghapus aset <strong class="text-slate-800 dark:text-slate-200" x-text="hapusAsetData.nama"></strong> (<span class="font-mono text-indigo-600" x-text="hapusAsetData.kode"></span>). Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="'{{ url('operasional/armada/kendaraan-aset') }}/' + hapusAsetData.kode" method="POST" class="flex items-center justify-center gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="modalHapusAsetTerbuka = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-md shadow-rose-600/20">Ya, Hapus Aset</button>
            </form>
        </div>
    </div>

</div>

<!-- Alpine.js Master State & Logic -->
<script>
    function kelolaArmadaTerpadu(initialTab = 'kendaraan') {
        return {
            tabAktif: initialTab,
            subTabJenisAset: 'aset',

            // Modals Kendaraan
            modalTambahKendaraanTerbuka: false,
            modalEditKendaraanTerbuka: false,
            modalDetailKendaraanTerbuka: false,
            modalHapusKendaraanTerbuka: false,

            // Modals Jenis Aset (Kategori)
            modalTambahJenisAsetTerbuka: false,
            modalEditJenisAsetTerbuka: false,
            modalDetailJenisAsetTerbuka: false,
            modalHapusJenisAsetTerbuka: false,

            // Modals Aset Perusahaan (Selaras SPV Keuangan)
            modalTambahAsetTerbuka: false,
            modalEditAsetTerbuka: false,
            modalDetailAsetTerbuka: false,
            modalHapusAsetTerbuka: false,

            keteranganKodeKendaraan: 'Mode: Daur Ulang Slot Kosong',
            keteranganKodeJenis: 'Mode: Daur Ulang Slot Kosong',
            keteranganKodeAset: 'Mode: Daur Ulang Slot Kosong',

            formTambahKendaraan: {
                kode_kendaraan: '',
                kode_aset: '',
                kode_jenis_aset: '{{ $daftarSemuaJenis->first()->kode_jenis_aset ?? "" }}',
                nama_aset: '',
                no_polisi: '',
                merek_kendaraan: 'Hino',
                merek_aset: 'Hino',
                muatan: '25 Ton (500 Zak)',
                harga_aset: 1200000000,
                tanggal_pembelian: new Date().toISOString().split('T')[0],
                tahun_pembuatan: new Date().getFullYear(),
                no_mesin: '',
                no_rangka: '',
                nama_pemilik: 'PT Putra Balkom Jaya',
                tanggal_kir: '',
                tanggal_pajak: '',
                status_kendaraan: 'aktif',
                status_aset: 'aktif'
            },

            formEditKendaraan: {
                kode_kendaraan: '',
                kode_aset: '',
                kode_jenis_aset: '',
                nama_aset: '',
                no_polisi: '',
                merek_kendaraan: '',
                merek_aset: '',
                muatan: '',
                harga_aset: 0,
                tanggal_pembelian: '',
                tahun_pembuatan: '',
                no_mesin: '',
                no_rangka: '',
                nama_pemilik: 'PT Putra Balkom Jaya',
                tanggal_kir: '',
                tanggal_pajak: '',
                status_kendaraan: 'aktif',
                status_aset: 'aktif'
            },

            formTambahJenisAset: {
                kode_jenis_aset: '',
                jenis_aset: '',
                keterangan: ''
            },

            formEditJenisAset: {
                kode_jenis_aset: '',
                jenis_aset: '',
                keterangan: ''
            },

            // Form Aset Perusahaan
            formTambahAset: {
                kode_aset: '',
                kode_jenis_aset: '{{ $daftarSemuaJenis->first()->kode_jenis_aset ?? "" }}',
                nama_aset: '',
                no_polisi: '',
                tanggal_pembelian: new Date().toISOString().split('T')[0],
                harga_aset: 350000000,
                status_aset: 'aktif'
            },

            formEditAset: {
                kode_aset: '',
                kode_jenis_aset: '',
                nama_aset: '',
                no_polisi: '',
                tanggal_pembelian: '',
                harga_aset: 0,
                status_aset: 'aktif'
            },

            detailKendaraan: {},
            hapusKendaraanData: { kode: '', nama: '', plat: '' },

            detailJenisAset: {},
            hapusJenisAsetData: { kode: '', nama: '', jumlahTruk: 0 },

            detailAset: {},
            hapusAsetData: { kode: '', nama: '', plat: '' },

            initArmada() {
                // Inisialisasi
            },

            gantiTab(namaTab) {
                this.tabAktif = namaTab;
                const url = new URL(window.location);
                url.searchParams.set('tab', namaTab);
                window.history.replaceState({}, '', url);
            },

            // --- Handler Kendaraan ---
            bukaModalTambahKendaraan() {
                this.formTambahKendaraan.nama_aset = '';
                this.formTambahKendaraan.no_polisi = '';
                this.formTambahKendaraan.no_mesin = '';
                this.formTambahKendaraan.no_rangka = '';
                this.buatKodeKendaraanOtomatis('gap');
                this.modalTambahKendaraanTerbuka = true;
            },

            async buatKodeKendaraanOtomatis(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.armada.kendaraan.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambahKendaraan.kode_kendaraan = data.kode_otomatis;
                        this.formTambahKendaraan.kode_aset = data.kode_otomatis;
                        this.keteranganKodeKendaraan = data.keterangan || (mode === 'acak' ? 'Mode: Kode Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode kendaraan:', e);
                }
            },

            async bukaModalDetailKendaraan(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/armada/kendaraan') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailKendaraan = data.data;
                        this.modalDetailKendaraanTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail spesifikasi kendaraan.');
                }
            },

            async bukaModalEditKendaraan(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/armada/kendaraan') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEditKendaraan = {
                            kode_kendaraan: d.kode_kendaraan || d.kode_aset || kode,
                            kode_aset: d.kode_aset || d.kode_kendaraan || kode,
                            kode_jenis_aset: d.kode_jenis_aset || (d.aset_perusahaan ? d.aset_perusahaan.kode_jenis_aset : 'AST-TRK'),
                            nama_aset: d.nama_aset || (d.aset_perusahaan ? d.aset_perusahaan.nama_aset : ''),
                            no_polisi: d.no_polisi || '',
                            merek_kendaraan: d.merek_kendaraan || d.merek_aset || '',
                            merek_aset: d.merek_kendaraan || d.merek_aset || '',
                            muatan: d.muatan || '',
                            harga_aset: d.harga_aset || (d.aset_perusahaan ? (d.aset_perusahaan.harga_perolehan || d.aset_perusahaan.harga_aset) : 0),
                            tanggal_pembelian: d.tanggal_pembelian ? String(d.tanggal_pembelian).split('T')[0] : (d.aset_perusahaan && d.aset_perusahaan.tanggal_pembelian ? String(d.aset_perusahaan.tanggal_pembelian).split('T')[0] : ''),
                            tahun_pembuatan: d.tahun_pembuatan || '',
                            no_mesin: d.no_mesin || '',
                            no_rangka: d.no_rangka || '',
                            nama_pemilik: d.nama_pemilik || 'PT Putra Balkom Jaya',
                            tanggal_kir: d.tanggal_kir ? String(d.tanggal_kir).split('T')[0] : '',
                            tanggal_pajak: d.tanggal_pajak ? String(d.tanggal_pajak).split('T')[0] : '',
                            status_kendaraan: d.status_kendaraan || d.status_aset || 'aktif',
                            status_aset: d.status_kendaraan || d.status_aset || 'aktif'
                        };
                        this.modalEditKendaraanTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data kendaraan untuk diedit.');
                }
            },

            bukaModalHapusKendaraan(kode, nama, plat) {
                this.hapusKendaraanData.kode = kode;
                this.hapusKendaraanData.nama = nama;
                this.hapusKendaraanData.plat = plat;
                this.modalHapusKendaraanTerbuka = true;
            },

            // --- Handler Aset Perusahaan (Selaras SPV Keuangan) ---
            bukaModalTambahAset() {
                this.formTambahAset.nama_aset = '';
                this.formTambahAset.no_polisi = '';
                this.formTambahAset.harga_aset = 0;
                this.buatKodeAsetOtomatis('gap');
                this.modalTambahAsetTerbuka = true;
            },

            async buatKodeAsetOtomatis(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.armada.kendaraan.aset.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambahAset.kode_aset = data.kode_otomatis;
                        this.keteranganKodeAset = data.keterangan || (mode === 'acak' ? 'Mode: Kode Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode aset otomatis:', e);
                }
            },

            async bukaModalDetailAset(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/armada/kendaraan-aset') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailAset = data.data;
                        this.modalDetailAsetTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail data aset perusahaan.');
                }
            },

            async bukaModalEditAset(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/armada/kendaraan-aset') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEditAset = {
                            kode_aset: d.kode_aset,
                            kode_jenis_aset: d.kode_jenis_aset,
                            nama_aset: d.nama_aset,
                            no_polisi: d.no_polisi === '-' ? '' : d.no_polisi,
                            tanggal_pembelian: d.tanggal_pembelian ? String(d.tanggal_pembelian).split('T')[0] : '',
                            harga_aset: d.harga_aset,
                            status_aset: d.status_aset || 'aktif'
                        };
                        this.modalEditAsetTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data aset untuk diedit.');
                }
            },

            bukaModalHapusAset(kode, nama, plat) {
                this.hapusAsetData.kode = kode;
                this.hapusAsetData.nama = nama;
                this.hapusAsetData.plat = plat;
                this.modalHapusAsetTerbuka = true;
            },

            // --- Handler Jenis Aset (Kategori) ---
            bukaModalTambahJenisAset() {
                this.formTambahJenisAset.jenis_aset = '';
                this.formTambahJenisAset.keterangan = '';
                this.buatKodeJenisOtomatis('gap');
                this.modalTambahJenisAsetTerbuka = true;
            },

            async buatKodeJenisOtomatis(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.armada.kendaraan.jenis_aset.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambahJenisAset.kode_jenis_aset = data.kode_otomatis;
                        this.keteranganKodeJenis = data.keterangan || (mode === 'acak' ? 'Mode: Kode Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode jenis aset:', e);
                }
            },

            async bukaModalDetailJenisAset(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/armada/kendaraan-jenis-aset') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailJenisAset = data.data;
                        this.modalDetailJenisAsetTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail jenis aset.');
                }
            },

            async bukaModalEditJenisAset(kode) {
                try {
                    const res = await fetch(`{{ url('operasional/armada/kendaraan-jenis-aset') }}/${kode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formEditJenisAset = {
                            kode_jenis_aset: data.data.kode_jenis_aset,
                            jenis_aset: data.data.jenis_aset,
                            keterangan: data.data.keterangan || ''
                        };
                        this.modalEditJenisAsetTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data jenis aset untuk diedit.');
                }
            },

            bukaModalHapusJenisAset(kode, nama, jumlahTruk) {
                this.hapusJenisAsetData.kode = kode;
                this.hapusJenisAsetData.nama = nama;
                this.hapusJenisAsetData.jumlahTruk = jumlahTruk;
                this.modalHapusJenisAsetTerbuka = true;
            }
        };
    }
</script>
@endsection
