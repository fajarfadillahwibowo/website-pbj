@extends('layouts.app')

@section('judul', 'Monitoring Distribusi & Armada Operasional')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold font-mono uppercase tracking-wider mb-1">SPV Operasional · Dev 2</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Live Monitoring Armada & Distribusi Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan posisi armada jalan, realisasi pengiriman harian, dan utilisasi truk.</p>
        </div>
    </div>

    <!-- Live Status Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-4 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Armada Aktif di Lapangan</div>
            <div class="text-xl font-bold font-mono text-blue-600 dark:text-blue-400 mt-1">18 / 24 Unit</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-4 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Surat Jalan Selesai Hari Ini</div>
            <div class="text-xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">32 Pengiriman</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-4 shadow-sm">
            <div class="text-xs text-slate-500 font-medium">Volume Semen Terkirim</div>
            <div class="text-xl font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">14.200 Zak</div>
        </div>
    </div>
</div>
@endsection
