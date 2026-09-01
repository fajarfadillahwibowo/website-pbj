@extends('layouts.app')

@section('judul', 'Surat Jalan Dispatcher')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-sky-600 dark:text-sky-400 font-semibold font-mono uppercase tracking-wider mb-1">Dispatcher Logistik · Dev 2</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Surat Jalan (SJ) Pengiriman Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penerbitan surat jalan pengiriman dari Sales Order (SO), penugasan driver, dan monitoring armada.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Terbitkan Surat Jalan
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Surat Jalan</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal Kirim</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Driver / Truk</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tujuan Alamat</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Muatan</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-sky-600 dark:text-sky-400">SJ-20260901-088</td>
                        <td class="px-4 py-3 font-mono">01/09/2026</td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900 dark:text-slate-100">Ahmad Supriyadi</div>
                            <div class="text-[10px] text-slate-400 font-mono">B 9283 TDF</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-400 truncate max-w-xs">TB Maju Jaya, Jl. Raya Karawang</td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">400 Zak</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">Dalam Perjalanan</span></td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Cetak SJ</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
