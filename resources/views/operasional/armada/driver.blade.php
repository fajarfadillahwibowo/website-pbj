@extends('layouts.app')

@section('judul', 'Data Karyawan Driver')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Pengawas Driver & Operasional · Dev 2</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Data Driver Supir Armada</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar tenaga kerja pengemudi truk logistik yang tersaring otomatis dari master data karyawan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Driver Baru
            </button>
        </div>
    </div>

    <!-- Tabel Data Driver -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <form method="GET" action="{{ route('operasional.armada.driver') }}" class="relative w-full sm:w-72">
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nama driver / kode / no hp..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            <span class="text-xs text-slate-400 font-mono">Sumber: data_karyawan (WHERE kategori_karyawan = 'driver')</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Driver</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Pengemudi</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. KTP / SIM</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Handphone</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Alamat Domisili</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status Karyawan</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarDriver as $driver)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $driver->kode_karyawan }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $driver->nama_karyawan }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">Tgl Masuk: {{ $driver->tanggal_mulai_kerja ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $driver->no_identitas }}</td>
                            <td class="px-4 py-3 font-mono">{{ $driver->no_hp }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 truncate max-w-xs">{{ $driver->alamat }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase font-mono {{ $driver->status_karyawan === 'aktif' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    {{ $driver->status_karyawan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</button>
                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                <button class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Lihat SIM/KTP</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada data driver ditemukan di tabel data_karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
