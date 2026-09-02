@extends('layouts.app')

@section('judul', 'Master Data Jenis Aset - PT Pura Balkom Jaya')

@section('konten')
<div x-data="kelolaJenisAset()" x-init="initJenisAset()" class="space-y-6">

    <!-- 1. Header Modul & Tombol Aksi -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-500/20 font-mono">
                    Master Logistik & Armada
                </span>
                <span class="text-xs text-slate-400 font-mono">Klasifikasi Unit Aset</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data Jenis Aset (Kategori Kendaraan)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola master tipe armada logistik (Tronton, Colt Diesel, Tangki Semen Curah, Trailer), kapasitas muatan, dan spesifikasi standar.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 active:scale-95 rounded-xl transition-all shadow-md shadow-violet-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Jenis Aset</span>
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
                <span>Terdapat kesalahan validasi:</span>
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
        <!-- Total Kategori -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Kategori</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalJenisAset }} <span class="text-xs font-normal text-slate-400 font-sans">Jenis</span></div>
            </div>
        </div>

        <!-- Total Armada Terhubung -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Armada Terpasang</div>
                <div class="text-xl font-bold text-orange-600 dark:text-orange-400 font-mono mt-0.5">{{ $totalArmadaTerpasang }} <span class="text-xs font-normal text-slate-400 font-sans">Unit Truk</span></div>
            </div>
        </div>

        <!-- Truk Berat (Tronton/Tangki/Trailer) -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Truk Berat (>20 Ton)</div>
                <div class="text-xl font-bold text-blue-600 dark:text-blue-400 font-mono mt-0.5">{{ $kategoriTrukBerat }} <span class="text-xs font-normal text-slate-400 font-sans">Jenis</span></div>
            </div>
        </div>

        <!-- Truk Sedang / Ringan -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Truk Sedang & Pick Up</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $kategoriTrukSedang }} <span class="text-xs font-normal text-slate-400 font-sans">Jenis</span></div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Data & Bar Pencarian -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Search Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <form method="GET" action="{{ route('master.jenis_aset.index') }}" class="flex items-center gap-2.5 flex-1 max-w-md">
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari kode jenis, nama tipe truk, atau keterangan..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Cari
                </button>
                @if(!empty($kataKunci))
                    <a href="{{ route('master.jenis_aset.index') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono">
                Total <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarJenisAset) }}</strong> Kategori Jenis Aset
            </div>
        </div>

        <!-- Tabel Jenis Aset -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
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
                            
                            <!-- Kode Jenis Aset -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg font-mono font-bold text-xs bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-500/20">
                                    {{ $j->kode_jenis_aset }}
                                </span>
                            </td>

                            <!-- Nama Jenis Aset -->
                            <td class="px-4 py-3.5 font-bold text-slate-900 dark:text-slate-100 text-sm whitespace-nowrap">
                                {{ $j->jenis_aset }}
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 max-w-md">
                                {{ $j->keterangan ?: '-' }}
                            </td>

                            <!-- Jumlah Armada Terdaftar -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-mono font-bold {{ $j->kendaraan_count > 0 ? 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
                                    <span>{{ $j->kendaraan_count }} Unit</span>
                                </span>
                            </td>

                            <!-- Tombol Aksi & Riwayat Terakhir Diedit Real-Time -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Detail -->
                                    <button @click="bukaModalDetail('{{ $j->kode_jenis_aset }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                            title="Lihat Detail & Daftar Armada">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <!-- Edit -->
                                    <button @click="bukaModalEdit('{{ $j->kode_jenis_aset }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                            title="Ubah Kategori Jenis Aset">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Hapus -->
                                    <button @click="bukaModalHapus('{{ $j->kode_jenis_aset }}', '{{ addslashes($j->jenis_aset) }}', {{ $j->kendaraan_count }})"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                            title="Hapus Kategori">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Riwayat Terakhir Diedit Real-Time -->
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
                                    <div class="text-xs text-slate-400 mt-0.5">Silakan tambah kategori jenis aset armada baru.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL FORM: TAMBAH JENIS ASET -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalTambahTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            
            <!-- Header -->
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
                <button @click="modalTambahTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form Tambah -->
            <form action="{{ route('master.jenis_aset.simpan') }}" method="POST" class="p-6 space-y-4 text-xs">
                @csrf

                <!-- Generator Kode Jenis Cerdas -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    Kode Jenis Aset <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] text-violet-600 dark:text-violet-400 font-semibold px-1.5 py-0.5 bg-violet-50 dark:bg-violet-950/50 rounded-md">Otomatis</span>
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="keteranganKodeOtomatis"></div>
                        </div>
                        
                        <!-- Tombol Generator Mode -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="buatKodeOtomatis('gap')"
                                    class="px-2.5 py-1 text-[11px] font-semibold text-violet-700 dark:text-violet-300 bg-violet-100 dark:bg-violet-900/30 hover:bg-violet-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs"
                                    title="Daur ulang nomor: mengisi slot nomor terkecil yang kosong (JNS-001, JNS-002, dst)">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Isi Nomor Kosong</span>
                            </button>
                            <button type="button" @click="buatKodeOtomatis('acak')"
                                    class="px-2.5 py-1 text-[11px] font-semibold text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs"
                                    title="Buat kode acak alfanumerik anti-tebak">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Kode Acak</span>
                            </button>
                        </div>
                    </div>

                    <input type="text" name="kode_jenis_aset" x-model="formTambah.kode_jenis_aset" required placeholder="Contoh: KND-TRN atau JNS-001"
                           class="w-full px-3 py-2 text-xs font-mono font-bold rounded-xl bg-white dark:bg-[#14161F] border border-violet-200 dark:border-violet-900/50 text-violet-600 dark:text-violet-400 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>

                <!-- Nama Jenis Aset -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Jenis / Kategori Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="jenis_aset" x-model="formTambah.jenis_aset" required placeholder="Contoh: Truk Tronton Wingbox"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan & Spesifikasi Muatan <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" x-model="formTambah.keterangan" rows="3" required placeholder="Contoh: Kapasitas 25 - 30 Ton (500 - 600 Zak Semen)"
                              class="w-full px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30"></textarea>
                </div>

                <!-- Footer Submit -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalTambahTerbuka = false"
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

    <!-- ========================================================================= -->
    <!-- 6. MODAL FORM: EDIT JENIS ASET -->
    <!-- ========================================================================= -->
    <div x-show="modalEditTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalEditTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Ubah Data Jenis Aset</h2>
                        <p class="text-[11px] text-slate-400">Kode: <span class="font-mono font-bold text-violet-600 dark:text-violet-400" x-text="formEdit.kode_jenis_aset"></span></p>
                    </div>
                </div>
                <button @click="modalEditTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form Edit -->
            <form :action="'{{ url('master/jenis-aset') }}/' + formEdit.kode_jenis_aset" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <!-- Kode Terkunci -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Jenis Aset (Terkunci)</label>
                    <input type="text" :value="formEdit.kode_jenis_aset" disabled
                           class="w-full px-3 py-2 text-xs font-mono font-bold rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 cursor-not-allowed">
                </div>

                <!-- Nama Jenis Aset -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Jenis / Kategori Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="jenis_aset" x-model="formEdit.jenis_aset" required
                           class="w-full px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <!-- Keterangan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan & Spesifikasi Muatan <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" x-model="formEdit.keterangan" rows="3" required
                              class="w-full px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <!-- Footer Submit -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalEditTerbuka = false"
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

    <!-- ========================================================================= -->
    <!-- 7. MODAL DETAIL: DAFTAR ARMADA TERPASANG -->
    <!-- ========================================================================= -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl my-8">
            
            <!-- Header -->
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
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
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
                            <table class="tabel-bertingkat w-full text-left text-xs">
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

            <!-- Footer -->
            <div class="flex items-center justify-end px-6 py-3.5 border-t border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <button @click="modalDetailTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. MODAL KONFIRMASI HAPUS -->
    <!-- ========================================================================= -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Jenis Aset?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus kategori jenis aset <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nama"></strong> (<span class="font-mono font-bold" x-text="hapusData.kode"></span>)?
            </p>

            <template x-if="hapusData.jumlahTruk > 0">
                <div class="mt-3 p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-300 text-[11px] text-left flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong>Perhatian:</strong> Kategori ini memiliki <span class="font-bold font-mono" x-text="hapusData.jumlahTruk"></span> unit truk yang terhubung. Hapus atau pindahkan unit kendaraan terlebih dahulu.
                    </div>
                </div>
            </template>

            <form :action="'{{ url('master/jenis-aset') }}/' + hapusData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
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

<!-- Script Alpine.js Logika CRUD Jenis Aset -->
<script>
    function kelolaJenisAset() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalDetailTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodeOtomatis: 'Mode: Daur Ulang Slot Kosong',

            formTambah: {
                kode_jenis_aset: '',
                jenis_aset: '',
                keterangan: ''
            },

            formEdit: {
                kode_jenis_aset: '',
                jenis_aset: '',
                keterangan: ''
            },

            detailJenisAset: {},
            hapusData: {
                kode: '',
                nama: '',
                jumlahTruk: 0
            },

            initJenisAset() {
                // Inisialisasi awal
            },

            bukaModalTambah() {
                this.formTambah.jenis_aset = '';
                this.formTambah.keterangan = '';
                this.buatKodeOtomatis('gap');
                this.modalTambahTerbuka = true;
            },

            async buatKodeOtomatis(mode = 'gap') {
                try {
                    const response = await fetch(`{{ route("master.jenis_aset.buat_kode") }}?mode=${mode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        this.formTambah.kode_jenis_aset = hasil.kode_otomatis;
                        this.keteranganKodeOtomatis = hasil.keterangan || (mode === 'acak' ? 'Mode: Kode Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode otomatis:', e);
                }
            },

            async bukaModalDetail(kode) {
                try {
                    const response = await fetch(`{{ url('master/jenis-aset') }}/${kode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        this.detailJenisAset = hasil.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail jenis aset.');
                }
            },

            async bukaModalEdit(kode) {
                try {
                    const response = await fetch(`{{ url('master/jenis-aset') }}/${kode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        this.formEdit = {
                            kode_jenis_aset: hasil.data.kode_jenis_aset,
                            jenis_aset: hasil.data.jenis_aset,
                            keterangan: hasil.data.keterangan || ''
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data untuk diedit.');
                }
            },

            bukaModalHapus(kode, nama, jumlahTruk) {
                this.hapusData.kode = kode;
                this.hapusData.nama = nama;
                this.hapusData.jumlahTruk = jumlahTruk;
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
