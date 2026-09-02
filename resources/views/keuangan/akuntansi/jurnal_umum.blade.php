@extends('layouts.app')

@section('judul', 'Jurnal Umum Transaksi (Akuntansi)')

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

    <!-- Header Modul Jurnal Umum -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-teal-600 dark:text-teal-400 font-semibold font-mono uppercase tracking-wider mb-1">Buku Besar & Akuntansi · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Buku Jurnal Umum Double-Entry</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan mutasi debit dan kredit otomatis dari transaksi operasional serta entri jurnal manual.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Input Jurnal Manual
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Jurnal -->
    <div class="wadah-bertingkat grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Mutasi Debet</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Mutasi Kredit</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Status Neraca Saldo</div>
            <div class="text-sm font-bold {{ ($isBalance ?? true) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} mt-1 font-mono flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full {{ ($isBalance ?? true) ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                {{ ($isBalance ?? true) ? 'SEIMBANG (BALANCED)' : 'TIDAK SEIMBANG' }}
            </div>
        </div>
    </div>

    <!-- Tabel Data Jurnal Umum -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.akuntansi.jurnal') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterPosisi = [
                    ['nilai' => '', 'label' => '-- Semua Posisi --'],
                    ['nilai' => 'Debit', 'label' => 'Debit'],
                    ['nilai' => 'Kredit', 'label' => 'Kredit'],
                ];
                $opsiAkunJurnal = ($daftarAkun ?? collect())->map(fn($a) => [
                    'nilai' => $a->kode_akun,
                    'label' => $a->nama_akun,
                    'sub'   => 'Kode: ' . $a->kode_akun
                ])->toArray();
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari no jurnal / akun / keterangan..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-40">
                    <x-dropdown-kustom 
                        nama="posisi" 
                        :nilaiAwal="$filterPosisi ?? ''" 
                        placeholder="-- Semua Posisi --" 
                        :opsi="$opsiFilterPosisi" 
                        warnaFokus="teal"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: jurnal_umum</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Jurnal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode & Nama Akun</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Debet</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarJurnal ?? [] as $jurnal)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-teal-600 dark:text-teal-400">
                                {{ $jurnal->nomor_jurnal }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($jurnal->tanggal_transaksi)) }}
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">
                                <span class="font-mono text-slate-500">{{ $jurnal->kode_akun }}</span> - {{ $jurnal->akun->nama_akun ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 truncate max-w-xs">
                                {{ $jurnal->keterangan }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ $jurnal->posisi_debit_kredit === 'Debit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-300 dark:text-slate-700' }}">
                                {{ $jurnal->posisi_debit_kredit === 'Debit' ? 'Rp ' . number_format($jurnal->nominal, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ $jurnal->posisi_debit_kredit === 'Kredit' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-300 dark:text-slate-700' }}">
                                {{ $jurnal->posisi_debit_kredit === 'Kredit' ? 'Rp ' . number_format($jurnal->nominal, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada entri ayat jurnal umum.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Input Jurnal Manual -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Input Entri Jurnal Double-Entry</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.akuntansi.jurnal.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <x-input-tanggal 
                        nama="tanggal_transaksi" 
                        nilaiAwal="{{ date('Y-m-d') }}" 
                        placeholder="Pilih Tanggal Transaksi"
                        :wajib="true"
                        warnaFokus="teal"
                    />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Akun Sisi Debet (+) <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_akun_debit"
                            placeholder="-- Pilih Akun Debet --"
                            :opsi="$opsiAkunJurnal"
                            :wajib="true"
                            warnaFokus="teal"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Akun Sisi Kredit (-) <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_akun_kredit"
                            placeholder="-- Pilih Akun Kredit --"
                            :opsi="$opsiAkunJurnal"
                            :wajib="true"
                            warnaFokus="teal"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nominal Jurnal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="nominal" required min="1000" step="50000" placeholder="5000000"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 font-mono font-semibold text-sm">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Transaksi <span class="text-rose-500">*</span></label>
                    <input type="text" name="keterangan" required placeholder="Ayat jurnal penyesuaian saldo..."
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-all shadow-sm">Simpan Jurnal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
