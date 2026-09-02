@extends('layouts.app')

@section('judul', 'Laporan Neraca Keuangan')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul Neraca -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold font-mono uppercase tracking-wider mb-1">Laporan Eksekutif · Direktur & Manager</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Laporan Posisi Keuangan (Neraca)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ringkasan posisi aktiva lancar, aset tetap, kewajiban, dan ekuitas perusahaan periode berjalan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Ekspor PDF
            </button>
        </div>
    </div>

    <!-- Dua Kolom: Aktiva vs Passiva -->
    <div class="wadah-bertingkat grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Kolom AKTIVA / ASET -->
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">AKTIVA / ASET</h3>
                <span class="text-xs font-mono text-emerald-600 dark:text-emerald-400 font-semibold">DEBET</span>
            </div>

            <!-- Sub-Grup: Aktiva Lancar -->
            <div class="space-y-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">1. Aktiva Lancar</div>
                <div class="space-y-1.5 text-xs pl-2">
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Kas & Rekening Bank (BCA, Mandiri, BRI)</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalKasBank ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Piutang Usaha Toko Bangunan (AR)</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalPiutangUsaha ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Persediaan Stok Semen Gudang</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 625.000.000</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Uang Muka / Kas Bon Supir</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 18.000.000</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-xs bg-[#F8FAFC] dark:bg-[#1C1E2A] px-2 rounded-lg text-slate-800 dark:text-slate-200">
                        <span>Total Aktiva Lancar:</span>
                        <span class="font-mono">Rp {{ number_format($totalAktivaLancar ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Sub-Grup: Aktiva Tetap -->
            <div class="space-y-2 pt-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">2. Aktiva Tetap</div>
                <div class="space-y-1.5 text-xs pl-2">
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Armada Truk & Peralatan Gudang</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalNilaiAset ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Akumulasi Penyusutan Aset Tetap</span>
                        <span class="font-mono tabular-nums font-semibold text-rose-600 dark:text-rose-400">- Rp 220.000.000</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-xs bg-[#F8FAFC] dark:bg-[#1C1E2A] px-2 rounded-lg text-slate-800 dark:text-slate-200">
                        <span>Total Nilai Buku Aktiva Tetap:</span>
                        <span class="font-mono">Rp {{ number_format($totalAktivaTetap ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- TOTAL AKTIVA -->
            <div class="flex justify-between py-3 font-bold text-sm bg-emerald-50 dark:bg-emerald-500/10 p-3.5 rounded-xl text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/20">
                <span class="uppercase">TOTAL AKTIVA (ASET)</span>
                <span class="font-mono tabular-nums text-base">Rp {{ number_format($totalAktiva ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Kolom PASIVA / KEWAJIBAN & MODAL -->
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">PASIVA / KEWAJIBAN & MODAL</h3>
                <span class="text-xs font-mono text-blue-600 dark:text-blue-400 font-semibold">KREDIT</span>
            </div>

            <!-- Sub-Grup: Kewajiban Lancar -->
            <div class="space-y-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">1. Kewajiban Lancar (Hutang Usaha)</div>
                <div class="space-y-1.5 text-xs pl-2">
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Hutang Dagang Pabrik Semen (AP)</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 320.000.000</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Titipan Saldo Deposit Customer</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalDepositCustomer ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Hutang Biaya Gaji & Akrual</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 42.000.000</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-xs bg-[#F8FAFC] dark:bg-[#1C1E2A] px-2 rounded-lg text-slate-800 dark:text-slate-200">
                        <span>Total Kewajiban:</span>
                        <span class="font-mono">Rp {{ number_format($totalKewajiban ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Sub-Grup: Modal & Ekuitas -->
            <div class="space-y-2 pt-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">2. Modal & Ekuitas</div>
                <div class="space-y-1.5 text-xs pl-2">
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Modal Disetor Pemilik</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 2.500.000.000</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Laba Ditahan & Laba Berjalan</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format(max(0, $totalModal - 2500000000), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-xs bg-[#F8FAFC] dark:bg-[#1C1E2A] px-2 rounded-lg text-slate-800 dark:text-slate-200">
                        <span>Total Ekuitas Bersih:</span>
                        <span class="font-mono">Rp {{ number_format($totalModal ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- TOTAL PASSIVA -->
            <div class="flex justify-between py-3 font-bold text-sm bg-blue-50 dark:bg-blue-500/10 p-3.5 rounded-xl text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-500/20">
                <span class="uppercase">TOTAL PASSIVA (KEWAJIBAN + MODAL)</span>
                <span class="font-mono tabular-nums text-base">Rp {{ number_format(($totalKewajiban + $totalModal) ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
