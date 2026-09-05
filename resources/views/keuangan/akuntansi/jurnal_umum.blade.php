@extends('layouts.app')

@section('judul', 'Jurnal Umum Transaksi (Akuntansi)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false,
    modalCetakTerbuka: false,
    detailCetak: {},
    cetakVoucherJurnal(data) {
        this.detailCetak = data;
        this.modalCetakTerbuka = true;
    }
}">
    <!-- Flash Notification -->
    @if(session('sukses'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('sukses') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Header Modul Jurnal Umum -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-teal-600 dark:text-teal-400 font-semibold font-mono uppercase tracking-wider mb-1">Buku Besar & Akuntansi · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Buku Jurnal Umum Double-Entry</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan mutasi debit dan kredit otomatis dari transaksi operasional serta entri jurnal manual.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Input Jurnal Manual
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Jurnal -->
    <div class="wadah-bertingkat grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Mutasi Debet</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Mutasi Kredit</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Status Neraca Saldo</div>
            <div class="text-sm font-bold {{ ($isBalance ?? true) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} mt-1 font-mono flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full {{ ($isBalance ?? true) ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                {{ ($isBalance ?? true) ? 'SEIMBANG (BALANCED)' : 'TIDAK SEIMBANG' }}
            </div>
        </div>
    </div>

    <!-- Tabel Data Jurnal Umum -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarJurnal ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.akuntansi.jurnal') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterPosisi = [
                    ['nilai' => '', 'label' => '-- Semua Posisi --'],
                    ['nilai' => 'Debit', 'label' => 'Debit'],
                    ['nilai' => 'Kredit', 'label' => 'Kredit'],
                ];
                $opsiFilterAkunJurnal = array_merge([
                    ['nilai' => '', 'label' => '-- Semua Akun COA --']
                ], ($daftarAkun ?? collect())->map(fn($a) => [
                    'nilai' => $a->kode_akun,
                    'label' => $a->kode_akun . ' - ' . $a->nama_akun,
                    'sub'   => 'Tipe: ' . ($a->tipe_akun ?? '-')
                ])->toArray());
                $opsiAkunJurnal = ($daftarAkun ?? collect())->map(fn($a) => [
                    'nilai' => $a->kode_akun,
                    'label' => $a->nama_akun,
                    'sub'   => 'Kode: ' . $a->kode_akun
                ])->toArray();
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-56">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari no / akun / ket..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-36">
                    <x-dropdown-kustom 
                        nama="posisi" 
                        :nilaiAwal="$filterPosisi ?? ''" 
                        placeholder="-- Semua Posisi --" 
                        :opsi="$opsiFilterPosisi" 
                        warnaFokus="teal"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                <div class="w-full sm:w-48">
                    <x-dropdown-kustom 
                        nama="akun" 
                        :nilaiAwal="$filterAkun ?? ''" 
                        placeholder="-- Semua Akun COA --" 
                        :opsi="$opsiFilterAkunJurnal" 
                        warnaFokus="teal"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                <div class="w-full sm:w-40">
                    <x-dropdown-kustom 
                        nama="periode" 
                        :nilaiAwal="$filterPeriode ?? ''" 
                        placeholder="-- Semua Periode --" 
                        :opsi="$opsiPeriode ?? []" 
                        warnaFokus="teal"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                @if(($filterPeriode ?? '') === 'kustom')
                <div class="flex items-center gap-1 bg-[#F8FAFC] dark:bg-[#1C1E2A] p-1 rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                    <input type="date" name="tgl_mulai" value="{{ $filterTglMulai ?? '' }}" class="px-2 py-1 text-xs rounded-lg bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300">
                    <span class="text-xs text-slate-400">-</span>
                    <input type="date" name="tgl_selesai" value="{{ $filterTglSelesai ?? '' }}" class="px-2 py-1 text-xs rounded-lg bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300">
                    <button type="submit" class="px-2.5 py-1 text-xs font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-lg transition-colors">
                        Terapkan
                    </button>
                </div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @if(($jumlahFilterAktif ?? 0) > 0)
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-teal-50 dark:bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                        {{ $jumlahFilterAktif }} Filter Aktif
                    </span>
                    <a href="{{ route('keuangan.akuntansi.jurnal') }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-500/10 border border-dashed border-slate-300 dark:border-slate-700 transition-colors" title="Bersihkan semua filter">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset
                    </a>
                </div>
                @endif
                <span class="text-xs text-slate-400 font-mono hidden md:inline">Tabel: jurnal_umum</span>
            </div>
        </form>

        <div class="overflow-x-auto min-h-[260px] pb-12">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Jurnal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Akun COA</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Keterangan Transaksi</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Debet</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Kredit</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarJurnal ?? [] as $jurnal)
                        @php /** @var object $jurnal */ @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-teal-600 dark:text-teal-400">
                                {{ $jurnal->nomor_jurnal }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($jurnal->tanggal_transaksi)) }}
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">
                                <span class="font-mono text-slate-500">{{ $jurnal->kode_akun }}</span> - {{ $jurnal->nama_akun ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 truncate max-w-xs">
                                {{ $jurnal->keterangan }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ ($jurnal->posisi ?? '') === 'Debit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-300 dark:text-slate-700' }}">
                                {{ ($jurnal->posisi ?? '') === 'Debit' ? 'Rp ' . number_format($jurnal->nominal, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ ($jurnal->posisi ?? '') === 'Kredit' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-300 dark:text-slate-700' }}">
                                {{ ($jurnal->posisi ?? '') === 'Kredit' ? 'Rp ' . number_format($jurnal->nominal, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$jurnal->nomor_jurnal" 
                                    labelSalin="Salin No"
                                    modulIzin="akun_jurnal"
                                >
                                    <button @click.stop="menuTerbuka = false; cetakVoucherJurnal($el.dataset)" 
                                            data-nomor="{{ $jurnal->nomor_jurnal }}"
                                            data-tanggal="{{ date('d/m/Y', strtotime($jurnal->tanggal_transaksi)) }}"
                                            data-akun="{{ $jurnal->kode_akun }} - {{ $jurnal->nama_akun ?? '' }}"
                                            data-keterangan="{{ $jurnal->keterangan }}"
                                            data-posisi="{{ $jurnal->posisi ?? 'Debit' }}"
                                            data-nominal="{{ number_format($jurnal->nominal, 0, ',', '.') }}"
                                            type="button" 
                                            class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-slate-700 dark:text-slate-200 hover:bg-[#F8FAFC] dark:hover:bg-[#1C1E2A] transition-colors text-left group">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span>Cetak Memorial</span>
                                    </button>
                                </x-menu-aksi-tabel>

                                <!-- Riwayat Terakhir Dibuat / Diedit Real-Time -->
                                @php
                                    $waktuJurnal = !empty($jurnal->diperbarui_pada) ? $jurnal->diperbarui_pada : (!empty($jurnal->dibuat_pada) ? $jurnal->dibuat_pada : null);
                                    $relatifWaktu = $waktuJurnal ? \Carbon\Carbon::parse($waktuJurnal)->locale('id')->diffForHumans() : 'Baru';
                                    $waktuPresisi = $waktuJurnal ? \Carbon\Carbon::parse($waktuJurnal)->format('d/m/Y H:i:s') : '-';
                                    $labelTitle = !empty($jurnal->diperbarui_pada) ? 'Terakhir diperbarui: ' : 'Dibuat pada: ';
                                @endphp
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                     title="{{ $labelTitle }}{{ $waktuPresisi }}">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $relatifWaktu }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Belum ada entri ayat jurnal umum.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarJurnal ?? [])" />
    </div>

    <!-- Modal Input Jurnal Manual -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Input Entri Jurnal Double-Entry</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.akuntansi.jurnal.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <x-input-tanggal 
                        nama="tanggal_transaksi" 
                        nilaiAwal="{{ date('Y-m-d') }}" 
                        placeholder="Pilih Tanggal Transaksi"
                        :wajib="true"
                        warnaFokus="teal"
                    />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Akun Sisi Debet (+) <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_akun_debit"
                            placeholder="-- Pilih Akun Debet --"
                            :opsi="$opsiAkunJurnal"
                            :wajib="true"
                            warnaFokus="teal"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Akun Sisi Kredit (-) <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_akun_kredit"
                            placeholder="-- Pilih Akun Kredit --"
                            :opsi="$opsiAkunJurnal"
                            :wajib="true"
                            warnaFokus="teal"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nominal Jurnal (Rp) <span class="text-rose-500">*</span></label>
                    <x-input-rupiah 
                        nama="nominal"
                        placeholder="5.000.000"
                        :wajib="true"
                        warnaFokus="teal"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Transaksi <span class="text-rose-500">*</span></label>
                    <input type="text" name="keterangan" required placeholder="Ayat jurnal penyesuaian saldo..."
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-all shadow-sm">Simpan Jurnal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pratinjau & Cetak Bukti Memorial Jurnal Umum -->
    <div x-show="modalCetakTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalCetakTerbuka = false"
             class="bg-white text-slate-900 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl my-8 border border-slate-200">
            
            <!-- Toolbar Aksi Modal (Disembunyikan saat cetak) -->
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-slate-200 bg-slate-50 print:hidden">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-teal-600"></span>
                    <span class="font-bold text-xs text-slate-800">Pratinjau Bukti Memorial Jurnal Umum</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="window.print()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Bukti Memorial</span>
                    </button>
                    <button type="button" @click="modalCetakTerbuka = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Lembar Cetak Bukti Memorial -->
            <div class="p-8 space-y-6 text-xs text-slate-800 font-sans">
                <!-- Kop Resmi -->
                <div class="border-b-2 border-slate-900 pb-3 flex items-center justify-between">
                    <div>
                        <div class="text-base font-black tracking-wide text-slate-900 uppercase">PT PUTRA BALKOM JAYA</div>
                        <div class="text-[10px] text-slate-600">Sistem Akuntansi Keuangan & Pembukuan Double-Entry</div>
                        <div class="text-[9px] text-slate-400">Jl. Raya Surabaya - Rembang Km. 45, Jawa Timur</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded bg-teal-50 text-[11px] font-mono font-bold text-teal-800 border border-teal-200">
                            BUKTI MEMORIAL AKUNTANSI
                        </span>
                        <div class="text-[10px] font-mono text-slate-500 mt-1" x-text="detailCetak.nomor"></div>
                    </div>
                </div>

                <!-- Detail Entri Jurnal -->
                <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <div>
                        <span class="text-slate-500 block text-[10px]">Nomor Jurnal:</span>
                        <strong class="text-slate-900 font-bold font-mono text-sm text-teal-700" x-text="detailCetak.nomor"></strong>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-500 block text-[10px]">Tanggal Transaksi:</span>
                        <strong class="font-mono text-slate-900" x-text="detailCetak.tanggal"></strong>
                    </div>
                </div>

                <div class="space-y-2 border border-slate-200 rounded-xl p-4">
                    <div class="flex justify-between py-1.5 border-b border-slate-200">
                        <span class="text-slate-500">Akun Perkiraan (COA):</span>
                        <strong class="font-bold text-slate-900" x-text="detailCetak.akun"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-200">
                        <span class="text-slate-500">Posisi Akun:</span>
                        <span class="px-2.5 py-0.5 rounded font-mono font-bold text-xs uppercase"
                              :class="detailCetak.posisi === 'Debit' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'"
                              x-text="detailCetak.posisi"></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-200">
                        <span class="text-slate-500">Nominal Transaksi:</span>
                        <strong class="font-mono font-bold text-sm text-slate-900" x-text="'Rp ' + (detailCetak.nominal || 0)"></strong>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500">Uraian / Keterangan Transaksi:</span>
                        <span class="text-slate-700 font-medium" x-text="detailCetak.keterangan || '-'"></span>
                    </div>
                </div>

                <!-- Tanda Tangan Pengesahan -->
                <div class="pt-6 grid grid-cols-2 gap-8 text-center text-[10px]">
                    <div>
                        <div class="text-slate-500 mb-14">Dibuat Oleh (Staf Pembukuan):</div>
                        <div class="font-bold underline text-slate-900">( ........................................ )</div>
                        <div class="text-slate-400">Akuntansi & Pajak</div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-14">Diperiksa & Disetujui:</div>
                        <div class="font-bold underline text-slate-900">( ........................................ )</div>
                        <div class="text-slate-400">SPV Keuangan & Akuntansi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
