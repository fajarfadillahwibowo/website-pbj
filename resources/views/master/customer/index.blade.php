@extends('layouts.app')

@section('judul', 'Master Data Customer & Toko Bangunan')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold font-mono uppercase tracking-wider mb-1">Master Data · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Data Customer & Toko Bangunan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar mitra toko, plafon limit piutang, dan saldo deposit berjalan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Customer
            </button>
        </div>
    </div>

    <!-- Tabel Data Customer -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari kode / nama toko..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: data_customer</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Toko & Pemilik</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Telepon / Alamat</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Saldo Deposit</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Limit Piutang</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">CUST-001</td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900 dark:text-slate-100">TB Maju Jaya Sentosa</div>
                            <div class="text-[11px] text-slate-400">H. Sulaeman</div>
                        </td>
                        <td class="px-4 py-3">
                            <div>0812-3456-7890</div>
                            <div class="text-[11px] text-slate-400 truncate max-w-xs">Jl. Raya Industri No. 45, Cikarang</div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-emerald-600 dark:text-emerald-400">Rp 15.000.000</td>
                        <td class="px-4 py-3 text-right font-mono tabular-nums text-slate-700 dark:text-slate-300">Rp 50.000.000</td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</button>
                            <span class="text-slate-300 dark:text-slate-700">|</span>
                            <button class="text-red-600 dark:text-red-400 hover:underline font-medium">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
