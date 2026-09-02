@extends('layouts.app')

@section('judul', 'Aset & Inventaris Perusahaan')

@section('konten')
<div class="space-y-5" x-data="{ bukaModalTambah: false }">
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

    <!-- Header Modul Aset Perusahaan -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Buku Besar & Akuntansi · Dev 1</div>
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
    <div class="wadah-bertingkat grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Nilai Perolehan Aset</div>
            <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mt-0.5 font-mono">Rp {{ number_format($totalNilaiAset ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Unit Aset</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalAset ?? 0 }} Unit</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Armada Truk Aktif</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">{{ $totalTruk ?? 0 }} Kendaraan</div>
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
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarAset ?? [] as $aset)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">
                                {{ $aset->kode_aset }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">
                                {{ $aset->nama_aset }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $aset->jenisAset->jenis_aset ?? $aset->kode_jenis_aset }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold text-slate-700 dark:text-slate-300">
                                {{ $aset->no_polisi ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-indigo-600 dark:text-indigo-400">
                                Rp {{ number_format($aset->harga_aset, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-slate-500">
                                {{ $aset->tanggal_pembelian ? date('d/m/Y', strtotime($aset->tanggal_pembelian)) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada aset tetap tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Aset -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Aset Perusahaan</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.akuntansi.aset.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Aset</label>
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_aset" value="{{ $kodeOtomatis }}" required placeholder="AST-001"
                               class="w-full px-3 py-2 rounded-xl bg-indigo-50/50 dark:bg-[#1C1E2A] border border-indigo-200 dark:border-indigo-900/50 text-indigo-900 dark:text-indigo-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Aset</label>
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
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Aset</label>
                    <input type="text" name="nama_aset" required placeholder="Hino Dutro 130 HD"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                </div>                <div class="space-y-3">
                    <x-input-plat-nomor 
                        nama="no_polisi" 
                        :wajib="false" 
                        label="No. Polisi (Khusus Kendaraan/Truk)" 
                    />

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian</label>
                        <input type="date" name="tanggal_pembelian" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
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
</div>
@endsection
