@extends('layouts.app')

@section('judul', 'Dashboard Terpadu')

@section('konten')
<div class="max-w-full space-y-5" x-data="{
    // Helper deskripsi dinamis per role
    get infoRole() {
        const deskripsiMap = {
            SUPER_ADMIN: {
                judul: 'Pusat Kontrol Akun & Matriks Hak Akses RBAC',
                sub: 'Pengelolaan akun personil, otorisasi peran pengguna, dan audit keamanan sistem.'
            },
            SPV_KEUANGAN: {
                judul: 'Ruang Kendali Finansial & Akuntansi Terpadu',
                sub: 'Monitoring arus kas, otorisasi faktur AR/AP, jurnal umum, serta laporan laba rugi & neraca.'
            },
            STAFF_AR: {
                judul: 'Portal Account Receivable (Penjualan & Piutang)',
                sub: 'Penerbitan faktur penjualan, rekonsiliasi piutang toko bangunan, dan pencatatan deposit.'
            },
            STAFF_AP: {
                judul: 'Portal Account Payable (Pembelian & Pengeluaran)',
                sub: 'Penerbitan Sales Order (SO) pabrik semen, verifikasi berita acara rilisan, dan pengeluaran kas.'
            },
            SPV_OPERASIONAL: {
                judul: 'Pusat Komando Operasional, Logistik & KSO',
                sub: 'Pengawasan distribusi armada, pengiriman surat jalan, master ongkos angkut, dan opname stok.'
            },
            DISPATCHER: {
                judul: 'Pusat Penugasan Distribusi & Surat Jalan',
                sub: 'Penerbitan surat jalan (SJ), penugasan supir armada truk, dan pelacakan status rute jalan.'
            },
            PENGAWAS_DRIVER: {
                judul: 'Portal Monitoring Supir & Personil Armada',
                sub: 'Pemantauan kesiapan driver, riwayat jalan armada, dan pengelolaan data supir.'
            },
            SPV_GUDANG: {
                judul: 'Pusat Manajemen Stok Semen & Opname Gudang',
                sub: 'Monitoring fisik persediaan semen zak/curah, mutasi barang, dan rekonsiliasi opname gudang.'
            },
            PENGAWAS_KENDARAAN: {
                judul: 'Pusat Bengkel Pemeliharaan & Suku Cadang Truk',
                sub: 'Penerbitan Surat Perintah Kerja (SPK) servis, inventaris sparepart, dan logistik perbaikan.'
            },
            DIREKTUR_MANAGER: {
                judul: 'Ringkasan Eksekutif Kinerja & Laporan Finansial',
                sub: 'Analisis profitabilitas, laporan posisi keuangan (neraca), dan performa distribusi semen.'
            }
        };
        return deskripsiMap[this.jabatanAktif] || deskripsiMap.SPV_KEUANGAN;
    }
}">

    <!-- ================================================================
         1. HERO HEADER: GREETING & PINTASAN AKSI DINAMIS SESUAI RBAC
    ================================================================ -->
    <div class="bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 font-mono">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                    <span x-text="labelJabatan">SPV Keuangan</span>
                </span>
                <span class="text-xs text-slate-300 dark:text-slate-600">·</span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ date('l, d F Y') }}</span>
            </div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-slate-100 tracking-tight"
                x-text="infoRole.judul">
                Ruang Kendali Finansial & Akuntansi Terpadu
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"
               x-text="infoRole.sub">
                Monitoring arus kas, otorisasi faktur AR/AP, jurnal umum, serta laporan laba rugi & neraca.
            </p>
        </div>

        <!-- Tombol Pintasan Aksi Dinamis Sesuai RBAC -->
        <div class="flex items-center flex-wrap gap-2 shrink-0">

            <!-- Akses Faktur (AR / Keuangan) -->
            <template x-if="bisaAkses('ar_faktur')">
                <a href="{{ route('keuangan.ar.faktur') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-sm shadow-blue-600/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Faktur Baru</span>
                </a>
            </template>

            <!-- Akses Surat Jalan (Operasional / Dispatcher) -->
            <template x-if="bisaAkses('kirim_sj')">
                <a href="{{ route('operasional.pengiriman.surat_jalan') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-600/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Surat Jalan Baru</span>
                </a>
            </template>

            <!-- Akses Pembelian SO (AP / Keuangan) -->
            <template x-if="bisaAkses('ap_pembelian')">
                <a href="{{ route('keuangan.ap.pembelian_so') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-600/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Pembelian SO</span>
                </a>
            </template>

            <!-- Akses Opname Gudang -->
            <template x-if="bisaAkses('gudang_opname')">
                <a href="{{ route('operasional.gudang.opname') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 dark:hover:bg-[#252837] transition-colors">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Stock Opname</span>
                </a>
            </template>

            <!-- Akses Bengkel SPK -->
            <template x-if="bisaAkses('bengkel_perbaikan')">
                <a href="{{ route('operasional.bengkel.perbaikan') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-white bg-red-600 hover:bg-red-700 transition-all shadow-sm shadow-red-600/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat SPK Servis</span>
                </a>
            </template>

            <!-- Akses Super Admin: Kelola Akun -->
            <template x-if="bisaAkses('admin_akun')">
                <a href="{{ route('superadmin.kelola_akun') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 transition-all shadow-sm shadow-purple-600/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Tambah Pengguna</span>
                </a>
            </template>

            <!-- Akses Laporan Eksekutif (Direktur / SPV Keuangan) -->
            <template x-if="bisaAkses('laporan_neraca')">
                <a href="{{ route('laporan.neraca') }}"
                   class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-200 bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 dark:hover:bg-[#252837] transition-colors">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Buka Laporan</span>
                </a>
            </template>

        </div>
    </div>

    <!-- ================================================================
         2. GRID METRIK & KPI CARD ADAPTIF SESUAI HAK AKSES JABATAN
    ================================================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- KELOMPOK KPI KEUANGAN (SPV Keuangan, Staff AR, Direktur) -->
        <template x-if="bisaAkses('ar_faktur') || bisaAkses('laporan_neraca')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penjualan Bulan Ini</span>
                    <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">Rp 842.500.000</div>
                <div class="mt-1 flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                    <span>+12.4% vs bulan lalu</span>
                </div>
            </div>
        </template>

        <!-- KELOMPOK KPI PIUTANG / AR -->
        <template x-if="bisaAkses('ar_piutang') || bisaAkses('laporan_neraca')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Piutang Beredar (AR)</span>
                    <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-amber-600 dark:text-amber-400">Rp 128.400.000</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    14 faktur aktif · <span class="text-rose-500 font-semibold">3 jatuh tempo</span>
                </div>
            </div>
        </template>

        <!-- KELOMPOK KPI AP & PENGELUARAN (SPV Keuangan & Staff AP) -->
        <template x-if="bisaAkses('ap_pengeluaran') && !bisaAkses('kirim_sj')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pengeluaran Kas (AP)</span>
                    <div class="w-7 h-7 rounded-lg bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-rose-600 dark:text-rose-400">Rp 315.820.000</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    Beli SO pabrik & operasional
                </div>
            </div>
        </template>

        <!-- KELOMPOK KPI OPERASIONAL & LOGISTIK (SPV Operasional, Dispatcher, Pengawas Driver) -->
        <template x-if="bisaAkses('armada_truk') || bisaAkses('kirim_sj')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Armada Beroperasi</span>
                    <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">8 / 12 Unit</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    4.800 Zak dalam perjalanan hari ini
                </div>
            </div>
        </template>

        <!-- KELOMPOK KPI DRIVER & PERSONIL -->
        <template x-if="bisaAkses('armada_driver')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Driver Siap Tugas</span>
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">18 Personil</div>
                <div class="mt-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">
                    14 Aktif Jalan · 4 Standby Pool
                </div>
            </div>
        </template>

        <!-- KELOMPOK KPI GUDANG & STOCK OPNAME (SPV Gudang, SPV Operasional) -->
        <template x-if="bisaAkses('gudang_stok') || bisaAkses('gudang_opname')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Stok Semen</span>
                    <div class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">14.250 Zak</div>
                <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                    4 Gudang aktif · 180 Ton Curah Silo
                </div>
            </div>
        </template>

        <!-- KELOMPOK KPI BENGKEL (Pengawas Kendaraan) -->
        <template x-if="bisaAkses('bengkel_perbaikan')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">SPK Servis Berjalan</span>
                    <div class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">3 Unit Truk</div>
                <div class="mt-1 text-[11px] text-amber-600 dark:text-amber-400 font-semibold">
                    1 Ganti Oli · 2 Perbaikan Rem
                </div>
            </div>
        </template>

        <!-- KELOMPOK KPI SUPER ADMIN (Super Admin) -->
        <template x-if="bisaAkses('admin_akun')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Akun Staf</span>
                    <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                    </div>
                </div>
                <div class="text-xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">12 Pengguna</div>
                <div class="mt-1 text-[11px] text-purple-600 dark:text-purple-400 font-semibold">
                    10 Peran RBAC Terverifikasi
                </div>
            </div>
        </template>

    </div>

    <!-- ================================================================
         3. PETA MODUL WEWENANG SESUAI ROLE (COMPACT ENTERPRISE GRID)
    ================================================================ -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-4 sm:p-5 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0] dark:border-[#252837] mb-3.5">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h2 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                    Modul Wewenang & Akses Cepat (<span x-text="labelJabatan"></span>)
                </h2>
            </div>
            <span class="text-[10px] font-mono font-semibold text-slate-400">
                Hierarki RBAC Aktif
            </span>
        </div>

        <!-- Grid Tombol Modul Akses Terkait -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 text-xs">
            
            <a x-show="bisaAkses('admin_akun')" href="{{ route('superadmin.kelola_akun') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-purple-300 dark:hover:border-purple-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-purple-600 dark:text-purple-400 font-bold">RBAC</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-purple-600 mt-1 truncate">Kelola Akun</span>
            </a>

            <a x-show="bisaAkses('master_customer')" href="{{ route('master.customer.index') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-cyan-300 dark:hover:border-cyan-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-cyan-600 dark:text-cyan-400 font-bold">Master</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-cyan-600 mt-1 truncate">Data Customer</span>
            </a>

            <a x-show="bisaAkses('ar_faktur')" href="{{ route('keuangan.ar.faktur') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-emerald-300 dark:hover:border-emerald-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">AR</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 mt-1 truncate">Faktur Penjualan</span>
            </a>

            <a x-show="bisaAkses('ar_piutang')" href="{{ route('keuangan.ar.piutang') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-amber-300 dark:hover:border-amber-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">AR</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-amber-600 mt-1 truncate">List Piutang</span>
            </a>

            <a x-show="bisaAkses('ap_pembelian')" href="{{ route('keuangan.ap.pembelian_so') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-blue-300 dark:hover:border-blue-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-blue-600 dark:text-blue-400 font-bold">AP</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 mt-1 truncate">Pembelian SO</span>
            </a>

            <a x-show="bisaAkses('ap_pengeluaran')" href="{{ route('keuangan.ap.pengeluaran') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-rose-300 dark:hover:border-rose-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-rose-600 dark:text-rose-400 font-bold">AP</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-rose-600 mt-1 truncate">Pengeluaran Kas</span>
            </a>

            <a x-show="bisaAkses('kirim_sj')" href="{{ route('operasional.pengiriman.surat_jalan') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-sky-300 dark:hover:border-sky-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-sky-600 dark:text-sky-400 font-bold">Kirim</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-sky-600 mt-1 truncate">Surat Jalan (SJ)</span>
            </a>

            <a x-show="bisaAkses('kirim_ongkos')" href="{{ route('operasional.pengiriman.ongkos_angkut') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-slate-300 dark:hover:border-slate-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-slate-500 font-bold">Tarif</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 mt-1 truncate">Ongkos Angkut</span>
            </a>

            <a x-show="bisaAkses('gudang_opname')" href="{{ route('operasional.gudang.opname') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-teal-300 dark:hover:border-teal-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-teal-600 dark:text-teal-400 font-bold">Opname</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-teal-600 mt-1 truncate">Opname Gudang</span>
            </a>

            <a x-show="bisaAkses('armada_driver')" href="{{ route('operasional.armada.driver') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-indigo-300 dark:hover:border-indigo-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-indigo-600 dark:text-indigo-400 font-bold">Supir</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 mt-1 truncate">Data Driver</span>
            </a>

            <a x-show="bisaAkses('armada_truk')" href="{{ route('operasional.armada.kendaraan') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-orange-300 dark:hover:border-orange-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-orange-600 dark:text-orange-400 font-bold">Armada</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-orange-600 mt-1 truncate">Data Kendaraan</span>
            </a>

            <a x-show="bisaAkses('ops_kso')" href="{{ route('operasional.kso') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-blue-300 dark:hover:border-blue-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-blue-600 dark:text-blue-400 font-bold">Mitra</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 mt-1 truncate">Data KSO</span>
            </a>

            <a x-show="bisaAkses('bengkel_perbaikan')" href="{{ route('operasional.bengkel.perbaikan') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-red-300 dark:hover:border-red-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-red-600 dark:text-red-400 font-bold">Bengkel</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-red-600 mt-1 truncate">SPK Servis</span>
            </a>

            <a x-show="bisaAkses('laporan_neraca')" href="{{ route('laporan.neraca') }}" class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-emerald-300 dark:hover:border-emerald-600 transition-all flex flex-col justify-between group">
                <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Laporan</span>
                <span class="font-semibold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 mt-1 truncate">Neraca & Laba Rugi</span>
            </a>

        </div>
    </div>

    <!-- ================================================================
         4. FEED TRANSAKSI & AKTIVITAS TERKINI (MENYESUAIKAN RBAC)
    ================================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- Feed 1: Faktur Penjualan Terkini (Hanya tampil jika ada akses AR / Keuangan) -->
        <template x-if="bisaAkses('ar_faktur') || bisaAkses('ar_piutang') || bisaAkses('laporan_neraca')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Faktur Penjualan Terkini (AR)</h3>
                        <p class="text-[10px] text-slate-400">Monitoring status pembayaran & piutang toko</p>
                    </div>
                    <a href="{{ route('keuangan.ar.faktur') }}" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 text-[11px]">
                            <tr>
                                <th class="px-3.5 py-2 text-left font-semibold">No. Faktur</th>
                                <th class="px-3.5 py-2 text-left font-semibold">Customer</th>
                                <th class="px-3.5 py-2 text-right font-semibold">Total Tagihan</th>
                                <th class="px-3.5 py-2 text-center font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300 text-xs">
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260901-001</td>
                                <td class="px-3.5 py-2.5 font-medium text-slate-900 dark:text-slate-100">TB Maju Jaya Sentosa</td>
                                <td class="px-3.5 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 42.500.000</td>
                                <td class="px-3.5 py-2.5 text-center"><span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Lunas</span></td>
                            </tr>
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260901-002</td>
                                <td class="px-3.5 py-2.5 font-medium text-slate-900 dark:text-slate-100">TB Cahaya Bangunan</td>
                                <td class="px-3.5 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 68.000.000</td>
                                <td class="px-3.5 py-2.5 text-center"><span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Piutang</span></td>
                            </tr>
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260831-048</td>
                                <td class="px-3.5 py-2.5 font-medium text-slate-900 dark:text-slate-100">TB Abadi Makmur</td>
                                <td class="px-3.5 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 32.150.000</td>
                                <td class="px-3.5 py-2.5 text-center"><span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400">Jatuh Tempo</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- Feed 2: Pengiriman Armada Terkini (Hanya tampil jika ada akses Pengiriman / Operasional) -->
        <template x-if="bisaAkses('kirim_sj') || bisaAkses('armada_truk') || bisaAkses('armada_driver')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Pengiriman & Surat Jalan (Logistik)</h3>
                        <p class="text-[10px] text-slate-400">Status penugasan supir & perjalanan rute truk</p>
                    </div>
                    <a href="{{ route('operasional.pengiriman.surat_jalan') }}" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 text-[11px]">
                            <tr>
                                <th class="px-3.5 py-2 text-left font-semibold">No. SJ</th>
                                <th class="px-3.5 py-2 text-left font-semibold">Driver & Truk</th>
                                <th class="px-3.5 py-2 text-right font-semibold">Muatan</th>
                                <th class="px-3.5 py-2 text-center font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300 text-xs">
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260901-088</td>
                                <td class="px-3.5 py-2.5">
                                    <div class="font-medium text-slate-900 dark:text-slate-100">Joko Susanto</div>
                                    <div class="text-[10px] text-slate-400 font-mono">B 9283 TDF (Tronton)</div>
                                </td>
                                <td class="px-3.5 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">400 Zak</td>
                                <td class="px-3.5 py-2.5 text-center"><span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">Dalam Jalan</span></td>
                            </tr>
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260901-089</td>
                                <td class="px-3.5 py-2.5">
                                    <div class="font-medium text-slate-900 dark:text-slate-100">Rahmat Hidayat</div>
                                    <div class="text-[10px] text-slate-400 font-mono">B 8411 UQ (Colt Diesel)</div>
                                </td>
                                <td class="px-3.5 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">280 Zak</td>
                                <td class="px-3.5 py-2.5 text-center"><span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Muat Barang</span></td>
                            </tr>
                            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                                <td class="px-3.5 py-2.5 font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260831-085</td>
                                <td class="px-3.5 py-2.5">
                                    <div class="font-medium text-slate-900 dark:text-slate-100">Sugeng Supriyadi</div>
                                    <div class="text-[10px] text-slate-400 font-mono">B 9102 VKA (Tronton)</div>
                                </td>
                                <td class="px-3.5 py-2.5 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">450 Zak</td>
                                <td class="px-3.5 py-2.5 text-center"><span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Terkirim</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- Feed 3: Stock Opname & Gudang Terkini (Hanya tampil jika ada akses Gudang) -->
        <template x-if="bisaAkses('gudang_opname') && !bisaAkses('ar_faktur')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Status Opname & Fisik Gudang</h3>
                        <p class="text-[10px] text-slate-400">Rekonsiliasi stok fisik dan sistem</p>
                    </div>
                    <a href="{{ route('operasional.gudang.opname') }}" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="p-3.5 space-y-2 text-xs">
                    <div class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">Gudang Utama Cakung</div>
                            <div class="text-[10px] text-slate-400">Semen Gresik 50kg (PCC)</div>
                        </div>
                        <div class="text-right">
                            <div class="font-mono font-bold text-slate-900 dark:text-slate-100">6.400 Zak</div>
                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-700 font-semibold">Sesuai Fisik</span>
                        </div>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100">Gudang Transit Marunda</div>
                            <div class="text-[10px] text-slate-400">Semen Padang 40kg</div>
                        </div>
                        <div class="text-right">
                            <div class="font-mono font-bold text-slate-900 dark:text-slate-100">4.150 Zak</div>
                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-700 font-semibold">Sesuai Fisik</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Feed 4: SPK Perbaikan Bengkel Terkini (Hanya tampil jika ada akses Bengkel) -->
        <template x-if="bisaAkses('bengkel_perbaikan') && !bisaAkses('kirim_sj')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">SPK Perbaikan Armada (Bengkel)</h3>
                        <p class="text-[10px] text-slate-400">Status servis berkala dan perbaikan armada truk</p>
                    </div>
                    <a href="{{ route('operasional.bengkel.perbaikan') }}" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="p-3.5 space-y-2 text-xs">
                    <div class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100 font-mono text-red-600">SPK-20260901-01</div>
                            <div class="text-[10px] text-slate-500">B 9283 TDF · Ganti Kanvas Rem & Oli Gardan</div>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-900/30 text-amber-700 font-semibold">Proses Servis</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                        <div>
                            <div class="font-semibold text-slate-900 dark:text-slate-100 font-mono text-emerald-600">SPK-20260830-08</div>
                            <div class="text-[10px] text-slate-500">B 8411 UQ · Tune-up Rutin 10.000 KM</div>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 font-semibold">Selesai / Lulus Uji</span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Feed 5: Log Keamanan & Akun (Super Admin) -->
        <template x-if="bisaAkses('admin_akun') && !bisaAkses('ar_faktur')">
            <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl overflow-hidden shadow-sm">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Aktivitas Sesi & Keamanan</h3>
                        <p class="text-[10px] text-slate-400">Monitoring login pengguna dan otorisasi wewenang</p>
                    </div>
                    <a href="{{ route('superadmin.kelola_akun') }}" class="text-[11px] font-semibold text-purple-600 dark:text-purple-400 hover:underline">
                        Kelola Akun &rarr;
                    </a>
                </div>
                <div class="p-3.5 space-y-2 text-xs">
                    <div class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">Budi Santoso (SPV Keuangan)</div>
                                <div class="text-[10px] text-slate-400">Login via Web Portal · 10 menit lalu</div>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono text-slate-500">192.168.1.10</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <div>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">Siti Rahmawati (SPV Operasional)</div>
                                <div class="text-[10px] text-slate-400">Login via Web Portal · 25 menit lalu</div>
                            </div>
                        </div>
                        <span class="text-[10px] font-mono text-slate-500">192.168.1.15</span>
                    </div>
                </div>
            </div>
        </template>

    </div>

</div>
@endsection
