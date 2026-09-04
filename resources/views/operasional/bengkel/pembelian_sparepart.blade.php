@extends('layouts.app')

@section('judul', 'Pembelian & Pengadaan Sparepart - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaPembelianSparepart()" x-init="initPembelian()" class="space-y-6">

    @php
        $opsiSparepart = collect($daftarSparepart ?? [])->map(fn($sp) => [
            'nilai' => $sp->kode_sparepart,
            'label' => $sp->nama_sparepart . ' (' . $sp->kode_sparepart . ')',
            'sub'   => 'Stok: ' . $sp->stok_part . ' ' . $sp->satuan . ' · Rp ' . number_format($sp->harga_satuan, 0, ',', '.')
        ])->values()->toArray();

        $opsiFilterSparepart = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Suku Cadang']
        ], collect($daftarSparepart ?? [])->map(fn($sp) => [
            'nilai' => $sp->kode_sparepart,
            'label' => $sp->nama_sparepart . ' (' . $sp->kode_sparepart . ')'
        ])->values()->toArray());
    @endphp

    <!-- 1. Header Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 font-mono">
                    Pengawas Kendaraan · Pengadaan Bengkel
                </span>
                <span class="text-xs text-slate-400 font-mono">Faktur Pembelian Sparepart</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Pembelian & Pengadaan Sparepart</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pencatatan faktur pengadaan suku cadang truk dari supplier/distributor resmi serta penambahan otomatis ke stok gudang bengkel.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Pembelian Baru</span>
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
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. Ringkasan 4 Kartu KPI Pembelian Sparepart -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Faktur Pembelian -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Faktur Beli</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalFaktur }} <span class="text-xs font-normal text-slate-400 font-sans">Faktur</span></div>
            </div>
        </div>

        <!-- Total Unit Dibeli -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Item Masuk</div>
                <div class="text-xl font-bold text-blue-600 dark:text-blue-400 font-mono mt-0.5">{{ number_format($totalUnitDibeli, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">Unit</span></div>
            </div>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Belanja Bulan Ini</div>
                <div class="text-base font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Total Akumulasi Belanja -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Belanja Sparepart</div>
                <div class="text-base font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">Rp {{ number_format($totalAkumulasiBelanja, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Pembelian Sparepart & Filter -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarPembelian ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('operasional.bengkel.pembelian_sparepart') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari no faktur, supplier, suku cadang, petugas..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Sparepart Dropdown Kustom -->
                <div class="w-full sm:w-64">
                    <x-dropdown-kustom 
                        nama="kode_sparepart"
                        placeholder="-- Semua Suku Cadang --"
                        :opsi="$opsiFilterSparepart"
                        :nilaiAwal="$filterPart ?? 'semua'"
                        :submitOnChange="true"
                        warnaFokus="rose"
                    />
                </div>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Cari
                </button>

                @if(!empty($kataKunci) || ($filterPart !== 'semua' && !empty($filterPart)))
                    <a href="{{ route('operasional.bengkel.pembelian_sparepart') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono">
                Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarPembelian) }}</strong> Faktur Pengadaan
            </div>
        </div>

        <!-- Tabel Pembelian -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Faktur Beli & Tanggal</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Suku Cadang & Kategori</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Toko / Supplier</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Kuantitas & Harga</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Total Pembelian</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarPembelian as $beli)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            
                            <!-- No Faktur & Tanggal -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-rose-600 dark:text-rose-400 text-sm">
                                    {{ $beli->nomor_faktur_beli }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $beli->tanggal_beli_format }}</span>
                                </div>
                            </td>

                            <!-- Sparepart -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                    {{ $beli->sparepart->nama_sparepart ?? $beli->kode_sparepart }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                    Kode: {{ $beli->kode_sparepart }} · {{ $beli->sparepart->kategori_part ?? 'Suku Cadang' }}
                                </div>
                            </td>

                            <!-- Toko / Supplier & Petugas -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $beli->nama_supplier }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Pencatat: {{ $beli->dibuat_oleh }}
                                </div>
                            </td>

                            <!-- Kuantitas & Harga -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap font-mono">
                                <div class="font-bold text-slate-900 dark:text-slate-100">
                                    {{ $beli->jumlah_beli }} {{ $beli->sparepart->satuan ?? 'Unit' }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    @ {{ $beli->harga_beli_rupiah }}
                                </div>
                            </td>

                            <!-- Total Pembelian -->
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm whitespace-nowrap">
                                {{ $beli->total_bayar_rupiah }}
                            </td>

                            <!-- Aksi & Riwayat Real-Time -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Detail -->
                                    <button @click="bukaModalDetail('{{ $beli->id_pembelian_part }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                            title="Lihat Detail Faktur Pembelian">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <!-- Edit -->
                                    <button @click="bukaModalEdit('{{ $beli->id_pembelian_part }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                            title="Ubah Data Faktur">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Hapus -->
                                    <button @click="bukaModalHapus('{{ $beli->id_pembelian_part }}', '{{ $beli->nomor_faktur_beli }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                            title="Hapus Faktur Pembelian">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Riwayat Diedit Real-Time -->
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                     title="Dicatat pada: {{ $beli->terakhir_diedit_waktu }}">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $beli->terakhir_diedit_relatif }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada faktur pembelian sparepart</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Catat pembelian suku cadang baru dengan tombol di kanan atas.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginasi Terpadu -->
        <x-paginasi-tabel :totalData="count($daftarPembelian ?? [])" />
    </div>

    <!-- Modal Tambah Pembelian Sparepart -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Catat Faktur Pembelian Sparepart</h3>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.bengkel.pembelian_sparepart.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Nomor Faktur Beli <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-rose-600 dark:text-rose-400 font-semibold px-1.5 py-0.5 bg-rose-50 dark:bg-rose-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="nomor_faktur_beli" x-model="formTambah.nomor_faktur_beli" required placeholder="FB-SP-001"
                               class="w-full px-3 py-2 rounded-xl bg-rose-50/50 dark:bg-[#1C1E2A] border border-rose-200 dark:border-rose-900/50 text-rose-900 dark:text-rose-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_beli" 
                            modelBind="formTambah.tanggal_beli" 
                            placeholder="Pilih Tanggal Beli"
                            :wajib="true"
                            warnaFokus="rose"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Suku Cadang / Sparepart <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="kode_sparepart"
                        placeholder="-- Pilih Suku Cadang --"
                        :opsi="$opsiSparepart"
                        :wajib="true"
                        warnaFokus="rose"
                        modelBind="formTambah.kode_sparepart"
                    />
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Toko / Supplier <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_supplier" x-model="formTambah.nama_supplier" required placeholder="Nama toko / distributor"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kuantitas Beli <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah_beli" x-model.number="formTambah.jumlah_beli" min="1" required placeholder="4"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30 font-mono">
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="harga_beli" 
                            label="Harga Beli Satuan (Rp)" 
                            modelBind="formTambah.harga_beli" 
                            :wajib="true" 
                            placeholder="3.450.000" 
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pengawas / Pencatat Pembelian <span class="text-rose-500">*</span></label>
                    <input type="text" name="dibuat_oleh" x-model="formTambah.dibuat_oleh" required placeholder="Nama pengawas kendaraan"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">Simpan Faktur</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Pembelian Sparepart -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Faktur Pembelian: <span class="font-mono text-rose-600" x-text="formEdit.nomor_faktur_beli"></span></h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/bengkel/pembelian-sparepart') }}/' + formEdit.id_pembelian_part" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Faktur (Terkunci)</label>
                        <input type="text" :value="formEdit.nomor_faktur_beli" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_beli" 
                            modelBind="formEdit.tanggal_beli" 
                            placeholder="Pilih Tanggal Beli"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Suku Cadang Sparepart <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="kode_sparepart"
                        placeholder="-- Pilih Suku Cadang --"
                        :opsi="$opsiSparepart"
                        :wajib="true"
                        warnaFokus="amber"
                        modelBind="formEdit.kode_sparepart"
                    />
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Toko / Supplier <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_supplier" x-model="formEdit.nama_supplier" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kuantitas Beli <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah_beli" x-model.number="formEdit.jumlah_beli" min="1" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>

                    <div>
                        <x-input-rupiah 
                            nama="harga_beli" 
                            label="Harga Beli Satuan (Rp)" 
                            modelBind="formEdit.harga_beli" 
                            :wajib="true" 
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pengawas Pencatat <span class="text-rose-500">*</span></label>
                    <input type="text" name="dibuat_oleh" x-model="formEdit.dibuat_oleh" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Pembelian Sparepart -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Rincian Faktur Pembelian Sparepart</h3>
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 flex justify-between items-center">
                    <div>
                        <span class="text-[10px] text-rose-600 dark:text-rose-400 font-mono block">No. Faktur Pembelian</span>
                        <strong class="text-base font-mono font-bold text-rose-700 dark:text-rose-300" x-text="detailBeli.nomor_faktur_beli"></strong>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-mono block">Tanggal Beli</span>
                        <strong class="font-mono text-slate-900 dark:text-slate-100" x-text="detailBeli.tanggal_beli_format"></strong>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Suku Cadang:</span>
                        <strong class="text-slate-900 dark:text-slate-100" x-text="detailBeli.sparepart ? detailBeli.sparepart.nama_sparepart : detailBeli.kode_sparepart"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Supplier / Toko:</span>
                        <strong class="text-slate-900 dark:text-slate-100" x-text="detailBeli.nama_supplier"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Kuantitas Dibeli:</span>
                        <strong class="font-mono text-slate-900 dark:text-slate-100" x-text="detailBeli.jumlah_beli + ' Unit'"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
                        <span class="text-slate-500">Harga Beli Satuan:</span>
                        <strong class="font-mono text-slate-900 dark:text-slate-100" x-text="detailBeli.harga_beli_rupiah"></strong>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E2E8F0] dark:border-[#252837] text-sm">
                        <span class="text-slate-700 dark:text-slate-300 font-bold">Total Pembayaran:</span>
                        <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="detailBeli.total_bayar_rupiah"></strong>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500">Pencatat / Pengawas:</span>
                        <span class="text-slate-800 dark:text-slate-200" x-text="detailBeli.dibuat_oleh"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Pembelian -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Faktur Pembelian?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus faktur <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nomor"></strong>? Stok pada inventaris master sparepart akan disesuaikan otomatis.
            </p>

            <form :action="'{{ url('operasional/bengkel/pembelian-sparepart') }}/' + hapusData.id" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Faktur
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika Pembelian Sparepart -->
<script>
    function kelolaPembelianSparepart() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalDetailTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodeFaktur: 'Mode: Daur Ulang Slot Kosong',

            formTambah: {
                nomor_faktur_beli: '',
                kode_sparepart: '{{ $daftarSparepart->first()->kode_sparepart ?? "" }}',
                tanggal_beli: new Date().toISOString().slice(0, 10),
                nama_supplier: '',
                jumlah_beli: 1,
                harga_beli: {{ $daftarSparepart->first()->harga_satuan ?? 0 }},
                dibuat_oleh: 'Bambang Supriyanto (Pengawas Kendaraan)'
            },

            formEdit: {
                id_pembelian_part: '',
                nomor_faktur_beli: '',
                kode_sparepart: '',
                tanggal_beli: '',
                nama_supplier: '',
                jumlah_beli: 1,
                harga_beli: 0,
                dibuat_oleh: ''
            },

            detailBeli: {},
            hapusData: { id: '', nomor: '' },

            get totalBayarTambah() {
                return (parseInt(this.formTambah.jumlah_beli) || 0) * (parseFloat(this.formTambah.harga_beli) || 0);
            },

            daftarHargaPart: {
                @foreach($daftarSparepart as $sp)
                    '{{ $sp->kode_sparepart }}': {{ (float) $sp->harga_satuan }},
                @endforeach
            },

            initPembelian() {
                this.$watch('formTambah.kode_sparepart', (val) => {
                    if (val && this.daftarHargaPart[val] !== undefined) {
                        this.formTambah.harga_beli = this.daftarHargaPart[val];
                    }
                });
            },

            ubahSparepartPilihan(kodePart) {
                if (kodePart && this.daftarHargaPart[kodePart] !== undefined) {
                    this.formTambah.harga_beli = this.daftarHargaPart[kodePart];
                }
            },

            bukaModalTambah() {
                this.buatNomorFaktur('gap');
                this.modalTambahTerbuka = true;
            },

            async buatNomorFaktur(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.bengkel.pembelian_sparepart.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambah.nomor_faktur_beli = data.kode_otomatis;
                        this.keteranganKodeFaktur = data.keterangan || (mode === 'acak' ? 'Format Tanggal & Acak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat nomor faktur:', e);
                }
            },

            async bukaModalEdit(id) {
                try {
                    const res = await fetch(`{{ url('operasional/bengkel/pembelian-sparepart') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEdit = {
                            id_pembelian_part: d.id_pembelian_part,
                            nomor_faktur_beli: d.nomor_faktur_beli,
                            kode_sparepart: d.kode_sparepart,
                            tanggal_beli: d.tanggal_beli ? d.tanggal_beli.split('T')[0] : '',
                            nama_supplier: d.nama_supplier,
                            jumlah_beli: d.jumlah_beli,
                            harga_beli: Math.round(parseFloat(d.harga_beli) || 0),
                            dibuat_oleh: d.dibuat_oleh
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data faktur pembelian.');
                }
            },

            async bukaModalDetail(id) {
                try {
                    const res = await fetch(`{{ url('operasional/bengkel/pembelian-sparepart') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailBeli = data.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail pembelian.');
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
