@extends('layouts.app')

@section('judul', 'Master Data Customer & Toko Bangunan')

@section('konten')
<div class="space-y-5" x-data="{ bukaModalTambah: false, bukaModalEdit: false, editData: {} }">
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

    <!-- Header Modul Customer -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-blue-600 dark:text-blue-400 font-semibold font-mono uppercase tracking-wider mb-1">Master Data · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Data Customer & Toko Bangunan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar mitra toko, plafon limit piutang, dan saldo deposit berjalan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Customer
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Customer -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Mitra Toko</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalCustomer ?? count($daftarCustomer ?? []) }} Toko</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Plafon Kredit</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">Rp {{ number_format($totalPlafon ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Piutang Berjalan</div>
            <div class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Deposit Customer</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalDeposit ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tabel Data Customer -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('master.customer.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari kode / nama toko / pemilik..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <select name="wilayah" onchange="this.form.submit()" class="px-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300">
                    <option value="">-- Semua Wilayah --</option>
                    @foreach($daftarWilayah ?? [] as $w)
                        <option value="{{ $w->kode_wilayah }}" {{ ($filterWilayah ?? '') === $w->kode_wilayah ? 'selected' : '' }}>
                            {{ $w->nama_wilayah }}
                        </option>
                    @endforeach
                </select>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: data_customer</span>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Toko & Pemilik</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Wilayah & Kontak</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Plafon Kredit</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Piutang Berjalan</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Saldo Deposit</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarCustomer ?? [] as $cust)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">
                                {{ $cust->kode_customer }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $cust->nama_toko_bangunan }}</div>
                                <div class="text-[11px] text-slate-400">Pemilik: {{ $cust->nama_pemilik }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $cust->wilayah->nama_wilayah ?? $cust->kode_wilayah }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $cust->no_hp }} · {{ $cust->alamat }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums text-slate-700 dark:text-slate-300">
                                Rp {{ number_format($cust->plafon_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold {{ $cust->saldo_piutang > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                                Rp {{ number_format($cust->saldo_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($cust->saldo_deposit, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="editData = {{ json_encode($cust) }}; bukaModalEdit = true" type="button" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</button>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <form method="POST" action="{{ route('master.customer.destroy', $cust->kode_customer) }}" onsubmit="return confirm('Hapus data customer {{ $cust->nama_toko_bangunan }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline font-medium">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Belum ada data customer yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Customer -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Data Customer Toko Bangunan</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('master.customer.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Customer</label>
                        <input type="text" name="kode_customer" required placeholder="CUST-006"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah Zonasi</label>
                        <select name="kode_wilayah" required class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                            <option value="">-- Pilih Wilayah --</option>
                            @foreach($daftarWilayah ?? [] as $w)
                                <option value="{{ $w->kode_wilayah }}">{{ $w->nama_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko Bangunan</label>
                        <input type="text" name="nama_toko_bangunan" required placeholder="TB Sumber Rezeki"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Toko</label>
                        <input type="text" name="nama_pemilik" required placeholder="H. Anwar Sanusi"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Handphone / WhatsApp</label>
                        <input type="text" name="no_hp" required placeholder="0812-xxxx-xxxx"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Plafon Limit Piutang (Rp)</label>
                        <input type="number" name="plafon_piutang" required value="50000000" min="0" step="1000000"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap Toko</label>
                    <textarea name="alamat" rows="2" required placeholder="Jl. Raya Utama No. ..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Customer -->
    <div x-show="bukaModalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalEdit = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Edit Data Customer: <span class="font-mono text-blue-600" x-text="editData.kode_customer"></span></h3>
                <button @click="bukaModalEdit = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('master/customer') }}/' + editData.kode_customer" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Toko Bangunan</label>
                        <input type="text" name="nama_toko_bangunan" x-model="editData.nama_toko_bangunan" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah Zonasi</label>
                        <select name="kode_wilayah" x-model="editData.kode_wilayah" required class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                            @foreach($daftarWilayah ?? [] as $w)
                                <option value="{{ $w->kode_wilayah }}">{{ $w->nama_wilayah }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik Toko</label>
                        <input type="text" name="nama_pemilik" x-model="editData.nama_pemilik" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Handphone</label>
                        <input type="text" name="no_hp" x-model="editData.no_hp" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Plafon Limit Piutang (Rp)</label>
                    <input type="number" name="plafon_piutang" x-model="editData.plafon_piutang" required min="0" step="1000000"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap Toko</label>
                    <textarea name="alamat" x-model="editData.alamat" rows="2" required
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalEdit = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
