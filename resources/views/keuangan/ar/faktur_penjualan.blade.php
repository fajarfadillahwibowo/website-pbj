@extends('layouts.app')

@section('judul', 'Faktur Penjualan Semen (AR)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false, 
    metode: 'Kredit / Piutang',
    bruto: 35000000,
    diskon: 0,
    hitungNetto() { return Math.max(0, this.bruto - this.diskon); }
}">
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

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs">
            <div class="font-semibold mb-1">Terjadi kesalahan validasi:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Modul Faktur Penjualan -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Receivable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Faktur Penjualan Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penerbitan faktur tagihan penjualan semen tunai, transfer, kredit tempo, atau potong deposit toko.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat Faktur Baru
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Penjualan -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Penjualan</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Penerimaan Lunas</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalLunas ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Sisa Piutang Berjalan</div>
            <div class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Faktur Diterbitkan</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">{{ $totalFaktur ?? count($daftarFaktur ?? []) }} Faktur</div>
        </div>
    </div>

    <!-- Tabel Data Faktur Penjualan -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ar.faktur') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterStatusFaktur = [
                    ['nilai' => '', 'label' => '-- Semua Status --'],
                    ['nilai' => 'Lunas', 'label' => 'Lunas'],
                    ['nilai' => 'Belum Lunas', 'label' => 'Belum Lunas'],
                ];
                $opsiFilterMetodeFaktur = [
                    ['nilai' => '', 'label' => '-- Semua Metode --'],
                    ['nilai' => 'Kredit / Piutang', 'label' => 'Kredit / Piutang'],
                    ['nilai' => 'Potong Deposit', 'label' => 'Potong Deposit'],
                    ['nilai' => 'Transfer', 'label' => 'Transfer'],
                    ['nilai' => 'Tunai', 'label' => 'Tunai'],
                ];
                $opsiCustomerFaktur = ($daftarCustomer ?? collect())->map(fn($c) => [
                    'nilai' => $c->kode_customer,
                    'label' => $c->nama_toko_bangunan,
                    'sub'   => 'Plafon: Rp ' . number_format($c->plafon_piutang, 0, ',', '.') . ' | Dep: Rp ' . number_format($c->saldo_deposit, 0, ',', '.')
                ])->toArray();
                $opsiMetodeModal = [
                    ['nilai' => 'Kredit / Piutang', 'label' => 'Kredit / Piutang (Tempo)'],
                    ['nilai' => 'Potong Deposit', 'label' => 'Potong Saldo Deposit Toko'],
                    ['nilai' => 'Transfer', 'label' => 'Transfer Bank'],
                    ['nilai' => 'Tunai', 'label' => 'Tunai / Cash'],
                ];
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari no faktur / nama toko..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-40">
                    <x-dropdown-kustom 
                        nama="status" 
                        :nilaiAwal="$filterStatus ?? ''" 
                        placeholder="-- Semua Status --" 
                        :opsi="$opsiFilterStatusFaktur" 
                        warnaFokus="emerald"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="metode" 
                        :nilaiAwal="$filterMetode ?? ''" 
                        placeholder="-- Semua Metode --" 
                        :opsi="$opsiFilterMetodeFaktur" 
                        warnaFokus="emerald"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: penjualan</span>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Faktur</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer / Toko</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Netto</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Sisa Piutang</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Metode Bayar</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarFaktur ?? [] as $faktur)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">
                                {{ $faktur->nomor_faktur }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($faktur->tanggal_penjualan)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $faktur->customer->nama_toko_bangunan ?? $faktur->kode_customer }}</div>
                                <div class="text-[11px] text-slate-400">{{ $faktur->customer->nama_pemilik ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($faktur->total_netto, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold {{ $faktur->sisa_piutang > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                                Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold font-mono bg-slate-100 dark:bg-[#1C1E2A] text-slate-700 dark:text-slate-300">
                                    {{ $faktur->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($faktur->status_pembayaran === 'Lunas')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-[11px] text-slate-500">
                                {{ $faktur->jatuh_tempo ? date('d/m/Y', strtotime($faktur->jatuh_tempo)) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-400">Belum ada faktur penjualan yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Buat Faktur Baru -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Penerbitan Faktur Penjualan Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ar.faktur.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer / Mitra Toko</label>
                        <x-dropdown-kustom 
                            nama="kode_customer"
                            placeholder="-- Pilih Customer --"
                            :opsi="$opsiCustomerFaktur"
                            :wajib="true"
                            warnaFokus="emerald"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Transaksi</label>
                        <input type="date" name="tanggal_penjualan" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Metode Pembayaran</label>
                        <x-dropdown-kustom 
                            nama="metode_pembayaran"
                            placeholder="-- Pilih Metode --"
                            :opsi="$opsiMetodeModal"
                            :wajib="true"
                            warnaFokus="emerald"
                            modelBind="metode"
                        />
                    </div>
                    <div x-show="metode === 'Kredit / Piutang'">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Jatuh Tempo</label>
                        <input type="date" name="jatuh_tempo" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Nilai Bruto (Rp)</label>
                        <input type="number" name="total_bruto" x-model.number="bruto" required min="1" step="100000"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Potongan Diskon (Rp)</label>
                        <input type="number" name="diskon" x-model.number="diskon" min="0" step="50000"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>
                </div>
                <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                    <span class="font-semibold text-slate-600 dark:text-slate-400">Total Netto Tagihan Faktur:</span>
                    <span class="font-mono font-bold text-sm text-emerald-600 dark:text-emerald-400">Rp <span x-text="new Intl.NumberFormat('id-ID').format(hitungNetto())"></span></span>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Terbitkan Faktur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
