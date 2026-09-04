@extends('layouts.app')

@section('judul', 'Monitoring List SO Semen - PT Putra Balkom Jaya')

@section('konten')
@php
    $opsiFilterStatus = [
        ['nilai' => '', 'label' => '-- Semua Status SO --'],
        ['nilai' => 'disetujui', 'label' => 'Disetujui / Aktif'],
        ['nilai' => 'diproses', 'label' => 'Sedang Diproses'],
        ['nilai' => 'selesai', 'label' => 'Selesai'],
    ];

    $opsiFilterGudang = array_merge([
        ['nilai' => '', 'label' => '-- Semua Gudang Penebusan --']
    ], ($daftarGudang ?? collect())->map(fn($g) => [
        'nilai' => $g->kode_gudang,
        'label' => $g->nama_gudang . ' (' . $g->kode_gudang . ')',
        'sub'   => 'Plant: ' . ($g->plant ?? '-')
    ])->toArray());
@endphp

<div x-data="manajemenListSO()" class="space-y-6">

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
            <!-- Tombol Cetak Rekapitulasi Kuota SO -->
            <button type="button" @click="bukaModalCetakRekap()"
                    class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 active:scale-95 rounded-xl transition-all shadow-xs">
                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Rekap Kuota</span>
            </button>

            <!-- Tombol Tambah Penebusan SO -->
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
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarSO ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-visible shadow-sm">
        
        <!-- Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('keuangan.ap.list_so') }}" class="flex flex-col sm:flex-row sm:items-center gap-2.5 flex-1 max-w-4xl">
                <!-- Search Input -->
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari nomor SO, nomor LO, gudang..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Dropdown Kustom Status -->
                <div class="w-full sm:w-48">
                    <x-dropdown-kustom 
                        nama="status"
                        placeholder="-- Status SO --"
                        :opsi="$opsiFilterStatus"
                        :nilaiAwal="$filterStatus ?? ''"
                        :submitOnChange="true"
                        warnaFokus="indigo"
                    />
                </div>

                <!-- Dropdown Kustom Gudang -->
                <div class="w-full sm:w-64">
                    <x-dropdown-kustom 
                        nama="gudang"
                        placeholder="-- Gudang Penebusan --"
                        :opsi="$opsiFilterGudang"
                        :nilaiAwal="$filterGudang ?? ''"
                        :submitOnChange="true"
                        warnaFokus="indigo"
                    />
                </div>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors shrink-0">
                    Filter
                </button>
                @if(!empty($kataKunci) || !empty($filterStatus) || !empty($filterGudang))
                    <a href="{{ route('keuangan.ap.list_so') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline shrink-0">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono shrink-0">
                Menampilkan <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarSO->count() }}</span> data SO
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto overflow-y-visible">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">No. SO / LO</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Tanggal & Gudang</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Pengiriman</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Kuota SO</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Terambil</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Sisa Kuota</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider w-36">Progres</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi & Cetak</th>
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
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <!-- No. SO & LO -->
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ $so->nomor_so }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">LO: {{ $so->nomor_lo ?? '-' }}</div>
                            </td>

                            <!-- Tanggal & Gudang -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $so->gudang->nama_gudang ?? $so->kode_gudang }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ date('d/m/Y', strtotime($so->tanggal_so)) }} · Plant: {{ $so->gudang->plant ?? '-' }}</div>
                            </td>

                            <!-- Pengiriman -->
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $so->jenis_pengiriman === 'FOT' ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-950/40 dark:text-sky-400' }}">
                                    {{ $so->jenis_pengiriman ?? 'FRC' }}
                                </span>
                            </td>

                            <!-- Kuota SO -->
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-slate-100">
                                {{ number_format($kuota, 0, ',', '.') }} Zak
                            </td>

                            <!-- Terambil -->
                            <td class="px-4 py-3 text-right font-mono font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($ambil, 0, ',', '.') }} Zak
                            </td>

                            <!-- Sisa Kuota -->
                            <td class="px-4 py-3 text-right font-mono font-semibold text-amber-600 dark:text-amber-400">
                                {{ number_format($sisa, 0, ',', '.') }} Zak
                            </td>

                            <!-- Progres Realisasi -->
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full {{ $persen >= 100 ? 'bg-emerald-500' : ($persen > 50 ? 'bg-blue-500' : 'bg-amber-500') }} transition-all" style="width: {{ $persen }}%;"></div>
                                    </div>
                                    <span class="text-[10px] font-mono font-bold text-slate-600 dark:text-slate-400 w-8 text-right">{{ $persen }}%</span>
                                </div>
                            </td>

                            <!-- Status -->
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

                            <!-- Aksi & Cetak Popover Modern -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$so->nomor_so" 
                                    labelSalin="Salin No SO"
                                    modulIzin="ap_so"
                                >
                                    <button @click.stop="menuTerbuka = false; bukaModalCetakSO({
                                        id_so: '{{ $so->id_so }}',
                                        nomor_so: '{{ $so->nomor_so }}',
                                        nomor_lo: '{{ $so->nomor_lo ?? '-' }}',
                                        tanggal_so: '{{ date('d/m/Y', strtotime($so->tanggal_so)) }}',
                                        nama_gudang: '{{ addslashes($so->gudang->nama_gudang ?? $so->kode_gudang) }}',
                                        plant: '{{ addslashes($so->gudang->plant ?? '-') }}',
                                        jenis_pengiriman: '{{ $so->jenis_pengiriman ?? 'FRC' }}',
                                        kuota: '{{ number_format($kuota, 0, ',', '.') }}',
                                        terambil: '{{ number_format($ambil, 0, ',', '.') }}',
                                        sisa: '{{ number_format($sisa, 0, ',', '.') }}',
                                        persen: '{{ $persen }}',
                                        status: '{{ $so->status_so }}',
                                        harga_satuan: '{{ number_format($so->harga_satuan ?? 0, 0, ',', '.') }}',
                                        total_harga: '{{ number_format($so->total_harga ?? 0, 0, ',', '.') }}'
                                    })" 
                                            type="button" 
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors text-left border-b border-slate-100 dark:border-[#252837]">
                                        <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span>Cetak Lembar Kontrol SO</span>
                                    </button>
                                </x-menu-aksi-tabel>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data Sales Order yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarSO ?? [])" />
    </div>

    <!-- 4. MODAL PRATINJAU & CETAK LEMBAR KONTROL KUOTA SO (INDIVIDUAL) -->
    <div x-show="modalCetakSOTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalCetakSOTerbuka = false"
             class="bg-white text-slate-900 rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl my-8 border border-slate-200">
            
            <!-- Toolbar Aksi Modal (Disembunyikan saat cetak) -->
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-slate-200 bg-slate-50 print:hidden">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                    <span class="font-bold text-xs text-slate-800">Pratinjau Lembar Kontrol Kuota Sales Order (SO)</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="window.print()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Dokumen (Print)</span>
                    </button>
                    <button type="button" @click="modalCetakSOTerbuka = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Konten Lembar Dokumen SO Resmi -->
            <div class="p-8 space-y-6 text-slate-900 bg-white" id="area-cetak-so">
                
                <!-- Kop Surat Perusahaan -->
                <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ asset('images/logo-pbj.png') }}" alt="Logo PT Putra Balkom Jaya" class="w-16 h-16 object-contain shrink-0" onerror="this.style.display='none'">
                        <div>
                            <h2 class="text-lg font-black uppercase tracking-wider text-slate-950">PT PUTRA BALKOM JAYA</h2>
                            <p class="text-[11px] text-slate-600 leading-tight">Distributor Resmi Semen Indonesia Group (SIG) & Logistik Armada</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Jl. Raya Cikarang - Cibarusah No. 88, Bekasi, Jawa Barat · Telp: (021) 8990-1234</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-mono font-bold px-2.5 py-1 bg-slate-100 rounded border border-slate-300 inline-block">
                            DOKUMEN KONTROL OPERASIONAL
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1 font-mono">Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>

                <!-- Judul Dokumen -->
                <div class="text-center">
                    <h3 class="text-base font-black uppercase tracking-wide text-slate-900 underline underline-offset-4">
                        LEMBAR KONTROL KUOTA SALES ORDER (SO)
                    </h3>
                    <p class="text-xs font-mono text-slate-500 mt-1">Nomor Registrasi SO: <strong class="text-indigo-600" x-text="detailSO.nomor_so"></strong></p>
                </div>

                <!-- Informasi Pokok Transaksi SO -->
                <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <div class="space-y-1.5">
                        <div class="flex"><span class="w-36 text-slate-500">Nomor Sales Order (SO)</span><span class="font-bold font-mono" x-text="detailSO.nomor_so"></span></div>
                        <div class="flex"><span class="w-36 text-slate-500">Nomor Loading Order (LO)</span><span class="font-semibold font-mono" x-text="detailSO.nomor_lo"></span></div>
                        <div class="flex"><span class="w-36 text-slate-500">Tanggal Terbit SO</span><span class="font-medium" x-text="detailSO.tanggal_so"></span></div>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex"><span class="w-36 text-slate-500">Gudang / Titik Penebusan</span><span class="font-bold" x-text="detailSO.nama_gudang"></span></div>
                        <div class="flex"><span class="w-36 text-slate-500">Plant / Pabrik SIG</span><span class="font-medium" x-text="detailSO.plant"></span></div>
                        <div class="flex"><span class="w-36 text-slate-500">Metode Pengiriman</span><span class="font-bold font-mono" x-text="detailSO.jenis_pengiriman"></span></div>
                    </div>
                </div>

                <!-- Tabel Breakdown Kuota & Realisasi -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Rincian Realisasi Kuota Semen</h4>
                    <table class="w-full border-collapse border border-slate-300 text-xs text-left">
                        <thead class="bg-slate-100 font-semibold text-slate-700">
                            <tr>
                                <th class="border border-slate-300 px-3 py-2">Komoditas Barang</th>
                                <th class="border border-slate-300 px-3 py-2 text-right">Total Kuota SO</th>
                                <th class="border border-slate-300 px-3 py-2 text-right">Realisasi Ambil</th>
                                <th class="border border-slate-300 px-3 py-2 text-right">Sisa Kuota</th>
                                <th class="border border-slate-300 px-3 py-2 text-center">Progres</th>
                                <th class="border border-slate-300 px-3 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2.5 font-medium">Semen Zak PCC (Pabrik SIG)</td>
                                <td class="border border-slate-300 px-3 py-2.5 text-right font-mono font-bold" x-text="detailSO.kuota + ' Zak'"></td>
                                <td class="border border-slate-300 px-3 py-2.5 text-right font-mono font-semibold text-emerald-700" x-text="detailSO.terambil + ' Zak'"></td>
                                <td class="border border-slate-300 px-3 py-2.5 text-right font-mono font-bold text-amber-700" x-text="detailSO.sisa + ' Zak'"></td>
                                <td class="border border-slate-300 px-3 py-2.5 text-center font-mono font-bold" x-text="detailSO.persen + '%'"></td>
                                <td class="border border-slate-300 px-3 py-2.5 text-center font-bold uppercase font-mono text-[10px]" x-text="detailSO.status"></td>
                            </tr>
                            <tr class="bg-slate-50 font-medium">
                                <td class="border border-slate-300 px-3 py-2 text-slate-600" colspan="3">Estimasi Nilai Total Penebusan (Satuan @ Rp <span x-text="detailSO.harga_satuan"></span>)</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-mono font-bold text-indigo-700" colspan="3">
                                    Rp <span x-text="detailSO.total_harga"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Catatan Operasional Logistik -->
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
                    <div class="font-bold text-slate-800">Petunjuk Operasional Dispatcher & Driver:</div>
                    <ul class="list-disc list-inside space-y-0.5 ml-1">
                        <li>Surat jalan pengiriman wajib mencantumkan Nomor SO di atas sebagai referensi kuota pabrik.</li>
                        <li>Pastikan supir membawa LO resmi dan menunjukkan lembar ini saat antre timbang muatan di plant pabrik.</li>
                        <li>Sisa kuota yang belum terambil otomatis dialokasikan untuk jadwal ritase berikutnya hingga status tuntas.</li>
                    </ul>
                </div>

                <!-- Kolom Tanda Tangan Resmi -->
                <div class="grid grid-cols-3 gap-6 pt-4 text-center text-xs">
                    <div>
                        <div class="text-slate-500 mb-12">Dibuat Oleh (Staf AP):</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[120px]">( Staf Keuangan AP )</div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-12">Dikoordinasikan (Dispatcher):</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[120px]">( Petugas Logistik )</div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-12">Menyetujui (SPV Keuangan):</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[120px]">( SPV Keuangan )</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. MODAL PRATINJAU & CETAK REKAPITULASI KUOTA SELURUH SO (BATCH REKAP) -->
    <div x-show="modalCetakRekapTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalCetakRekapTerbuka = false"
             class="bg-white text-slate-900 rounded-2xl w-full max-w-4xl overflow-hidden shadow-2xl my-8 border border-slate-200">
            
            <!-- Toolbar Aksi Modal -->
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-slate-200 bg-slate-50 print:hidden">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                    <span class="font-bold text-xs text-slate-800">Pratinjau Lembar Rekapitulasi Kuota SO Semen</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="window.print()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Rekapitulasi (Print)</span>
                    </button>
                    <button type="button" @click="modalCetakRekapTerbuka = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Konten Lembar Rekapitulasi -->
            <div class="p-8 space-y-6 text-slate-900 bg-white" id="area-cetak-rekap">
                <!-- Kop Surat -->
                <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ asset('images/logo-pbj.png') }}" alt="Logo PT Putra Balkom Jaya" class="w-16 h-16 object-contain shrink-0" onerror="this.style.display='none'">
                        <div>
                            <h2 class="text-lg font-black uppercase tracking-wider text-slate-950">PT PUTRA BALKOM JAYA</h2>
                            <p class="text-[11px] text-slate-600 leading-tight">Distributor Resmi Semen Indonesia Group (SIG) & Logistik Armada</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Jl. Raya Cikarang - Cibarusah No. 88, Bekasi, Jawa Barat · Telp: (021) 8990-1234</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-mono font-bold px-2.5 py-1 bg-slate-100 rounded border border-slate-300 inline-block">
                            LAPORAN MONITORING KUOTA
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1 font-mono">Periode: {{ date('d F Y') }}</div>
                    </div>
                </div>

                <!-- Judul -->
                <div class="text-center">
                    <h3 class="text-base font-black uppercase tracking-wide text-slate-900 underline underline-offset-4">
                        REKAPITULASI MONITORING KUOTA PENGAMBILAN SALES ORDER (SO)
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Daftar alokasi kuota semen, volume terambil, dan sisa kuota aktif di seluruh plant/gudang.</p>
                </div>

                <!-- Tabel Rekapitulasi -->
                <table class="w-full border-collapse border border-slate-300 text-xs text-left">
                    <thead class="bg-slate-100 font-semibold text-slate-700">
                        <tr>
                            <th class="border border-slate-300 px-2 py-2 text-center w-8">No</th>
                            <th class="border border-slate-300 px-3 py-2">Nomor SO / LO</th>
                            <th class="border border-slate-300 px-3 py-2">Gudang & Plant</th>
                            <th class="border border-slate-300 px-3 py-2 text-right">Total Kuota</th>
                            <th class="border border-slate-300 px-3 py-2 text-right">Realisasi Ambil</th>
                            <th class="border border-slate-300 px-3 py-2 text-right">Sisa Kuota</th>
                            <th class="border border-slate-300 px-3 py-2 text-center">Progres</th>
                            <th class="border border-slate-300 px-3 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarSO as $so)
                            @php
                                $k = $so->jumlah_zak ?? 0;
                                $a = $so->qty_pengambilan ?? 0;
                                $s = max(0, $k - $a);
                                $p = $k > 0 ? min(100, round(($a / $k) * 100)) : 0;
                            @endphp
                            <tr>
                                <td class="border border-slate-300 px-2 py-1.5 text-center font-mono">{{ $loop->iteration }}</td>
                                <td class="border border-slate-300 px-3 py-1.5 font-mono font-bold">
                                    {{ $so->nomor_so }}
                                    <div class="text-[10px] text-slate-500 font-normal">LO: {{ $so->nomor_lo ?? '-' }}</div>
                                </td>
                                <td class="border border-slate-300 px-3 py-1.5">
                                    {{ $so->gudang->nama_gudang ?? $so->kode_gudang }}
                                    <div class="text-[10px] text-slate-500">Plant: {{ $so->gudang->plant ?? '-' }}</div>
                                </td>
                                <td class="border border-slate-300 px-3 py-1.5 text-right font-mono font-bold">{{ number_format($k, 0, ',', '.') }} Zak</td>
                                <td class="border border-slate-300 px-3 py-1.5 text-right font-mono text-emerald-700 font-semibold">{{ number_format($a, 0, ',', '.') }} Zak</td>
                                <td class="border border-slate-300 px-3 py-1.5 text-right font-mono text-amber-700 font-bold">{{ number_format($s, 0, ',', '.') }} Zak</td>
                                <td class="border border-slate-300 px-3 py-1.5 text-center font-mono font-bold">{{ $p }}%</td>
                                <td class="border border-slate-300 px-3 py-1.5 text-center uppercase font-mono text-[10px] font-bold">{{ $so->status_so }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold">
                        <tr>
                            <td colspan="3" class="border border-slate-300 px-3 py-2 text-right uppercase">Total Akumulasi:</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-mono text-blue-700">{{ number_format($totalKuantitasSO, 0, ',', '.') }} Zak</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-mono text-emerald-700">{{ number_format($totalTerambil, 0, ',', '.') }} Zak</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-mono text-amber-700">{{ number_format($totalSisaKuota, 0, ',', '.') }} Zak</td>
                            <td colspan="2" class="border border-slate-300 px-3 py-2 text-center text-slate-500 text-[10px] font-normal">
                                Terdaftar {{ count($daftarSO) }} Record SO
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Kolom Tanda Tangan Resmi -->
                <div class="grid grid-cols-2 gap-8 pt-6 text-center text-xs">
                    <div>
                        <div class="text-slate-500 mb-12">Disiapkan Oleh (Staf AP):</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[150px]">( Staf Keuangan AP )</div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-12">Mengetahui (SPV Keuangan):</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[150px]">( SPV Keuangan )</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Script Alpine.js Pengelola Cetak SO -->
<script>
    function manajemenListSO() {
        return {
            modalCetakSOTerbuka: false,
            modalCetakRekapTerbuka: false,
            detailSO: {},

            bukaModalCetakSO(data) {
                this.detailSO = data;
                this.modalCetakSOTerbuka = true;
            },

            bukaModalCetakRekap() {
                this.modalCetakRekapTerbuka = true;
            }
        };
    }
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #area-cetak-so, #area-cetak-so *,
    #area-cetak-rekap, #area-cetak-rekap * {
        visibility: visible;
    }
    #area-cetak-so, #area-cetak-rekap {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>
@endsection
