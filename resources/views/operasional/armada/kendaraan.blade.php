@extends('layouts.app')

@section('judul', 'Data Kendaraan & Jenis Aset - PT Pura Balkom Jaya')

@section('konten')
<div x-data="kelolaArmadaTerpadu('{{ $tabAktif ?? 'kendaraan' }}')" x-init="initArmada()" class="space-y-6">

    <!-- 1. Header Modul & Navigasi Tab Terpadu -->
    <div class="bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm space-y-4">
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
                <button x-show="tabAktif === 'jenis_aset'" @click="bukaModalTambahJenisAset()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 active:scale-95 rounded-xl transition-all shadow-md shadow-violet-600/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Jenis Aset</span>
                </button>
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
                    :class="tabAktif === 'jenis_aset' ? 'bg-violet-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300'"
                    class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Data Jenis Aset (Kategori)</span>
                <span class="px-2 py-0.5 text-[10px] font-mono rounded-md font-bold"
                      :class="tabAktif === 'jenis_aset' ? 'bg-violet-800/40 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300'">
                    {{ count($daftarJenisAset) }}
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

    @if($errors->any())
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
    <!-- TAB 1: KONTEN DATA KENDARAAN (ARMADA TRUK) -->
    <!-- ========================================================================= -->
    <div x-show="tabAktif === 'kendaraan'" class="space-y-6">
        
        <!-- 3. Ringkasan Kartu KPI Statistik Kendaraan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Unit Truk -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Armada</div>
                    <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalKendaraan }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
                </div>
            </div>

            <!-- Truk Siap Jalan (Aktif) -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Siap Jalan (Aktif)</div>
                    <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $kendaraanAktif }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
                </div>
            </div>

            <!-- Dalam Perbaikan / Bengkel -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Perbaikan / Servis</div>
                    <div class="text-xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">{{ $kendaraanServis }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
                </div>
            </div>

            <!-- Peringatan KIR & Pajak -->
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Perhatian KIR / Pajak</div>
                    <div class="text-xl font-bold text-rose-600 dark:text-rose-400 font-mono mt-0.5">{{ $kendaraanPerhatianPajakKir }} <span class="text-xs font-normal text-slate-400 font-sans">Jatuh Tempo</span></div>
                </div>
            </div>
        </div>

        <!-- 4. Tabel Data Kendaraan & Bar Pencarian -->
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
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

                    <!-- Jenis Aset Filter -->
                    <select name="jenis" onchange="this.form.submit()"
                            class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                        <option value="semua" {{ ($jenisFilter ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Jenis Aset</option>
                        @foreach($daftarJenisAset as $j)
                            <option value="{{ $j->kode_jenis_aset }}" {{ ($jenisFilter ?? '') === $j->kode_jenis_aset ? 'selected' : '' }}>{{ $j->jenis_aset }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter Dropdown -->
                    <select name="status" onchange="this.form.submit()"
                            class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                        <option value="semua" {{ ($statusFilter ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif (Siap Jalan)</option>
                        <option value="dalam_perbaikan" {{ ($statusFilter ?? '') === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="rusak" {{ ($statusFilter ?? '') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="dijual" {{ ($statusFilter ?? '') === 'dijual' ? 'selected' : '' }}>Dijual</option>
                        <option value="non-aktif" {{ ($statusFilter ?? '') === 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>

                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                        Cari
                    </button>

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
                <table class="w-full text-left text-xs">
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
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors group">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900 dark:text-slate-100 text-sm px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 inline-block">
                                        {{ $k->no_polisi ?: '-' }}
                                    </div>
                                    <div class="text-[11px] font-mono text-orange-600 dark:text-orange-400 font-semibold mt-1">
                                        {{ $k->kode_aset }}
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
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $k->merek_aset }}</div>
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
                                        $warnaStatus = match($k->status_aset) {
                                            'aktif' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                                            'dalam_perbaikan' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                                            'rusak' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                                            default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                                        };
                                        $labelStatus = match($k->status_aset) {
                                            'aktif' => 'Aktif (Siap Jalan)',
                                            'dalam_perbaikan' => 'Servis Bengkel',
                                            'rusak' => 'Rusak',
                                            'dijual' => 'Dijual',
                                            default => 'Non-Aktif',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono border {{ $warnaStatus }}">
                                        {{ $labelStatus }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button @click="bukaModalDetailKendaraan('{{ $k->kode_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                                title="Lihat Spesifikasi & Detail Truk">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalEditKendaraan('{{ $k->kode_aset }}')"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                title="Ubah Data Kendaraan">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <button @click="bukaModalHapusKendaraan('{{ $k->kode_aset }}', '{{ addslashes($k->nama_aset) }}', '{{ $k->no_polisi }}')"
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
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: KONTEN DATA JENIS ASET (KATEGORI KENDARAAN) -->
    <!-- ========================================================================= -->
    <div x-show="tabAktif === 'jenis_aset'" class="space-y-6">
        
        <!-- Ringkasan Kartu KPI Jenis Aset -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Kategori</div>
                    <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ count($daftarJenisAset) }} <span class="text-xs font-normal text-slate-400 font-sans">Jenis</span></div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Armada Terhubung</div>
                    <div class="text-xl font-bold text-orange-600 dark:text-orange-400 font-mono mt-0.5">{{ $totalKendaraan }} <span class="text-xs font-normal text-slate-400 font-sans">Unit Truk</span></div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Truk Muatan Berat</div>
                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400 font-mono mt-0.5">Tronton & Tangki</div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Truk Sedang / Pick Up</div>
                    <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">CDD & Pick Up</div>
                </div>
            </div>
        </div>

        <!-- Tabel Data Jenis Aset -->
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
            <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <form method="GET" action="{{ route('operasional.armada.kendaraan') }}" class="flex items-center gap-2.5 flex-1 max-w-md">
                    <input type="hidden" name="tab" value="jenis_aset">
                    <div class="relative flex-1">
                        <input type="text" name="cari" value="{{ ($tabAktif === 'jenis_aset') ? ($kataKunci ?? '') : '' }}"
                               placeholder="Cari kode jenis, nama kategori tipe truk, atau keterangan..."
                               class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                        Cari
                    </button>
                    @if(!empty($kataKunci) && $tabAktif === 'jenis_aset')
                        <a href="{{ route('operasional.armada.kendaraan', ['tab' => 'jenis_aset']) }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-[11px] text-slate-400 font-mono">
                    Total <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarJenisAset) }}</strong> Kategori Jenis Aset
                </div>
            </div>

            <div class="overflow-x-auto">
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
    <div x-show="modalTambahKendaraanTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahKendaraanTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Tambah Unit Kendaraan Baru</h2>
                        <p class="text-[11px] text-slate-400">Lengkapi data teknis armada truk, nomor mesin/rangka, dan perizinan KIR/Pajak.</p>
                    </div>
                </div>
                <button @click="modalTambahKendaraanTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('operasional.armada.kendaraan.simpan') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <!-- Generator Kode Aset Cerdas -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div>
                            <label class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                Kode Aset Kendaraan <span class="text-rose-500">*</span>
                            </label>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="keteranganKodeKendaraan"></div>
                        </div>
                        
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="buatKodeKendaraanOtomatis('gap')"
                                    class="px-2.5 py-1 text-[11px] font-semibold text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Daur Ulang Slot</span>
                            </button>
                            <button type="button" @click="buatKodeKendaraanOtomatis('acak')"
                                    class="px-2.5 py-1 text-[11px] font-semibold text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Kode Acak</span>
                            </button>
                        </div>
                    </div>

                    <input type="text" name="kode_aset" x-model="formTambahKendaraan.kode_aset" required placeholder="Contoh: TRK-001 atau TRK-7K8B"
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-orange-600 dark:text-orange-400 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Plat Polisi <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_polisi" x-model="formTambahKendaraan.no_polisi" required placeholder="Contoh: B 9283 TDF"
                               class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-900 dark:text-slate-100 uppercase focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Model Truk / Aset <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_aset" x-model="formTambahKendaraan.nama_aset" required placeholder="Contoh: Hino 500 Tronton Wingbox FL 260"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Aset Truk <span class="text-rose-500">*</span></label>
                        <select name="kode_jenis_aset" x-model="formTambahKendaraan.kode_jenis_aset" required
                                class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                            <option value="">-- Pilih Jenis Aset --</option>
                            @foreach($daftarJenisAset as $j)
                                <option value="{{ $j->kode_jenis_aset }}">{{ $j->jenis_aset }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek Truk <span class="text-rose-500">*</span></label>
                        <input type="text" name="merek_aset" x-model="formTambahKendaraan.merek_aset" required placeholder="Contoh: Hino, Mitsubishi, Isuzu"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="muatan" x-model="formTambahKendaraan.muatan" required placeholder="Contoh: 25 Ton (500 Zak)"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Perolehan Unit (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga_aset" x-model="formTambahKendaraan.harga_aset" required min="0" step="1000"
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_pembelian" x-model="formTambahKendaraan.tanggal_pembelian" required
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pembuatan <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_pembuatan" x-model="formTambahKendaraan.tahun_pembuatan" required min="1990" max="2099"
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Mesin <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_mesin" x-model="formTambahKendaraan.no_mesin" required placeholder="Contoh: J08E-UG-12948"
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rangka (VIN) <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_rangka" x-model="formTambahKendaraan.no_rangka" required placeholder="Contoh: MJECFL8J7N-039182"
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Legal <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_pemilik" x-model="formTambahKendaraan.nama_pemilik" required placeholder="PT Pura Balkom Jaya"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Masa Berlaku Uji KIR</label>
                        <input type="date" name="tanggal_kir" x-model="formTambahKendaraan.tanggal_kir"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Masa Berlaku Pajak STNK</label>
                        <input type="date" name="tanggal_pajak" x-model="formTambahKendaraan.tanggal_pajak"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                        <select name="status_aset" x-model="formTambahKendaraan.status_aset" required
                                class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                            <option value="aktif">Aktif (Siap Jalan)</option>
                            <option value="dalam_perbaikan">Dalam Perbaikan (Bengkel)</option>
                            <option value="rusak">Rusak</option>
                            <option value="dijual">Dijual</option>
                            <option value="non-aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalTambahKendaraanTerbuka = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-orange-600 hover:bg-orange-700 active:scale-95 rounded-xl transition-all shadow-md shadow-orange-600/20">
                        Simpan Data Kendaraan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kendaraan -->
    <div x-show="modalEditKendaraanTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditKendaraanTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Ubah Data Kendaraan</h2>
                        <p class="text-[11px] text-slate-400">Plat: <span class="font-mono font-bold text-amber-600" x-text="formEditKendaraan.no_polisi"></span></p>
                    </div>
                </div>
                <button @click="modalEditKendaraanTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('operasional/armada/kendaraan') }}/' + formEditKendaraan.kode_aset" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Aset (Terkunci)</label>
                        <input type="text" :value="formEditKendaraan.kode_aset" disabled
                               class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Plat Polisi <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_polisi" x-model="formEditKendaraan.no_polisi" required
                               class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-900 dark:text-slate-100 uppercase focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Model Truk <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_aset" x-model="formEditKendaraan.nama_aset" required
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Aset Truk <span class="text-rose-500">*</span></label>
                        <select name="kode_jenis_aset" x-model="formEditKendaraan.kode_jenis_aset" required
                                class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                            <option value="">-- Pilih Jenis Aset --</option>
                            @foreach($daftarJenisAset as $j)
                                <option value="{{ $j->kode_jenis_aset }}">{{ $j->jenis_aset }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek Truk <span class="text-rose-500">*</span></label>
                        <input type="text" name="merek_aset" x-model="formEditKendaraan.merek_aset" required
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="muatan" x-model="formEditKendaraan.muatan" required
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Perolehan Unit (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga_aset" x-model="formEditKendaraan.harga_aset" required min="0" step="1000"
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_pembelian" x-model="formEditKendaraan.tanggal_pembelian" required
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pembuatan <span class="text-rose-500">*</span></label>
                        <input type="number" name="tahun_pembuatan" x-model="formEditKendaraan.tahun_pembuatan" required min="1990" max="2099"
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Mesin <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_mesin" x-model="formEditKendaraan.no_mesin" required
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rangka (VIN) <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_rangka" x-model="formEditKendaraan.no_rangka" required
                               class="w-full px-3.5 py-2 text-xs font-mono rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 uppercase focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Legal <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_pemilik" x-model="formEditKendaraan.nama_pemilik" required
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Masa Berlaku Uji KIR</label>
                        <input type="date" name="tanggal_kir" x-model="formEditKendaraan.tanggal_kir"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Masa Berlaku Pajak STNK</label>
                        <input type="date" name="tanggal_pajak" x-model="formEditKendaraan.tanggal_pajak"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                        <select name="status_aset" x-model="formEditKendaraan.status_aset" required
                                class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                            <option value="aktif">Aktif (Siap Jalan)</option>
                            <option value="dalam_perbaikan">Dalam Perbaikan (Bengkel)</option>
                            <option value="rusak">Rusak</option>
                            <option value="dijual">Dijual</option>
                            <option value="non-aktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalEditKendaraanTerbuka = false"
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

    <!-- Modal Detail Kendaraan -->
    <div x-show="modalDetailKendaraanTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailKendaraanTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-bold font-mono text-sm">
                        <span x-text="detailKendaraan.kode_aset ? detailKendaraan.kode_aset.substring(0,3) : 'TRK'"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100" x-text="detailKendaraan.nama_aset"></h2>
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-xs bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-slate-100"
                                  x-text="detailKendaraan.no_polisi"></span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-mono" x-text="detailKendaraan.kode_aset"></p>
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
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailKendaraan.jenis_aset ? detailKendaraan.jenis_aset.jenis_aset : detailKendaraan.kode_jenis_aset"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Merek & Tahun</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailKendaraan.merek_aset + ' (' + (detailKendaraan.tahun_pembuatan || '-') + ')'"></div>
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
                                  x-text="detailKendaraan.status_aset"></span>
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

</div>

<!-- Alpine.js Master State & Logic -->
<script>
    function kelolaArmadaTerpadu(initialTab = 'kendaraan') {
        return {
            tabAktif: initialTab,

            // Modals Kendaraan
            modalTambahKendaraanTerbuka: false,
            modalEditKendaraanTerbuka: false,
            modalDetailKendaraanTerbuka: false,
            modalHapusKendaraanTerbuka: false,

            // Modals Jenis Aset
            modalTambahJenisAsetTerbuka: false,
            modalEditJenisAsetTerbuka: false,
            modalDetailJenisAsetTerbuka: false,
            modalHapusJenisAsetTerbuka: false,

            keteranganKodeKendaraan: 'Mode: Daur Ulang Slot Kosong',
            keteranganKodeJenis: 'Mode: Daur Ulang Slot Kosong',

            formTambahKendaraan: {
                kode_aset: '',
                kode_jenis_aset: '{{ $daftarJenisAset->first()->kode_jenis_aset ?? "" }}',
                nama_aset: '',
                no_polisi: '',
                merek_aset: 'Hino',
                muatan: '25 Ton (500 Zak)',
                harga_aset: 1200000000,
                tanggal_pembelian: new Date().toISOString().split('T')[0],
                tahun_pembuatan: new Date().getFullYear(),
                no_mesin: '',
                no_rangka: '',
                nama_pemilik: 'PT Pura Balkom Jaya',
                tanggal_kir: '',
                tanggal_pajak: '',
                status_aset: 'aktif'
            },

            formEditKendaraan: {
                kode_aset: '',
                kode_jenis_aset: '',
                nama_aset: '',
                no_polisi: '',
                merek_aset: '',
                muatan: '',
                harga_aset: 0,
                tanggal_pembelian: '',
                tahun_pembuatan: '',
                no_mesin: '',
                no_rangka: '',
                nama_pemilik: '',
                tanggal_kir: '',
                tanggal_pajak: '',
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

            detailKendaraan: {},
            hapusKendaraanData: { kode: '', nama: '', plat: '' },

            detailJenisAset: {},
            hapusJenisAsetData: { kode: '', nama: '', jumlahTruk: 0 },

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
                            kode_aset: d.kode_aset,
                            kode_jenis_aset: d.kode_jenis_aset,
                            nama_aset: d.nama_aset,
                            no_polisi: d.no_polisi,
                            merek_aset: d.merek_aset,
                            muatan: d.muatan,
                            harga_aset: d.harga_aset,
                            tanggal_pembelian: d.tanggal_pembelian || '',
                            tahun_pembuatan: d.tahun_pembuatan || '',
                            no_mesin: d.no_mesin,
                            no_rangka: d.no_rangka,
                            nama_pemilik: d.nama_pemilik,
                            tanggal_kir: d.tanggal_kir || '',
                            tanggal_pajak: d.tanggal_pajak || '',
                            status_aset: d.status_aset
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

            // --- Handler Jenis Aset ---
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
