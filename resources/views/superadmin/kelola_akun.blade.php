@extends('layouts.app')

@section('judul', 'Manajemen Akun Staf & Hak Akses')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-purple-600 dark:text-purple-400 font-semibold font-mono uppercase tracking-wider mb-1">Admin Sistem · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Daftar Akun Pengguna & Hak Akses</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola akun staf, reset kata sandi, dan status aktifasi pengguna.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Akun Staf
            </button>
        </div>
    </div>

    <!-- Tabel Data Akun -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari username / nama staf..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel Database: account & super_account</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Username</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Pegawai</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-purple-600 dark:text-purple-400">superadmin</td>
                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">Administrator Kontrol Akun</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 font-mono">Super Admin</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-slate-400">Utama</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-slate-700 dark:text-slate-300">spv_keuangan</td>
                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">Siti Rahmawati</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-mono">SPV Keuangan</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Reset Sandi</button>
                            <span class="text-slate-300 dark:text-slate-700">|</span>
                            <button class="text-red-600 dark:text-red-400 hover:underline font-medium">Nonaktifkan</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-slate-700 dark:text-slate-300">staff_ar</td>
                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">Dewi Anggraeni</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-mono">Staff AR</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Reset Sandi</button>
                            <span class="text-slate-300 dark:text-slate-700">|</span>
                            <button class="text-red-600 dark:text-red-400 hover:underline font-medium">Nonaktifkan</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-slate-700 dark:text-slate-300">dispatcher</td>
                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">Bambang Wijaya</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 font-mono">Dispatcher</span></td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Reset Sandi</button>
                            <span class="text-slate-300 dark:text-slate-700">|</span>
                            <button class="text-red-600 dark:text-red-400 hover:underline font-medium">Nonaktifkan</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
