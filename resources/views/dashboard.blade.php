@extends('layouts.app')

@section('judul', 'Dashboard')

@section('konten')
<div class="max-w-full space-y-5">

  <!-- ================================================================
       1. HEADER HALAMAN + TOMBOL AKSI CEPAT
  ================================================================ -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
        Selamat datang, <span x-text="labelJabatan">SPV Keuangan</span>
      </h1>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
        Berikut ringkasan ruang kerja Anda — {{ date('l, d F Y') }}.
      </p>
    </div>

    <!-- Tombol Aksi Cepat Sesuai Jabatan -->
    <div class="flex items-center gap-2">
      <template x-if="['SPV_KEUANGAN', 'STAFF_AR'].includes(jabatanAktif)">
        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Faktur Baru
        </button>
      </template>
      <template x-if="['DISPATCHER', 'SPV_OPERASIONAL'].includes(jabatanAktif)">
        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          Surat Jalan Baru
        </button>
      </template>
      <template x-if="jabatanAktif === 'SUPER_ADMIN'">
        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          Tambah Akun
        </button>
      </template>
      <template x-if="jabatanAktif === 'SPV_GUDANG'">
        <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition-colors shadow-sm">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
          Mulai Opname
        </button>
      </template>

      <button class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-50 dark:hover:bg-[#252837] rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Ekspor
      </button>
    </div>
  </div>

  <!-- ================================================================
       2. GRID KARTU KPI — Dinamis per Jabatan
  ================================================================ -->

  <!-- KPI: SPV Keuangan & Direktur -->
  <template x-if="['SPV_KEUANGAN', 'DIREKTUR_MANAGER'].includes(jabatanAktif)">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Penjualan -->
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Penjualan Bulan Ini</span>
          <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          </div>
        </div>
        <div class="text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">Rp 842.500.000</div>
        <div class="mt-1.5 flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
          <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
          +12.4% vs bulan lalu
        </div>
      </div>
      <!-- Piutang -->
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Piutang Belum Lunas</span>
          <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <div class="text-2xl font-bold font-mono tabular-nums text-amber-600 dark:text-amber-400">Rp 128.400.000</div>
        <div class="mt-1.5 text-xs text-slate-500">14 faktur · 3 jatuh tempo</div>
      </div>
      <!-- Laba Bersih -->
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Laba Bersih Berjalan</span>
          <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
        <div class="text-2xl font-bold font-mono tabular-nums text-emerald-600 dark:text-emerald-400">Rp 194.200.000</div>
        <div class="mt-1.5 text-xs text-slate-500">Margin 23.0%</div>
      </div>
      <!-- Saldo Kas -->
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Kas & Bank</span>
          <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-500/10 flex items-center justify-center">
            <svg class="w-4 h-4 text-slate-600 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          </div>
        </div>
        <div class="text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">Rp 415.800.000</div>
        <div class="mt-1.5 text-xs text-slate-500">3 rekening operasional</div>
      </div>
    </div>
  </template>

  <!-- KPI: Staff AR -->
  <template x-if="jabatanAktif === 'STAFF_AR'">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Tagihan AR</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-amber-600 dark:text-amber-400">Rp 128.400.000</div>
        <div class="mt-1 text-xs text-slate-500">14 Faktur beredar</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Jatuh Tempo</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-red-600 dark:text-red-400">Rp 32.150.000</div>
        <div class="mt-1 text-xs text-red-500 font-medium">3 toko perlu ditagih</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Deposit</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-emerald-600 dark:text-emerald-400">Rp 56.000.000</div>
        <div class="mt-1 text-xs text-slate-500">Uang muka aktif</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelunasan Hari Ini</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-blue-600 dark:text-blue-400">Rp 18.500.000</div>
        <div class="mt-1 text-xs text-slate-500">5 transaksi lunas</div>
      </div>
    </div>
  </template>

  <!-- KPI: Dispatcher & SPV Operasional -->
  <template x-if="['DISPATCHER', 'SPV_OPERASIONAL'].includes(jabatanAktif)">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Armada Dalam Perjalanan</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-blue-600 dark:text-blue-400">8 Truk</div>
        <div class="mt-1 text-xs text-slate-500">Dari total 12 unit</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Surat Jalan Hari Ini</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">12 Rilis</div>
        <div class="mt-1 text-xs text-slate-500">4.800 Zak dikirim</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Supir Standby</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-emerald-600 dark:text-emerald-400">6 Driver</div>
        <div class="mt-1 text-xs text-slate-500">Siap penugasan rute</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Armada Servis</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-red-600 dark:text-red-400">2 Unit</div>
        <div class="mt-1 text-xs text-slate-500">SPK dalam perbaikan</div>
      </div>
    </div>
  </template>

  <!-- KPI: SPV Gudang -->
  <template x-if="jabatanAktif === 'SPV_GUDANG'">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Semen Zak</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">14.250</div>
        <div class="mt-1 text-xs text-slate-500">Semua gudang lokal</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Semen Curah (Ton)</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">180 Ton</div>
        <div class="mt-1 text-xs text-slate-500">Kapasitas silo aman</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Opname</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-emerald-600 dark:text-emerald-400">Cocok</div>
        <div class="mt-1 text-xs text-slate-500">Terakhir dicek kemarin</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gudang Aktif</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">4</div>
        <div class="mt-1 text-xs text-slate-500">Lokasi Jakarta & Jabar</div>
      </div>
    </div>
  </template>

  <!-- KPI: Super Admin -->
  <template x-if="jabatanAktif === 'SUPER_ADMIN'">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Akun Staf</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">18</div>
        <div class="mt-1 text-xs text-slate-500">Semua unit kerja</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Akun Aktif</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-emerald-600 dark:text-emerald-400">16</div>
        <div class="mt-1 text-xs text-slate-500">2 akun dinonaktifkan</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Master Jabatan</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-violet-600 dark:text-violet-400">9</div>
        <div class="mt-1 text-xs text-slate-500">Matriks RBAC granular</div>
      </div>
      <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl p-5">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Login Hari Ini</span>
        <div class="mt-3 text-2xl font-bold font-mono tabular-nums text-slate-900 dark:text-slate-100">42</div>
        <div class="mt-1 text-xs text-emerald-500 font-medium">0 upaya login gagal</div>
      </div>
    </div>
  </template>

  <!-- ================================================================
       3. TABEL TRANSAKSI UTAMA
  ================================================================ -->
  <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-xl overflow-hidden">

    <!-- Header Tabel -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
      <div>
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">
          <span x-show="['SPV_KEUANGAN', 'STAFF_AR', 'DIREKTUR_MANAGER'].includes(jabatanAktif)">Faktur Penjualan Terbaru</span>
          <span x-show="['DISPATCHER', 'SPV_OPERASIONAL'].includes(jabatanAktif)">Status Surat Jalan & Pengiriman</span>
          <span x-show="jabatanAktif === 'SPV_GUDANG'">Status Stok Gudang Terkini</span>
          <span x-show="jabatanAktif === 'SUPER_ADMIN'">Daftar Akun Pengguna Sistem</span>
          <span x-show="jabatanAktif === 'STAFF_AP'">Pengeluaran & Pembelian SO Terbaru</span>
          <span x-show="['PENGAWAS_KENDARAAN', 'PENGAWAS_DRIVER'].includes(jabatanAktif)">Aktivitas Operasional Kendaraan</span>
        </h3>
        <p class="text-[11px] text-slate-400 mt-0.5">Data real-time sesuai akses jabatan Anda</p>
      </div>
      <div class="flex items-center gap-2">
        <div class="relative">
          <input type="text" placeholder="Cari transaksi..."
                 class="w-44 pl-7 pr-3 py-1.5 text-xs rounded-lg bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
          <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <button class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-300 bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 dark:hover:bg-[#252837] rounded-lg transition-colors">
          <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
          Filter
        </button>
      </div>
    </div>

    <!-- Tabel dengan Sticky Header -->
    <div class="overflow-x-auto">
      <table class="w-full text-sm">

        <!-- Header Kolom Finansial (SPV Keuangan / AR / Direktur) -->
        <thead x-show="['SPV_KEUANGAN', 'STAFF_AR', 'DIREKTUR_MANAGER'].includes(jabatanAktif)"
               class="sticky top-0 z-10 bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837]">
          <tr>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">No. Faktur</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tgl</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Metode</th>
            <th class="px-4 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Netto</th>
            <th class="px-4 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sisa Piutang</th>
            <th class="px-4 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="px-4 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>

        <!-- Header Kolom Pengiriman (Dispatcher / SPV Operasional) -->
        <thead x-show="['DISPATCHER', 'SPV_OPERASIONAL'].includes(jabatanAktif)"
               class="sticky top-0 z-10 bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837]">
          <tr>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">No. Surat Jalan</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Waktu Kirim</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Plat / Armada</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Driver</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tujuan</th>
            <th class="px-4 py-2.5 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jumlah Zak</th>
            <th class="px-4 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="px-4 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>

        <!-- Header Kolom Super Admin -->
        <thead x-show="jabatanAktif === 'SUPER_ADMIN'"
               class="sticky top-0 z-10 bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837]">
          <tr>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Username</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Nama Pegawai</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Jabatan</th>
            <th class="px-4 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Status</th>
            <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Dibuat</th>
            <th class="px-4 py-2.5 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Aksi Kontrol</th>
          </tr>
        </thead>

        <!-- BODY TABEL -->
        <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">

          <!-- Baris Finansial -->
          <template x-if="['SPV_KEUANGAN', 'STAFF_AR', 'DIREKTUR_MANAGER'].includes(jabatanAktif)">
            <div>
              <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260901-001</td>
                <td class="px-4 py-3 text-xs text-slate-500">01 Sep 2026</td>
                <td class="px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-200">TB Maju Jaya Sentosa</td>
                <td class="px-4 py-3 text-xs text-slate-500">Transfer</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 42.500.000</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums text-slate-400">Rp 0</td>
                <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Lunas</span></td>
                <td class="px-4 py-3 text-center"><button class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Rincian</button></td>
              </tr>
              <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260901-002</td>
                <td class="px-4 py-3 text-xs text-slate-500">01 Sep 2026</td>
                <td class="px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-200">TB Cahaya Bangunan</td>
                <td class="px-4 py-3 text-xs text-slate-500">Kredit / Piutang</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 68.000.000</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums text-amber-600 dark:text-amber-400 font-semibold">Rp 28.000.000</td>
                <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Piutang</span></td>
                <td class="px-4 py-3 text-center"><button class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Rincian</button></td>
              </tr>
              <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-blue-600 dark:text-blue-400 font-medium">INV-20260831-048</td>
                <td class="px-4 py-3 text-xs text-slate-500">31 Agu 2026</td>
                <td class="px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-200">TB Abadi Makmur</td>
                <td class="px-4 py-3 text-xs text-slate-500">Kredit / Piutang</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">Rp 32.150.000</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums text-red-600 dark:text-red-400 font-semibold">Rp 32.150.000</td>
                <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400">Jatuh Tempo</span></td>
                <td class="px-4 py-3 text-center"><button class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Rincian</button></td>
              </tr>
            </div>
          </template>

          <!-- Baris Pengiriman -->
          <template x-if="['DISPATCHER', 'SPV_OPERASIONAL'].includes(jabatanAktif)">
            <div>
              <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260901-088</td>
                <td class="px-4 py-3 text-xs text-slate-500">08:30 WIB</td>
                <td class="px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-200">B 9283 TDF (Tronton)</td>
                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">Ahmad Supriyadi</td>
                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">TB Abadi Makmur, Bekasi</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">400 Zak</td>
                <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">Dalam Perjalanan</span></td>
                <td class="px-4 py-3 text-center"><button class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Lacak</button></td>
              </tr>
              <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-blue-600 dark:text-blue-400 font-medium">SJ-20260901-089</td>
                <td class="px-4 py-3 text-xs text-slate-500">09:15 WIB</td>
                <td class="px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-200">B 8411 UQ (Colt Diesel)</td>
                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">Rian Hidayat</td>
                <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">TB Mitra Sejati, Tangerang</td>
                <td class="px-4 py-3 text-xs text-right font-mono tabular-nums text-slate-900 dark:text-slate-100">280 Zak</td>
                <td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">Muat Barang</span></td>
                <td class="px-4 py-3 text-center"><button class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Lacak</button></td>
              </tr>
            </div>
          </template>

          <!-- Baris Super Admin -->
          <template x-if="jabatanAktif === 'SUPER_ADMIN'">
            <div>
              <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-slate-600 dark:text-slate-400">spv_keuangan</td>
                <td class="px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-200">Siti Rahmawati</td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">SPV Keuangan</span></td>
                <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
                <td class="px-4 py-3 text-xs text-slate-500">15 Jan 2024</td>
                <td class="px-4 py-3 text-center flex items-center justify-center gap-2">
                  <button class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-medium">Reset</button>
                  <span class="text-slate-300 dark:text-slate-700">|</span>
                  <button class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium">Nonaktifkan</button>
                </td>
              </tr>
              <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                <td class="px-4 py-3 text-xs font-mono text-slate-600 dark:text-slate-400">staff_ar</td>
                <td class="px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-200">Dewi Anggraeni</td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Staff AR</span></td>
                <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
                <td class="px-4 py-3 text-xs text-slate-500">01 Feb 2024</td>
                <td class="px-4 py-3 text-center flex items-center justify-center gap-2">
                  <button class="text-xs text-amber-600 dark:text-amber-400 hover:underline font-medium">Reset</button>
                  <span class="text-slate-300 dark:text-slate-700">|</span>
                  <button class="text-xs text-red-600 dark:text-red-400 hover:underline font-medium">Nonaktifkan</button>
                </td>
              </tr>
            </div>
          </template>

          <!-- Empty State -->
          <tr x-show="['PENGAWAS_DRIVER', 'PENGAWAS_KENDARAAN'].includes(jabatanAktif)">
            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-400">
              Pilih jabatan yang sesuai untuk melihat data operasional Anda.
            </td>
          </tr>

        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between px-4 py-3 border-t border-[#EEF0F4] dark:border-[#252837]">
      <span class="text-xs text-slate-400">Menampilkan 1–3 dari 48 data</span>
      <div class="flex items-center gap-1">
        <button class="px-2.5 py-1 text-xs rounded-lg border border-[#E2E8F0] dark:border-[#252837] text-slate-500 hover:bg-slate-50 dark:hover:bg-[#252837] transition-colors disabled:opacity-40" disabled>
          Sebelumnya
        </button>
        <button class="px-2.5 py-1 text-xs rounded-lg border border-[#E2E8F0] dark:border-[#252837] text-slate-500 hover:bg-slate-50 dark:hover:bg-[#252837] transition-colors">
          Selanjutnya
        </button>
      </div>
    </div>

  </div><!-- /tabel -->

</div><!-- /konten -->
@endsection
