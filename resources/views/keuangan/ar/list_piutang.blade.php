@extends('layouts.app')

@section('judul', 'Daftar Piutang Pelanggan (AR)')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-amber-600 dark:text-amber-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Receivable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Buku Pembantu Piutang Pelanggan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Monitoring saldo piutang toko, umur piutang (Aging AR), dan jatuh tempo pembayaran.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Faktur</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Piutang</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Sisa Saldo</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">INV-20260901-002</td>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">TB Cahaya Bangunan</td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums text-slate-600 dark:text-slate-400">Rp 68.000.000</td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-amber-600 dark:text-amber-400">Rp 68.000.000</td>
                        <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">15/09/2026</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Belum Lunas</span></td>
                        <td class="px-4 py-3 text-center">
                            <button class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold">Catat Pelunasan</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
