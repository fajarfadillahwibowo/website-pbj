@extends('layouts.app')

@section('judul', 'Aset & Inventaris Perusahaan')

@section('konten')
<div class="space-y-5" x-data="kelolaAsetPerusahaan()">
    <!-- Flash Notification -->
    @if(session('sukses'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('sukses') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Header Modul Aset Perusahaan -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Buku Besar & Akuntansi · SPV Keuangan</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Inventarisasi Aset Tetap Perusahaan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pendataan aktiva tetap: Armada Truk Tronton, Wingbox, Dump Truck, Gudang, dan Mesin Operasional.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Aset
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Aset -->
    <div class="wadah-bertingkat grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Nilai Perolehan Aset</div>
                <div class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-0.5 font-mono">Rp {{ number_format($totalNilaiAset ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Unit Aset</div>
                <div class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalAset ?? 0 }} Unit</div>
            </div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
            </div>
            <div>
                <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Armada Truk Aktif</div>
                <div class="text-base sm:text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">{{ $totalTruk ?? 0 }} Kendaraan</div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Aset -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.akuntansi.aset') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterJenisAset = array_merge([['nilai' => '', 'label' => '-- Semua Jenis Aset --']], ($daftarJenis ?? collect())->map(fn($j) => [
                    'nilai' => $j->kode_jenis_aset,
                    'label' => $j->jenis_aset
                ])->toArray());
                $opsiJenisAsetModal = ($daftarJenis ?? collect())->map(fn($j) => [
                    'nilai' => $j->kode_jenis_aset,
                    'label' => $j->jenis_aset,
                    'sub'   => 'Kode: ' . $j->kode_jenis_aset
                ])->toArray();
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nama aset / kode / no polisi..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-48">
                    <x-dropdown-kustom 
                        nama="jenis" 
                        :nilaiAwal="$filterJenis ?? ''" 
                        placeholder="-- Semua Jenis Aset --" 
                        :opsi="$opsiFilterJenisAset" 
                        warnaFokus="indigo"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                @if(!empty($kataKunci) || !empty($filterJenis))
                    <a href="{{ route('keuangan.akuntansi.aset') }}" class="text-xs font-semibold text-rose-600 hover:underline">Reset</a>
                @endif
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: data_aset</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Aset</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Aset & Spesifikasi</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kategori Jenis</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Plat / No. Polisi</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Harga Perolehan</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Tanggal Beli</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarAset ?? [] as $aset)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                {{ $aset->kode_aset }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">
                                {{ $aset->nama_aset }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/20">
                                    {{ $aset->jenis_aset ?? ($aset->jenisAset->jenis_aset ?? $aset->kode_jenis_aset) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                @if(!empty($aset->no_polisi) && $aset->no_polisi !== '-')
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                        {{ $aset->no_polisi }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                Rp {{ number_format($aset->harga_aset, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-slate-500 whitespace-nowrap">
                                {{ $aset->tanggal_pembelian ? date('d/m/Y', strtotime($aset->tanggal_pembelian)) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button @click="bukaDetail('{{ $aset->kode_aset }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                            title="Lihat Detail Aset">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <button @click="bukaEdit('{{ $aset->kode_aset }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                            title="Ubah Data Aset">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button @click="bukaHapus('{{ $aset->kode_aset }}', '{{ addslashes($aset->nama_aset) }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                            title="Hapus Aset">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Riwayat Diedit Real-Time -->
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center justify-center gap-1 font-mono cursor-help"
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
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada aset tetap tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Aset -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Aset Perusahaan</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.akuntansi.aset.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Aset <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_aset" value="{{ $kodeOtomatis }}" required placeholder="AST-001"
                               class="w-full px-3 py-2 rounded-xl bg-indigo-50/50 dark:bg-[#1C1E2A] border border-indigo-200 dark:border-indigo-900/50 text-indigo-900 dark:text-indigo-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Aset <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_jenis_aset"
                            placeholder="-- Pilih Jenis Aset --"
                            :opsi="$opsiJenisAsetModal"
                            :wajib="true"
                            warnaFokus="indigo"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_aset" required placeholder="Hino Dutro 130 HD"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
                <div class="space-y-3">
                    <x-input-plat-nomor 
                        nama="no_polisi" 
                        :wajib="false" 
                        label="No. Polisi (Khusus Kendaraan/Truk)" 
                    />

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_pembelian" 
                            nilaiAwal="{{ date('Y-m-d') }}" 
                            placeholder="Pilih Tanggal Pembelian"
                            :wajib="true"
                            warnaFokus="indigo"
                        />
                    </div>
                </div>
                <div>
                    <x-input-rupiah 
                        nama="harga_aset" 
                        label="Harga Perolehan Unit (Rp)" 
                        :wajib="true" 
                        placeholder="400.000.000" 
                    />
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Aset -->
    <div x-show="modalDetailTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl my-8">
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
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
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
                    <button @click="modalDetailTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Aset -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Aset Perusahaan</h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('keuangan/akuntansi/aset-perusahaan') }}/' + formEdit.kode_aset" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Aset (Terkunci)</label>
                        <input type="text" :value="formEdit.kode_aset" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Jenis <span class="text-rose-500">*</span></label>
                        <select name="kode_jenis_aset" x-model="formEdit.kode_jenis_aset" required
                                class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            @foreach($daftarJenis ?? [] as $j)
                                <option value="{{ $j->kode_jenis_aset }}">{{ $j->jenis_aset }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Aset <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_aset" x-model="formEdit.nama_aset" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Plat / No. Polisi</label>
                        <input type="text" name="no_polisi" x-model="formEdit.no_polisi" placeholder="B 1234 ABC"
                               class="w-full px-3 py-2 rounded-xl font-mono uppercase bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Aset</label>
                        <select name="status_aset" x-model="formEdit.status_aset"
                                class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
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
                        <input type="date" name="tanggal_pembelian" x-model="formEdit.tanggal_pembelian" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Perolehan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga_aset" x-model="formEdit.harga_aset" required min="0"
                               class="w-full px-3 py-2 rounded-xl font-mono bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="modalEditTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Aset -->
    <div x-show="modalHapusTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 text-center mb-1">Hapus Aset Perusahaan?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 text-center mb-4 leading-relaxed">
                Anda akan menghapus data aset <strong class="text-slate-800 dark:text-slate-200" x-text="hapusData.nama"></strong> (<span class="font-mono text-indigo-600" x-text="hapusData.kode"></span>). Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="'{{ url('keuangan/akuntansi/aset-perusahaan') }}/' + hapusData.kode" method="POST" class="flex items-center justify-center gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="modalHapusTerbuka = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-md shadow-rose-600/20">Ya, Hapus Aset</button>
            </form>
        </div>
    </div>
</div>

<script>
    function kelolaAsetPerusahaan() {
        return {
            bukaModalTambah: false,
            modalDetailTerbuka: false,
            modalEditTerbuka: false,
            modalHapusTerbuka: false,
            detailAset: {},
            formEdit: {
                kode_aset: '',
                kode_jenis_aset: '',
                nama_aset: '',
                no_polisi: '',
                tanggal_pembelian: '',
                harga_aset: 0,
                status_aset: 'aktif'
            },
            hapusData: { kode: '', nama: '' },

            async bukaDetail(kode) {
                try {
                    const res = await fetch(`{{ url('keuangan/akuntansi/aset-perusahaan') }}/${kode}`);
                    const json = await res.json();
                    if (json.status === 'sukses') {
                        this.detailAset = json.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal memuat detail data aset.');
                }
            },

            async bukaEdit(kode) {
                try {
                    const res = await fetch(`{{ url('keuangan/akuntansi/aset-perusahaan') }}/${kode}`);
                    const json = await res.json();
                    if (json.status === 'sukses') {
                        const d = json.data;
                        this.formEdit = {
                            kode_aset: d.kode_aset,
                            kode_jenis_aset: d.kode_jenis_aset,
                            nama_aset: d.nama_aset,
                            no_polisi: d.no_polisi === '-' ? '' : d.no_polisi,
                            tanggal_pembelian: d.tanggal_pembelian,
                            harga_aset: d.harga_aset,
                            status_aset: d.status_aset || 'aktif'
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal memuat form edit data aset.');
                }
            },

            bukaHapus(kode, nama) {
                this.hapusData = { kode: kode, nama: nama };
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
