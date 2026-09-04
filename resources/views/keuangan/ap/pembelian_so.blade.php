@extends('layouts.app')

@section('judul', 'Pembelian SO Pabrik (AP)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false,
    jumlahZak: 500,
    hargaSatuan: 58000,
    hitungTotal() { return this.jumlahZak * this.hargaSatuan; }
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

    <!-- Header Modul Pembelian SO -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Payable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Pembelian Sales Order (SO) Pabrik</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan pemesanan semen ke produsen/pabrik (Plant) dan alokasi gudang distribusi.</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-show="!apakahReadOnly('ap_pembelian')" @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Buat Pembelian SO
            </button>
            <span x-show="apakahReadOnly('ap_pembelian')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Mode Lihat Saja (Read-Only)
            </span>
        </div>
    </div>

    <!-- Ringkasan Statistik SO -->
    <div class="wadah-bertingkat grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Transaksi SO</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalSO ?? 0 }} Transaksi</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Nilai Pembelian</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">Rp {{ number_format($totalNilaiSO ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Volume Zak</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">{{ number_format($totalZak ?? 0, 0, ',', '.') }} Zak</div>
        </div>
    </div>

    <!-- Tabel Data Pembelian SO -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarSO ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ap.pembelian_so') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterStatusSO = [
                    ['nilai' => '', 'label' => '-- Semua Status --'],
                    ['nilai' => 'disetujui', 'label' => 'Disetujui'],
                    ['nilai' => 'diproses', 'label' => 'Diproses'],
                    ['nilai' => 'dikirim', 'label' => 'Dikirim'],
                    ['nilai' => 'selesai', 'label' => 'Selesai'],
                ];
                $opsiCustomerSO = ($daftarCustomer ?? collect())->map(fn($c) => [
                    'nilai' => $c->kode_customer,
                    'label' => $c->nama_toko_bangunan
                ])->toArray();
                $opsiGudangSO = ($daftarGudang ?? collect())->map(fn($g) => [
                    'nilai' => $g->kode_gudang,
                    'label' => $g->nama_gudang,
                    'sub'   => 'Plant: ' . $g->plant
                ])->toArray();
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari no SO / customer / gudang..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="status" 
                        :nilaiAwal="$filterStatus ?? ''" 
                        placeholder="-- Semua Status --" 
                        :opsi="$opsiFilterStatusSO" 
                        warnaFokus="blue"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: pembelian_so</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. SO Pembelian</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Alokasi Customer</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Gudang / Plant</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Volume (Zak)</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Harga Beli / Zak</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Biaya</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarSO ?? [] as $so)
                        @php /** @var object $so */ @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">
                                {{ $so->nomor_so }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($so->tanggal_so)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $so->nama_toko_bangunan ?? $so->kode_customer }}</div>
                                <div class="text-[11px] text-slate-400">{{ $so->nama_pemilik ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $so->nama_gudang ?? $so->kode_gudang }}</div>
                                <div class="text-[11px] text-slate-400">Plant: {{ $so->plant ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900 dark:text-slate-100">
                                {{ number_format($so->jumlah_zak, 0, ',', '.') }} Zak
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums text-slate-600 dark:text-slate-400">
                                Rp {{ number_format($so->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($so->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($so->status_so === 'disetujui')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20">
                                        Disetujui
                                    </span>
                                @elseif($so->status_so === 'diproses')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                        Diproses
                                    </span>
                                @elseif($so->status_so === 'dikirim')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20">
                                        Dikirim
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$so->nomor_so" 
                                    labelSalin="Salin No"
                                    modulIzin="ap_so"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-slate-400">Belum ada transaksi pembelian SO pabrik tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarSO ?? [])" />
    </div>

    <!-- Modal Buat Pembelian SO -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Buat Pembelian SO Pabrik Semen</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ap.pembelian_so.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer / Alokasi Toko <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_customer"
                            placeholder="-- Pilih Customer --"
                            :opsi="$opsiCustomerSO"
                            :wajib="true"
                            warnaFokus="blue"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Gudang / Plant Pengambilan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_gudang"
                            placeholder="-- Pilih Gudang SO --"
                            :opsi="$opsiGudangSO"
                            :wajib="true"
                            warnaFokus="blue"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pemesanan SO <span class="text-rose-500">*</span></label>
                    <x-input-tanggal 
                        nama="tanggal_so" 
                        nilaiAwal="{{ date('Y-m-d') }}" 
                        placeholder="Pilih Tanggal SO"
                        :wajib="true"
                        warnaFokus="blue"
                    />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Volume Pemesanan (Zak) <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah_zak" x-model.number="jumlahZak" required min="1" step="1" placeholder="200" data-hanya-angka="true"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Satuan Pabrik (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_satuan"
                            modelBind="hargaSatuan"
                            placeholder="55.000"
                            :wajib="true"
                            warnaFokus="blue"
                        />
                    </div>
                </div>
                <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between">
                    <span class="font-semibold text-slate-600 dark:text-slate-400">Total Biaya Pembelian SO:</span>
                    <span class="font-mono font-bold text-sm text-blue-600 dark:text-blue-400">Rp <span x-text="new Intl.NumberFormat('id-ID').format(hitungTotal())"></span></span>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">Simpan SO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
