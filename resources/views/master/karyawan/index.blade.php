@extends('layouts.app')

@section('judul', 'Master Data Karyawan & Driver')

@section('konten')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Master Data · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Data Pegawai & Driver Supir</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar staf kantor, supir armada logistik, teknisi bengkel, dan pengawas gudang.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Karyawan
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Karyawan</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Karyawan</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jabatan / Kategori</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Kontak</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">KRY-001</td>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">Siti Rahmawati</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">SPV Keuangan</span></td>
                        <td class="px-4 py-3">0812-9876-0001</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
                        <td class="px-4 py-3 text-center space-x-2">
                            <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</button>
                            <span class="text-slate-300 dark:text-slate-700">|</span>
                            <button class="text-red-600 dark:text-red-400 hover:underline font-medium">Hapus</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                        <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">KRY-005</td>
                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">Bambang Wijaya</td>
                        <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400">Dispatcher</span></td>
                        <td class="px-4 py-3">0812-9876-0005</td>
                        <td class="px-4 py-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">Aktif</span></td>
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
