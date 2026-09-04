@extends('layouts.app')

@section('judul', 'Master Data Toko Bangunan & Proyek - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaTokoBangunan()" x-init="initToko()" class="space-y-6">

    <!-- 1. Header Modul & Tombol Aksi -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 font-mono">
                    Master Finansial & Distribusi
                </span>
                <span class="text-xs text-slate-400 font-mono">Titik Drop Point & Cabang Pelanggan</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data Toko Bangunan & Proyek Cabang</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola master outlet toko fisik, proyek konstruksi, dan gudang transit yang terhubung dengan entitas Customer Pemilik.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.customer.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Master Customer Pemilik</span>
            </a>
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 rounded-xl transition-all shadow-md shadow-emerald-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Toko / Proyek</span>
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

    @if(session('gagal') || session('error'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('gagal') ?? session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:text-rose-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- 3. Ringkasan Kartu KPI -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Toko & Proyek -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Toko & Proyek</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalToko }} <span class="text-xs font-normal text-slate-400 font-sans">Titik</span></div>
            </div>
        </div>

        <!-- Toko Retail Fisik -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Toko Retail Fisik</div>
                <div class="text-xl font-bold text-sky-600 dark:text-sky-400 font-mono mt-0.5">{{ $totalRetail }} <span class="text-xs font-normal text-slate-400 font-sans">Outlet</span></div>
            </div>
        </div>

        <!-- Proyek Konstruksi -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Proyek Konstruksi</div>
                <div class="text-xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">{{ $totalProyek }} <span class="text-xs font-normal text-slate-400 font-sans">Lokasi</span></div>
            </div>
        </div>

        <!-- Mitra Pemilik Terhubung -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Customer Pemilik</div>
                <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 font-mono mt-0.5">{{ $totalCustomerTerhubung }} <span class="text-xs font-normal text-slate-400 font-sans">Mitra</span></div>
            </div>
        </div>
    </div>

    <!-- 4. Filter & Tabel Data -->
    <div x-data="tabelPaginasi({ totalData: {{ $daftarToko->count() }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Search & Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('master.toko_bangunan.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari kode toko, nama toko/proyek, penanggung jawab..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Customer -->
                <select name="customer" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <option value="">-- Semua Customer Pemilik --</option>
                    @foreach($daftarCustomer as $c)
                        <option value="{{ $c->kode_customer }}" {{ ($filterCustomer ?? '') == $c->kode_customer ? 'selected' : '' }}>
                            {{ $c->nama_pemilik }} ({{ $c->kode_customer }})
                        </option>
                    @endforeach
                </select>

                <!-- Filter Wilayah -->
                <select name="wilayah" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <option value="">-- Semua Wilayah Zonasi --</option>
                    @foreach($daftarWilayah as $w)
                        <option value="{{ $w->kode_wilayah }}" {{ ($filterWilayah ?? '') == $w->kode_wilayah ? 'selected' : '' }}>
                            {{ $w->nama_wilayah }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Filter
                </button>
                @if(!empty($kataKunci) || !empty($filterWilayah) || !empty($filterCustomer))
                    <a href="{{ route('master.toko_bangunan.index') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono shrink-0">
                Total Data: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarToko->count() }}</span>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Kode Toko</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Nama Toko / Proyek</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Customer Pemilik</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Wilayah Zonasi</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Kontak & Alamat</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarToko as $toko)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $toko->kode_toko }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                    <span>{{ $toko->nama_toko_bangunan }}</span>
                                    @if($toko->tipe_lokasi === 'proyek_kontraktor')
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400">Proyek</span>
                                    @elseif($toko->tipe_lokasi === 'gudang_transit')
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-400">Gudang</span>
                                    @else
                                        <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-400">Retail</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">PIC: {{ $toko->penanggung_jawab }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $toko->customer->nama_pemilik ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    {{ $toko->kode_customer }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $toko->wilayah->nama_wilayah ?? $toko->kode_wilayah }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-mono text-[11px] text-slate-800 dark:text-slate-200">{{ $toko->no_hp_toko }}</div>
                                <div class="text-[10px] text-slate-400 truncate max-w-xs" title="{{ $toko->alamat_lengkap }}">
                                    {{ $toko->alamat_lengkap }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($toko->status_toko === 'aktif')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$toko->kode_toko"
                                    labelSalin="Salin Kode"
                                    modulIzin="master_customer"
                                    aksiDetail="bukaModalDetail('{{ $toko->kode_toko }}')"
                                    labelDetail="Detail"
                                    aksiEdit="bukaModalEdit({{ json_encode($toko) }})"
                                    labelEdit="Edit"
                                    aksiHapus="{{ route('master.toko_bangunan.hapus', $toko->kode_toko) }}"
                                    labelHapus="Hapus"
                                    pesanHapus="Apakah Anda yakin ingin menghapus toko/proyek {{ $toko->nama_toko_bangunan }} ({{ $toko->kode_toko }})?"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data toko bangunan atau proyek cabang yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Toolbar Paginasi -->
        <x-paginasi-tabel :totalData="count($daftarToko ?? [])" />
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL FORM: TAMBAH TOKO BANGUNAN / PROYEK -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Tambah Toko Bangunan / Proyek Baru</h2>
                        <p class="text-[11px] text-slate-400">Hubungkan outlet cabang baru ke entitas Customer Pemilik.</p>
                    </div>
                </div>
                <button @click="modalTambahTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('master.toko_bangunan.simpan') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Toko / Proyek <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_toko" x-model="formTambah.kode_toko" required placeholder="TKB-001"
                               class="w-full px-3 py-2 rounded-xl bg-emerald-50/50 dark:bg-[#1C1E2A] border border-emerald-200 dark:border-emerald-900/50 text-emerald-900 dark:text-emerald-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer Pemilik (Entitas Induk) <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_customer"
                            placeholder="-- Pilih Customer Pemilik --"
                            :opsi="$opsiCustomer"
                            :wajib="true"
                            warnaFokus="emerald"
                            modelBind="formTambah.kode_customer"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko / Proyek Cabang <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_toko_bangunan" x-model="formTambah.nama_toko_bangunan" required placeholder="TB Maju Jaya Cabang Cikarang"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Lokasi / Kategori <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="tipe_lokasi"
                            placeholder="-- Pilih Tipe --"
                            :opsi="$opsiTipeLokasi"
                            :wajib="true"
                            warnaFokus="emerald"
                            modelBind="formTambah.tipe_lokasi"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah Zonasi Distribusi <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_wilayah"
                            placeholder="-- Pilih Wilayah --"
                            :opsi="$opsiWilayah"
                            :wajib="true"
                            warnaFokus="emerald"
                            modelBind="formTambah.kode_wilayah"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Toko <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_toko"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusToko"
                            :wajib="true"
                            warnaFokus="emerald"
                            modelBind="formTambah.status_toko"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Penanggung Jawab Lapangan (PIC) <span class="text-rose-500">*</span></label>
                        <input type="text" name="penanggung_jawab" x-model="formTambah.penanggung_jawab" required placeholder="Nama Kepala Toko / Site Manager"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / Telepon Toko <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp_toko" x-model="formTambah.no_hp_toko" required placeholder="0812-xxxx-xxxx" inputmode="numeric" data-hanya-angka="true"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap Pengiriman Semen <span class="text-rose-500">*</span></label>
                    <textarea name="alamat_lengkap" x-model="formTambah.alamat_lengkap" required rows="2" placeholder="Jl. Raya Utama No. ... (Detail patokan jalan)"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Titik Koordinat / Maps <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <input type="text" name="titik_koordinat" x-model="formTambah.titik_koordinat" placeholder="-6.2088, 106.8456"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalTambahTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Simpan Toko / Proyek</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL FORM: EDIT TOKO BANGUNAN / PROYEK -->
    <!-- ========================================================================= -->
    <div x-show="modalEditTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Ubah Data Toko / Proyek: <span class="font-mono text-emerald-600" x-text="formEdit.kode_toko"></span></h2>
                        <p class="text-[11px] text-slate-400">Perbarui spesifikasi titik pengiriman atau kontak cabang.</p>
                    </div>
                </div>
                <button @click="modalEditTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('master/toko-bangunan') }}/' + formEdit.kode_toko" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Toko (Terkunci)</label>
                        <input type="text" :value="formEdit.kode_toko" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer Pemilik (Entitas Induk) <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_customer"
                            placeholder="-- Pilih Customer Pemilik --"
                            :opsi="$opsiCustomer"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_customer"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko / Proyek Cabang <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_toko_bangunan" x-model="formEdit.nama_toko_bangunan" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Lokasi / Kategori <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="tipe_lokasi"
                            placeholder="-- Pilih Tipe --"
                            :opsi="$opsiTipeLokasi"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.tipe_lokasi"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah Zonasi Distribusi <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_wilayah"
                            placeholder="-- Pilih Wilayah --"
                            :opsi="$opsiWilayah"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_wilayah"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Toko <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_toko"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusToko"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.status_toko"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Penanggung Jawab Lapangan (PIC) <span class="text-rose-500">*</span></label>
                        <input type="text" name="penanggung_jawab" x-model="formEdit.penanggung_jawab" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / Telepon Toko <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp_toko" x-model="formEdit.no_hp_toko" required inputmode="numeric" data-hanya-angka="true"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap Pengiriman Semen <span class="text-rose-500">*</span></label>
                    <textarea name="alamat_lengkap" x-model="formEdit.alamat_lengkap" required rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Titik Koordinat / Maps <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <input type="text" name="titik_koordinat" x-model="formEdit.titik_koordinat"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalEditTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL DETAIL KINERJA 360 DERAJAT TOKO BANGUNAN -->
    <!-- ========================================================================= -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold font-mono text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="detailData.toko ? detailData.toko.nama_toko_bangunan : ''"></h2>
                        <p class="text-[11px] text-slate-600 dark:text-slate-300 font-mono font-medium" x-text="detailData.toko ? detailData.toko.kode_toko : ''"></p>
                    </div>
                </div>
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <!-- Info Customer Induk -->
                <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Customer Pemilik Induk</div>
                        <div class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5" x-text="detailData.customer ? detailData.customer.nama_pemilik : '-'"></div>
                        <div class="text-[11px] text-slate-600 dark:text-slate-400 font-mono" x-text="detailData.customer ? detailData.customer.kode_customer : ''"></div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Plafon Kredit Pemilik</div>
                        <div class="text-sm font-mono font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5" x-text="'Rp ' + (detailData.customer ? new Intl.NumberFormat('id-ID').format(detailData.customer.plafon_piutang) : '0')"></div>
                    </div>
                </div>

                <!-- Kartu Performa Ringkas -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Total Nilai Pembelian Toko</div>
                        <div class="text-base font-extrabold font-mono text-slate-900 dark:text-white mt-1" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(detailData.total_penjualan || 0)"></div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Total Transaksi Selesai</div>
                        <div class="text-base font-extrabold font-mono text-emerald-600 dark:text-emerald-400 mt-1" x-text="(detailData.total_transaksi || 0) + ' Transaksi'"></div>
                    </div>
                </div>

                <!-- Detail Lokasi & Kontak -->
                <div class="p-4 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 font-medium">Penanggung Jawab:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 ml-1" x-text="detailData.toko ? detailData.toko.penanggung_jawab : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium">No. Telepon:</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200 ml-1" x-text="detailData.toko ? detailData.toko.no_hp_toko : '-'"></span>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Alamat Pengiriman:</span>
                        <div class="text-slate-700 dark:text-slate-300 mt-0.5" x-text="detailData.toko ? detailData.toko.alamat_lengkap : '-'"></div>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button type="button" @click="modalDetailTerbuka = false" class="px-5 py-2 font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function kelolaTokoBangunan() {
    return {
        modalTambahTerbuka: false,
        modalEditTerbuka: false,
        modalDetailTerbuka: false,
        formTambah: {
            kode_toko: '{{ $kodeOtomatis }}',
            kode_customer: '',
            kode_wilayah: '',
            nama_toko_bangunan: '',
            tipe_lokasi: 'toko_retail',
            penanggung_jawab: '',
            no_hp_toko: '',
            alamat_lengkap: '',
            titik_koordinat: '',
            status_toko: 'aktif',
        },
        formEdit: {
            kode_toko: '',
            kode_customer: '',
            kode_wilayah: '',
            nama_toko_bangunan: '',
            tipe_lokasi: '',
            penanggung_jawab: '',
            no_hp_toko: '',
            alamat_lengkap: '',
            titik_koordinat: '',
            status_toko: '',
        },
        detailData: {},

        initToko() {
            // Inisialisasi
        },

        bukaModalTambah() {
            this.modalTambahTerbuka = true;
        },

        bukaModalEdit(toko) {
            this.formEdit = Object.assign({}, toko);
            this.modalEditTerbuka = true;
        },

        async bukaModalDetail(kodeToko) {
            try {
                const respon = await fetch('{{ url("master/toko-bangunan") }}/' + kodeToko + '/detail');
                const data = await respon.json();
                if (data.status === 'sukses') {
                    this.detailData = data;
                    this.modalDetailTerbuka = true;
                }
            } catch (error) {
                console.error('Gagal memuat detail toko:', error);
            }
        }
    }
}
</script>
@endsection
