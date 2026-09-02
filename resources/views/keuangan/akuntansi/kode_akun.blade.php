@extends('layouts.app')

@section('judul', 'Bagan Kode Akun (COA)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false, 
    bukaModalEdit: false, 
    bukaModalHapus: false,
    editData: {},
    hapusData: { kode_akun: '', nama_akun: '' },
    kodeAkunOtomatis: '',
    modeKode: 'gap',
    sedangBuatKode: false,
    keteranganKode: 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)',

    async buatKode(mode = 'gap') {
        this.modeKode = mode;
        this.sedangBuatKode = true;
        try {
            const res = await fetch(`{{ route('keuangan.akuntansi.kode_akun.buat_kode') }}?mode=${mode}`);
            const data = await res.json();
            if (data.status === 'sukses') {
                this.kodeAkunOtomatis = data.kode_otomatis;
                this.keteranganKode = data.keterangan;
            }
        } catch (e) {
            console.error('Gagal generate kode akun', e);
        } finally {
            this.sedangBuatKode = false;
        }
    }
}" x-init="buatKode('gap')">

    <!-- Flash Notification -->
    @if(session('sukses'))
        <div class="animasi-masuk p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('sukses') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    @if(session('gagal'))
        <div class="animasi-masuk p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs font-medium flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('gagal') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Header Modul Bagan Akun -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-violet-600 dark:text-violet-400 font-semibold font-mono uppercase tracking-wider mb-1">Akuntansi & Buku Besar · SPV Keuangan</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Data Kode Akun (Chart of Accounts)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Master kode akun akuntansi standar: Aktiva, Kewajiban, Modal, Pendapatan, dan Beban Operasional.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Akun</span>
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik COA -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Bagan Akun</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalAkun ?? 0 }} Akun</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Nilai Aktiva</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalAktiva ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Kewajiban (Hutang)</div>
            <div class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono">Rp {{ number_format($totalKewajiban ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Modal / Ekuitas</div>
            <div class="text-lg font-bold text-violet-600 dark:text-violet-400 mt-0.5 font-mono">Rp {{ number_format($totalModal ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tabel Data Bagan Akun (Kolom: kode_akun, nama_akun, tipe_akun, kelompok_akun, saldo) -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        @php
            $opsiFilterTipeCOA = [
                ['nilai' => '', 'label' => '-- Semua Tipe Akun --'],
                ['nilai' => 'Aktiva Lancar', 'label' => 'Aktiva Lancar'],
                ['nilai' => 'Aktiva Tetap', 'label' => 'Aktiva Tetap'],
                ['nilai' => 'Kewajiban Lancar', 'label' => 'Kewajiban Lancar'],
                ['nilai' => 'Modal', 'label' => 'Modal'],
                ['nilai' => 'Pendapatan', 'label' => 'Pendapatan'],
                ['nilai' => 'Harga Pokok Penjualan', 'label' => 'Harga Pokok Penjualan'],
                ['nilai' => 'Beban Operasional', 'label' => 'Beban Operasional'],
            ];
            $opsiTipeCOA = [
                ['nilai' => 'Aktiva Lancar', 'label' => 'Aktiva Lancar'],
                ['nilai' => 'Aktiva Tetap', 'label' => 'Aktiva Tetap'],
                ['nilai' => 'Kewajiban Lancar', 'label' => 'Kewajiban Lancar'],
                ['nilai' => 'Modal', 'label' => 'Modal'],
                ['nilai' => 'Pendapatan', 'label' => 'Pendapatan'],
                ['nilai' => 'Harga Pokok Penjualan', 'label' => 'Harga Pokok Penjualan'],
                ['nilai' => 'Beban Operasional', 'label' => 'Beban Operasional'],
            ];
            $opsiSaldoNormal = [
                ['nilai' => 'Debit', 'label' => 'Debit'],
                ['nilai' => 'Kredit', 'label' => 'Kredit'],
            ];
        @endphp

        <form method="GET" action="{{ route('keuangan.akuntansi.kode_akun') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari kode / nama / kelompok..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-48">
                    <x-dropdown-kustom 
                        nama="tipe" 
                        :nilaiAwal="$filterTipe ?? ''" 
                        placeholder="-- Semua Tipe Akun --" 
                        :opsi="$opsiFilterTipeCOA" 
                        warnaFokus="violet"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: data_kode_akun</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Akun</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Akun</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tipe Akun</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kelompok Akun</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Saldo</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi & Riwayat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarAkun ?? [] as $acc)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            
                            <!-- 1. Kode Akun -->
                            <td class="px-4 py-3 font-mono font-bold text-violet-600 dark:text-violet-400 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-md bg-violet-50 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/20">
                                    {{ $acc->kode_akun }}
                                </span>
                            </td>

                            <!-- 2. Nama Akun -->
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">
                                <div>{{ $acc->nama_akun }}</div>
                                <div class="text-[10px] text-slate-400 font-normal">Saldo Normal: {{ $acc->saldo_normal }}</div>
                            </td>

                            <!-- 3. Tipe Akun -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ in_array($acc->tipe_akun, ['Aktiva Lancar', 'Aktiva Tetap']) ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' : (str_contains($acc->tipe_akun, 'Kewajiban') ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20' : 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20') }}">
                                    {{ $acc->tipe_akun }}
                                </span>
                            </td>

                            <!-- 4. Kelompok Akun -->
                            <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-700 dark:text-slate-300">
                                <span class="px-2 py-0.5 text-[11px] rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $acc->kelompok_akun ?? '-' }}
                                </span>
                            </td>

                            <!-- 5. Saldo -->
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                Rp {{ number_format($acc->saldo_berjalan ?? $acc->saldo ?? 0, 0, ',', '.') }}
                            </td>

                            <!-- 6. Aksi & Riwayat -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="editData = {{ json_encode($acc) }}; bukaModalEdit = true" type="button"
                                            class="p-1 rounded-lg text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-500/10 font-semibold"
                                            title="Ubah Data Akun">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="hapusData = { kode_akun: '{{ $acc->kode_akun }}', nama_akun: '{{ $acc->nama_akun }}' }; bukaModalHapus = true" type="button"
                                            class="p-1 rounded-lg text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 font-semibold"
                                            title="Hapus Akun">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                <div class="mt-1 text-[10px] text-slate-400 dark:text-slate-500 font-mono" title="Terakhir diperbarui: {{ $acc->terakhir_diedit_waktu }}">
                                    🕒 {{ $acc->terakhir_diedit_relatif }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada bagan akun yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Akun -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Akun COA Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.akuntansi.kode_akun.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Akun</label>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="buatKode('gap')" :disabled="sedangBuatKode"
                                        :class="modeKode === 'gap' ? 'bg-violet-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                        class="text-[9px] font-semibold px-1.5 py-0.5 rounded transition-all">
                                    Daur Ulang
                                </button>
                                <button type="button" @click="buatKode('acak')" :disabled="sedangBuatKode"
                                        :class="modeKode === 'acak' ? 'bg-purple-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                        class="text-[9px] font-semibold px-1.5 py-0.5 rounded transition-all">
                                    Acak
                                </button>
                            </div>
                        </div>
                        <input type="text" name="kode_akun" x-model="kodeAkunOtomatis" required placeholder="1001"
                               class="w-full px-3 py-2 rounded-xl bg-violet-50/50 dark:bg-[#1C1E2A] border border-violet-200 dark:border-violet-900/50 text-violet-900 dark:text-violet-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                        <span class="text-[9px] text-slate-400 font-mono mt-0.5 block" x-text="keteranganKode"></span>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Saldo Normal</label>
                        <x-dropdown-kustom 
                            nama="saldo_normal"
                            placeholder="-- Pilih Posisi --"
                            :opsi="$opsiSaldoNormal"
                            :wajib="true"
                            warnaFokus="violet"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Akun</label>
                    <input type="text" name="nama_akun" required placeholder="Kas Kecil Cabang"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Akun</label>
                        <x-dropdown-kustom 
                            nama="tipe_akun"
                            placeholder="-- Pilih Tipe Akun --"
                            :opsi="$opsiTipeCOA"
                            :wajib="true"
                            warnaFokus="violet"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelompok Akun</label>
                        <input type="text" name="kelompok_akun" required placeholder="Kas & Setara Kas"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Saldo (Rp)</label>
                    <input type="number" name="saldo" value="0" min="0" step="100000"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30 font-mono">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition-all shadow-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Akun -->
    <div x-show="bukaModalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalEdit = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Edit Akun: <span class="font-mono text-violet-600" x-text="editData.kode_akun"></span></h3>
                <button @click="bukaModalEdit = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('keuangan/akuntansi/kode-akun') }}/' + editData.kode_akun" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Akun</label>
                    <input type="text" name="nama_akun" x-model="editData.nama_akun" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Akun</label>
                        <x-dropdown-kustom 
                            nama="tipe_akun"
                            placeholder="-- Pilih Tipe Akun --"
                            :opsi="$opsiTipeCOA"
                            :wajib="true"
                            warnaFokus="violet"
                            modelBind="editData.tipe_akun"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Saldo Normal</label>
                        <x-dropdown-kustom 
                            nama="saldo_normal"
                            placeholder="-- Pilih Posisi --"
                            :opsi="$opsiSaldoNormal"
                            :wajib="true"
                            warnaFokus="violet"
                            modelBind="editData.saldo_normal"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelompok Akun</label>
                    <input type="text" name="kelompok_akun" x-model="editData.kelompok_akun" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Saldo Berjalan (Rp)</label>
                    <input type="number" name="saldo" x-model="editData.saldo_berjalan" required step="100000" min="0"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-violet-500/30 font-mono">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalEdit = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Akun -->
    <div x-show="bukaModalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalHapus = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-sm overflow-hidden shadow-xl p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Hapus Akun COA?</h3>
            <p class="text-xs text-slate-500 mt-1">Yakin ingin menghapus akun <strong x-text="hapusData.nama_akun"></strong> (<span class="font-mono" x-text="hapusData.kode_akun"></span>)?</p>
            <form :action="'{{ url('keuangan/akuntansi/kode-akun') }}/' + hapusData.kode_akun" method="POST" class="mt-5 flex justify-center gap-2">
                @csrf
                @method('DELETE')
                <button @click="bukaModalHapus = false" type="button" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection
