@extends('layouts.app')

@section('judul', 'Stok Opname Gudang')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-teal-600 dark:text-teal-400 font-semibold font-mono uppercase tracking-wider mb-1">Operasional · SPV Gudang</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Stok Opname Gudang</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan data No. SO, Loading Order (LO), Identitas Pemilik, Dokumen KTP, dan Status Aset Gudang.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Data Opname
            </button>
        </div>
    </div>

    <!-- Tabel Data Stok Opname Gudang -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari No. SO / LO / Pemilik..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: opname_gudang</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. SO</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. LO</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Pemilik & Alamat</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. HP</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. KTP & Foto</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status Aset</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-teal-600 dark:text-teal-400">SO-2026-0801</td>
                        <td class="px-4 py-3 font-mono text-slate-700 dark:text-slate-300">LO-CKR-9921</td>
                        <td class="px-4 py-3 font-mono">01/09/2026</td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900 dark:text-slate-100">H. Sulaeman</div>
                            <div class="text-[11px] text-slate-400 truncate max-w-xs">Jl. Raya Industri No. 45, Cikarang, Bekasi</div>
                        </td>
                        <td class="px-4 py-3 font-mono">0812-3456-7890</td>
                        <td class="px-4 py-3">
                            <div class="font-mono">3216012908800001</div>
                            <div class="inline-flex items-center gap-1 text-[10px] text-teal-600 dark:text-teal-400 hover:underline font-medium cursor-pointer mt-0.5">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                foto_ktp_sulaeman.jpeg
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                Tersedia
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-teal-600 dark:text-teal-400 hover:underline font-medium">Edit</button>
                            <span class="text-slate-300 dark:text-slate-700">|</span>
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Detail</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
