@extends('layouts.app')

@section('judul', 'Master Data Karyawan & Seluruh Pegawai')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul Master Karyawan -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Master Data · Keuangan & SDM</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Master Seluruh Data Karyawan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Database terpadu seluruh personil perusahaan: staf kantor keuangan/operasional, pengemudi armada (driver), staf gudang, teknisi, dan pimpinan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Karyawan Baru
            </button>
        </div>
    </div>

    <!-- Filter Tab Kategori Karyawan & Pencarian -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Tab Bar Kategori -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 border-b border-[#E2E8F0] dark:border-[#252837] bg-[#F8FAFC] dark:bg-[#1C1E2A]">
            <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
                <a href="{{ route('master.karyawan.index') }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ empty($filterKategori) ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Semua Karyawan ({{ $totalSemua }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'staf']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'staf' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Staf Kantor ({{ $totalStaf }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'driver']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'driver' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Driver Supir ({{ $totalDriver }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'gudang']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'gudang' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Gudang ({{ $totalGudang }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'teknisi']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'teknisi' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Teknisi Bengkel ({{ $totalTeknisi }})
                </a>
            </div>

            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('master.karyawan.index') }}" class="relative w-full sm:w-64">
                @if($filterKategori)
                    <input type="hidden" name="kategori" value="{{ $filterKategori }}">
                @endif
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nama / kode / no hp..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
        </div>

        <!-- Tabel Data Karyawan Lengkap -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Karyawan</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Lengkap & Kontak</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jabatan Peran</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Identitas (KTP/SIM)</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Alamat Domisili</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarKaryawan as $karyawan)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">
                                {{ $karyawan->kode_karyawan }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $karyawan->nama_karyawan }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $karyawan->no_hp }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">
                                    {{ $karyawan->jabatan->nama_jabatan ?? 'Staf' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($karyawan->kategori_karyawan === 'driver')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 uppercase font-mono">Driver</span>
                                @elseif($karyawan->kategori_karyawan === 'staf')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 uppercase font-mono">Staf</span>
                                @elseif($karyawan->kategori_karyawan === 'gudang')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 uppercase font-mono">Gudang</span>
                                @elseif($karyawan->kategori_karyawan === 'teknisi')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 uppercase font-mono">Teknisi</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 uppercase font-mono">{{ $karyawan->kategori_karyawan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $karyawan->no_identitas }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 truncate max-w-xs">{{ $karyawan->alamat }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase font-mono {{ $karyawan->status_karyawan === 'aktif' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    {{ $karyawan->status_karyawan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</button>
                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                <button class="text-red-600 dark:text-red-400 hover:underline font-medium">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada data karyawan yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
