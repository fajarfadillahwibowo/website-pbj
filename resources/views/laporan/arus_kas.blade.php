@extends('layouts.app')

@section('judul', 'Laporan Arus Kas Eksekutif')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul Arus Kas -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-sky-600 dark:text-sky-400 font-semibold font-mono uppercase tracking-wider mb-1">Laporan Eksekutif · Direktur & Manager</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Laporan Arus Kas (Cash Flow)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan arus kas masuk dari pelunasan customer, kas keluar operasional, dan saldo akhir kas & bank.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Ekspor PDF
            </button>
        </div>
    </div>

    <!-- Ringkasan Kartu Metrik Arus Kas -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Saldo Awal Kas & Bank</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">Rp {{ number_format($saldoAwalKas ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Penerimaan Kas Operasi</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($penerimaanCustomer ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Pengeluaran Kas Operasi</div>
            <div class="text-lg font-bold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($pengeluaranOperasional ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Saldo Akhir Kas & Bank</div>
            <div class="text-lg font-bold text-sky-600 dark:text-sky-400 mt-0.5 font-mono">Rp {{ number_format($saldoAkhirKas ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Rincian Laporan Arus Kas -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-6 shadow-sm max-w-4xl space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 pb-2 border-b border-[#E2E8F0] dark:border-[#252837] uppercase tracking-wider">
            LAPORAN ARUS KAS METODE LANGSUNG
        </h3>
        
        <div class="space-y-3 text-xs">
            <!-- 1. ARUS KAS DARI AKTIVITAS OPERASI -->
            <div class="space-y-1.5">
                <div class="font-bold text-slate-900 dark:text-slate-100 uppercase">1. Arus Kas dari Aktivitas Operasional</div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Penerimaan Pembayaran dari Customer & Toko Bangunan</span>
                    <span class="font-mono tabular-nums font-semibold text-emerald-600 dark:text-emerald-400">+ Rp {{ number_format($penerimaanCustomer ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Pembayaran Kas untuk Beban Operasional, BBM & Tol</span>
                    <span class="font-mono tabular-nums text-rose-600 dark:text-rose-400 font-semibold">- Rp {{ number_format($pengeluaranOperasional ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 font-bold bg-[#F8FAFC] dark:bg-[#1C1E2A] px-3 rounded-lg text-slate-800 dark:text-slate-200">
                    <span>Arus Kas Bersih dari Aktivitas Operasional:</span>
                    <span class="font-mono {{ ($arusKasOperasi ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        Rp {{ number_format($arusKasOperasi ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- 2. ARUS KAS DARI AKTIVITAS INVESTASI -->
            <div class="space-y-1.5 pt-2">
                <div class="font-bold text-slate-900 dark:text-slate-100 uppercase">2. Arus Kas dari Aktivitas Investasi</div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Perolehan Kendaraan & Aset Tetap Armada</span>
                    <span class="font-mono tabular-nums text-slate-500">Rp 0</span>
                </div>
                <div class="flex justify-between py-1.5 font-bold bg-[#F8FAFC] dark:bg-[#1C1E2A] px-3 rounded-lg text-slate-800 dark:text-slate-200">
                    <span>Arus Kas Bersih untuk Aktivitas Investasi:</span>
                    <span class="font-mono text-slate-600 dark:text-slate-400">Rp 0</span>
                </div>
            </div>

            <!-- 3. SALDO AKHIR -->
            <div class="space-y-1.5 pt-3">
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Kenaikan / (Penurunan) Kas Bersih Periode Berjalan</span>
                    <span class="font-mono tabular-nums font-semibold text-sky-600 dark:text-sky-400">Rp {{ number_format($arusKasOperasi ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837] pl-3">
                    <span class="text-slate-600 dark:text-slate-400">Saldo Awal Kas & Bank Perusahaan</span>
                    <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($saldoAwalKas ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-3.5 font-bold text-sm bg-sky-50 dark:bg-sky-500/10 p-3.5 rounded-xl text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-500/20">
                    <span class="uppercase">SALDO AKHIR KAS & SETARA KAS</span>
                    <span class="font-mono tabular-nums text-base">Rp {{ number_format($saldoAkhirKas ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
