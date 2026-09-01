@extends('layouts.app')

@section('judul', 'Pembelian Sparepart Kendaraan')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-red-600 dark:text-red-400 font-semibold font-mono uppercase tracking-wider mb-1">Pengawas Kendaraan · Dev 2</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Pembelian & Pengadaan Sparepart Bengkel</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan faktur pembelian suku cadang armada truk (ban, oli, rem, filter) dari toko/distributor.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Catat Pembelian Baru
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari No. Faktur / Supplier..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: pembelian_sparepart</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Faktur Beli</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Toko / Supplier</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Pembelian</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status Bayar</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-red-600 dark:text-red-400">PO-SP-2026-012</td>
                        <td class="px-4 py-3 font-mono">29/08/2026</td>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">Toko Onderdil Berkah Motor</td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 8.750.000</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Lunas</span></td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Detail Rincian</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
