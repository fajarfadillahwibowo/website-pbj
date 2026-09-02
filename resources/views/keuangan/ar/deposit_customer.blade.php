@extends('layouts.app')

@section('judul', 'Deposit Customer (AR)')

@section('konten')
<div class="space-y-5" x-data="{ bukaModalTopUp: false }">
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

    @if(session('gagal'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('gagal') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Header Modul Deposit -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-sky-600 dark:text-sky-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Receivable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Saldo & Mutasi Deposit Customer</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan uang muka setoran (deposit) dan riwayat pemotongan otomatis saat penerbitan faktur.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTopUp = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Top Up Deposit
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Deposit -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Deposit Aktif</div>
            <div class="text-lg font-bold text-sky-600 dark:text-sky-400 mt-0.5 font-mono">Rp {{ number_format($totalDepositAktif ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Setoran Masuk</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Terpakai Faktur</div>
            <div class="text-lg font-bold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($totalTerpakai ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Mitra Memiliki Deposit</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalMitraDeposit ?? 0 }} Toko</div>
        </div>
    </div>

    <!-- Tabel Mutasi Deposit -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ar.deposit') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterTipe = [
                    ['nilai' => '', 'label' => '-- Semua Mutasi --'],
                    ['nilai' => 'Masuk', 'label' => 'Setoran Masuk'],
                    ['nilai' => 'Keluar / Terpakai', 'label' => 'Keluar / Terpakai'],
                ];
                $opsiCustomerDeposit = ($daftarCustomer ?? collect())->map(fn($c) => [
                    'nilai' => $c->kode_customer,
                    'label' => $c->nama_toko_bangunan,
                    'sub'   => 'Saldo Saat Ini: Rp ' . number_format($c->saldo_deposit, 0, ',', '.')
                ])->toArray();
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari no bukti / nama customer..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="tipe" 
                        :nilaiAwal="$filterTipe ?? ''" 
                        placeholder="-- Semua Mutasi --" 
                        :opsi="$opsiFilterTipe" 
                        warnaFokus="sky"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: list_deposit</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Bukti Mutasi</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer Toko</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Jenis Mutasi</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Jumlah Nominal</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Saldo Akhir</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Keterangan / Ref</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarMutasi ?? [] as $dep)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-sky-600 dark:text-sky-400">
                                {{ $dep->nomor_bukti_deposit }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($dep->tanggal_deposit)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $dep->customer->nama_toko_bangunan ?? $dep->kode_customer }}</div>
                                <div class="text-[11px] text-slate-400">{{ $dep->customer->nama_pemilik ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($dep->tipe_mutasi === 'Masuk')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Setoran Masuk
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                        Terpakai
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ $dep->tipe_mutasi === 'Masuk' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $dep->tipe_mutasi === 'Masuk' ? '+' : '-' }} Rp {{ number_format($dep->jumlah_nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($dep->saldo_akhir_deposit, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 truncate max-w-xs">
                                {{ $dep->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Belum ada riwayat mutasi deposit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Top Up Deposit -->
    <div x-show="bukaModalTopUp" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTopUp = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Top Up Saldo Deposit Customer</h3>
                <button @click="bukaModalTopUp = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ar.deposit.topup') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Customer Toko</label>
                    <x-dropdown-kustom 
                        nama="kode_customer"
                        placeholder="-- Pilih Customer --"
                        :opsi="$opsiCustomerDeposit"
                        :wajib="true"
                        warnaFokus="sky"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Setoran</label>
                    <input type="date" name="tanggal_deposit" required value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Nominal Top Up (Rp)</label>
                    <input type="number" name="jumlah_nominal" required min="100000" step="100000" placeholder="10000000"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/30 font-mono font-semibold text-sm">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Referensi Bank</label>
                    <input type="text" name="keterangan" placeholder="Setoran via transfer Bank BCA..."
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTopUp = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">Simpan Top Up</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
