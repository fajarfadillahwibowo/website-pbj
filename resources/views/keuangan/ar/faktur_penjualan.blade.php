@extends('layouts.app')

@section('judul', 'Faktur Penjualan Semen (AR)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false, 
    metode: 'Kredit / Piutang',
    kodeBarang: '{{ $daftarBarang->first()->kode_barang ?? '' }}',
    petaBarang: {{ json_encode($daftarBarang->keyBy('kode_barang')) }},
    satuanBarang: '{{ $daftarBarang->first()->satuan_barang ?? 'Zak' }}',
    jumlahZak: 500,
    hargaSatuan: {{ (float)($daftarBarang->first()->harga_jual_standar ?? 70000) }},
    diskon: 0,
    init() {
        this.$watch('kodeBarang', val => {
            if (this.petaBarang && this.petaBarang[val]) {
                this.hargaSatuan = parseFloat(this.petaBarang[val].harga_jual_standar) || 0;
                this.satuanBarang = this.petaBarang[val].satuan_barang || 'Zak';
            }
        });
    },
    hitungBruto() { 
        return (parseFloat(this.jumlahZak) || 0) * (parseFloat(this.hargaSatuan) || 0); 
    },
    hitungNetto() { 
        return Math.max(0, this.hitungBruto() - (parseFloat(this.diskon) || 0)); 
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

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs">
            <div class="font-semibold mb-1">Terjadi kesalahan validasi:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Modul Faktur Penjualan -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Receivable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Faktur Penjualan Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Penerbitan faktur tagihan penjualan semen tunai, transfer, kredit tempo, atau potong deposit toko.</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-show="!apakahReadOnly('ar_faktur')" @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat Faktur Baru
            </button>
            <span x-show="apakahReadOnly('ar_faktur')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Mode Lihat Saja (Read-Only)
            </span>
        </div>
    </div>

    <!-- Ringkasan Statistik Penjualan -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Penjualan</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">Rp {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Penerimaan Lunas</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalLunas ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Sisa Piutang Berjalan</div>
            <div class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Faktur Diterbitkan</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">{{ $totalFaktur ?? count($daftarFaktur ?? []) }} Faktur</div>
        </div>
    </div>

    <!-- Tabel Data Faktur Penjualan -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarFaktur ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ar.faktur') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterStatusFaktur = [
                    ['nilai' => '', 'label' => '-- Semua Status --'],
                    ['nilai' => 'Lunas', 'label' => 'Lunas'],
                    ['nilai' => 'Belum Lunas', 'label' => 'Belum Lunas'],
                ];
                $opsiFilterMetodeFaktur = [
                    ['nilai' => '', 'label' => '-- Semua Metode --'],
                    ['nilai' => 'Tunai', 'label' => 'Tunai Kas'],
                    ['nilai' => 'Transfer', 'label' => 'Transfer Bank'],
                    ['nilai' => 'Kredit / Piutang', 'label' => 'Kredit Tempo'],
                    ['nilai' => 'Potong Deposit', 'label' => 'Potong Deposit'],
                ];
                $opsiCustomerFaktur = ($daftarCustomer ?? collect())->map(fn($c) => ['nilai' => $c->kode_customer, 'label' => $c->nama_toko_bangunan . ' (' . $c->kode_customer . ')'])->toArray();
                $opsiBarangFaktur = ($daftarBarang ?? collect())->map(fn($b) => [
                    'nilai' => $b->kode_barang, 
                    'label' => $b->nama_barang . ' (' . ($b->satuan_barang ?? 'Zak') . ')',
                    'sub'   => 'Harga: Rp ' . number_format($b->harga_jual_standar, 0, ',', '.') . ' / ' . ($b->satuan_barang ?? 'Zak')
                ])->toArray();
                $opsiMetodeModal = [
                    ['nilai' => 'Kredit / Piutang', 'label' => 'Kredit Tempo'],
                    ['nilai' => 'Tunai', 'label' => 'Tunai Kas'],
                    ['nilai' => 'Transfer', 'label' => 'Transfer Bank'],
                    ['nilai' => 'Potong Deposit', 'label' => 'Potong Deposit'],
                ];
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari no faktur / customer / semen..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-40">
                    <x-dropdown-kustom 
                        nama="status" 
                        :nilaiAwal="$filterStatus ?? ''" 
                        placeholder="-- Semua Status --" 
                        :opsi="$opsiFilterStatusFaktur" 
                        warnaFokus="emerald"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="metode" 
                        :nilaiAwal="$filterMetode ?? ''" 
                        placeholder="-- Semua Metode --" 
                        :opsi="$opsiFilterMetodeFaktur" 
                        warnaFokus="emerald"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: penjualan</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th x-show="!apakahReadOnly('ar_faktur')" class="w-10 px-3 py-2.5 text-center">
                            <input type="checkbox" 
                                   @change="togglePilihSemua({{ json_encode(($daftarFaktur ?? collect())->pluck('nomor_faktur')->toArray()) }})"
                                   :checked="apakahSemuaTerpilih({{ json_encode(($daftarFaktur ?? collect())->pluck('nomor_faktur')->toArray()) }})"
                                   class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-blue-600 focus:ring-blue-500/30 cursor-pointer">
                        </th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Faktur</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer / Toko</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Komoditas & Kuantitas</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Netto</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Sisa Piutang</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Metode Bayar</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarFaktur ?? [] as $faktur)
                        @php /** @var \App\Models\Keuangan\FakturPenjualan $faktur */ @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            :class="{ 'bg-blue-50/50 dark:bg-blue-950/20': apakahTerpilih('{{ $faktur->nomor_faktur }}') }"
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td x-show="!apakahReadOnly('ar_faktur')" class="w-10 px-3 py-3 text-center">
                                <input type="checkbox" 
                                       :checked="apakahTerpilih('{{ $faktur->nomor_faktur }}')"
                                       @change="togglePilih('{{ $faktur->nomor_faktur }}')"
                                       class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-blue-600 focus:ring-blue-500/30 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">
                                {{ $faktur->nomor_faktur }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($faktur->tanggal_penjualan)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $faktur->customer->nama_toko_bangunan ?? $faktur->kode_customer }}</div>
                                <div class="text-[11px] text-slate-400">{{ $faktur->customer->nama_pemilik ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $faktur->nama_barang ?? ($faktur->barang->nama_barang ?? 'Semen Portland (PCC)') }}</div>
                                <div class="text-[11px] font-mono text-slate-500 dark:text-slate-400">
                                    {{ number_format($faktur->jumlah_zak ?? 0, 0, ',', '.') }} {{ $faktur->satuan_barang ?? 'Zak' }} 
                                    @ Rp {{ number_format($faktur->harga_satuan ?? 0, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100">
                                Rp {{ number_format($faktur->total_netto, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold {{ $faktur->sisa_piutang > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                                Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold font-mono bg-slate-100 dark:bg-[#1C1E2A] text-slate-700 dark:text-slate-300">
                                    {{ $faktur->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($faktur->status_pembayaran === 'Lunas')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-slate-500">
                                {{ $faktur->tanggal_jatuh_tempo ? date('d/m/Y', strtotime($faktur->tanggal_jatuh_tempo)) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$faktur->nomor_faktur"
                                    labelSalin="Salin No"
                                    modulIzin="ar_faktur"
                                    :urlDetail="route('keuangan.ar.faktur.cetak', $faktur->nomor_faktur)"
                                    labelDetail="Cetak"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-6 text-center text-slate-400">Belum ada faktur penjualan tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarFaktur ?? [])" />

        <!-- Bar Aksi Massal (Multi-Select Floating Bar) -->
        <x-bar-aksi-massal labelItem="faktur" warna="blue" modulIzin="ar_faktur" />
    </div>

    <!-- Modal Tambah Faktur Penjualan -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Penerbitan Faktur Penjualan Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ar.faktur.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <!-- Nilai Bruto Terkalkulasi Otomatis -->
                <input type="hidden" name="total_bruto" :value="hitungBruto()">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Toko Bangunan / Proyek Tujuan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_toko"
                            placeholder="-- Pilih Toko / Proyek --"
                            :opsi="$opsiToko ?? $opsiCustomerFaktur"
                            :wajib="true"
                            warnaFokus="emerald"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_penjualan" 
                            nilaiAwal="{{ date('Y-m-d') }}" 
                            placeholder="Pilih Tanggal"
                            :wajib="true"
                            warnaFokus="emerald"
                        />
                    </div>
                </div>

                <!-- Pemilihan Produk Semen & Kuantitas -->
                <div class="p-3.5 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837] space-y-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Produk Semen <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_barang"
                            placeholder="-- Pilih Jenis Semen --"
                            :opsi="$opsiBarangFaktur"
                            :wajib="true"
                            warnaFokus="emerald"
                            modelBind="kodeBarang"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-3 items-end">
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Kuantitas (<span x-text="satuanBarang">Zak</span>) <span class="text-rose-500">*</span></label>
                            <div class="relative flex items-center rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/30 transition-all overflow-hidden h-[38px]">
                                <input type="text"
                                       name="jumlah_zak"
                                       inputmode="numeric"
                                       :value="jumlahZak"
                                       @input="let val = $event.target.value.replace(/[^0-9]/g, ''); jumlahZak = val ? parseInt(val, 10) : 0;"
                                       @keydown="if(!/^[0-9]$/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End', 'Enter'].includes($event.key) && !$event.ctrlKey && !$event.metaKey) { $event.preventDefault(); }"
                                       placeholder="500"
                                       required
                                       class="w-full px-3 py-2 text-xs font-mono font-bold text-slate-900 dark:text-slate-100 placeholder-slate-400 bg-transparent border-none focus:outline-none focus:ring-0 text-left">
                                <span class="px-3 py-2 text-[11px] font-bold text-slate-500 dark:text-slate-400 bg-[#F4F6F9] dark:bg-[#1C1E2A] border-l border-[#E2E8F0] dark:border-[#252837] select-none shrink-0" x-text="satuanBarang">
                                    Zak
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Satuan Jual <span class="text-slate-400 font-normal text-[10px]">(Master Data)</span></label>
                            <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-100/90 dark:bg-[#14161F]/80 border border-slate-200 dark:border-[#252837] text-xs h-[38px]">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-[10px] font-bold text-slate-400 font-mono">Rp</span>
                                    <span class="font-mono font-bold text-slate-900 dark:text-slate-100 text-xs" x-text="new Intl.NumberFormat('id-ID').format(hargaSatuan)"></span>
                                    <span class="text-[10px] text-slate-400 font-medium">/ <span x-text="satuanBarang">Zak</span></span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/30">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Otomatis
                                </span>
                            </div>
                            <input type="hidden" name="harga_satuan" :value="hargaSatuan">
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1.5 text-[11px] text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-[#252837]">
                        <span>Subtotal Bruto (<span x-text="new Intl.NumberFormat('id-ID').format(jumlahZak)"></span> <span x-text="satuanBarang">Zak</span>):</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200">Rp <span x-text="new Intl.NumberFormat('id-ID').format(hitungBruto())"></span></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div :class="(metode === 'Kredit / Piutang' || metode === 'Kredit') ? 'col-span-1' : 'col-span-1 sm:col-span-2'">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Metode Pembayaran <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="metode_pembayaran"
                            placeholder="-- Pilih Metode --"
                            :opsi="$opsiMetodeModal"
                            :wajib="true"
                            warnaFokus="emerald"
                            modelBind="metode"
                        />
                    </div>
                    <div x-show="metode === 'Kredit / Piutang' || metode === 'Kredit'" x-cloak class="col-span-1">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Jatuh Tempo <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                        <x-input-tanggal 
                            nama="jatuh_tempo" 
                            nilaiAwal="{{ date('Y-m-d', strtotime('+30 days')) }}" 
                            placeholder="Pilih Jatuh Tempo"
                            :wajib="false"
                            warnaFokus="emerald"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Potongan Diskon (Rp) <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <x-input-rupiah 
                        nama="diskon"
                        modelBind="diskon"
                        placeholder="0"
                        :wajib="false"
                        warnaFokus="emerald"
                    />
                </div>

                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/20 rounded-xl border border-emerald-200 dark:border-emerald-800/30 flex items-center justify-between">
                    <span class="font-semibold text-emerald-800 dark:text-emerald-300">Total Netto Tagihan Faktur:</span>
                    <span class="font-mono font-bold text-sm text-emerald-600 dark:text-emerald-400">Rp <span x-text="new Intl.NumberFormat('id-ID').format(hitungNetto())"></span></span>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Terbitkan Faktur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
