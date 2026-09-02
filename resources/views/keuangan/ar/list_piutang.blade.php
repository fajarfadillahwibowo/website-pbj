@extends('layouts.app')

@section('judul', 'Daftar Piutang Pelanggan (AR)')

@section('konten')
<div class="space-y-5" x-data="{ bukaModalBayar: false, piutangTerpilih: {} }">
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

    <!-- Header Modul List Piutang -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-amber-600 dark:text-amber-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Receivable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Buku Pembantu Piutang Pelanggan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Monitoring saldo piutang toko, pencatatan pembayaran cicilan, dan status jatuh tempo faktur.</p>
        </div>
    </div>

    <!-- Ringkasan Statistik Piutang -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Piutang Terbit</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Sisa Piutang Berjalan</div>
            <div class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono">Rp {{ number_format($totalSisa ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Terbayar / Lunas</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Toko Berpiutang Aktif</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">{{ $totalCustomerPiutang ?? 0 }} Toko</div>
        </div>
    </div>

    <!-- Tabel Data Piutang -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ar.piutang') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterStatusPiutang = [
                    ['nilai' => '', 'label' => '-- Semua Status --'],
                    ['nilai' => 'belum_lunas', 'label' => 'Belum Lunas'],
                    ['nilai' => 'sebagian', 'label' => 'Cicilan Sebagian'],
                    ['nilai' => 'lunas', 'label' => 'Lunas'],
                ];
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nama customer / no faktur..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="status" 
                        :nilaiAwal="$filterStatus ?? ''" 
                        placeholder="-- Semua Status --" 
                        :opsi="$opsiFilterStatusPiutang" 
                        warnaFokus="amber"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: list_piutang</span>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Faktur</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer Toko</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Piutang</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Sisa Piutang</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarPiutang ?? [] as $piutang)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">
                                {{ $piutang->penjualan->nomor_faktur ?? "ID-{$piutang->id_penjualan}" }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $piutang->customer->nama_toko_bangunan ?? $piutang->kode_customer }}</div>
                                <div class="text-[11px] text-slate-400">Pemilik: {{ $piutang->customer->nama_pemilik ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums text-slate-600 dark:text-slate-400">
                                Rp {{ number_format($piutang->jumlah_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ $piutang->sisa_piutang > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                                Rp {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-[11px] text-slate-500">
                                {{ date('d/m/Y', strtotime($piutang->tanggal_jatuh_tempo)) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($piutang->status_piutang === 'lunas')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Lunas
                                    </span>
                                @elseif($piutang->status_piutang === 'sebagian')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20">
                                        Sebagian
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($piutang->sisa_piutang > 0)
                                    <button @click="piutangTerpilih = {{ json_encode([
                                        'id_piutang' => $piutang->id_piutang,
                                        'nama_toko'  => $piutang->customer->nama_toko_bangunan ?? $piutang->kode_customer,
                                        'faktur'     => $piutang->penjualan->nomor_faktur ?? "ID-{$piutang->id_penjualan}",
                                        'sisa'       => $piutang->sisa_piutang
                                    ]) }}; bukaModalBayar = true" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200 dark:border-emerald-500/20">
                                        Bayar Cicilan
                                    </button>
                                @else
                                    <span class="text-slate-400 font-mono text-[11px]">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Tidak ada catatan piutang yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Catat Pembayaran Piutang -->
    <div x-show="bukaModalBayar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalBayar = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Pelunasan / Cicilan Piutang</h3>
                <button @click="bukaModalBayar = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('keuangan/ar/list-piutang') }}/' + piutangTerpilih.id_piutang + '/bayar'" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837] space-y-1">
                    <div class="text-[11px] text-slate-400">Toko: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="piutangTerpilih.nama_toko"></span></div>
                    <div class="text-[11px] text-slate-400">Faktur: <span class="font-mono text-blue-600 dark:text-blue-400" x-text="piutangTerpilih.faktur"></span></div>
                    <div class="text-[11px] text-slate-400">Sisa Piutang: <span class="font-mono font-bold text-amber-600 dark:text-amber-400">Rp <span x-text="new Intl.NumberFormat('id-ID').format(piutangTerpilih.sisa || 0)"></span></span></div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Nominal Bayar (Rp)</label>
                    <input type="number" name="jumlah_bayar" :max="piutangTerpilih.sisa" required min="1" step="50000" :value="piutangTerpilih.sisa"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono font-semibold text-sm">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalBayar = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
