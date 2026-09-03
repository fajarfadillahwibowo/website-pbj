@extends('layouts.app')

@section('judul', 'Daftar Rilisan Biaya Operasional (AP)')

@section('konten')
<div class="space-y-5" x-data="{ bukaModalTambah: false }">
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

    <!-- Header Modul Rilisan Kas Bon -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-rose-600 dark:text-rose-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Payable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Rilisan Kas Bon & Uang Jalan Supir</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan rilis kas operasional driver supir, kas bon talangan, dan verifikasi SPV.</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-show="!apakahReadOnly('ap_rilisan')" @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Rilis Uang Jalan Supir
            </button>
            <span x-show="apakahReadOnly('ap_rilisan')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Mode Lihat Saja (Read-Only)
            </span>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="wadah-bertingkat grid grid-cols-2 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Dana Rilisan Operasional</div>
            <div class="text-lg font-bold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($totalRilisan ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Jumlah Rilisan Diterbitkan</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $jumlahTransaksi ?? 0 }} Transaksi</div>
        </div>
    </div>

    <!-- Tabel Data Rilisan -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ap.rilisan') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-64">
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nomor bukti / keterangan..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: pengeluaran (Akun 1107)</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Bukti Rilis</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Keterangan / Penerima</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Sumber Dana</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Nominal Rilis</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarRilisan ?? [] as $r)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-rose-600 dark:text-rose-400">
                                {{ $r->nomor_pengeluaran }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($r->tanggal_pengeluaran)) }}
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">
                                {{ $r->keterangan }}
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                {{ $r->nama_bank ?? 'Kas Tunai' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($r->total_nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold font-mono bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                    Disetujui SPV
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada riwayat rilisan kas bon.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @php
        $opsiDriver = ($daftarDriver ?? collect())->map(fn($d) => [
            'nilai' => $d->kode_karyawan,
            'label' => $d->nama_karyawan,
            'sub'   => 'ID: ' . $d->kode_karyawan
        ])->toArray();
        $opsiRekeningRilisan = array_merge([['nilai' => '', 'label' => 'Kas Tunai']], ($daftarRekening ?? collect())->map(fn($r) => [
            'nilai' => $r->id_rekening,
            'label' => $r->nama_bank,
            'sub'   => 'Saldo: Rp ' . number_format($r->saldo_rekening, 0, ',', '.')
        ])->toArray());
    @endphp

    <!-- Modal Rilis Uang Jalan -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Rilisan Uang Jalan Supir / Kas Bon</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ap.rilisan.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Driver Supir <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="kode_driver"
                        placeholder="-- Pilih Driver --"
                        :opsi="$opsiDriver"
                        :wajib="true"
                        warnaFokus="rose"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Rilisan <span class="text-rose-500">*</span></label>
                    <x-input-tanggal 
                        nama="tanggal_rilisan" 
                        nilaiAwal="{{ date('Y-m-d') }}" 
                        placeholder="Pilih Tanggal Rilisan"
                        :wajib="true"
                        warnaFokus="rose"
                    />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nominal Uang Jalan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal" required min="0" step="any" placeholder="1500000"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30 font-mono font-semibold text-sm">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rekening Sumber Kas <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                        <x-dropdown-kustom 
                            nama="id_rekening_sumber"
                            placeholder="-- Kas Tunai --"
                            :opsi="$opsiRekeningRilisan"
                            warnaFokus="rose"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan / Rute Pengiriman <span class="text-rose-500">*</span></label>
                    <input type="text" name="keterangan" required placeholder="Uang jalan pengiriman 500 zak ke Cikarang..."
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">Simpan Rilisan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
