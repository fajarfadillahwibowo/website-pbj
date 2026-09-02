@extends('layouts.app')

@section('judul', 'Pengeluaran Kas Operasional (AP)')

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

    <!-- Header Modul Pengeluaran Kas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-rose-600 dark:text-rose-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Payable · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Pengeluaran Kas & Biaya Operasional</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pencatatan pengeluaran kas kantor, bahan bakar & tol armada truk, dan operasional harian terintegrasi COA.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Catat Pengeluaran
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Pengeluaran -->
    <div class="grid grid-cols-3 gap-3">
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
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ap.pengeluaran') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nomor bukti / keterangan..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <select name="kategori" onchange="this.form.submit()" class="px-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300">
                    <option value="">-- Semua Kategori --</option>
                    <option value="BBM & Tol Armada" {{ ($filterKategori ?? '') === 'BBM & Tol Armada' ? 'selected' : '' }}>BBM & Tol Armada</option>
                    <option value="Operasional Kantor" {{ ($filterKategori ?? '') === 'Operasional Kantor' ? 'selected' : '' }}>Operasional Kantor</option>
                    <option value="Servis Truk" {{ ($filterKategori ?? '') === 'Servis Truk' ? 'selected' : '' }}>Servis Truk</option>
                </select>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: pengeluaran</span>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Transaksi</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kategori & Akun Beban</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Rekening Sumber</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Nominal</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Keterangan</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarPengeluaran ?? [] as $out)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-rose-600 dark:text-rose-400">
                                {{ $out->nomor_pengeluaran }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">
                                {{ date('d/m/Y', strtotime($out->tanggal_pengeluaran)) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $out->kategori_pengeluaran }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $out->kode_akun }} - {{ $out->nama_akun ?? 'Beban Operasional' }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                {{ $out->nama_bank ?? 'Kas Tunai' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($out->total_nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 truncate max-w-xs">
                                {{ $out->keterangan }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold font-mono bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                    Disetujui
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Belum ada catatan pengeluaran kas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Catat Pengeluaran -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Catat Pengeluaran Kas Operasional</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ap.pengeluaran.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pengeluaran</label>
                        <input type="date" name="tanggal_pengeluaran" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Biaya</label>
                        <select name="kategori_pengeluaran" required class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                            <option value="BBM & Tol Armada">BBM & Tol Armada</option>
                            <option value="Operasional Kantor">Operasional Kantor / ATK</option>
                            <option value="Servis Truk">Servis Kendaraan / Bengkel</option>
                            <option value="Konsumsi & Umum">Konsumsi & Operasional Lapangan</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bagan Akun COA</label>
                        <select name="kode_akun" required class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                            @foreach($daftarAkunBeban ?? [] as $akun)
                                <option value="{{ $akun->kode_akun }}">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Rekening Sumber Kas</label>
                        <select name="id_rekening_sumber" class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                            <option value="">-- Kas Tunai Brankas --</option>
                            @foreach($daftarRekening ?? [] as $rek)
                                <option value="{{ $rek->id_rekening }}">{{ $rek->nama_bank }} (Saldo: Rp {{ number_format($rek->saldo_rekening, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Nominal Pengeluaran (Rp)</label>
                    <input type="number" name="total_nominal" required min="1000" step="10000" placeholder="1500000"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-500/30 font-mono font-semibold text-sm">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Keperluan</label>
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
</div>
@endsection
