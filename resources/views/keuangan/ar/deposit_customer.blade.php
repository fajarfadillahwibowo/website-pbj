@extends('layouts.app')

@section('judul', 'Deposit Customer (AR)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTopUp: false,
    modalCetakTerbuka: false,
    detailCetak: {},
    cetakKwitansi(data) {
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

    @if(session('gagal'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('gagal') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Header Modul Deposit -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-sky-600 dark:text-sky-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Receivable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Saldo & Mutasi Deposit Customer</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan uang muka setoran (deposit) dan riwayat pemotongan otomatis saat penerbitan faktur.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTopUp = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Top Up Deposit
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Deposit -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Deposit Aktif</div>
            <div class="text-lg font-bold text-sky-600 dark:text-sky-400 mt-0.5 font-mono">Rp {{ number_format($totalDepositAktif ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Setoran Masuk</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Terpakai Faktur</div>
            <div class="text-lg font-bold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($totalTerpakai ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Mitra Memiliki Deposit</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalMitraDeposit ?? 0 }} Toko</div>
        </div>
    </div>

    <!-- Tabel Mutasi Deposit -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarMutasi ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ar.deposit') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterTipe = [
                    ['nilai' => '', 'label' => '-- Semua Mutasi --'],
                    ['nilai' => 'Masuk', 'label' => 'Setoran Masuk'],
                    ['nilai' => 'Keluar / Terpakai', 'label' => 'Keluar / Terpakai'],
                ];
                $opsiCustomerDeposit = ($daftarCustomer ?? collect())->map(fn($c) => [
                    'nilai' => $c->kode_customer,
                    'label' => $c->nama_toko_bangunan,
                    'sub'   => 'Saldo Saat Ini: Rp ' . number_format($c->saldo_deposit, 0, ',', '.')
                ])->toArray();
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari no bukti / nama customer..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="tipe" 
                        :nilaiAwal="$filterTipe ?? ''" 
                        placeholder="-- Semua Mutasi --" 
                        :opsi="$opsiFilterTipe" 
                        warnaFokus="sky"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="periode" 
                        :nilaiAwal="$filterPeriode ?? ''" 
                        placeholder="-- Semua Periode --" 
                        :opsi="$opsiPeriode ?? []" 
                        warnaFokus="sky"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                @if(($filterPeriode ?? '') === 'kustom')
                    <div class="flex items-center gap-1.5 w-full sm:w-auto mt-1 sm:mt-0">
                        <input type="date" name="tgl_mulai" value="{{ $filterTglMulai ?? '' }}" 
                               class="px-2.5 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500/30" title="Tanggal Mulai">
                        <span class="text-xs text-slate-400">s/d</span>
                        <input type="date" name="tgl_selesai" value="{{ $filterTglSelesai ?? '' }}" 
                               class="px-2.5 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500/30" title="Tanggal Selesai">
                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-xs">Terapkan</button>
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if(($jumlahFilterAktif ?? 0) > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/40">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>{{ $jumlahFilterAktif }} Filter Aktif</span>
                    </span>
                    <a href="{{ route('keuangan.ar.deposit') }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/40 border border-rose-200 dark:border-rose-800/40 rounded-lg transition-colors" title="Bersihkan seluruh filter">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset</span>
                    </a>
                @endif
                <span class="text-xs text-slate-400 font-mono hidden md:inline">Tabel: list_deposit</span>
            </div>
        </form>

        <div class="overflow-x-auto min-h-[260px] pb-12">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Bukti Mutasi</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer Toko</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Jenis Mutasi</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Jumlah Nominal</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Saldo Akhir</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Keterangan / Ref</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarMutasi ?? [] as $dep)
                        @php /** @var \App\Models\Keuangan\DepositCustomer $dep */ @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-sky-600 dark:text-sky-400">
                                {{ $dep->nomor_bukti_deposit }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($dep->tanggal_deposit)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $dep->customer->nama_toko_bangunan ?? $dep->kode_customer }}</div>
                                <div class="text-[11px] text-slate-400">{{ $dep->customer->nama_pemilik ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($dep->tipe_mutasi === 'Masuk')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Setoran Masuk
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                        Terpakai
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ $dep->tipe_mutasi === 'Masuk' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $dep->tipe_mutasi === 'Masuk' ? '+' : '-' }} Rp {{ number_format($dep->jumlah_nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($dep->saldo_akhir_deposit, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 truncate max-w-xs">
                                {{ $dep->keterangan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$dep->nomor_bukti_deposit" 
                                    labelSalin="Salin No"
                                    modulIzin="ar_deposit"
                                    :aksiCetak="'cetakKwitansi(' . json_encode([
                                        'nomor' => $dep->nomor_bukti_deposit,
                                        'tanggal' => date('d/m/Y', strtotime($dep->tanggal_deposit)),
                                        'customer' => $dep->customer->nama_toko_bangunan ?? $dep->kode_customer,
                                        'pemilik' => $dep->customer->nama_pemilik ?? '-',
                                        'tipe' => $dep->tipe_mutasi,
                                        'nominal' => number_format($dep->jumlah_nominal, 0, ',', '.'),
                                        'saldo_akhir' => number_format($dep->saldo_akhir_deposit, 0, ',', '.'),
                                        'keterangan' => $dep->keterangan ?? '-'
                                    ]) . ')'"
                                    labelCetak="Cetak Kwitansi"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-400">Belum ada riwayat mutasi deposit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarMutasi ?? [])" />
    </div>

    <!-- Modal Top Up Deposit -->
    <div x-show="bukaModalTopUp" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTopUp = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Top Up Saldo Deposit Customer</h3>
                <button @click="bukaModalTopUp = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ar.deposit.topup') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Customer Toko <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="kode_customer"
                        placeholder="-- Pilih Customer --"
                        :opsi="$opsiCustomerDeposit"
                        :wajib="true"
                        warnaFokus="sky"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Setoran <span class="text-rose-500">*</span></label>
                    <x-input-tanggal 
                        nama="tanggal_deposit" 
                        nilaiAwal="{{ date('Y-m-d') }}" 
                        placeholder="Pilih Tanggal Setoran"
                        :wajib="true"
                        warnaFokus="sky"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Nominal Top Up (Rp) <span class="text-rose-500">*</span></label>
                    <x-input-rupiah 
                        nama="jumlah_nominal"
                        placeholder="10.000.000"
                        :wajib="true"
                        warnaFokus="sky"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rekening Bank Penerima <span class="text-slate-400 font-normal text-[10px]">(Opsional - Default Kas Operasional)</span></label>
                    <x-dropdown-kustom 
                        nama="id_rekening_tujuan"
                        placeholder="-- Pilih Rekening Penerima (Kas / Bank) --"
                        :opsi="$opsiRekeningDeposit ?? []"
                        :wajib="false"
                        warnaFokus="sky"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Catatan Setoran <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <input type="text" name="keterangan" placeholder="Setoran via transfer Bank BCA..."
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTopUp = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">Simpan Top Up</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pratinjau & Cetak Kwitansi Resmi Deposit Customer -->
    <div x-show="modalCetakTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalCetakTerbuka = false"
             class="bg-white text-slate-900 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl my-8 border border-slate-200">
            
            <!-- Toolbar Aksi Modal (Disembunyikan saat cetak) -->
            <div class="flex items-center justify-between px-6 py-3.5 border-b border-slate-200 bg-slate-50 print:hidden">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-sky-600"></span>
                    <span class="font-bold text-xs text-slate-800">Pratinjau Kwitansi / Bukti Mutasi Deposit</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="window.print()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Kwitansi</span>
                    </button>
                    <button type="button" @click="modalCetakTerbuka = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Lembar Cetak Kwitansi -->
            <div class="p-8 space-y-6 text-xs text-slate-800 font-sans">
                <!-- Kop Resmi -->
                <div class="border-b-2 border-slate-900 pb-3 flex items-center justify-between">
                    <div>
                        <div class="text-base font-black tracking-wide text-slate-900 uppercase">PT PUTRA BALKOM JAYA</div>
                        <div class="text-[10px] text-slate-600">Distribusi Semen & Layanan Jasa Logistik Armada Nasional</div>
                        <div class="text-[9px] text-slate-400">Jl. Raya Surabaya - Rembang Km. 45, Jawa Timur</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded bg-sky-50 text-[11px] font-mono font-bold text-sky-800 border border-sky-200">
                            BUKTI MUTASI DEPOSIT
                        </span>
                        <div class="text-[10px] font-mono text-slate-500 mt-1" x-text="detailCetak.nomor"></div>
                    </div>
                </div>

                <!-- Detail Transaksi -->
                <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <div>
                        <span class="text-slate-500 block text-[10px]">Toko Bangunan / Proyek:</span>
                        <strong class="text-slate-900 font-bold text-sm" x-text="detailCetak.customer"></strong>
                        <div class="text-[10px] text-slate-500 mt-0.5">Pemilik: <span x-text="detailCetak.pemilik"></span></div>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-500 block text-[10px]">Tanggal Transaksi:</span>
                        <strong class="font-mono text-slate-900" x-text="detailCetak.tanggal"></strong>
                        <div class="mt-1">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono uppercase"
                                  :class="detailCetak.tipe === 'Masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                  x-text="detailCetak.tipe === 'Masuk' ? 'Setoran Deposit Masuk' : 'Pemotongan Faktur'"></span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 border border-slate-200 rounded-xl p-4">
                    <div class="flex justify-between py-1.5 border-b border-slate-200">
                        <span class="text-slate-500">Nominal Transaksi:</span>
                        <strong class="font-mono font-bold text-sm"
                                :class="detailCetak.tipe === 'Masuk' ? 'text-emerald-700' : 'text-rose-700'"
                                x-text="(detailCetak.tipe === 'Masuk' ? '+ Rp ' : '- Rp ') + (detailCetak.nominal || 0)"></strong>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-200">
                        <span class="text-slate-500">Saldo Akhir Deposit:</span>
                        <strong class="font-mono font-bold text-slate-900" x-text="'Rp ' + (detailCetak.saldo_akhir || 0)"></strong>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500">Keterangan / Berita:</span>
                        <span class="text-slate-700 font-medium" x-text="detailCetak.keterangan || '-'"></span>
                    </div>
                </div>

                <!-- Tanda Tangan Pengesahan -->
                <div class="pt-6 grid grid-cols-2 gap-8 text-center text-[10px]">
                    <div>
                        <div class="text-slate-500 mb-14">Penyetor / Customer:</div>
                        <div class="font-bold underline text-slate-900" x-text="detailCetak.pemilik || detailCetak.customer"></div>
                        <div class="text-slate-400">Pihak Toko</div>
                    </div>
                    <div>
                        <div class="text-slate-500 mb-14">Diterima & Disahkan:</div>
                        <div class="font-bold underline text-slate-900">( ........................................ )</div>
                        <div class="text-slate-400">Kasir / Bagian AR Keuangan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
