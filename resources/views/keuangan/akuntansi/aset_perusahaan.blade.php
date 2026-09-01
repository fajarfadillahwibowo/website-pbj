@extends('layouts.app')

@section('judul', 'Aset & Inventaris Perusahaan')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Akuntansi · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Daftar Aset & Inventaris Perusahaan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan aset tetap, truk armada, bangunan gudang, dan akumulasi penyusutan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Aset
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Aset</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Aset</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jenis Aset</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Nilai Perolehan</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal Beli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">AST-TRK-01</td>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">Truk Hino Tronton 10 Roda (B 9283 TDF)</td>
                        <td class="px-4 py-3">Kendaraan Operasional</td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 650.000.000</td>
                        <td class="px-4 py-3 font-mono">15/01/2023</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
