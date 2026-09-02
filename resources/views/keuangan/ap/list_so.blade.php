@extends('layouts.app')

@section('judul', 'Monitoring List SO Semen - PT Pura Balkom Jaya')

@section('konten')
<div class="space-y-6">

    <!-- 1. Header Halaman -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 font-mono">
                    Monitoring Kuota Pabrik
                </span>
                <span class="text-xs text-slate-400 font-mono">Sales Order & Realisasi DO</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Monitoring List SO (Sales Order)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Pantau realisasi pengambilan kuota semen per nomor SO/LO pabrik SIG secara real-time untuk operasional pengiriman.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('keuangan.ap.pembelian_so') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-95 rounded-xl transition-all shadow-md shadow-indigo-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Input Penebusan SO</span>
            </a>
        </div>
    </div>

    <!-- 2. Ringkasan Kartu KPI -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total SO Terdaftar -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Sales Order</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalSO }} <span class="text-xs font-normal text-slate-400 font-sans">SO</span></div>
            </div>
        </div>

        <!-- Total Volume Kuantitas SO -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Volume Penebusan</div>
                <div class="text-xl font-bold text-blue-600 dark:text-blue-400 font-mono mt-0.5">{{ number_format($totalKuantitasSO, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">Zak</span></div>
            </div>
        </div>

        <!-- Realisasi Terambil -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Realisasi Pengambilan</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ number_format($totalTerambil, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">Zak</span></div>
            </div>
        </div>

        <!-- Sisa Kuota Pengambilan -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Sisa Kuota Belum Ambil</div>
                <div class="text-xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">{{ number_format($totalSisaKuota, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">Zak</span></div>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Tabel Monitoring List SO -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.ap.list_so') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari nomor SO, nomor LO, gudang..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Status -->
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">-- Semua Status SO --</option>
                    <option value="disetujui" {{ ($filterStatus ?? '') == 'disetujui' ? 'selected' : '' }}>Disetujui / Aktif</option>
                    <option value="diproses" {{ ($filterStatus ?? '') == 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ ($filterStatus ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>

                <!-- Filter Gudang -->
                <select name="gudang" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <option value="">-- Semua Gudang Penebusan --</option>
                    @foreach($daftarGudang as $gdg)
                        <option value="{{ $gdg->kode_gudang }}" {{ ($filterGudang ?? '') == $gdg->kode_gudang ? 'selected' : '' }}>
                            {{ $gdg->nama_gudang }} ({{ $gdg->kode_gudang }})
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Filter
                </button>
                @if(!empty($kataKunci) || !empty($filterStatus) || !empty($filterGudang))
                    <a href="{{ route('keuangan.ap.list_so') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono shrink-0">
                Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarSO->count() }}</span> data SO
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">No. SO / LO</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Tanggal & Gudang</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Pengiriman</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Kuota SO</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Terambil</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Sisa Kuota</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider w-40">Progres Realisasi</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarSO as $so)
                        @php
                            $kuota = $so->jumlah_zak ?? 0;
                            $ambil = $so->qty_pengambilan ?? 0;
                            $sisa = max(0, $kuota - $ambil);
                            $persen = $kuota > 0 ? min(100, round(($ambil / $kuota) * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $so->nomor_so }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">LO: {{ $so->nomor_lo ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $so->gudang->nama_gudang ?? $so->kode_gudang }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ date('d/m/Y', strtotime($so->tanggal_so)) }} · Plant: {{ $so->gudang->plant ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $so->jenis_pengiriman === 'FOT' ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-400' }}">
                                    {{ $so->jenis_pengiriman ?? 'FRC' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-slate-100">
                                {{ number_format($kuota, 0, ',', '.') }} Zak
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($ambil, 0, ',', '.') }} Zak
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-amber-600 dark:text-amber-400">
                                {{ number_format($sisa, 0, ',', '.') }} Zak
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full {{ $persen >= 100 ? 'bg-emerald-500' : ($persen > 50 ? 'bg-blue-500' : 'bg-amber-500') }} transition-all" style="width: {{ $persen }}%;"></div>
                                    </div>
                                    <span class="text-[10px] font-mono font-bold text-slate-600 dark:text-slate-400 w-8 text-right">{{ $persen }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($sisa == 0 && $kuota > 0)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400">
                                        Selesai
                                    </span>
                                @elseif($ambil > 0)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-400">
                                        Proses
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-400">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data Sales Order yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
