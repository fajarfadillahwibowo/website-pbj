@extends('layouts.app')

@section('judul', 'Laporan Neraca Keuangan')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold font-mono uppercase tracking-wider mb-1">Laporan Eksekutif · Direktur & Manager</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Laporan Neraca Saldo (Balance Sheet)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan posisi aset, liabilitas, dan ekuitas perusahaan per {{ date('d F Y') }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Ekspor PDF Neraca
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Aset -->
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 pb-2 border-b border-[#E2E8F0] dark:border-[#252837]">AKTIVA / ASET</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                    <span class="text-slate-600 dark:text-slate-400">Kas & Setara Kas</span>
                    <span class="font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 300.000.000</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                    <span class="text-slate-600 dark:text-slate-400">Piutang Dagang (AR)</span>
                    <span class="font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 128.400.000</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                    <span class="text-slate-600 dark:text-slate-400">Persediaan Stok Semen</span>
                    <span class="font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 825.000.000</span>
                </div>
                <div class="flex justify-between py-2 font-bold text-sm bg-[#F8FAFC] dark:bg-[#1C1E2A] p-2.5 rounded-xl text-emerald-600 dark:text-emerald-400">
                    <span>TOTAL ASET</span>
                    <span class="font-mono tabular-nums">Rp 1.253.400.000</span>
                </div>
            </div>
        </div>

        <!-- Kewajiban & Modal -->
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 pb-2 border-b border-[#E2E8F0] dark:border-[#252837]">PASIVA / KEWAJIBAN & EKUITAS</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                    <span class="text-slate-600 dark:text-slate-400">Hutang Dagang Pabrik (AP)</span>
                    <span class="font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 232.000.000</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                    <span class="text-slate-600 dark:text-slate-400">Modal Disetor & Laba Ditahan</span>
                    <span class="font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">Rp 1.021.400.000</span>
                </div>
                <div class="flex justify-between py-2 font-bold text-sm bg-[#F8FAFC] dark:bg-[#1C1E2A] p-2.5 rounded-xl text-blue-600 dark:text-blue-400">
                    <span>TOTAL KEWAJIBAN & EKUITAS</span>
                    <span class="font-mono tabular-nums">Rp 1.253.400.000</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
