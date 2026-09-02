@extends('layouts.app')

@section('judul', 'Master Data Wilayah & Zonasi')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false, 
    bukaModalEdit: false, 
    editData: {},
    kodeWilayahOtomatis: '{{ $kodeOtomatis }}',
    modeKode: 'gap',
    sedangBuatKode: false,
    keteranganKode: 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)',

    async buatKode(mode = 'gap') {
        this.modeKode = mode;
        this.sedangBuatKode = true;
        try {
            const res = await fetch(`{{ route('master.wilayah.buat_kode') }}?mode=${mode}`);
            const data = await res.json();
            if (data.status === 'sukses') {
                this.kodeWilayahOtomatis = data.kode_otomatis;
                this.keteranganKode = data.keterangan;
            }
        } catch (e) {
            console.error('Gagal generate kode wilayah', e);
        } finally {
            this.sedangBuatKode = false;
        }
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

    <!-- Header Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold font-mono uppercase tracking-wider mb-1">Master Data · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Master Wilayah & Area Distribusi</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemetaan zonasi wilayah pengiriman, penetapan area customer, dan rujukan tarif ongkos angkut.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Wilayah
            </button>
        </div>
    </div>

    <!-- Tabel Data Wilayah -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('master.wilayah.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-72">
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari kode / nama wilayah..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: data_wilayah</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Wilayah</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Wilayah & Zonasi</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Jumlah Mitra Toko</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarWilayah ?? [] as $w)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-emerald-600 dark:text-emerald-400">
                                {{ $w->kode_wilayah }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">
                                {{ $w->nama_wilayah }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold font-mono {{ $w->daftar_customer_count > 0 ? 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                    {{ $w->daftar_customer_count ?? 0 }} Toko
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="editData = {{ json_encode($w) }}; bukaModalEdit = true" type="button" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Edit</button>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <form method="POST" action="{{ route('master.wilayah.destroy', $w->kode_wilayah) }}" onsubmit="return confirm('Hapus wilayah {{ $w->nama_wilayah }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline font-medium">Hapus</button>
                                    </form>
                                </div>
                                <div class="mt-1 text-[10px] text-slate-400 dark:text-slate-500 font-mono" title="Terakhir diperbarui: {{ $w->terakhir_diedit_waktu }}">
                                    🕒 {{ $w->terakhir_diedit_relatif }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada data wilayah zonasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Wilayah -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Wilayah Zonasi Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('master.wilayah.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Wilayah</label>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="buatKode('gap')" :disabled="sedangBuatKode"
                                    :class="modeKode === 'gap' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
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
                    <input type="text" name="kode_wilayah" x-model="kodeWilayahOtomatis" required placeholder="WLY-001"
                           class="w-full px-3 py-2 rounded-xl bg-emerald-50/50 dark:bg-[#1C1E2A] border border-emerald-200 dark:border-emerald-900/50 text-emerald-900 dark:text-emerald-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    <span class="text-[9px] text-slate-400 font-mono mt-0.5 block" x-text="keteranganKode"></span>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Wilayah / Zonasi Distribusi</label>
                    <input type="text" name="nama_wilayah" required placeholder="Semarang Raya & Kendal"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Simpan Wilayah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Wilayah -->
    <div x-show="bukaModalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalEdit = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Edit Wilayah: <span class="font-mono text-emerald-600" x-text="editData.kode_wilayah"></span></h3>
                <button @click="bukaModalEdit = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('master/wilayah') }}/' + editData.kode_wilayah" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Wilayah / Zonasi Distribusi</label>
                    <input type="text" name="nama_wilayah" x-model="editData.nama_wilayah" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalEdit = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
