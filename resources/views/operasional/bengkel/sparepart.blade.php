@extends('layouts.app')

@section('judul', 'Katalog & Stok Sparepart Truk - PT Pura Balkom Jaya')

@section('konten')
<div x-data="kelolaStokSparepart()" x-init="initSparepart()" class="space-y-6">

    <!-- 1. Header Modul -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
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
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
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

                <!-- Filter Kategori -->
                <select name="kategori" onchange="this.form.submit()"
                        class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <option value="semua" {{ ($kategoriFilter ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($daftarKategori as $kat)
                        <option value="{{ $kat }}" {{ ($kategoriFilter ?? '') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>

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
            <table class="w-full text-left text-xs">
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
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            
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

                            <!-- Aksi & Mutasi Stok Cepat -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Mutasi Cepat -->
                                    <button @click="bukaModalMutasi('{{ $part->kode_sparepart }}', '{{ $part->nama_sparepart }}', {{ $part->stok_part }}, '{{ $part->satuan }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 transition-colors"
                                            title="Penyesuaian Kuantitas Fisik Suku Cadang">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                        <span>Mutasi</span>
                                    </button>

                                    <!-- Edit -->
                                    <button @click="bukaModalEdit('{{ $part->kode_sparepart }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                            title="Ubah Data Sparepart">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Hapus -->
                                    <button @click="bukaModalHapus('{{ $part->kode_sparepart }}', '{{ $part->nama_sparepart }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                            title="Hapus Sparepart">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Riwayat Diedit Real-Time -->
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                     title="Terakhir diperbarui: {{ $part->terakhir_diedit_waktu }}">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $part->terakhir_diedit_relatif }}</span>
                                </div>
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
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL FORM: TAMBAH MASTER SPAREPART -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Tambah Suku Cadang Sparepart Baru</h2>
                        <p class="text-[11px] text-slate-400">Pendaftaran item katalog inventaris bengkel truk.</p>
                    </div>
                </div>
                <button @click="modalTambahTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('operasional.bengkel.sparepart.simpan') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Generator Kode Part -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div>
                            <label class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                Kode Sparepart <span class="text-rose-500">*</span>
                            </label>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="keteranganKodePart"></div>
                        </div>
                        
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" @click="buatKodeOtomatis('gap')"
                                    class="px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 hover:bg-amber-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Daur Ulang (PRT-001)</span>
                            </button>
                            <button type="button" @click="buatKodeOtomatis('acak')"
                                    class="px-2.5 py-1 text-[11px] font-semibold text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900/30 hover:bg-purple-200 rounded-lg transition-colors flex items-center gap-1 shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Kode Acak</span>
                            </button>
                        </div>
                    </div>

                    <input type="text" name="kode_sparepart" x-model="formTambah.kode_sparepart" required placeholder="Contoh: PRT-001 atau PRT-OLI-15W40"
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-amber-600 dark:text-amber-400 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <!-- Nama Sparepart -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Suku Cadang / Sparepart <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_sparepart" x-model="formTambah.nama_sparepart" required placeholder="Contoh: Oli Mesin Diesel Meditran SX 15W-40"
                           class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Kategori Part -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Sparepart <span class="text-rose-500">*</span></label>
                        <select name="kategori_part" x-model="formTambah.kategori_part" required
                                class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                            <option value="Pelumas & Oli">Pelumas & Oli</option>
                            <option value="Ban & Roda">Ban & Roda</option>
                            <option value="Pengereman">Pengereman (Kampas/Brake)</option>
                            <option value="Filter">Filter (Oli/Solar/Udara)</option>
                            <option value="Elektrikal & Aki">Elektrikal & Aki</option>
                            <option value="Mesin & Transmisi">Mesin & Transmisi</option>
                            <option value="Suspensi & Sasis">Suspensi & Sasis</option>
                            <option value="Lainnya">Lainnya / Aksesoris</option>
                        </select>
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="satuan" x-model="formTambah.satuan" required placeholder="Contoh: Pcs, Set, Drum, Liter"
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <!-- Stok Awal -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Awal Fisik <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_part" x-model="formTambah.stok_part" min="0" required placeholder="10"
                               class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <!-- Harga Beli Satuan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Beli Standar (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga_satuan" x-model="formTambah.harga_satuan" step="1000" min="0" required placeholder="150000"
                               class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-emerald-600 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalTambahTerbuka = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 active:scale-95 rounded-xl transition-all shadow-md shadow-amber-600/20">
                        Simpan Sparepart
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL FORM: EDIT MASTER SPAREPART -->
    <!-- ========================================================================= -->
    <div x-show="modalEditTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Ubah Data Suku Cadang</h2>
                        <p class="text-[11px] text-slate-400">Kode: <span class="font-mono font-bold text-amber-600" x-text="formEdit.kode_sparepart"></span></p>
                    </div>
                </div>
                <button @click="modalEditTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('operasional/bengkel/sparepart') }}/' + formEdit.kode_sparepart" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Sparepart (Terkunci)</label>
                    <input type="text" :value="formEdit.kode_sparepart" disabled
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Suku Cadang <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_sparepart" x-model="formEdit.nama_sparepart" required
                           class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Sparepart <span class="text-rose-500">*</span></label>
                        <select name="kategori_part" x-model="formEdit.kategori_part" required
                                class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                            <option value="Pelumas & Oli">Pelumas & Oli</option>
                            <option value="Ban & Roda">Ban & Roda</option>
                            <option value="Pengereman">Pengereman (Kampas/Brake)</option>
                            <option value="Filter">Filter (Oli/Solar/Udara)</option>
                            <option value="Elektrikal & Aki">Elektrikal & Aki</option>
                            <option value="Mesin & Transmisi">Mesin & Transmisi</option>
                            <option value="Suspensi & Sasis">Suspensi & Sasis</option>
                            <option value="Lainnya">Lainnya / Aksesoris</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan Barang <span class="text-rose-500">*</span></label>
                        <input type="text" name="satuan" x-model="formEdit.satuan" required
                               class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kuantitas Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_part" x-model="formEdit.stok_part" min="0" required
                               class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Beli Satuan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga_satuan" x-model="formEdit.harga_satuan" step="1000" min="0" required
                               class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-emerald-600 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E2E8F0] dark:border-[#252837]">
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
    <!-- 7. MODAL MUTASI & PENYESUAIAN STOK CEPAT -->
    <!-- ========================================================================= -->
    <div x-show="modalMutasiTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalMutasiTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6">
            
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

            <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] mb-4 flex justify-between items-center text-xs">
                <span class="text-slate-500">Stok Fisik Saat Ini:</span>
                <strong class="font-mono text-slate-900 dark:text-slate-100 text-sm" x-text="mutasiData.stok_sekarang + ' ' + mutasiData.satuan"></strong>
            </div>

            <form :action="'{{ url('operasional/bengkel/sparepart') }}/' + mutasiData.kode + '/mutasi'" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Aksi Mutasi <span class="text-rose-500">*</span></label>
                    <select name="tipe_mutasi" x-model="mutasiData.tipe" required
                            class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                        <option value="masuk">➕ Tambah Stok Masuk (Penerimaan Toko)</option>
                        <option value="keluar">➖ Kurang Stok Keluar (Pemakaian Servis / Rusak)</option>
                        <option value="atur">⚙️ Set Kuantitas Fisik Langsung (Hasil Opname)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Kuantitas <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah" min="1" required placeholder="Contoh: 2"
                           class="w-full px-3.5 py-2 text-xs font-mono font-bold rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Referensi SPK / Faktur</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Pemakaian SPK-002 atau Koreksi Opname"
                           class="w-full px-3.5 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
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

    <!-- ========================================================================= -->
    <!-- 8. MODAL KONFIRMASI HAPUS SPAREPART -->
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
                            harga_satuan: d.harga_satuan
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
