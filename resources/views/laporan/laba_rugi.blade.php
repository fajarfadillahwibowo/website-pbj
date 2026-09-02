@extends('layouts.app')

@section('judul', 'Laporan Laba Rugi Eksekutif')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul Laba Rugi -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold font-mono uppercase tracking-wider mb-1">Laporan Eksekutif · Direktur & Manager</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Laporan Laba Rugi Komprehensif</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan pendapatan penjualan semen, HPP pabrik, biaya operasional BBM/tol/gaji, dan laba bersih periode berjalan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Ekspor PDF
            </button>
        </div>
    </div>

    <!-- Ringkasan Kartu Metrik Laba Rugi -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Pendapatan</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Laba Kotor (Gross)</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">Rp {{ number_format($labaKotor ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Beban Operasional</div>
            <div class="text-lg font-bold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($totalBebanOperasional ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Laba Bersih Setelah Pajak</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($labaBersihSetelahPajak ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Rincian Laporan Laba Rugi -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-6 shadow-sm max-w-4xl space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 pb-2 border-b border-[#E2E8F0] dark:border-[#252837] uppercase tracking-wider">
            PERHITUNGAN LABA RUGI OPERASIONAL
        </h3>
        
        <div class="space-y-3 text-xs">
            <!-- 1. PENDAPATAN USAHA -->
            <div class="space-y-1.5">
                <div class="font-bold text-slate-900 dark:text-slate-100 uppercase">1. Pendapatan Usaha</div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Pendapatan Penjualan Semen (Zak & Curah)</span>
                    <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($penjualanSemen ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Pendapatan Jasa Distribusi & Ongkos Angkut</span>
                    <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($pendapatanOngkosAngkut ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 font-bold bg-[#F8FAFC] dark:bg-[#1C1E2A] px-3 rounded-lg text-slate-800 dark:text-slate-200">
                    <span>Total Pendapatan Usaha:</span>
                    <span class="font-mono text-blue-600 dark:text-blue-400">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 2. HPP -->
            <div class="space-y-1.5 pt-2">
                <div class="font-bold text-slate-900 dark:text-slate-100 uppercase">2. Harga Pokok Penjualan (HPP)</div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Harga Pokok Pembelian Semen Pabrik</span>
                    <span class="font-mono tabular-nums text-rose-600 dark:text-rose-400 font-semibold">- Rp {{ number_format($hpp ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 font-bold bg-[#F8FAFC] dark:bg-[#1C1E2A] px-3 rounded-lg text-slate-800 dark:text-slate-200">
                    <span>LABA KOTOR (GROSS PROFIT):</span>
                    <span class="font-mono text-emerald-600 dark:text-emerald-400">Rp {{ number_format($labaKotor ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 3. BEBAN OPERASIONAL -->
            <div class="space-y-1.5 pt-2">
                <div class="font-bold text-slate-900 dark:text-slate-100 uppercase">3. Beban Operasional Usaha</div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Beban Bahan Bakar (Solar B35) & E-Toll Truk</span>
                    <span class="font-mono tabular-nums text-slate-700 dark:text-slate-300">Rp {{ number_format($bebanBBM ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Beban Servis Bengkel & Sparepart Armada</span>
                    <span class="font-mono tabular-nums text-slate-700 dark:text-slate-300">Rp {{ number_format($bebanServis ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Beban Gaji Karyawan, Upah Supir & Manajemen</span>
                    <span class="font-mono tabular-nums text-slate-700 dark:text-slate-300">Rp {{ number_format($bebanGaji ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Beban Listrik, Air, ATK & Keperluan Kantor</span>
                    <span class="font-mono tabular-nums text-slate-700 dark:text-slate-300">Rp {{ number_format($bebanKantor ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 font-bold bg-[#F8FAFC] dark:bg-[#1C1E2A] px-3 rounded-lg text-slate-800 dark:text-slate-200">
                    <span>Total Beban Operasional:</span>
                    <span class="font-mono text-rose-600 dark:text-rose-400">- Rp {{ number_format($totalBebanOperasional ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 4. HASIL BERSIH -->
            <div class="space-y-1.5 pt-2">
                <div class="flex justify-between py-2 font-bold text-xs bg-slate-100 dark:bg-[#252837] px-3 rounded-lg text-slate-800 dark:text-slate-200">
                    <span>LABA BERSIH SEBELUM PAJAK (EBT):</span>
                    <span class="font-mono">Rp {{ number_format($labaBersihOperasional ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-500 dark:text-slate-400">Estimasi Pajak Penghasilan (11%)</span>
                    <span class="font-mono tabular-nums text-rose-500">- Rp {{ number_format($estimasiPajak ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-3.5 font-bold text-sm bg-emerald-50 dark:bg-emerald-500/10 p-3.5 rounded-xl text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/20">
                    <span class="uppercase">LABA BERSIH SETELAH PAJAK (NET PROFIT)</span>
                    <span class="font-mono tabular-nums text-base">Rp {{ number_format($labaBersihSetelahPajak ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
