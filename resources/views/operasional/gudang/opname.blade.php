@extends('layouts.app')

@section('judul', 'Stok Opname Fisik Gudang Semen - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaStockOpname()" x-init="initOpname()" class="space-y-6">

    @php
        $opsiStatusOpname = [
            ['nilai' => 'dikonfirmasi_spv', 'label' => 'Dikonfirmasi SPV (Langsung Sinkron)'],
            ['nilai' => 'draft', 'label' => 'Draft (Simpan Sementara)'],
        ];
        $opsiStatusFilterOpname = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Status Konfirmasi']
        ], $opsiStatusOpname);

        $opsiGudang = ($daftarGudang ?? collect())->map(fn($g) => [
            'nilai' => $g->kode_gudang,
            'label' => $g->nama_gudang . ' (' . $g->kode_gudang . ')',
            'sub'   => 'Stok: ' . number_format($g->stok_tersedia, 0, ',', '.') . ' Zak'
        ])->toArray();
    @endphp

    <!-- 1. Header Banner Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-500/20 font-mono">
                    SPV Gudang · Audit Persediaan
                </span>
                <span class="text-xs text-slate-400 font-mono">Stock Opname Fisik</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Stok Opname Fisik Gudang Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Audit berkala stok fisik riil di gudang dibandingkan catatan sistem logistik, perhitungan selisih otomatis, dan persetujuan penyesuaian oleh SPV Gudang.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="bukaModalTambah()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 active:scale-95 rounded-xl transition-all shadow-md shadow-teal-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Catat Opname Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Flash Messages -->
    @if(session('sukses'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('sukses') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:text-rose-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm space-y-1">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Terdapat kesalahan validasi formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. 4 Kartu KPI Ringkasan Opname Fisik -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Opname -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Audit Opname</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalOpname }} <span class="text-xs font-normal text-slate-400 font-sans">Audit</span></div>
            </div>
        </div>

        <!-- Dikonfirmasi SPV -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Disetujui SPV</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $opnameDikonfirmasi }} <span class="text-xs font-normal text-slate-400 font-sans">Tersinkron</span></div>
            </div>
        </div>

        <!-- Draft / Menunggu -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Draft / Belum Konfirmasi</div>
                <div class="text-xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">{{ $opnameDraft }} <span class="text-xs font-normal text-slate-400 font-sans">Draft</span></div>
            </div>
        </div>

        <!-- Total Selisih Fisik -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl {{ $totalSelisihFisik >= 0 ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Selisih Bersih</div>
                <div class="text-xl font-bold font-mono mt-0.5 {{ $totalSelisihFisik >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ ($totalSelisihFisik > 0 ? '+' : '') . number_format($totalSelisihFisik, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400 font-sans">Zak</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Data Stok Opname & Filter -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarOpname ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('operasional.gudang.opname') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari no opname, kode gudang, nama gudang, petugas, catatan..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Filter Status Dropdown Kustom -->
                <div class="w-full sm:w-60">
                    <x-dropdown-kustom 
                        nama="status"
                        placeholder="-- Status Konfirmasi --"
                        :opsi="$opsiStatusFilterOpname"
                        :nilaiAwal="$statusFilter ?? 'semua'"
                        :submitOnChange="true"
                        warnaFokus="teal"
                    />
                </div>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Cari
                </button>

                @if(!empty($kataKunci) || ($statusFilter !== 'semua' && !empty($statusFilter)))
                    <a href="{{ route('operasional.gudang.opname') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono">
                Menampilkan <strong class="text-slate-700 dark:text-slate-300">{{ count($daftarOpname) }}</strong> Catatan Opname
            </div>
        </div>

        <!-- Tabel Opname -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. Opname & Tanggal</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Fasilitas Gudang & Komoditas</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Stok Sistem vs Fisik</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Selisih Stok</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Petugas & Catatan</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarOpname as $opn)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            
                            <!-- No Opname & Tanggal -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="font-mono font-bold text-teal-600 dark:text-teal-400 text-sm">
                                    {{ $opn->nomor_opname }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>{{ $opn->tanggal_format }}</span>
                                </div>
                            </td>

                            <!-- Gudang -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-xs">
                                    {{ $opn->gudang->nama_gudang ?? $opn->kode_gudang }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $opn->gudang->barang->nama_barang ?? 'Semen PCC' }} · <span class="font-mono text-slate-400">{{ $opn->gudang->plant ?? 'Plant Cikarang' }}</span>
                                </div>
                            </td>

                            <!-- Sistem vs Fisik -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2 font-mono text-xs">
                                    <span class="text-slate-500" title="Stok Sistem">{{ number_format($opn->stok_sistem, 0, ',', '.') }}</span>
                                    <span class="text-slate-300 dark:text-slate-700">➔</span>
                                    <span class="font-bold text-slate-900 dark:text-slate-100" title="Stok Fisik Riil">{{ number_format($opn->stok_fisik, 0, ',', '.') }} Zak</span>
                                </div>
                            </td>

                            <!-- Selisih -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @php $selisihBadge = $opn->selisih_badge; @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono border {{ $selisihBadge['bg'] }}">
                                    {{ $selisihBadge['label'] }}
                                </span>
                            </td>

                            <!-- Petugas & Catatan -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-900 dark:text-slate-100 text-xs">
                                    {{ $opn->petugas_opname }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs mt-0.5">
                                    {{ $opn->keterangan_selisih ?: 'Perhitungan fisik gudang' }}
                                </div>
                            </td>

                            <!-- Status & Aksi -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="flex flex-col items-center gap-1.5">
                                    @php $badge = $opn->status_badge; @endphp
                                    @if($opn->status_konfirmasi === 'draft')
                                        <form action="{{ route('operasional.gudang.opname.konfirmasi', $opn->id_opname) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('Konfirmasi opname dan sinkronkan stok fisik gudang sekarang?')"
                                                    class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 hover:bg-emerald-600 hover:text-white transition-colors"
                                                    title="Klik untuk setujui & sinkronkan stok fisik">
                                                Konfirmasi SPV ✔
                                            </button>
                                        </form>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono border {{ $badge['bg'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    @endif

                                    <div class="inline-flex items-center gap-1">
                                        <!-- Edit -->
                                        <button @click="bukaModalEdit('{{ $opn->id_opname }}')"
                                                class="p-1 rounded text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                title="Ubah Data Opname">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Hapus -->
                                        <button @click="bukaModalHapus('{{ $opn->id_opname }}', '{{ $opn->nomor_opname }}')"
                                                class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                title="Hapus Data Opname">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Riwayat Diedit Real-Time -->
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center justify-center gap-1 font-mono cursor-help"
                                     title="Dibuat pada: {{ $opn->dibuat_pada ? \Carbon\Carbon::parse($opn->dibuat_pada)->format('d/m/Y H:i:s') : '-' }}">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $opn->terakhir_diedit_relatif }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada catatan stok opname</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Catat opname fisik baru dengan menekan tombol di kanan atas.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-paginasi-tabel :totalData="count($daftarOpname ?? [])" />
    </div>

    <!-- Modal Tambah Catatan Opname -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Catat Stock Opname Fisik Gudang</h3>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form action="{{ route('operasional.gudang.opname.simpan') }}" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Nomor Opname <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-teal-600 dark:text-teal-400 font-semibold px-1.5 py-0.5 bg-teal-50 dark:bg-teal-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="nomor_opname" x-model="formTambah.nomor_opname" required placeholder="OPN-001"
                               class="w-full px-3 py-2 rounded-xl bg-teal-50/50 dark:bg-[#1C1E2A] border border-teal-200 dark:border-teal-900/50 text-teal-900 dark:text-teal-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Fasilitas Gudang <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_gudang"
                            placeholder="-- Pilih Gudang --"
                            :opsi="$opsiGudang"
                            :wajib="true"
                            warnaFokus="teal"
                            modelBind="formTambah.kode_gudang"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Opname Fisik <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_opname" 
                            modelBind="formTambah.tanggal_opname" 
                            placeholder="Pilih Tanggal Opname"
                            :wajib="true"
                            warnaFokus="teal"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Petugas / Auditor Opname <span class="text-rose-500">*</span></label>
                        <input type="text" name="petugas_opname" x-model="formTambah.petugas_opname" required placeholder="Nama auditor / SPV Gudang"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Tercatat Sistem (Zak) <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_sistem" x-model.number="formTambah.stok_sistem" min="0" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30 font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Fisik Hasil Hitung (Zak) <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_fisik" x-model.number="formTambah.stok_fisik" min="0" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-teal-600 dark:text-teal-400 font-bold focus:outline-none focus:ring-2 focus:ring-teal-500/30 font-mono">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                    <div>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Hasil Kalkulasi Selisih:</span>
                        <div class="text-[10px] text-slate-400">(Stok Fisik - Stok Sistem)</div>
                    </div>
                    <div>
                        <span class="font-mono font-bold px-2.5 py-1 rounded-lg inline-block text-xs"
                              :class="selisihHitungTambah > 0 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300' : (selisihHitungTambah < 0 ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/50 dark:text-rose-300' : 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-300')"
                              x-text="(selisihHitungTambah > 0 ? '+' : '') + selisihHitungTambah + ' Zak'"></span>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Konfirmasi Opname <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="status_konfirmasi"
                        placeholder="-- Pilih Status --"
                        :opsi="$opsiStatusOpname"
                        :wajib="true"
                        warnaFokus="teal"
                        modelBind="formTambah.status_konfirmasi"
                    />
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Alasan Selisih Fisik <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="keterangan_selisih" x-model="formTambah.keterangan_selisih" rows="2" placeholder="Contoh: Ditemukan 20 zak semen robek/rusak saat bongkar muat..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambahTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-all shadow-sm">Simpan Catatan Opname</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Catatan Opname -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Stock Opname: <span class="font-mono text-teal-600" x-text="formEdit.nomor_opname"></span></h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <form :action="'{{ url('operasional/gudang/opname') }}/' + formEdit.id_opname" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Opname (Terkunci)</label>
                        <input type="text" :value="formEdit.nomor_opname" disabled
                               class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Fasilitas Gudang <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_gudang"
                            placeholder="-- Pilih Gudang --"
                            :opsi="$opsiGudang"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_gudang"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Opname <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_opname" 
                            modelBind="formEdit.tanggal_opname" 
                            placeholder="Pilih Tanggal Opname"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Petugas Opname <span class="text-rose-500">*</span></label>
                        <input type="text" name="petugas_opname" x-model="formEdit.petugas_opname" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Sistem (Zak) <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_sistem" x-model.number="formEdit.stok_sistem" min="0" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Stok Fisik Riil (Zak) <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok_fisik" x-model.number="formEdit.stok_fisik" min="0" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-amber-600 dark:text-amber-400 font-bold focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Konfirmasi SPV <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="status_konfirmasi"
                        placeholder="-- Pilih Status --"
                        :opsi="$opsiStatusOpname"
                        :wajib="true"
                        warnaFokus="amber"
                        modelBind="formEdit.status_konfirmasi"
                    />
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan / Alasan Selisih Fisik <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="keterangan_selisih" x-model="formEdit.keterangan_selisih" rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="modalEditTerbuka = false" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 7. MODAL KONFIRMASI HAPUS OPNAME -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Catatan Opname?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus data audit opname <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nomor_opname"></strong>?
            </p>

            <form :action="'{{ url('operasional/gudang/opname') }}/' + hapusData.id" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika Opname Gudang -->
<script>
    function kelolaStockOpname() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodeOpn: 'Mode: Daur Ulang Slot Kosong',

            formTambah: {
                nomor_opname: '',
                kode_gudang: '{{ $daftarGudang->first()->kode_gudang ?? "" }}',
                tanggal_opname: new Date().toISOString().slice(0, 10),
                stok_sistem: {{ $daftarGudang->first()->stok_tersedia ?? 0 }},
                stok_fisik: {{ $daftarGudang->first()->stok_tersedia ?? 0 }},
                status_konfirmasi: 'dikonfirmasi_spv',
                petugas_opname: 'Ahmad Fauzi (SPV Gudang)',
                keterangan_selisih: ''
            },

            formEdit: {
                id_opname: '',
                nomor_opname: '',
                kode_gudang: '',
                tanggal_opname: '',
                stok_sistem: 0,
                stok_fisik: 0,
                status_konfirmasi: 'draft',
                petugas_opname: '',
                keterangan_selisih: ''
            },

            hapusData: { id: '', nomor_opname: '' },

            get selisihHitungTambah() {
                return (parseInt(this.formTambah.stok_fisik) || 0) - (parseInt(this.formTambah.stok_sistem) || 0);
            },

            initOpname() {},

            ubahGudangPilihan(kodeGudang) {
                const selectEl = document.querySelector('select[name="kode_gudang"]');
                const selectedOpt = selectEl.options[selectEl.selectedIndex];
                const stok = selectedOpt.getAttribute('data-stok');
                if (stok !== null) {
                    this.formTambah.stok_sistem = parseInt(stok) || 0;
                    this.formTambah.stok_fisik = parseInt(stok) || 0;
                }
            },

            bukaModalTambah() {
                this.buatNomorOpname('gap');
                this.modalTambahTerbuka = true;
            },

            async buatNomorOpname(mode = 'gap') {
                try {
                    const res = await fetch(`{{ route("operasional.gudang.opname.buat_kode") }}?mode=${mode}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.formTambah.nomor_opname = data.kode_otomatis;
                        this.keteranganKodeOpn = data.keterangan || (mode === 'acak' ? 'Format Tanggal & Acak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat nomor opname:', e);
                }
            },

            async bukaModalEdit(id) {
                try {
                    const res = await fetch(`{{ url('operasional/gudang/opname') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        const d = data.data;
                        this.formEdit = {
                            id_opname: d.id_opname,
                            nomor_opname: d.nomor_opname,
                            kode_gudang: d.kode_gudang,
                            tanggal_opname: d.tanggal_opname ? d.tanggal_opname.slice(0, 10) : '',
                            stok_sistem: d.stok_sistem,
                            stok_fisik: d.stok_fisik,
                            status_konfirmasi: d.status_konfirmasi,
                            petugas_opname: d.petugas_opname,
                            keterangan_selisih: d.keterangan_selisih || ''
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data opname.');
                }
            },

            bukaModalHapus(id, nomorOpname) {
                this.hapusData = { id: id, nomor_opname: nomorOpname };
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
