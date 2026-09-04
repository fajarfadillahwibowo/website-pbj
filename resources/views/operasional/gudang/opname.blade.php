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
                            </td>

                            <!-- Status & Aksi -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    @php $badge = $opn->status_badge; @endphp
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono border {{ $badge['bg'] }}">
                                        {{ $badge['label'] }}
                                    </span>

                                    <x-menu-aksi-tabel 
                                        kodeSalin="{{ $opn->nomor_opname }}"
                                        labelSalin="Salin No. Opname"
                                        aksiDetail="bukaModalDetail('{{ $opn->id_opname }}')"
                                        labelDetail="Detail Opname"
                                        :aksiCetak="'cetakLangsungOpname(\'' . $opn->id_opname . '\')'"
                                        labelCetak="Cetak BASO"
                                        :aksiEdit="$opn->status_konfirmasi === 'draft' ? 'bukaModalEdit(\'' . $opn->id_opname . '\')' : null"
                                        labelEdit="Ubah Hitung Fisik"
                                        modulIzin="gudang_opname"
                                    >
                                        @if($opn->status_konfirmasi === 'draft')
                                            <!-- Opsi Konfirmasi SPV -->
                                            <form action="{{ route('operasional.gudang.opname.konfirmasi', $opn->id_opname) }}" method="POST" class="block w-full">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" onclick="return confirm('Konfirmasi opname dan sinkronkan stok fisik gudang sekarang?')"
                                                        @click="menuTerbuka = false"
                                                        class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors text-left font-medium">
                                                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Konfirmasi SPV</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Opsi Hapus -->
                                        <div x-show="!apakahReadOnly('gudang_opname')" class="border-t border-slate-100 dark:border-[#252837] pt-1 mt-1">
                                            <button @click.stop="menuTerbuka = false; bukaModalHapus('{{ $opn->id_opname }}', '{{ $opn->nomor_opname }}')"
                                                    type="button"
                                                    class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors text-left font-medium">
                                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span>Hapus Opname</span>
                                            </button>
                                        </div>
                                    </x-menu-aksi-tabel>
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

    <!-- Modal Detail Stock Opname (Lihat Data) -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl my-8">
            
            <!-- Header Modal Detail -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50/50 dark:bg-[#1C1E2A]/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Detail Stock Opname Fisik</h3>
                            <span class="font-mono text-xs font-bold text-teal-600 dark:text-teal-400 bg-teal-50 dark:bg-teal-500/10 px-2 py-0.5 rounded-md border border-teal-200 dark:border-teal-500/20" x-text="detailOpname.nomor_opname"></span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="'Tanggal Audit: ' + (detailOpname.tanggal_format || detailOpname.tanggal_opname || '-')"></p>
                    </div>
                </div>
                <button @click="modalDetailTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>

            <!-- Konten Modal Detail -->
            <div class="p-6 space-y-5 text-xs">
                <!-- Status Konfirmasi Banner -->
                <div class="p-3 rounded-xl flex items-center justify-between border"
                     :class="detailOpname.status_konfirmasi === 'dikonfirmasi_spv' ? 'bg-emerald-50/70 dark:bg-emerald-500/10 border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300' : 'bg-amber-50/70 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-300'">
                    <div class="flex items-center gap-2 font-medium">
                        <template x-if="detailOpname.status_konfirmasi === 'dikonfirmasi_spv'">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                        <template x-if="detailOpname.status_konfirmasi !== 'dikonfirmasi_spv'">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </template>
                        <span x-text="detailOpname.status_konfirmasi === 'dikonfirmasi_spv' ? 'Status: Telah Disetujui & Stok Riil Tersinkronisasi' : 'Status: Menunggu Persetujuan / Verifikasi SPV Gudang'"></span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono uppercase"
                          :class="detailOpname.status_konfirmasi === 'dikonfirmasi_spv' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400'"
                          x-text="detailOpname.status_konfirmasi === 'dikonfirmasi_spv' ? 'DIKONFIRMASI SPV' : 'DRAFT'"></span>
                </div>

                <!-- Grid 3 Kartu Perbandingan Kuantitas Stok -->
                <div class="grid grid-cols-3 gap-3">
                    <!-- Stok Sistem -->
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-center">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Stok Sistem (Buku)</div>
                        <div class="text-base font-bold font-mono text-slate-900 dark:text-slate-100 mt-1">
                            <span x-text="new Intl.NumberFormat('id-ID').format(detailOpname.stok_sistem || 0)"></span>
                            <span class="text-xs font-normal text-slate-400">Zak</span>
                        </div>
                        <div class="text-[9px] text-slate-400 mt-1">Pencatatan Awal</div>
                    </div>

                    <!-- Stok Fisik -->
                    <div class="p-3.5 rounded-xl bg-sky-50/70 dark:bg-sky-500/10 border border-sky-200/80 dark:border-sky-500/20 text-center">
                        <div class="text-[10px] font-semibold text-sky-700 dark:text-sky-400 uppercase tracking-wider">Stok Fisik Riil</div>
                        <div class="text-base font-bold font-mono text-sky-800 dark:text-sky-300 mt-1">
                            <span x-text="new Intl.NumberFormat('id-ID').format(detailOpname.stok_fisik || 0)"></span>
                            <span class="text-xs font-normal text-sky-500">Zak</span>
                        </div>
                        <div class="text-[9px] text-sky-600/80 dark:text-sky-400 mt-1">Hasil Hitung Lapangan</div>
                    </div>

                    <!-- Selisih -->
                    <div class="p-3.5 rounded-xl border text-center"
                         :class="(detailOpname.selisih < 0) ? 'bg-rose-50/70 dark:bg-rose-500/10 border-rose-200 text-rose-800 dark:text-rose-300' : ((detailOpname.selisih > 0) ? 'bg-emerald-50/70 dark:bg-emerald-500/10 border-emerald-200 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100/70 dark:bg-slate-800/50 border-slate-200 text-slate-700 dark:text-slate-300')">
                        <div class="text-[10px] font-semibold uppercase tracking-wider"
                             :class="(detailOpname.selisih < 0) ? 'text-rose-700 dark:text-rose-400' : ((detailOpname.selisih > 0) ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500')">
                            Selisih (Varian)
                        </div>
                        <div class="text-base font-bold font-mono mt-1">
                            <span x-text="(detailOpname.selisih > 0 ? '+' : '') + new Intl.NumberFormat('id-ID').format(detailOpname.selisih || 0)"></span>
                            <span class="text-xs font-normal opacity-75">Zak</span>
                        </div>
                        <div class="text-[9px] mt-1 font-semibold"
                             x-text="(detailOpname.selisih < 0) ? 'Kurang / Minus' : ((detailOpname.selisih > 0) ? 'Surplus / Lebih' : 'Cocok Sesuai')"></div>
                    </div>
                </div>

                <!-- Informasi Fasilitas Gudang -->
                <div class="p-4 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] space-y-3">
                    <div class="text-[11px] font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider pb-2 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <span>Informasi Fasilitas Gudang & Audit</span>
                        <span class="font-mono font-semibold text-teal-600 dark:text-teal-400" x-text="detailOpname.gudang ? detailOpname.gudang.nama_gudang : detailOpname.kode_gudang"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 pt-1">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Kode Fasilitas Gudang:</span>
                            <strong class="font-mono text-slate-900 dark:text-slate-100" x-text="detailOpname.kode_gudang || '-'"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Tipe / Jenis Gudang:</span>
                            <strong class="text-slate-900 dark:text-slate-100" x-text="detailOpname.gudang ? detailOpname.gudang.jenis_gudang : '-'"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Komoditas Semen:</span>
                            <strong class="text-slate-900 dark:text-slate-100" x-text="(detailOpname.gudang && detailOpname.gudang.barang) ? (detailOpname.gudang.barang.nama_barang + ' (' + (detailOpname.gudang.barang.jenis_barang || '-') + ')') : (detailOpname.gudang ? detailOpname.gudang.kode_barang : '-')"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Petugas Auditor Lapangan:</span>
                            <strong class="text-slate-900 dark:text-slate-100" x-text="detailOpname.petugas_opname || '-'"></strong>
                        </div>
                    </div>
                </div>

                <!-- Catatan Keterangan Selisih -->
                <div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Catatan & Keterangan Selisih Fisik</div>
                    <div class="p-3 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 leading-relaxed font-mono"
                         x-text="detailOpname.keterangan_selisih || 'Tidak ada catatan selisih fisik khusus.'"></div>
                </div>

                <!-- Riwayat Terakhir Diperbarui / Dibuat -->
                <div class="p-3 rounded-xl bg-teal-50/50 dark:bg-teal-500/5 border border-teal-200/60 dark:border-teal-500/20 flex items-center justify-between font-mono text-[11px]">
                    <div class="flex items-center gap-2 text-teal-900 dark:text-teal-300">
                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Dicatat: <strong x-text="detailOpname.terakhir_diedit_relatif || '-'"></strong></span>
                    </div>
                    <span class="text-slate-500 dark:text-slate-400" x-text="detailOpname.terakhir_diedit_waktu || '-'"></span>
                </div>
            </div>

            <!-- Footer Modal Detail -->
            <div class="flex items-center justify-between px-6 py-3.5 border-t border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2">
                    <template x-if="detailOpname.status_konfirmasi === 'draft'">
                        <form :action="'{{ url('operasional/gudang/opname') }}/' + detailOpname.id_opname + '/konfirmasi'" method="POST"
                              onsubmit="return confirm('Konfirmasi opname ini dan sinkronkan kuantitas stok riil ke gudang?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-100/80 dark:bg-amber-500/20 hover:bg-amber-200 rounded-xl transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Konfirmasi SPV</span>
                            </button>
                        </form>
                    </template>
                    <button @click="modalDetailTerbuka = false; bukaModalEdit(detailOpname.id_opname)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-200/80 dark:bg-slate-700 hover:bg-slate-300 rounded-xl transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Ubah Data</span>
                    </button>
                    <button type="button" @click="cetakBeritaAcaraOpname()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-colors shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak BASO</span>
                    </button>
                </div>
                <button @click="modalDetailTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
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
            modalDetailTerbuka: false,
            modalHapusTerbuka: false,

            keteranganKodeOpn: 'Mode: Daur Ulang Slot Kosong',

            petaStokGudang: @json(($daftarGudang ?? collect())->pluck('stok_tersedia', 'kode_gudang')),

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

            detailOpname: {},
            hapusData: { id: '', nomor_opname: '' },

            get selisihHitungTambah() {
                return (parseInt(this.formTambah.stok_fisik) || 0) - (parseInt(this.formTambah.stok_sistem) || 0);
            },

            async bukaModalDetail(id) {
                try {
                    const res = await fetch(`{{ url('operasional/gudang/opname') }}/${id}`);
                    const data = await res.json();
                    if (data.status === 'sukses') {
                        this.detailOpname = data.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil detail data opname.');
                }
            },

            initOpname() {
                this.$watch('formTambah.kode_gudang', (kode) => {
                    if (kode && typeof this.petaStokGudang[kode] !== 'undefined') {
                        const stok = parseInt(this.petaStokGudang[kode]) || 0;
                        this.formTambah.stok_sistem = stok;
                        this.formTambah.stok_fisik = stok;
                    }
                });
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
                            tanggal_opname: d.tanggal_opname ? String(d.tanggal_opname).split('T')[0] : '',
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
            },

            async cetakLangsungOpname(id) {
                await this.bukaModalDetail(id);
                setTimeout(() => {
                    this.cetakBeritaAcaraOpname();
                }, 400);
            },

            cetakBeritaAcaraOpname() {
                const nomor = this.detailOpname.nomor_opname || '';
                const tanggal = this.detailOpname.tanggal_format || this.detailOpname.tanggal_opname || '';
                const gudang = (this.detailOpname.gudang ? this.detailOpname.gudang.nama_gudang : this.detailOpname.kode_gudang) || '-';
                const komoditas = (this.detailOpname.gudang && this.detailOpname.gudang.barang) ? (this.detailOpname.gudang.barang.nama_barang + ' (' + (this.detailOpname.gudang.barang.jenis_barang || '-') + ')') : 'Semen Zak PCC';
                const plant = (this.detailOpname.gudang ? this.detailOpname.gudang.plant : '-') || '-';
                const stokSistem = new Intl.NumberFormat('id-ID').format(this.detailOpname.stok_sistem || 0);
                const stokFisik = new Intl.NumberFormat('id-ID').format(this.detailOpname.stok_fisik || 0);
                const selisih = (parseInt(this.detailOpname.selisih) || 0);
                const selisihFmt = (selisih > 0 ? '+' : '') + new Intl.NumberFormat('id-ID').format(selisih);
                const status = this.detailOpname.status_konfirmasi === 'dikonfirmasi_spv' ? 'DIKONFIRMASI SPV & TERSINKRON' : 'DRAFT / AUDIT FISIK';
                const petugas = this.detailOpname.petugas_opname || '-';
                const catatan = this.detailOpname.keterangan_selisih || 'Tidak ada catatan selisih fisik khusus.';

                const html = `
                <div class="p-8 space-y-6 text-slate-900 bg-white font-sans text-xs">
                    <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                        <div>
                            <h2 class="text-base font-black uppercase tracking-wider text-slate-950">PT PUTRA BALKOM JAYA</h2>
                            <p class="text-[10px] text-slate-600 leading-tight">Distributor Resmi Semen Indonesia Group (SIG) & Logistik Armada</p>
                            <p class="text-[9px] text-slate-500 mt-0.5">Bekasi, Jawa Barat · Telp: (021) 8990-1234</p>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-mono font-bold px-2.5 py-1 bg-slate-100 rounded border border-slate-300 inline-block">
                                BERITA ACARA STOCK OPNAME
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1 font-mono">No: ${nomor}</div>
                        </div>
                    </div>

                    <div class="text-center">
                        <h3 class="text-sm font-black uppercase tracking-wide text-slate-900 underline underline-offset-4">
                            BERITA ACARA AUDIT FISIK STOCK OPNAME GUDANG (BASO)
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">Hasil Rekonsiliasi Kuantitas Fisik Riil vs Pencatatan Sistem</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="space-y-1.5">
                            <div><span class="text-slate-500 w-32 inline-block">No. Dokumen:</span><strong>${nomor}</strong></div>
                            <div><span class="text-slate-500 w-32 inline-block">Tanggal Audit:</span><span>${tanggal}</span></div>
                            <div><span class="text-slate-500 w-32 inline-block">Status Audit:</span><strong class="uppercase">${status}</strong></div>
                        </div>
                        <div class="space-y-1.5">
                            <div><span class="text-slate-500 w-32 inline-block">Fasilitas Gudang:</span><strong>${gudang}</strong></div>
                            <div><span class="text-slate-500 w-32 inline-block">Plant Produksi:</span><span>${plant}</span></div>
                            <div><span class="text-slate-500 w-32 inline-block">Auditor Lapangan:</span><span>${petugas}</span></div>
                        </div>
                    </div>

                    <table class="w-full border-collapse border border-slate-300 text-left">
                        <thead class="bg-slate-100 font-semibold">
                            <tr>
                                <th class="border border-slate-300 px-3 py-2">Komoditas Semen</th>
                                <th class="border border-slate-300 px-3 py-2 text-right">Stok Sistem (Zak)</th>
                                <th class="border border-slate-300 px-3 py-2 text-right">Stok Fisik (Zak)</th>
                                <th class="border border-slate-300 px-3 py-2 text-right">Selisih Fisik (Zak)</th>
                                <th class="border border-slate-300 px-3 py-2 text-center">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 font-medium">${komoditas}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-mono">${stokSistem}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-mono font-bold">${stokFisik}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-mono font-bold">${selisihFmt}</td>
                                <td class="border border-slate-300 px-3 py-2 text-center font-bold">${selisih === 0 ? 'SESUAI' : (selisih < 0 ? 'SELISIH KURANG' : 'SELISIH LEBIH')}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="font-bold text-slate-800 mb-1">Catatan & Keterangan Lapangan:</div>
                        <div class="italic text-slate-700">${catatan}</div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 pt-6 text-center text-xs">
                        <div>
                            <div class="text-slate-500 mb-10">Petugas Auditor:</div>
                            <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[110px]">( ${petugas} )</div>
                        </div>
                        <div>
                            <div class="text-slate-500 mb-10">Kepala Gudang:</div>
                            <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[110px]">( Staf Gudang )</div>
                        </div>
                        <div>
                            <div class="text-slate-500 mb-10">SPV Gudang:</div>
                            <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[110px]">( SPV Gudang )</div>
                        </div>
                    </div>
                </div>
                `;

                const win = window.open('', '', 'height=750,width=900');
                win.document.write('<html><head><title>Cetak BASO ' + nomor + '</title>');
                win.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">');
                win.document.write('</head><body class="p-4">');
                win.document.write(html);
                win.document.write('</body></html>');
                win.document.close();
                win.focus();
                setTimeout(() => { win.print(); win.close(); }, 500);
            }
        };
    }
</script>
@endsection
