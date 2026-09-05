@extends('layouts.app')

@section('judul', 'Pengeluaran Kas Operasional (AP)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false,
    modalCetakBKK: false,
    detailBKK: {},
    bukaModalCetak(data) {
        this.detailBKK = data;
        this.modalCetakBKK = true;
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

    <!-- Header Modul Pengeluaran Kas -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-rose-600 dark:text-rose-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Payable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Pengeluaran Kas & Biaya Operasional</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan kas keluar operasional: BBM, Tol armada, servis bengkel, dan beban kantor.</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-show="!apakahReadOnly('ap_pengeluaran')" @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Catat Pengeluaran
            </button>
            <span x-show="apakahReadOnly('ap_pengeluaran')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Mode Lihat Saja (Read-Only)
            </span>
        </div>
    </div>

    <!-- Ringkasan Statistik Pengeluaran -->
    <div class="wadah-bertingkat grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Kas Keluar</div>
            <div class="text-lg font-bold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Biaya BBM & Tol Truk</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">Rp {{ number_format($totalBBM ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Operasional Kantor & Lainnya</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">Rp {{ number_format($totalKantor ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tabel Data Pengeluaran Kas -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarPengeluaran ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ap.pengeluaran') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterKategoriPengeluaran = [
                    ['nilai' => '', 'label' => '-- Semua Kategori --'],
                    ['nilai' => 'BBM & Tol Armada', 'label' => 'BBM & Tol Armada'],
                    ['nilai' => 'Operasional Kantor', 'label' => 'Operasional Kantor'],
                    ['nilai' => 'Servis Truk', 'label' => 'Servis Truk'],
                ];
                $opsiKategoriModal = [
                    ['nilai' => 'BBM & Tol Armada', 'label' => 'BBM & Tol Armada'],
                    ['nilai' => 'Operasional Kantor', 'label' => 'Operasional Kantor / ATK'],
                    ['nilai' => 'Servis Truk', 'label' => 'Servis Kendaraan / Bengkel'],
                    ['nilai' => 'Konsumsi & Umum', 'label' => 'Konsumsi & Operasional Lapangan'],
                ];
                $opsiAkunBeban = ($daftarAkunBeban ?? collect())->map(fn($a) => [
                    'nilai' => $a->kode_akun,
                    'label' => $a->nama_akun,
                    'sub'   => 'Kode: ' . $a->kode_akun
                ])->toArray();
                $opsiRekeningSumber = array_merge([['nilai' => '', 'label' => 'Kas Tunai Brankas']], ($daftarRekening ?? collect())->map(fn($r) => [
                    'nilai' => $r->id_rekening,
                    'label' => $r->nama_bank,
                    'sub'   => 'Saldo: Rp ' . number_format($r->saldo_rekening, 0, ',', '.')
                ])->toArray());
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nomor bukti / keterangan..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-48">
                    <x-dropdown-kustom 
                        nama="kategori" 
                        :nilaiAwal="$filterKategori ?? ''" 
                        placeholder="-- Semua Kategori --" 
                        :opsi="$opsiFilterKategoriPengeluaran" 
                        warnaFokus="rose"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: pengeluaran</span>
        </form>

        <div class="overflow-x-auto min-h-[260px] pb-12">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Transaksi</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kategori Pengeluaran</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Akun COA Terkait</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Nominal Kas Keluar</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Sumber Rekening</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarPengeluaran ?? [] as $keluar)
                        @php /** @var \App\Models\Keuangan\PengeluaranKas $keluar */ @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-rose-600 dark:text-rose-400">
                                {{ $keluar->nomor_pengeluaran }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($keluar->tanggal_pengeluaran)) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                    {{ $keluar->kategori_pengeluaran }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-700 dark:text-slate-300">
                                {{ $keluar->kode_akun }} - {{ $keluar->akun->nama_akun ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($keluar->total_nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-300">
                                {{ $keluar->rekening->nama_bank ?? 'Kas Tunai Brankas' }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 truncate max-w-xs">
                                {{ $keluar->keterangan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$keluar->nomor_pengeluaran" 
                                    labelSalin="Salin No"
                                    modulIzin="ap_pengeluaran"
                                >
                                    <button @click.stop="menuTerbuka = false; bukaModalCetak({
                                        nomor_pengeluaran: '{{ $keluar->nomor_pengeluaran }}',
                                        tanggal: '{{ date('d/m/Y', strtotime($keluar->tanggal_pengeluaran)) }}',
                                        kategori: '{{ $keluar->kategori_pengeluaran }}',
                                        kode_akun: '{{ $keluar->kode_akun }}',
                                        nama_akun: '{{ addslashes($keluar->akun->nama_akun ?? '-') }}',
                                        nominal: '{{ number_format($keluar->total_nominal, 0, ',', '.') }}',
                                        sumber_dana: '{{ addslashes($keluar->rekening->nama_bank ?? 'Kas Tunai Brankas') }}',
                                        keterangan: '{{ addslashes($keluar->keterangan ?? '-') }}'
                                    })" 
                                            type="button" 
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 transition-colors text-left border-b border-slate-100 dark:border-[#252837]">
                                        <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span>Cetak Bukti Kas</span>
                                    </button>
                                </x-menu-aksi-tabel>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-400">Belum ada pengeluaran kas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarPengeluaran ?? [])" />
    </div>

    <!-- Modal Tambah Pengeluaran -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Catat Pengeluaran Kas Operasional</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ap.pengeluaran.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pengeluaran <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_pengeluaran" 
                            nilaiAwal="{{ date('Y-m-d') }}" 
                            placeholder="Pilih Tanggal Pengeluaran"
                            :wajib="true"
                            warnaFokus="rose"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Biaya <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kategori_pengeluaran"
                            placeholder="-- Pilih Kategori --"
                            :opsi="$opsiKategoriModal"
                            :wajib="true"
                            warnaFokus="rose"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bagan Akun COA <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_akun"
                            placeholder="-- Pilih Akun Beban --"
                            :opsi="$opsiAkunBeban"
                            :wajib="true"
                            warnaFokus="rose"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rekening Sumber Kas <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                        <x-dropdown-kustom 
                            nama="id_rekening_sumber"
                            placeholder="-- Kas Tunai Brankas --"
                            :opsi="$opsiRekeningSumber"
                            warnaFokus="rose"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Nominal Pengeluaran (Rp) <span class="text-rose-500">*</span></label>
                    <x-input-rupiah 
                        nama="total_nominal"
                        placeholder="1.500.000"
                        :wajib="true"
                        warnaFokus="rose"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Keperluan <span class="text-rose-500">*</span></label>
                    <textarea name="keterangan" rows="2" required placeholder="Pengisian Solar B35 dan e-toll rute Cikarang-Bandung..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pratinjau & Cetak Bukti Kas Keluar (BKK) -->
    <div x-show="modalCetakBKK" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalCetakBKK = false"
             class="bg-white text-slate-900 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl my-8 border border-slate-200">
            
            <!-- Toolbar Aksi Modal -->
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-slate-200 bg-slate-50 print:hidden">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-600"></span>
                    <span class="font-bold text-xs text-slate-800">Pratinjau Bukti Kas Keluar (BKK)</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="window.print()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Dokumen (Print)</span>
                    </button>
                    <button type="button" @click="modalCetakBKK = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Lembar Cetak Dokumen BKK -->
            <div class="p-8 space-y-6 text-slate-900 bg-white" id="area-cetak-bkk">
                <!-- Kop Surat -->
                <div class="flex items-start justify-between border-b-2 border-slate-900 pb-4">
                    <div class="flex items-center gap-3.5">
                        <img src="{{ asset('images/logo-pbj.png') }}" alt="Logo PT Putra Balkom Jaya" class="w-14 h-14 object-contain shrink-0" onerror="this.style.display='none'">
                        <div>
                            <h2 class="text-base font-black uppercase tracking-wider text-slate-950">PT PUTRA BALKOM JAYA</h2>
                            <p class="text-[10px] text-slate-600 leading-tight">Distributor Semen & Logistik Armada</p>
                            <p class="text-[9px] text-slate-500 mt-0.5">Bekasi, Jawa Barat · Telp: (021) 8990-1234</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-mono font-bold px-2.5 py-1 bg-slate-100 rounded border border-slate-300 inline-block">
                            BUKTI KAS KELUAR (BKK)
                        </div>
                        <div class="text-[10px] text-slate-500 mt-1 font-mono">Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
                    </div>
                </div>

                <!-- Nomor & Tanggal -->
                <div class="flex justify-between items-center text-xs pb-2 border-b border-slate-200">
                    <div>No. Bukti: <strong class="font-mono text-rose-600" x-text="detailBKK.nomor_pengeluaran"></strong></div>
                    <div>Tanggal: <span class="font-medium" x-text="detailBKK.tanggal"></span></div>
                </div>

                <!-- Detail Pengeluaran -->
                <div class="space-y-3 text-xs">
                    <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <div class="text-slate-500 text-[11px]">Kategori Beban:</div>
                            <div class="font-bold text-slate-900 mt-0.5" x-text="detailBKK.kategori"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 text-[11px]">Akun Akuntansi:</div>
                            <div class="font-mono font-semibold text-slate-800 mt-0.5" x-text="detailBKK.kode_akun + ' - ' + detailBKK.nama_akun"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 text-[11px]">Dibayarkan Dari:</div>
                            <div class="font-medium text-slate-800 mt-0.5" x-text="detailBKK.sumber_dana"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 text-[11px]">Total Nominal Dibayar:</div>
                            <div class="font-mono font-black text-rose-600 text-sm mt-0.5" x-text="'Rp ' + detailBKK.nominal"></div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="text-slate-500 text-[11px] mb-1">Keterangan / Uraian Keperluan:</div>
                        <div class="font-medium text-slate-900 italic" x-text="detailBKK.keterangan"></div>
                    </div>
                </div>

                <!-- Kolom Tanda Tangan -->
                <div class="grid grid-cols-3 gap-4 pt-6 text-center text-xs">
                    <div>
                        <div class="text-slate-500 mb-10">Dibayarkan Oleh (Kasir):</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[100px]">( Kasir AP )</div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-10">Diperiksa (SPV Keuangan):</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[100px]">( SPV Keuangan )</div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-10">Penerima Kas:</div>
                        <div class="font-bold border-b border-slate-400 pb-1 inline-block min-w-[100px]">( Penerima )</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #area-cetak-bkk, #area-cetak-bkk * {
        visibility: visible;
    }
    #area-cetak-bkk {
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
