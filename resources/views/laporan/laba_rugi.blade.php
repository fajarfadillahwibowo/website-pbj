@extends('layouts.app')

@section('judul', 'Laporan Laba dan Rugi Eksekutif')

@section('konten')
<div x-data="{
    tabAktif: 'laporan',
    bulanDipilih: '{{ $bulan }}',
    tahunDipilih: '{{ $tahun }}',
    eksporCsv() {
        const baris = [
            ['LAPORAN LABA DAN RUGI KOMPREHENSIF'],
            ['PT PUTRA BALKOM JAYA - DISTRIBUSI & LOGISTIK'],
            ['Periode: {{ $namaBulan }} {{ $tahun }}'],
            [''],
            ['KOMPONEN', 'NOMINAL (RP)', 'PERSENTASE (%)'],
            ['1. PENDAPATAN USAHA', '', ''],
            ['  - Penjualan Semen Zak', '{{ $penjualanSemenZak }}', '{{ number_format(($penjualanSemenZak / $totalPendapatan) * 100, 1) }}%'],
            ['  - Penjualan Semen Curah', '{{ $penjualanSemenCurah }}', '{{ number_format(($penjualanSemenCurah / $totalPendapatan) * 100, 1) }}%'],
            ['  - Pendapatan Ongkos Angkut', '{{ $pendapatanOngkosAngkut }}', '{{ number_format(($pendapatanOngkosAngkut / $totalPendapatan) * 100, 1) }}%'],
            ['  - Potongan Penjualan', '-{{ $potonganPenjualan }}', '-{{ number_format(($potonganPenjualan / $totalPendapatan) * 100, 1) }}%'],
            ['TOTAL PENDAPATAN', '{{ $totalPendapatan }}', '100.0%'],
            [''],
            ['2. HARGA POKOK PENJUALAN (HPP)', '', ''],
            ['  - Pembelian Semen Pabrik', '{{ $pembelianSemenPabrik }}', '{{ number_format(($pembelianSemenPabrik / $totalPendapatan) * 100, 1) }}%'],
            ['  - Biaya Bongkar Muat Pabrik', '{{ $ongkosBongkarMuatPabrik }}', '{{ number_format(($ongkosBongkarMuatPabrik / $totalPendapatan) * 100, 1) }}%'],
            ['  - Biaya Kuli & Tenaga Angkut', '{{ $biayaKuliPabrik }}', '{{ number_format(($biayaKuliPabrik / $totalPendapatan) * 100, 1) }}%'],
            ['TOTAL HPP', '-{{ $totalHpp }}', '-{{ number_format(($totalHpp / $totalPendapatan) * 100, 1) }}%'],
            ['LABA KOTOR (GROSS PROFIT)', '{{ $labaKotor }}', '{{ number_format($marginLabaKotor, 1) }}%'],
            [''],
            ['3. BEBAN OPERASIONAL USAHA', '', ''],
            ['  - Bahan Bakar Solar Truk B35', '{{ $bebanBBM }}', '{{ number_format(($bebanBBM / $totalPendapatan) * 100, 1) }}%'],
            ['  - E-Toll & Biaya Penyeberangan', '{{ $bebanTolPenyeberangan }}', '{{ number_format(($bebanTolPenyeberangan / $totalPendapatan) * 100, 1) }}%'],
            ['  - KIR & Retribusi Armada', '{{ $bebanKirPajakArmada }}', '{{ number_format(($bebanKirPajakArmada / $totalPendapatan) * 100, 1) }}%'],
            ['  - Servis Bengkel & Perbaikan', '{{ $bebanServisBengkel }}', '{{ number_format(($bebanServisBengkel / $totalPendapatan) * 100, 1) }}%'],
            ['  - Sparepart & Penggantian Ban', '{{ $bebanSparepartBan }}', '{{ number_format(($bebanSparepartBan / $totalPendapatan) * 100, 1) }}%'],
            ['  - Gaji Supir & Driver', '{{ $bebanGajiSupir }}', '{{ number_format(($bebanGajiSupir / $totalPendapatan) * 100, 1) }}%'],
            ['  - Uang Jalan & Operasional Supir', '{{ $bebanUangJalanSupir }}', '{{ number_format(($bebanUangJalanSupir / $totalPendapatan) * 100, 1) }}%'],
            ['  - Gaji Staff & Manajemen', '{{ $bebanGajiManajemen }}', '{{ number_format(($bebanGajiManajemen / $totalPendapatan) * 100, 1) }}%'],
            ['  - Listrik & Utilitas Gudang', '{{ $bebanListrikAir }}', '{{ number_format(($bebanListrikAir / $totalPendapatan) * 100, 1) }}%'],
            ['  - ATK & Keperluan Kantor', '{{ $bebanAtkKomunikasi }}', '{{ number_format(($bebanAtkKomunikasi / $totalPendapatan) * 100, 1) }}%'],
            ['TOTAL BEBAN OPERASIONAL', '-{{ $totalBebanOperasional }}', '-{{ number_format($rasioBebanOperasional, 1) }}%'],
            [''],
            ['LABA BERSIH SEBELUM PAJAK (EBIT)', '{{ $labaBersihOperasional }}', '{{ number_format($marginLabaOperasional, 1) }}%'],
            ['ESTIMASI PAJAK PENGHASILAN (11%)', '-{{ $estimasiPajak }}', '{{ number_format(($estimasiPajak / $totalPendapatan) * 100, 1) }}%'],
            ['LABA BERSIH SETELAH PAJAK (NET PROFIT)', '{{ $labaBersihSetelahPajak }}', '{{ number_format($marginLabaBersih, 1) }}%']
        ];
        let csvContent = 'data:text/csv;charset=utf-8,' + baris.map(e => e.join(',')).join('\\n');
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'Laporan_Laba_Rugi_PBJ_{{ $namaBulan }}_{{ $tahun }}.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}" class="space-y-5">

    <!-- ================================================================
         1. KOP RESMI KHUSUS PRINT (HANYA MUNCUL SAAT CETAK / PDF)
    ================================================================ -->
    <div class="hidden print:block mb-6 border-b-2 border-slate-900 pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-pbj.png') }}" alt="Logo PBJ" class="h-12 w-auto object-contain">
                <div>
                    <h2 class="text-base font-black tracking-wide uppercase text-slate-900">PT PUTRA BALKOM JAYA</h2>
                    <p class="text-[11px] text-slate-600 font-medium">Distribusi Semen & Layanan Jasa Logistik Armada Nasional</p>
                    <p class="text-[9px] text-slate-500">Jl. Raya Surabaya - Rembang Km. 45, Jawa Timur | Telp: (031) 8988-1234</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-bold uppercase tracking-wider text-blue-900">LAPORAN LABA DAN RUGI KOMPREHENSIF</div>
                <div class="text-[10px] text-slate-600 mt-0.5">Periode: <span class="font-bold text-slate-900">{{ $namaBulan }} {{ $tahun }}</span></div>
                <div class="text-[9px] text-slate-400">Dicetak pada: {{ date('d F Y, H:i') }} WIB</div>
            </div>
        </div>
    </div>

    <!-- ================================================================
         2. HEADER MODUL EKSEKUTIF & TOOLBAR FILTER (TAMPILAN WEB)
    ================================================================ -->
    <div class="print:hidden animasi-masuk flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[10px] font-bold font-mono tracking-widest text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 rounded-md border border-blue-200/60 dark:border-blue-500/20 uppercase">
                    Laporan Eksekutif Finansial
                </span>
                <span class="text-slate-300 dark:text-slate-700">•</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Periode Aktif: <strong class="text-slate-800 dark:text-slate-200 font-semibold">{{ $namaBulan }} {{ $tahun }}</strong>
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Net Margin {{ number_format($marginLabaBersih, 1) }}% (Sehat)
                </span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Laporan Laba dan Rugi Komprehensif
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Evaluasi pendapatan usaha penjualan semen, HPP pengadaan pabrik, beban operasional logistik, dan laba bersih periode berjalan.
            </p>
        </div>

        <!-- Filter Form & Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Form Filter Periode -->
            <form method="GET" action="{{ route('laporan.laba_rugi') }}" class="flex items-center gap-1.5 bg-[#F8FAFC] dark:bg-[#1A1D2A] p-1 rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                <select name="bulan" class="text-xs font-semibold bg-transparent text-slate-800 dark:text-slate-200 py-1.5 px-2 focus:outline-none cursor-pointer">
                    @foreach($daftarBulan as $noBulan => $labelBulan)
                        <option value="{{ $noBulan }}" {{ $bulan == $noBulan ? 'selected' : '' }} class="dark:bg-[#1A1D2A]">{{ $labelBulan }}</option>
                    @endforeach
                </select>
                <span class="text-slate-300 dark:text-slate-700">/</span>
                <select name="tahun" class="text-xs font-semibold bg-transparent text-slate-800 dark:text-slate-200 py-1.5 px-2 focus:outline-none cursor-pointer">
                    @for($thn = 2024; $thn <= 2027; $thn++)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }} class="dark:bg-[#1A1D2A]">{{ $thn }}</option>
                    @endfor
                </select>
                <button type="submit" class="p-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-xs" title="Terapkan Filter">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>

            <!-- Ekspor Excel / CSV -->
            <button @click="eksporCsv()" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-[#1C1E2A] hover:bg-slate-50 dark:hover:bg-[#252837] border border-[#E2E8F0] dark:border-[#252837] rounded-xl transition-all shadow-xs" title="Unduh format spreadsheet">
                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Unduh CSV</span>
            </button>

            <!-- Tombol Cetak / PDF -->
            <button onclick="window.print()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Lembar Resmi</span>
            </button>
        </div>
    </div>

    <!-- ================================================================
         3. RINGKASAN 4 KARTU METRIK FINANSIAL EKSEKUTIF (TOP KPI CARDS)
    ================================================================ -->
    <div class="wadah-bertingkat grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        
        <!-- Kartu 1: Total Pendapatan Usaha -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs hover:border-blue-300 dark:hover:border-blue-700/50 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Total Pendapatan Usaha</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-2 font-mono tabular-nums tracking-tight">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#F1F5F9] dark:border-[#252837] text-[10px]">
                <span class="text-slate-400">Semen Zak, Curah & Jasa OA</span>
                <span class="font-mono font-semibold text-blue-600 dark:text-blue-400">100% Omzet</span>
            </div>
        </div>

        <!-- Kartu 2: Laba Kotor (Gross Profit) -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs hover:border-indigo-300 dark:hover:border-indigo-700/50 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Laba Kotor (Gross Profit)</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-2 font-mono tabular-nums tracking-tight">
                Rp {{ number_format($labaKotor, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#F1F5F9] dark:border-[#252837] text-[10px]">
                <span class="text-slate-400">Gross Profit Margin (GPM)</span>
                <span class="font-mono font-bold px-1.5 py-0.2 rounded bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                    {{ number_format($marginLabaKotor, 1) }}%
                </span>
            </div>
        </div>

        <!-- Kartu 3: Beban Operasional (OPEX) -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs hover:border-rose-300 dark:hover:border-rose-700/50 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Beban Operasional (OPEX)</span>
                <div class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-2 font-mono tabular-nums tracking-tight">
                Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#F1F5F9] dark:border-[#252837] text-[10px]">
                <span class="text-slate-400">BBM, Tol, Gaji, Servis, Kantor</span>
                <span class="font-mono font-semibold text-rose-600 dark:text-rose-400">
                    {{ number_format($rasioBebanOperasional, 1) }}% dari Omzet
                </span>
            </div>
        </div>

        <!-- Kartu 4: Laba Bersih Setelah Pajak (Net Profit) -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border-2 border-emerald-500/30 dark:border-emerald-500/40 bg-gradient-to-br from-emerald-500/[0.04] to-transparent shadow-xs hover:border-emerald-500 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-wider">Laba Bersih (Net Profit)</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs shadow-emerald-600/30 group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-2 font-mono tabular-nums tracking-tight">
                Rp {{ number_format($labaBersihSetelahPajak, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-emerald-200/60 dark:border-emerald-500/20 text-[10px]">
                <span class="text-slate-500 dark:text-slate-400">Net Profit Margin (NPM)</span>
                <span class="font-mono font-bold px-1.5 py-0.2 rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                    {{ number_format($marginLabaBersih, 1) }}%
                </span>
            </div>
        </div>

    </div>

    <!-- ================================================================
         4. NAVIGASI TAB INTERAKTIF (MODERN ENTERPRISE TABS)
    ================================================================ -->
    <div class="print:hidden flex items-center justify-between border-b border-[#E2E8F0] dark:border-[#252837] pb-1 gap-2">
        <div class="flex items-center gap-1 sm:gap-2">
            <!-- Tab 1: Format Standar PSAK -->
            <button @click="tabAktif = 'laporan'"
                    type="button"
                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2"
                    :class="tabAktif === 'laporan'
                        ? 'bg-blue-600 text-white shadow-xs'
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Format Standar PSAK 1</span>
            </button>

            <!-- Tab 2: Analisis Rasio & Efisiensi -->
            <button @click="tabAktif = 'rasio'"
                    type="button"
                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2"
                    :class="tabAktif === 'rasio'
                        ? 'bg-blue-600 text-white shadow-xs'
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                <span>Analisis Rasio & Margin</span>
            </button>

            <!-- Tab 3: Tren & Komposisi Beban -->
            <button @click="tabAktif = 'tren'"
                    type="button"
                    class="px-3 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-2"
                    :class="tabAktif === 'tren'
                        ? 'bg-blue-600 text-white shadow-xs'
                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#1E212E] hover:text-slate-900 dark:hover:text-white'">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                <span>Grafik Komposisi & Tren 6 Bulan</span>
            </button>
        </div>

        <div class="text-[11px] text-slate-400 hidden md:block">
            Mata Uang: <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">IDR (Rupiah)</span>
        </div>
    </div>

    <!-- ================================================================
         5. TAB 1: TABEL LAPORAN STANDAR PSAK (FORMAT RESMI)
    ================================================================ -->
    <div x-show="tabAktif === 'laporan'" class="animasi-masuk space-y-4">
        <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-xs">
            
            <!-- Table Header -->
            <div class="px-5 py-3.5 bg-[#F8FAFC] dark:bg-[#181B26] border-b border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">
                        PERHITUNGAN LABA DAN RUGI KOMPREHENSIF PERIODE {{ strtoupper($namaBulan) }} {{ $tahun }}
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Disusun berdasarkan standar pembukuan akuntansi PSAK dengan metode kalkulasi periodik.</p>
                </div>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded font-semibold bg-blue-100/80 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/60">
                    Buku Besar Terpadu
                </span>
            </div>

            <!-- Table Body -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50/60 dark:bg-[#161823] text-slate-500 dark:text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                            <th class="py-2.5 px-4 w-12 text-center">No</th>
                            <th class="py-2.5 px-4 w-28">Kode Akun</th>
                            <th class="py-2.5 px-4">Deskripsi Pos Laporan Finansial</th>
                            <th class="py-2.5 px-4 w-44 text-right">Nominal (Rp)</th>
                            <th class="py-2.5 px-4 w-24 text-right">% Omzet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F1F5F9] dark:divide-[#1F2230]">
                        
                        <!-- --------------------------------------------------------
                             SEKSI 1: PENDAPATAN USAHA (REVENUE)
                        -------------------------------------------------------- -->
                        <tr class="bg-blue-50/40 dark:bg-blue-950/20 font-bold text-blue-900 dark:text-blue-300">
                            <td class="py-2.5 px-4 text-center font-mono">1</td>
                            <td class="py-2.5 px-4 font-mono">4-0000</td>
                            <td class="py-2.5 px-4 uppercase tracking-wide">PENDAPATAN USAHA (REVENUE)</td>
                            <td class="py-2.5 px-4 text-right font-mono"></td>
                            <td class="py-2.5 px-4 text-right font-mono"></td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">1.1</td>
                            <td class="py-2 px-4 font-mono text-slate-500">4-1100</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Penjualan Semen Zak (50kg & 40kg)</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($penjualanSemenZak, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($penjualanSemenZak / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">1.2</td>
                            <td class="py-2 px-4 font-mono text-slate-500">4-1200</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Penjualan Semen Curah (Batching Plant & Industri)</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($penjualanSemenCurah, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($penjualanSemenCurah / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">1.3</td>
                            <td class="py-2 px-4 font-mono text-slate-500">4-1300</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Pendapatan Jasa Distribusi & Ongkos Angkut (OA)</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($pendapatanOngkosAngkut, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($pendapatanOngkosAngkut / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">1.4</td>
                            <td class="py-2 px-4 font-mono text-slate-500">4-1400</td>
                            <td class="py-2 px-4 text-rose-600 dark:text-rose-400 pl-8">Potongan Harga & Diskon Penjualan (-)</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-rose-600 dark:text-rose-400">
                                (Rp {{ number_format($potonganPenjualan, 0, ',', '.') }})
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-rose-500">
                                -{{ number_format(($potonganPenjualan / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <!-- Subtotal Pendapatan -->
                        <tr class="bg-blue-50/80 dark:bg-blue-900/20 font-bold border-y-2 border-blue-200 dark:border-blue-800/60 text-blue-950 dark:text-blue-200">
                            <td colspan="3" class="py-2.5 px-4 text-right uppercase tracking-wider">
                                TOTAL PENDAPATAN USAHA BERSIH (NET REVENUE):
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono tabular-nums text-sm font-bold text-blue-700 dark:text-blue-300">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-blue-700 dark:text-blue-300">
                                100.0%
                            </td>
                        </tr>

                        <!-- --------------------------------------------------------
                             SEKSI 2: HARGA POKOK PENJUALAN (HPP / COGS)
                        -------------------------------------------------------- -->
                        <tr class="bg-slate-100/50 dark:bg-slate-900/30 font-bold text-slate-900 dark:text-slate-100">
                            <td class="py-2.5 px-4 text-center font-mono">2</td>
                            <td class="py-2.5 px-4 font-mono">5-0000</td>
                            <td class="py-2.5 px-4 uppercase tracking-wide">HARGA POKOK PENJUALAN (HPP / COGS)</td>
                            <td class="py-2.5 px-4 text-right font-mono"></td>
                            <td class="py-2.5 px-4 text-right font-mono"></td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">2.1</td>
                            <td class="py-2 px-4 font-mono text-slate-500">5-1100</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Harga Pokok Pembelian Semen Pabrik (Gresik/Tuban)</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($pembelianSemenPabrik, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($pembelianSemenPabrik / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">2.2</td>
                            <td class="py-2 px-4 font-mono text-slate-500">5-1200</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Ongkos Bongkar Muat Pabrik & Handling</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($ongkosBongkarMuatPabrik, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($ongkosBongkarMuatPabrik / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">2.3</td>
                            <td class="py-2 px-4 font-mono text-slate-500">5-1300</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Biaya Kuli & Tenaga Angkut Gudang Pabrik</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($biayaKuliPabrik, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($biayaKuliPabrik / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <!-- Subtotal HPP -->
                        <tr class="bg-rose-50/40 dark:bg-rose-950/20 font-bold border-y border-rose-200 dark:border-rose-900/40 text-rose-900 dark:text-rose-300">
                            <td colspan="3" class="py-2 px-4 text-right uppercase tracking-wider">
                                TOTAL HARGA POKOK PENJUALAN (HPP):
                            </td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums font-bold text-rose-600 dark:text-rose-400">
                                (Rp {{ number_format($totalHpp, 0, ',', '.') }})
                            </td>
                            <td class="py-2 px-4 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                                -{{ number_format(($totalHpp / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <!-- Laba Kotor -->
                        <tr class="bg-indigo-50/80 dark:bg-indigo-950/30 font-bold border-b-2 border-indigo-300 dark:border-indigo-800 text-indigo-950 dark:text-indigo-200">
                            <td colspan="3" class="py-2.5 px-4 text-right uppercase tracking-wider text-xs">
                                LABA KOTOR (GROSS PROFIT):
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono tabular-nums text-sm font-bold text-indigo-700 dark:text-indigo-300">
                                Rp {{ number_format($labaKotor, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-indigo-700 dark:text-indigo-300">
                                {{ number_format($marginLabaKotor, 1) }}%
                            </td>
                        </tr>

                        <!-- --------------------------------------------------------
                             SEKSI 3: BEBAN OPERASIONAL USAHA (OPEX)
                        -------------------------------------------------------- -->
                        <tr class="bg-slate-100/50 dark:bg-slate-900/30 font-bold text-slate-900 dark:text-slate-100">
                            <td class="py-2.5 px-4 text-center font-mono">3</td>
                            <td class="py-2.5 px-4 font-mono">6-0000</td>
                            <td class="py-2.5 px-4 uppercase tracking-wide">BEBAN OPERASIONAL USAHA & DISTRIBUSI (OPEX)</td>
                            <td class="py-2.5 px-4 text-right font-mono"></td>
                            <td class="py-2.5 px-4 text-right font-mono"></td>
                        </tr>

                        <!-- Subseksi 3.1: Beban Logistik & Armada -->
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.1</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-1100</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Bahan Bakar Solar B35 Armada Truk Distribusi</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanBBM, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanBBM / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.2</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-1200</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Biaya E-Toll & Penyeberangan Feri Armada Truk</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanTolPenyeberangan, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanTolPenyeberangan / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.3</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-1300</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">KIR, Pajak Kendaraan & Retribusi Trayek Jalan</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanKirPajakArmada, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanKirPajakArmada / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>

                        <!-- Subseksi 3.2: Bengkel & Pemeliharaan -->
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.4</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-2100</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Jasa Servis & Perbaikan Berkala Truk Bengkel</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanServisBengkel, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanServisBengkel / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.5</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-2200</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Pembelian Sparepart, Oli Pelumas & Penggantian Ban Truk</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanSparepartBan, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanSparepartBan / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>

                        <!-- Subseksi 3.3: Gaji & Personalia -->
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.6</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-3100</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Gaji Pokok & Upah Supir Pengiriman</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanGajiSupir, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanGajiSupir / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.7</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-3200</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Uang Makan & Tunjangan Jalan Per Ritase Supir</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanUangJalanSupir, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanUangJalanSupir / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.8</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-3300</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Gaji Staff Administrasi, Keuangan, Operasional & Manajemen</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanGajiManajemen, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanGajiManajemen / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>

                        <!-- Subseksi 3.4: Kantor & Umum -->
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.9</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-4100</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Beban Listrik, Air, & Utilitas Gudang Semen</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanListrikAir, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanListrikAir / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">3.10</td>
                            <td class="py-2 px-4 font-mono text-slate-500">6-4200</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">Beban ATK, Telekomunikasi, Internet & Perlengkapan Kantor</td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($bebanAtkKomunikasi, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-slate-500">
                                {{ number_format(($bebanAtkKomunikasi / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>

                        <!-- Subtotal Beban Operasional -->
                        <tr class="bg-rose-50/80 dark:bg-rose-950/30 font-bold border-y border-rose-200 dark:border-rose-900/60 text-rose-950 dark:text-rose-200">
                            <td colspan="3" class="py-2.5 px-4 text-right uppercase tracking-wider">
                                TOTAL BEBAN OPERASIONAL USAHA (OPEX):
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono tabular-nums text-sm font-bold text-rose-600 dark:text-rose-400">
                                (Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }})
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold text-rose-600 dark:text-rose-400">
                                -{{ number_format($rasioBebanOperasional, 1) }}%
                            </td>
                        </tr>

                        <!-- --------------------------------------------------------
                             SEKSI 4: HASIL BERSIH & PAJAK
                        -------------------------------------------------------- -->
                        <!-- Laba Operasional Sebelum Pajak (EBIT) -->
                        <tr class="bg-slate-100/90 dark:bg-[#1E212E] font-bold text-slate-900 dark:text-slate-100 border-b border-[#E2E8F0] dark:border-[#252837]">
                            <td colspan="3" class="py-2.5 px-4 text-right uppercase tracking-wider">
                                LABA BERSIH SEBELUM PAJAK (EBIT / OPERATING INCOME):
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono tabular-nums text-sm font-bold">
                                Rp {{ number_format($labaBersihOperasional, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold">
                                {{ number_format($marginLabaOperasional, 1) }}%
                            </td>
                        </tr>

                        <!-- Pajak Penghasilan -->
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-[#181B26]">
                            <td class="py-2 px-4 text-center text-slate-400">4</td>
                            <td class="py-2 px-4 font-mono text-slate-500">8-1000</td>
                            <td class="py-2 px-4 text-slate-700 dark:text-slate-300 pl-8">
                                Estimasi Beban Pajak Penghasilan (PPh Badan 11%)
                            </td>
                            <td class="py-2 px-4 text-right font-mono tabular-nums text-rose-600 dark:text-rose-400">
                                (Rp {{ number_format($estimasiPajak, 0, ',', '.') }})
                            </td>
                            <td class="py-2 px-4 text-right font-mono text-rose-500">
                                -{{ number_format(($estimasiPajak / $totalPendapatan) * 100, 1) }}%
                            </td>
                        </tr>

                        <!-- GRAND TOTAL: LABA BERSIH SETELAH PAJAK -->
                        <tr class="bg-gradient-to-r from-emerald-50 via-emerald-100/80 to-emerald-50 dark:from-emerald-950/40 dark:via-emerald-900/30 dark:to-emerald-950/40 border-t-2 border-b-2 border-emerald-500 dark:border-emerald-500/80 text-emerald-950 dark:text-emerald-200">
                            <td colspan="3" class="py-3.5 px-4 text-right font-black uppercase tracking-wider text-xs sm:text-sm">
                                LABA BERSIH SETELAH PAJAK (NET PROFIT / PROFIT SETELAH PAJAK):
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono tabular-nums text-base sm:text-lg font-black text-emerald-700 dark:text-emerald-300">
                                Rp {{ number_format($labaBersihSetelahPajak, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-sm font-black text-emerald-700 dark:text-emerald-300">
                                {{ number_format($marginLabaBersih, 1) }}%
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Table Footer Note -->
            <div class="px-5 py-3 bg-[#F8FAFC] dark:bg-[#181B26] border-t border-[#E2E8F0] dark:border-[#252837] flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400">
                <div>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Catatan Auditor:</span> Laba bersih periode ini diakumulasikan ke pos Ekuitas (Laba Ditahan) pada Laporan Posisi Keuangan (Neraca).
                </div>
                <div class="font-mono text-[10px] text-slate-400">
                    Status: <span class="text-emerald-600 dark:text-emerald-400 font-bold">REKONSILIASI LENGKAP</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================
         6. TAB 2: ANALISIS RASIO & EFISIENSI FINANSIAL
    ================================================================ -->
    <div x-show="tabAktif === 'rasio'" class="animasi-masuk space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Rasio 1: Gross Profit Margin (GPM) -->
            <div class="bg-white dark:bg-[#14161F] p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Margin Laba Kotor</div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Gross Profit Margin (GPM)</h4>
                    </div>
                    <span class="text-lg font-bold font-mono text-indigo-600 dark:text-indigo-400">{{ number_format($marginLabaKotor, 1) }}%</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min(100, $marginLabaKotor * 3) }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Menunjukkan persentase laba kotor yang dihasilkan dari setiap rupiah penjualan setelah dikurangi HPP pabrik. Nilai <strong>{{ number_format($marginLabaKotor, 1) }}%</strong> berada dalam rentang sehat standar industri distribusi semen (15% - 22%).
                </p>
            </div>

            <!-- Rasio 2: Operating Profit Margin (OPM) -->
            <div class="bg-white dark:bg-[#14161F] p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Margin Laba Operasional</div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Operating Profit Margin (OPM)</h4>
                    </div>
                    <span class="text-lg font-bold font-mono text-blue-600 dark:text-blue-400">{{ number_format($marginLabaOperasional, 1) }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, $marginLabaOperasional * 5) }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Mengukur kemampuan operasional perusahaan dalam menghasilkan keuntungan murni sebelum bunga dan pajak. Rasio <strong>{{ number_format($marginLabaOperasional, 1) }}%</strong> menunjukkan efisiensi tata kelola armada dan bengkel.
                </p>
            </div>

            <!-- Rasio 3: Net Profit Margin (NPM) -->
            <div class="bg-white dark:bg-[#14161F] p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Margin Laba Bersih</div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Net Profit Margin (NPM)</h4>
                    </div>
                    <span class="text-lg font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($marginLabaBersih, 1) }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-emerald-600 h-2.5 rounded-full" style="width: {{ min(100, $marginLabaBersih * 8) }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Persentase sisa omzet yang murni menjadi profit dividen dan cadangan modal perusahaan setelah pajak. Nilai <strong>{{ number_format($marginLabaBersih, 1) }}%</strong> melampaui target minimum manajemen (5.0%).
                </p>
            </div>

            <!-- Rasio 4: OPEX-to-Revenue Ratio -->
            <div class="bg-white dark:bg-[#14161F] p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Rasio Beban Operasional</div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">OPEX to Revenue Ratio</h4>
                    </div>
                    <span class="text-lg font-bold font-mono text-rose-600 dark:text-rose-400">{{ number_format($rasioBebanOperasional, 1) }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                    <div class="bg-rose-500 h-2.5 rounded-full" style="width: {{ min(100, $rasioBebanOperasional * 3) }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Menunjukkan porsi pengeluaran operasional (BBM, gaji supir, tol, servis) terhadap total omzet. Nilai <strong>{{ number_format($rasioBebanOperasional, 1) }}%</strong> berada dalam batas kontrol optimal yang aman (&lt; 15%).
                </p>
            </div>

        </div>
    </div>

    <!-- ================================================================
         7. TAB 3: GRAFIK KOMPOSISI BEBAN & TREN 6 BULAN TERAKHIR
    ================================================================ -->
    <div x-show="tabAktif === 'tren'" class="animasi-masuk space-y-4">
        
        <!-- Baris 1: Komposisi Pengeluaran Operasional -->
        <div class="bg-white dark:bg-[#14161F] p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">
                        KOMPOSISI STRUKTUR BEBAN OPERASIONAL
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Proporsi pos pengeluaran terbesar yang mempengaruhi margin laba bersih.</p>
                </div>
                <span class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300">
                    Total: Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}
                </span>
            </div>

            <div class="space-y-3">
                <!-- Bar 1: Logistik & BBM -->
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Beban Bahan Bakar (BBM Solar B35) & E-Toll</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-slate-100">
                            Rp {{ number_format($subtotalBebanLogistik, 0, ',', '.') }} ({{ number_format(($subtotalBebanLogistik / $totalBebanOperasional) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                        <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ ($subtotalBebanLogistik / $totalBebanOperasional) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Bar 2: Gaji Supir & Staff -->
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Gaji Karyawan, Upah Supir & Uang Jalan</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-slate-100">
                            Rp {{ number_format($subtotalBebanGaji, 0, ',', '.') }} ({{ number_format(($subtotalBebanGaji / $totalBebanOperasional) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($subtotalBebanGaji / $totalBebanOperasional) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Bar 3: Bengkel & Sparepart -->
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Servis Bengkel, Sparepart & Pergantian Ban</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-slate-100">
                            Rp {{ number_format($subtotalBebanBengkel, 0, ',', '.') }} ({{ number_format(($subtotalBebanBengkel / $totalBebanOperasional) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                        <div class="bg-rose-500 h-2.5 rounded-full" style="width: {{ ($subtotalBebanBengkel / $totalBebanOperasional) * 100 }}%"></div>
                    </div>
                </div>

                <!-- Bar 4: Kantor & Utilitas -->
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700 dark:text-slate-300">Listrik, Air, ATK & Kebutuhan Kantor</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-slate-100">
                            Rp {{ number_format($subtotalBebanKantor, 0, ',', '.') }} ({{ number_format(($subtotalBebanKantor / $totalBebanOperasional) * 100, 1) }}%)
                        </span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-[#252837] rounded-full h-2.5 overflow-hidden">
                        <div class="bg-slate-400 h-2.5 rounded-full" style="width: {{ ($subtotalBebanKantor / $totalBebanOperasional) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris 2: Tren Historis 6 Bulan Terakhir -->
        <div class="bg-white dark:bg-[#14161F] p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs space-y-4">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">
                    HISTORI TREN PENDAPATAN & LABA BERSIH (6 BULAN TERAKHIR)
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Pertumbuhan omzet penjualan semen dan profitabilitas berjalan PT Putra Balkom Jaya.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5 pt-2">
                @foreach($trenBulanan as $dataTren)
                <div class="p-3 rounded-xl bg-[#F8FAFC] dark:bg-[#1A1D2A] border border-[#E2E8F0] dark:border-[#252837] flex flex-col justify-between text-center">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">{{ $dataTren['bulan'] }}</span>
                    <div class="my-2">
                        <div class="text-xs font-bold font-mono text-slate-800 dark:text-slate-200 truncate">
                            Rp {{ number_format($dataTren['pendapatan'] / 1000000, 0) }} Jt
                        </div>
                        <div class="text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                            Laba: Rp {{ number_format($dataTren['laba_bersih'] / 1000000, 1) }} Jt
                        </div>
                    </div>
                    <span class="text-[9px] px-1.5 py-0.2 rounded font-mono font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300">
                        Margin {{ $dataTren['margin'] }}%
                    </span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- ================================================================
         8. LEMBAR TANDA TANGAN RESMI KHUSUS CETAK (PRINT ONLY)
    ================================================================ -->
    <div class="hidden print:block pt-8 mt-6 border-t border-slate-300 text-xs">
        <div class="grid grid-cols-2 text-center gap-10">
            <div>
                <p class="text-slate-600">Disusun oleh,</p>
                <p class="font-bold text-slate-900 mt-1">SPV KEUANGAN & AKUNTANSI</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">( {{ session('nama_lengkap', 'Supervisor Keuangan') }} )</p>
                <p class="text-[10px] text-slate-500">NIP: PBJ-FIN-{{ date('Ym') }}</p>
            </div>
            <div>
                <p class="text-slate-600">Mengetahui & Menyetujui,</p>
                <p class="font-bold text-slate-900 mt-1">DIREKTUR UTAMA / MANAJEMEN</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">( H. Bambang Soeprapto, S.E., M.M. )</p>
                <p class="text-[10px] text-slate-500">Direksi PT Putra Balkom Jaya</p>
            </div>
        </div>
    </div>

</div>
@endsection
