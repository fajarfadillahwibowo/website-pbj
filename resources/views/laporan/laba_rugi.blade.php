@extends('layouts.app')

@section('judul', 'Laporan Laba Rugi')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold font-mono uppercase tracking-wider mb-1">Laporan Eksekutif · Direktur & Manager</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Laporan Laba Rugi Komprehensif</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan pendapatan penjualan, harga pokok penjualan (HPP), dan laba bersih.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor PDF Laba Rugi
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm max-w-3xl space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 pb-2 border-b border-[#E2E8F0] dark:border-[#252837]">PERHITUNGAN LABA BERSIH PERIODE BERJALAN</h3>
        <div class="space-y-2.5 text-xs">
            <div class="flex justify-between py-1.5 border-b border-[#EEF0F4] dark:border-[#252837]">
                <span class="font-semibold text-slate-800 dark:text-slate-200">1. Pendapatan Penjualan Semen Kotor</span>
                <span class="font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 842.500.000</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-[#EEF0F4] dark:border-[#252837]">
                <span class="text-slate-600 dark:text-slate-400 pl-4">Dikurangi: Potongan Diskon Penjualan</span>
                <span class="font-mono tabular-nums text-red-500">-Rp 12.000.000</span>
            </div>
            <div class="flex justify-between py-1.5 font-bold bg-[#F8FAFC] dark:bg-[#1C1E2A] p-2 rounded-lg">
                <span class="text-blue-700 dark:text-blue-400">PENDAPATAN NETTO</span>
                <span class="font-mono tabular-nums">Rp 830.500.000</span>
            </div>

            <div class="flex justify-between py-1.5 border-b border-[#EEF0F4] dark:border-[#252837] pt-3">
                <span class="font-semibold text-slate-800 dark:text-slate-200">2. Harga Pokok Penjualan (HPP Semen)</span>
                <span class="font-mono tabular-nums text-red-500">-Rp 710.000.000</span>
            </div>
            <div class="flex justify-between py-1.5 font-bold bg-[#F8FAFC] dark:bg-[#1C1E2A] p-2 rounded-lg">
                <span class="text-emerald-700 dark:text-emerald-400">LABA KOTOR (GROSS PROFIT)</span>
                <span class="font-mono tabular-nums">Rp 120.500.000</span>
            </div>

            <div class="flex justify-between py-1.5 border-b border-[#EEF0F4] dark:border-[#252837] pt-3">
                <span class="font-semibold text-slate-800 dark:text-slate-200">3. Beban Operasional, BBM & Armada</span>
                <span class="font-mono tabular-nums text-red-500">-Rp 38.500.000</span>
            </div>
            <div class="flex justify-between py-2.5 font-bold text-sm bg-emerald-50 dark:bg-emerald-500/10 p-3 rounded-xl text-emerald-700 dark:text-emerald-400">
                <span>LABA BERSIH (NET PROFIT)</span>
                <span class="font-mono tabular-nums">Rp 82.000.000</span>
            </div>
        </div>
    </div>
</div>
@endsection
