@extends('layouts.app')

@section('judul', 'Dashboard Terpadu')

@section('konten')
<div class="max-w-full space-y-6">

  <!-- ================================================================
       1. HEADER RINGKASAN RUANG KERJA
  ================================================================ -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-mono">
          <span x-text="labelJabatan">SPV Keuangan</span>
        </span>
        <span class="text-xs text-slate-400">·</span>
        <span class="text-xs text-slate-500 dark:text-slate-400">{{ date('l, d F Y') }}</span>
      </div>
      <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
        Selamat Datang di Portal Operasional Terpadu
      </h1>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
        Pemantauan real-time arus keuangan, distribusi semen armada, dan stok persediaan gudang.
      </p>
    </div>

    <!-- Pintasan Aksi Utama -->
    <div class="flex items-center gap-2.5 shrink-0">
      <a href="{{ route('keuangan.ar.faktur') }}"
         class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm shadow-blue-600/20">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        <span>Faktur Baru</span>
      </a>
      <a href="{{ route('operasional.pengiriman.surat_jalan') }}"
         class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm shadow-emerald-600/20">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
        <span>Surat Jalan Baru</span>
      </a>
      <a href="{{ route('operasional.gudang.opname') }}"
         class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 dark:hover:bg-[#252837] rounded-xl transition-colors">
        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        <span>Stock Opname</span>
      </a>
    </div>
  </div>

  <!-- ================================================================
       2. GRID KARTU METRIK UTAMA (4 PILAR BISNIS)
  ================================================================ -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    <!-- KPI 1: Penjualan & Pendapatan (Keuangan) -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Penjualan Bulan Ini</span>
        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
          <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
      </div>
      <div class="text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">Rp 842.500.000</div>
      <div class="mt-2 flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
        <span>+12.4% vs bulan lalu</span>
      </div>
    </div>

    <!-- KPI 2: Total Piutang Aktif (AR) -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Piutang Beredar</span>
        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
          <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
      <div class="text-2xl font-bold font-mono tabular-nums text-amber-600 dark:text-amber-400">Rp 128.400.000</div>
      <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
        14 faktur aktif · <span class="text-red-500 font-semibold">3 jatuh tempo</span>
      </div>
    </div>

    <!-- KPI 3: Armada Pengiriman (Logistik) -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Armada Beroperasi</span>
        <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 flex items-center justify-center">
          <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1"/></svg>
        </div>
      </div>
      <div class="text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">8 / 12 Unit</div>
      <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
        4.800 Zak dalam perjalanan hari ini
      </div>
    </div>

    <!-- KPI 4: Stok Semen Tersedia (Gudang) -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Stok Semen</span>
        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
          <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
        </div>
      </div>
      <div class="text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">14.250 Zak</div>
      <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
        4 Gudang aktif · 180 Ton Curah Silo
      </div>
    </div>

  </div>

  <!-- ================================================================
       3. PETA PEMBAGIAN PENGERJAAN DEVELOPER 1 & DEVELOPER 2
  ================================================================ -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    <!-- Card Modul Developer 1 -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm">
      <div class="flex items-center justify-between pb-3.5 border-b border-[#E2E8F0] dark:border-[#252837] mb-4">
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold text-xs font-mono">
            D1
          </div>
          <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ruang Lingkup Developer 1</h2>
            <p class="text-[11px] text-slate-400">Core RBAC, Keuangan (AR/AP), Master Data & Laporan</p>
          </div>
        </div>
        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-semibold">
          feat/dev1-*
        </span>
      </div>

      <div class="grid grid-cols-2 gap-2 text-xs">
        <a href="{{ route('superadmin.kelola_akun') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-purple-300 dark:hover:border-purple-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-purple-600 dark:group-hover:text-purple-400 font-medium">Kelola Akun Staf</span>
          <span class="text-[10px] text-slate-400 font-mono">RBAC</span>
        </a>
        <a href="{{ route('master.customer.index') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-blue-300 dark:hover:border-blue-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 font-medium">Customer & Toko</span>
          <span class="text-[10px] text-slate-400 font-mono">Master</span>
        </a>
        <a href="{{ route('keuangan.ar.faktur') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-emerald-300 dark:hover:border-emerald-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 font-medium">Faktur Penjualan</span>
          <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono">AR</span>
        </a>
        <a href="{{ route('keuangan.ar.piutang') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-amber-300 dark:hover:border-amber-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-amber-600 dark:group-hover:text-amber-400 font-medium">List Piutang</span>
          <span class="text-[10px] text-amber-600 dark:text-amber-400 font-mono">AR</span>
        </a>
        <a href="{{ route('keuangan.ap.pengeluaran') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-rose-300 dark:hover:border-rose-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-rose-600 dark:group-hover:text-rose-400 font-medium">Pengeluaran Kas</span>
          <span class="text-[10px] text-rose-600 dark:text-rose-400 font-mono">AP</span>
        </a>
        <a href="{{ route('keuangan.akuntansi.jurnal') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-teal-300 dark:hover:border-teal-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 font-medium">Jurnal Umum & COA</span>
          <span class="text-[10px] text-teal-600 dark:text-teal-400 font-mono">Akun</span>
        </a>
      </div>
    </div>

    <!-- Card Modul Developer 2 -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-5 shadow-sm">
      <div class="flex items-center justify-between pb-3.5 border-b border-[#E2E8F0] dark:border-[#252837] mb-4">
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xs font-mono">
            D2
          </div>
          <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ruang Lingkup Developer 2</h2>
            <p class="text-[11px] text-slate-400">Gudang, Distribusi, Armada Truk, Driver & Bengkel</p>
          </div>
        </div>
        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold">
          feat/dev2-*
        </span>
      </div>

      <div class="grid grid-cols-2 gap-2 text-xs">
        <a href="{{ route('operasional.pengiriman.surat_jalan') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-sky-300 dark:hover:border-sky-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-sky-600 dark:group-hover:text-sky-400 font-medium">Surat Jalan (SJ)</span>
          <span class="text-[10px] text-sky-600 dark:text-sky-400 font-mono">Kirim</span>
        </a>
        <a href="{{ route('operasional.gudang.stok') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-amber-300 dark:hover:border-amber-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-amber-600 dark:group-hover:text-amber-400 font-medium">Stok & Mutasi Semen</span>
          <span class="text-[10px] text-amber-600 dark:text-amber-400 font-mono">Gudang</span>
        </a>
        <a href="{{ route('operasional.gudang.opname') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-teal-300 dark:hover:border-teal-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-teal-600 dark:group-hover:text-teal-400 font-medium">Modul Stock Opname</span>
          <span class="text-[10px] text-teal-600 dark:text-teal-400 font-mono">Opname</span>
        </a>
        <a href="{{ route('operasional.armada.driver') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-indigo-300 dark:hover:border-indigo-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 font-medium">Data Driver Supir</span>
          <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-mono">Driver</span>
        </a>
        <a href="{{ route('operasional.armada.kendaraan') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-orange-300 dark:hover:border-orange-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-orange-600 dark:group-hover:text-orange-400 font-medium">Armada Truk Kendaraan</span>
          <span class="text-[10px] text-orange-600 dark:text-orange-400 font-mono">Truk</span>
        </a>
        <a href="{{ route('operasional.bengkel.perbaikan') }}" class="p-2.5 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:border-red-300 dark:hover:border-red-600 transition-all flex items-center justify-between group">
          <span class="text-slate-700 dark:text-slate-300 group-hover:text-red-600 dark:group-hover:text-red-400 font-medium">SPK Bengkel & Sparepart</span>
          <span class="text-[10px] text-red-600 dark:text-red-400 font-mono">Servis</span>
        </a>
      </div>
    </div>

  </div>

  <!-- ================================================================
       4. TABEL TRANSAKSI & STATUS DISTRIBUSI TERBARU
  ================================================================ -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    <!-- Tabel 1: Faktur Penjualan Terbaru (Keuangan) -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
        <div>
          <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Faktur Penjualan Terkini</h3>
          <p class="text-[11px] text-slate-400">Monitoring tagihan AR dan status lunas</p>
        </div>
        <a href="{{ route('keuangan.ar.faktur') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
          Lihat Semua &rarr;
        </a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
            <tr>
              <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Faktur</th>
              <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer</th>
              <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Netto</th>
              <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
              <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260901-001</td>
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">TB Maju Jaya Sentosa</td>
              <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 42.500.000</td>
              <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Lunas</span></td>
            </tr>
            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
              <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260901-002</td>
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">TB Cahaya Bangunan</td>
              <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 68.000.000</td>
              <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Piutang</span></td>
            </tr>
            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
              <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260831-048</td>
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">TB Abadi Makmur</td>
              <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 32.150.000</td>
              <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400">Jatuh Tempo</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tabel 2: Surat Jalan & Pengiriman Armada (Logistik) -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
      <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
        <div>
          <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Pengiriman Armada Terkini</h3>
          <p class="text-[11px] text-slate-400">Surat jalan aktif dan penugasan driver</p>
        </div>
        <a href="{{ route('operasional.pengiriman.surat_jalan') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
          Lihat Semua &rarr;
        </a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-xs">
          <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
            <tr>
              <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. SJ</th>
              <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Driver / Truk</th>
              <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Muatan</th>
              <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
              <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260901-088</td>
              <td class="px-4 py-3">
                <div class="font-medium text-slate-900 dark:text-slate-100">Ahmad Supriyadi</div>
                <div class="text-[10px] text-slate-400 font-mono">B 9283 TDF (Tronton)</div>
              </td>
              <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">400 Zak</td>
              <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">Dalam Jalan</span></td>
            </tr>
            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
              <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260901-089</td>
              <td class="px-4 py-3">
                <div class="font-medium text-slate-900 dark:text-slate-100">Rian Hidayat</div>
                <div class="text-[10px] text-slate-400 font-mono">B 8411 UQ (Colt Diesel)</div>
              </td>
              <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">280 Zak</td>
              <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Muat Barang</span></td>
            </tr>
            <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
              <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260831-085</td>
              <td class="px-4 py-3">
                <div class="font-medium text-slate-900 dark:text-slate-100">Rudi Hartono</div>
                <div class="text-[10px] text-slate-400 font-mono">B 9102 VKA (Tronton)</div>
              </td>
              <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">450 Zak</td>
              <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Terkirim</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>
@endsection
