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

    <!-- Filter Periode Laporan & Badge Eksekutif Read-Only -->
    @php
        $opsiBulan = [
            ['nilai' => '01', 'label' => 'Januari'],
            ['nilai' => '02', 'label' => 'Februari'],
            ['nilai' => '03', 'label' => 'Maret'],
            ['nilai' => '04', 'label' => 'April'],
            ['nilai' => '05', 'label' => 'Mei'],
            ['nilai' => '06', 'label' => 'Juni'],
            ['nilai' => '07', 'label' => 'Juli'],
            ['nilai' => '08', 'label' => 'Agustus'],
            ['nilai' => '09', 'label' => 'September'],
            ['nilai' => '10', 'label' => 'Oktober'],
            ['nilai' => '11', 'label' => 'November'],
            ['nilai' => '12', 'label' => 'Desember'],
        ];
        $opsiTahun = [
            ['nilai' => '2024', 'label' => 'Tahun 2024'],
            ['nilai' => '2025', 'label' => 'Tahun 2025'],
            ['nilai' => '2026', 'label' => 'Tahun 2026'],
            ['nilai' => '2027', 'label' => 'Tahun 2027'],
        ];
        $bulanTerpilih = str_pad((string)$bulan, 2, '0', STR_PAD_LEFT);
        $tahunTerpilih = (string)$tahun;
    @endphp

    <div class="animasi-masuk flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <form method="GET" action="{{ route('laporan.neraca') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
            <div class="w-44">
                <x-dropdown-kustom 
                    nama="bulan"
                    placeholder="-- Pilih Bulan --"
                    :opsi="$opsiBulan"
                    :nilaiAwal="$bulanTerpilih"
                    :submitOnChange="true"
                    warnaFokus="emerald"
                />
            </div>
            <div class="w-36">
                <x-dropdown-kustom 
                    nama="tahun"
                    placeholder="-- Pilih Tahun --"
                    :opsi="$opsiTahun"
                    :nilaiAwal="$tahunTerpilih"
                    :submitOnChange="true"
                    warnaFokus="emerald"
                />
            </div>
            @if($bulanTerpilih != date('m') || $tahunTerpilih != date('Y'))
                <a href="{{ route('laporan.neraca') }}" class="px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Periode Berjalan
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-medium font-mono">
                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Akses Eksekutif: Read-Only</span>
            </span>
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
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalPersediaan ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Uang Muka / Kas Bon Supir</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalUangMukaSupir ?? 0, 0, ',', '.') }}</span>
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
                        <span class="font-mono tabular-nums font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($totalAkumulasiPenyusutan ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-xs bg-[#F8FAFC] dark:bg-[#1C1E2A] px-2 rounded-lg text-slate-800 dark:text-slate-200">
                        <span>Total Nilai Buku Aktiva Tetap:</span>
                        <span class="font-mono">Rp {{ number_format($totalAktivaTetap ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- TOTAL AKTIVA -->
            <div class="flex justify-between items-center py-3.5 font-bold text-sm bg-emerald-50 dark:bg-emerald-500/10 p-4 rounded-xl text-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30">
                <span class="uppercase tracking-wider font-extrabold text-xs text-emerald-900 dark:text-emerald-300">TOTAL AKTIVA (ASET)</span>
                <span class="font-mono tabular-nums text-base font-extrabold text-emerald-950 dark:text-emerald-200">Rp {{ number_format($totalAktiva ?? 0, 0, ',', '.') }}</span>
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
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalHutangDagang ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Titipan Saldo Deposit Customer</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalDepositCustomer ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Hutang Biaya Gaji & Akrual</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($totalHutangGaji ?? 0, 0, ',', '.') }}</span>
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
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($modalDisetor ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#EEF0F4] dark:border-[#252837]">
                        <span class="text-slate-600 dark:text-slate-400">Laba Ditahan & Laba Berjalan</span>
                        <span class="font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp {{ number_format($labaDitahan ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 font-bold text-xs bg-[#F8FAFC] dark:bg-[#1C1E2A] px-2 rounded-lg text-slate-800 dark:text-slate-200">
                        <span>Total Ekuitas Bersih:</span>
                        <span class="font-mono">Rp {{ number_format($totalModal ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- TOTAL PASSIVA -->
            <div class="flex justify-between items-center py-3.5 font-bold text-sm bg-blue-50 dark:bg-blue-500/10 p-4 rounded-xl text-blue-950 dark:text-blue-300 border border-blue-300 dark:border-blue-500/30">
                <span class="uppercase tracking-wider font-extrabold text-xs text-blue-900 dark:text-blue-300">TOTAL PASSIVA (KEWAJIBAN + MODAL)</span>
                <span class="font-mono tabular-nums text-base font-extrabold text-blue-950 dark:text-blue-200">Rp {{ number_format(($totalKewajiban + $totalModal) ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
