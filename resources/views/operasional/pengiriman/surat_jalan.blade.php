@extends('layouts.app')

@section('judul', 'Surat Jalan Pengiriman Distribusi Semen - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaSuratJalan()" x-init="initSuratJalan()" class="space-y-6">

    @php
        $opsiStatusFilter = [
            ['nilai' => 'semua', 'label' => 'Semua Status Pengiriman'],
            ['nilai' => 'menunggu', 'label' => 'Menunggu Muat'],
            ['nilai' => 'dalam_perjalanan', 'label' => 'Dalam Perjalanan'],
            ['nilai' => 'terkirim', 'label' => 'Terkirim / Selesai'],
            ['nilai' => 'retur', 'label' => 'Retur / Ditolak'],
        ];
        $opsiStatusPengiriman = [
            ['nilai' => 'menunggu', 'label' => 'Menunggu Muat di Gudang'],
            ['nilai' => 'dalam_perjalanan', 'label' => 'Dalam Perjalanan (Berangkat)'],
            ['nilai' => 'terkirim', 'label' => 'Terkirim / Tiba di Lokasi'],
            ['nilai' => 'retur', 'label' => 'Retur / Ditolak Toko'],
        ];
        $opsiSO = ($daftarSO ?? collect())->map(fn($so) => [
            'nilai' => $so->id_so,
            'label' => $so->nomor_so . ' — ' . ($so->customer->nama_toko_bangunan ?? $so->customer->nama_customer ?? 'Toko Pelanggan'),
            'sub'   => ($so->jumlah_zak ?? 0) . ' Zak | ' . ($so->customer->alamat ?? 'Alamat pengiriman')
        ])->toArray();
        $opsiDriver = ($daftarDriver ?? collect())->map(fn($drv) => [
            'nilai' => $drv->kode_karyawan,
            'label' => $drv->nama_karyawan . ' (' . $drv->kode_karyawan . ')',
            'sub'   => 'HP: ' . ($drv->no_hp ?? '-')
        ])->toArray();
        $opsiKendaraan = ($daftarKendaraan ?? collect())->map(fn($knd) => [
            'nilai' => $knd->kode_aset,
            'label' => ($knd->no_polisi ?? '-') . ' — ' . ($knd->nama_aset ?? 'Truk Armada'),
            'sub'   => 'Kapasitas: ' . ($knd->muatan ?? 'Standar')
        ])->toArray();
    @endphp

    <!-- 1. Header Modul & Tombol Aksi -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 font-mono">
                    Dispatcher & Logistik Distribusi
                </span>
                <span class="text-xs text-slate-400 font-mono">Manajemen Pengiriman</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Surat Jalan (SJ) Pengiriman Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Penerbitan surat jalan distribusi dari Sales Order (SO), penugasan driver & truk armada, pemantauan status perjalanan, dan cetak dokumen resmi.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 active:scale-95 rounded-xl transition-all shadow-md shadow-sky-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Terbitkan Surat Jalan</span>
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

    <!-- 3. Ringkasan Kartu KPI Logistik Pengiriman -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Pengiriman -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Pengiriman</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalPengiriman }} <span class="text-xs font-normal text-slate-400 font-sans">Surat Jalan</span></div>
            </div>
        </div>

        <!-- Dalam Perjalanan -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Dalam Perjalanan</div>
                <div class="text-xl font-bold text-blue-600 dark:text-blue-400 font-mono mt-0.5">{{ $pengirimanJalan }} <span class="text-xs font-normal text-slate-400 font-sans">Truk Jalan</span></div>
            </div>
        </div>

        <!-- Terkirim / Selesai -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Terkirim (Selesai)</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $pengirimanSelesai }} <span class="text-xs font-normal text-slate-400 font-sans">Terkirim</span></div>
            </div>
        </div>

        <!-- Menunggu / Retur -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Antrean / Retur</div>
                <div class="text-xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">{{ $pengirimanMenunggu }} <span class="text-xs font-normal text-slate-400 font-sans">Menunggu</span></div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Data Surat Jalan & Filter -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarPengiriman ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('operasional.pengiriman.surat_jalan') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari no SJ, no SO, nama toko/customer, driver, plat truk, alamat..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Status Dropdown Kustom -->
                <div class="w-full sm:w-56">
                    <x-dropdown-kustom 
                        nama="status"
                        placeholder="-- Status Pengiriman --"
                        :opsi="$opsiStatusFilter"
                        :nilaiAwal="$statusFilter ?? 'semua'"
                        :submitOnChange="true"
                        warnaFokus="sky"
                    />
                </div>

                @if(!empty($kataKunci) || ($statusFilter !== 'semua' && !empty($statusFilter)))
                    <a href="{{ route('operasional.pengiriman.surat_jalan') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono">
                Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarPengiriman) }}</strong> Surat Jalan
            </div>
        </div>

        <!-- Tabel Data Pengiriman -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Surat Jalan & Waktu</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Sales Order & Destinasi Customer</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Driver Pengemudi</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Truk Armada & Muatan</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status Perjalanan</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi & Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarPengiriman as $sj)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <!-- No SJ & Tanggal -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-sky-600 dark:text-sky-400 text-sm">
                                    {{ $sj->nomor_surat_jalan }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $sj->tanggal_kirim_format }}</span>
                                </div>
                            </td>

                            <!-- Sales Order & Customer -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-sm">
                                    {{ $sj->salesOrder->customer->nama_customer ?? 'Customer Umum' }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs mt-0.5">
                                    {{ $sj->salesOrder->customer->alamat ?? '-' }}
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-1.5 py-0.2 rounded font-mono text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        {{ $sj->salesOrder->nomor_so ?? '-' }}
                                    </span>
                                    <span class="font-mono font-bold text-orange-600 dark:text-orange-400 text-[11px]">
                                        {{ $sj->salesOrder->jumlah_zak ?? 0 }} Zak Semen
                                    </span>
                                </div>
                            </td>

                            <!-- Driver -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold font-mono text-[11px] flex items-center justify-center shrink-0">
                                        {{ substr($sj->driver->nama_karyawan ?? 'D', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ $sj->driver->nama_karyawan ?? '-' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono">
                                            {{ $sj->driver->no_hp ?? $sj->kode_driver }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Armada Truk -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-xs text-slate-900 dark:text-slate-100 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 inline-block">
                                    {{ $sj->kendaraan->no_polisi ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">
                                    {{ $sj->kendaraan->nama_aset ?? $sj->kode_aset }}
                                </div>
                            </td>

                            <!-- Status Pengiriman -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @php $badge = $sj->status_badge; @endphp
                                <button @click="bukaModalStatus('{{ $sj->id_pengiriman }}', '{{ $sj->nomor_surat_jalan }}', '{{ $sj->status_pengiriman }}')"
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono border {{ $badge['bg'] }} hover:opacity-80 transition-opacity"
                                        title="Klik untuk ubah status perjalanan secara cepat">
                                    {{ $badge['label'] }} ✎
                                </button>
                            </td>

                            <!-- Aksi Popover Modern -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$sj->nomor_surat_jalan" 
                                    labelSalin="Salin No"
                                    modulIzin="ops_surat_jalan"
                                    :aksiDetail="'bukaModalCetak(\'' . $sj->id_pengiriman . '\')'"
                                    labelDetail="Cetak"
                                    :aksiEdit="'bukaModalEdit(\'' . $sj->id_pengiriman . '\')'"
                                    labelEdit="Edit"
                                >
                                    <button @click="bukaModalStatus('{{ $sj->id_pengiriman }}', '{{ $sj->nomor_surat_jalan }}', '{{ $sj->status_pengiriman }}'); terbuka = false" 
                                            type="button" 
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-sky-50 dark:hover:bg-sky-500/10 hover:text-sky-600 dark:hover:text-sky-400 transition-colors text-left border-b border-slate-100 dark:border-[#252837]">
                                        <svg class="w-3.5 h-3.5 text-sky-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <span>Ubah Status</span>
                                    </button>

                                    <template x-if="!apakahReadOnly('ops_surat_jalan')">
                                        <button @click="bukaModalHapus('{{ $sj->id_pengiriman }}', '{{ $sj->nomor_surat_jalan }}'); terbuka = false" 
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada pengiriman terdaftar</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Terbitkan surat jalan baru dari Sales Order pelanggan.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-paginasi-tabel :totalData="count($daftarPengiriman ?? [])" />
    </div>

    <!-- Modal Tambah Surat Jalan -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Terbitkan Surat Jalan (SJ) Baru</h3>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.pengiriman.surat_jalan.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Nomor Surat Jalan <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-sky-600 dark:text-sky-400 font-semibold px-1.5 py-0.5 bg-sky-50 dark:bg-sky-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="nomor_surat_jalan" x-model="formTambah.nomor_surat_jalan" required placeholder="SJ-001"
                               class="w-full px-3 py-2 rounded-xl bg-sky-50/50 dark:bg-[#1C1E2A] border border-sky-200 dark:border-sky-900/50 text-sky-900 dark:text-sky-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sales Order (SO) Pelanggan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="id_so"
                            placeholder="-- Pilih Sales Order --"
                            :opsi="$opsiSO"
                            :wajib="true"
                            warnaFokus="sky"
                            modelBind="formTambah.id_so"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Driver Pengemudi <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_driver"
                            placeholder="-- Pilih Driver --"
                            :opsi="$opsiDriver"
                            :wajib="true"
                            warnaFokus="sky"
                            modelBind="formTambah.kode_driver"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Truk Armada Pengiriman <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_aset"
                            placeholder="-- Pilih Truk Armada --"
                            :opsi="$opsiKendaraan"
                            :wajib="true"
                            warnaFokus="sky"
                            modelBind="formTambah.kode_aset"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Keberangkatan <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_kirim" 
                            modelBind="formTambah.tanggal_kirim" 
                            placeholder="Pilih Tanggal Kirim"
                            :wajib="true"
                            warnaFokus="sky"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Keberangkatan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_pengiriman"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusPengiriman"
                            :wajib="true"
                            warnaFokus="sky"
                            modelBind="formTambah.status_pengiriman"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Nomor Segel / Rute <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="keterangan" x-model="formTambah.keterangan" rows="2" placeholder="Contoh: Nomor Segel: PBJ-9921, dikirim via Jalur Pantura"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">Terbitkan Surat Jalan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Surat Jalan -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Surat Jalan: <span class="font-mono text-sky-600" x-text="formEdit.nomor_surat_jalan"></span></h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/pengiriman/surat-jalan') }}/' + formEdit.id_pengiriman" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat Jalan (Terkunci)</label>
                        <input type="text" :value="formEdit.nomor_surat_jalan" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Sales Order (SO) Pelanggan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="id_so"
                            placeholder="-- Pilih Sales Order --"
                            :opsi="$opsiSO"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.id_so"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Driver Pengemudi <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_driver"
                            placeholder="-- Pilih Driver --"
                            :opsi="$opsiDriver"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_driver"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Truk Armada <span class="text-rose-500">*</span></label>
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
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Keberangkatan <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_kirim" 
                            modelBind="formEdit.tanggal_kirim" 
                            placeholder="Pilih Tanggal Kirim"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Pengiriman <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_pengiriman"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusPengiriman"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.status_pengiriman"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan Rute <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="keterangan" x-model="formEdit.keterangan" rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL PRATINJAU & CETAK LEMBAR SURAT JALAN (PRINT READY VIEW) -->
    <!-- ========================================================================= -->
    <div x-show="modalCetakTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalCetakTerbuka = false"
             class="bg-white text-slate-900 rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl my-8 border border-slate-200">
            
            <!-- Action Toolbar (Hidden during print) -->
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-slate-200 bg-slate-50 print:hidden">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="font-bold text-xs text-slate-800">Pratinjau Dokumen Surat Jalan Resmi</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="window.print()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Dokumen (Print)</span>
                    </button>
                    <button type="button" @click="modalCetakTerbuka = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Konten Lembar Surat Jalan (Format Standar PT PBJ) -->
            <div class="p-8 space-y-6 text-slate-900 bg-white" id="lembar-surat-jalan">
                
                <!-- Kop Surat Perusahaan -->
                <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ asset('images/logo-pbj.png') }}" alt="Logo PT Putra Balkom Jaya" class="w-16 h-16 object-contain shrink-0">
                        <div>
                            <h1 class="text-xl font-extrabold tracking-tight text-slate-900 uppercase">PT PUTRA BALKOM JAYA</h1>
                            <p class="text-xs text-slate-600 font-medium">Distribusi & Logistik Semen Curah & Zak</p>
                            <p class="text-[11px] text-slate-500">Kawasan Industri Cikarang Blok B-12, Bekasi · Telp: (021) 8983-4921</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="inline-block border-2 border-slate-900 px-3 py-1 text-center rounded-md">
                            <span class="text-[10px] font-bold block uppercase tracking-widest text-slate-500">DOKUMEN RESMI</span>
                            <span class="text-sm font-extrabold tracking-wider">SURAT JALAN</span>
                        </div>
                        <div class="text-xs font-mono font-bold mt-1.5 text-sky-700" x-text="detailPengiriman.nomor_surat_jalan"></div>
                    </div>
                </div>

                <!-- Info Header Pengiriman -->
                <div class="grid grid-cols-2 gap-6 text-xs">
                    <!-- Kolom Penerima -->
                    <div class="space-y-1 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="font-bold uppercase tracking-wider text-[10px] text-slate-500">Kepada Yth. (Penerima):</div>
                        <div class="font-bold text-sm text-slate-900" x-text="detailPengiriman.sales_order?.customer?.nama_customer || '-'"></div>
                        <div class="text-slate-600" x-text="detailPengiriman.sales_order?.customer?.alamat || '-'"></div>
                        <div class="text-slate-600"><span class="font-semibold">Kontak:</span> <span x-text="detailPengiriman.sales_order?.customer?.no_hp || '-'"></span></div>
                    </div>

                    <!-- Kolom Angkutan / Truk -->
                    <div class="space-y-1 p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="font-bold uppercase tracking-wider text-[10px] text-slate-500">Data Pengangkutan:</div>
                        <div><span class="font-semibold">Tanggal Berangkat:</span> <span class="font-mono" x-text="detailPengiriman.tanggal_kirim_format || '-'"></span></div>
                        <div><span class="font-semibold">No. Sales Order:</span> <span class="font-mono font-bold" x-text="detailPengiriman.sales_order?.nomor_so || '-'"></span></div>
                        <div><span class="font-semibold">Driver / Pengemudi:</span> <span class="font-bold" x-text="detailPengiriman.driver?.nama_karyawan || '-'"></span> (<span x-text="detailPengiriman.driver?.no_hp || '-'"></span>)</div>
                        <div><span class="font-semibold">Kendaraan / Truk:</span> <span class="font-mono font-bold text-slate-900" x-text="detailPengiriman.kendaraan?.no_polisi || '-'"></span> - <span x-text="detailPengiriman.kendaraan?.nama_aset || '-'"></span></div>
                    </div>
                </div>

                <!-- Tabel Muatan Semen -->
                <div>
                    <table class="w-full text-left text-xs border border-slate-300">
                        <thead class="bg-slate-100 text-slate-800 uppercase font-bold border-b border-slate-300">
                            <tr>
                                <th class="px-3 py-2 border-r border-slate-300 text-center w-12">No.</th>
                                <th class="px-3 py-2 border-r border-slate-300">Nama Barang / Deskripsi Semen</th>
                                <th class="px-3 py-2 border-r border-slate-300 text-right w-28">Kuantitas</th>
                                <th class="px-3 py-2 border-r border-slate-300 text-center w-20">Satuan</th>
                                <th class="px-3 py-2">Keterangan / Segel</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr>
                                <td class="px-3 py-3 text-center border-r border-slate-300 font-mono">1</td>
                                <td class="px-3 py-3 border-r border-slate-300">
                                    <div class="font-bold text-slate-900">Semen Portland Composite Cement (PCC) 50 Kg</div>
                                    <div class="text-[11px] text-slate-500">Kualitas Standar SNI Pabrik PBJ</div>
                                </td>
                                <td class="px-3 py-3 text-right border-r border-slate-300 font-mono font-bold text-sm" x-text="detailPengiriman.sales_order?.jumlah_zak || '0'"></td>
                                <td class="px-3 py-3 text-center border-r border-slate-300 font-bold">Zak</td>
                                <td class="px-3 py-3 text-slate-600 text-[11px]" x-text="detailPengiriman.keterangan || 'Kondisi barang baik & tersegel'"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Kolom Tanda Tangan 3 Pihak -->
                <div class="grid grid-cols-3 gap-4 text-center text-xs pt-6">
                    <div>
                        <div class="font-bold uppercase text-slate-600 text-[10px]">Petugas Logistik / Dispatcher</div>
                        <div class="h-16 border-b border-dashed border-slate-400 mt-2"></div>
                        <div class="font-bold mt-1.5 text-slate-900">Dispatcher Logistik</div>
                    </div>
                    <div>
                        <div class="font-bold uppercase text-slate-600 text-[10px]">Sopir / Pengemudi</div>
                        <div class="h-16 border-b border-dashed border-slate-400 mt-2"></div>
                        <div class="font-bold mt-1.5 text-slate-900" x-text="detailPengiriman.driver?.nama_karyawan || 'Driver'"></div>
                    </div>
                    <div>
                        <div class="font-bold uppercase text-slate-600 text-[10px]">Penerima Barang (Toko/Proyek)</div>
                        <div class="h-16 border-b border-dashed border-slate-400 mt-2"></div>
                        <div class="font-bold mt-1.5 text-slate-900">( Tanda Tangan & Stempel )</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. MODAL UPDATE STATUS CEPAT -->
    <!-- ========================================================================= -->
    <div x-show="modalStatusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalStatusTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl p-6 text-xs">
            
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Perbarui Status Pengiriman</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Surat Jalan: <strong class="font-mono text-sky-600" x-text="statusData.nomor_sj"></strong>
            </p>

            <form :action="'{{ url('operasional/pengiriman/surat-jalan') }}/' + statusData.id + '/status'" method="POST" class="mt-4 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Status Baru <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="status_pengiriman"
                        placeholder="-- Pilih Status --"
                        :opsi="$opsiStatusPengiriman"
                        :wajib="true"
                        warnaFokus="sky"
                        modelBind="statusData.status"
                    />
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalStatusTerbuka = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-md shadow-sky-600/20">
                        Simpan Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 9. MODAL KONFIRMASI HAPUS SURAT JALAN -->
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

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Surat Jalan?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus Surat Jalan <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nomor_sj"></strong>?
            </p>

            <form :action="'{{ url('operasional/pengiriman/surat-jalan') }}/' + hapusData.id" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Surat Jalan
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika CRUD Pengiriman -->
<script>
    function kelolaSuratJalan() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalCetakTerbuka: false,
            modalStatusTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodeSJ: 'Mode: Daur Ulang Slot Kosong',

            formTambah: {
                nomor_surat_jalan: '',
                id_so: '{{ $daftarSO->first()->id_so ?? "" }}',
                kode_driver: '{{ $daftarDriver->first()->kode_karyawan ?? "" }}',
                kode_aset: '{{ $daftarKendaraan->first()->kode_aset ?? "" }}',
                tanggal_kirim: new Date().toISOString().slice(0, 16),
                status_pengiriman: 'dalam_perjalanan',
                keterangan: ''
            },

            formEdit: {
                id_pengiriman: '',
                nomor_surat_jalan: '',
                id_so: '',
                kode_driver: '',
                kode_aset: '',
                tanggal_kirim: '',
                status_pengiriman: 'menunggu',
                keterangan: ''
            },

            detailPengiriman: {},
            statusData: { id: '', nomor_sj: '', status: '' },
            hapusData: { id: '', nomor_sj: '' },

            initSuratJalan() {
                // Inisialisasi
            },

            bukaModalTambah() {
                this.buatKodeOtomatis('gap');
                this.modalTambahTerbuka = true;
            },

            async buatKodeOtomatis(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.pengiriman.surat_jalan.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambah.nomor_surat_jalan = data.kode_otomatis;
                        this.keteranganKodeSJ = data.keterangan || (mode === 'acak' ? 'Format Tanggal & Acak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat nomor surat jalan:', e);
                }
            },

            async bukaModalCetak(id) {
                try {
                    const res = await fetch(`{{ url('operasional/pengiriman/surat-jalan') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailPengiriman = data.data;
                        this.modalCetakTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data surat jalan untuk pratinjau cetak.');
                }
            },

            async bukaModalEdit(id) {
                try {
                    const res = await fetch(`{{ url('operasional/pengiriman/surat-jalan') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEdit = {
                            id_pengiriman: d.id_pengiriman,
                            nomor_surat_jalan: d.nomor_surat_jalan,
                            id_so: d.id_so,
                            kode_driver: d.kode_driver,
                            kode_aset: d.kode_kendaraan || d.kode_aset,
                            tanggal_kirim: d.tanggal_kirim ? d.tanggal_kirim.slice(0, 16) : '',
                            status_pengiriman: d.status_pengiriman,
                            keterangan: d.keterangan || ''
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data surat jalan untuk diedit.');
                }
            },

            bukaModalStatus(id, nomorSJ, statusSaatIni) {
                this.statusData = {
                    id: id,
                    nomor_sj: nomorSJ,
                    status: statusSaatIni
                };
                this.modalStatusTerbuka = true;
            },

            bukaModalHapus(id, nomorSJ) {
                this.hapusData = {
                    id: id,
                    nomor_sj: nomorSJ
                };
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
