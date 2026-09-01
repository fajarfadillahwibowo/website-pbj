@extends('layouts.app')

@section('judul', 'Pembelian SO Pabrik (AP)')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Payable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Pembelian Sales Order (SO) Pabrik</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan pemesanan semen ke produsen/pabrik dan manajemen hutang supplier.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat Pembelian SO
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. SO Pembelian</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Supplier Pabrik</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Biaya</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status SO</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">PO-SMN-2026-004</td>
                        <td class="px-4 py-3 font-mono">28/08/2026</td>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">PT Semen Indonesia (Persero) Tbk</td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 232.000.000</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Terkonfirmasi</span></td>
                        <td class="px-4 py-3 text-center">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Detail Rincian</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
