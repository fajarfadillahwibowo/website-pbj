@extends('layouts.app')

@section('judul', 'Master Data Produk & Semen')

@section('konten')
<div class="space-y-5" 
     x-data="{ 
         bukaModalTambah: false, 
         bukaModalEdit: false, 
         editData: {},
         semuaBarang: @js($daftarBarang->keyBy('kode_barang')),
         bukaEditBarang(kode) {
             if (this.semuaBarang && this.semuaBarang[kode]) {
                 this.editData = Object.assign({}, this.semuaBarang[kode]);
                 this.bukaModalEdit = true;
                 window.dispatchEvent(new CustomEvent('set-nilai-jenis_barang', { detail: this.editData.jenis_barang }));
             }
         }
     }"
     @buka-edit-barang.window="bukaEditBarang($event.detail)">
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

    <!-- Header Modul Barang -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-amber-600 dark:text-amber-400 font-semibold font-mono uppercase tracking-wider mb-1">Master Data · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Katalog Produk Semen</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar semen kemasan zak, curah, harga tebus pabrik, dan standar harga jual.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Produk Semen
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Produk -->
    <div class="wadah-bertingkat grid grid-cols-3 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Produk Terdaftar</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalBarang ?? count($daftarBarang ?? []) }} SKU</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Semen Kemasan Zak</div>
            <div class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono">{{ $totalZak ?? 0 }} Produk</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Semen Curah (Tonase)</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">{{ $totalCurah ?? 0 }} Produk</div>
        </div>
    </div>

    <!-- Tabel Data Barang -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarBarang ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('master.barang.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterJenis = [
                    ['nilai' => '', 'label' => '-- Semua Jenis --'],
                    ['nilai' => 'Zak', 'label' => 'Kemasan Zak'],
                    ['nilai' => 'Curah', 'label' => 'Curah (Tonase)'],
                ];
                $opsiJenis = [
                    ['nilai' => 'Zak', 'label' => 'Kemasan Zak'],
                    ['nilai' => 'Curah', 'label' => 'Curah (Tonase)'],
                ];
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari kode / nama semen..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-44">
                    <x-dropdown-kustom 
                        nama="jenis" 
                        :nilaiAwal="$filterJenis ?? ''" 
                        placeholder="-- Semua Jenis --" 
                        :opsi="$opsiFilterJenis" 
                        warnaFokus="amber"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <div class="text-[11px] text-slate-400 font-mono">
                Total Produk: <span class="font-bold text-slate-700 dark:text-slate-300">{{ count($daftarBarang ?? []) }}</span>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Produk</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Produk Semen</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jenis & Satuan</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Harga Beli Pabrik</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Harga Jual Standar</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Margin Estimasi</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarBarang ?? [] as $b)
                        @php
                            /** @var \App\Models\Master\Barang $b */
                            $margin = $b->harga_jual_standar - $b->harga_pokok;
                        @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-amber-600 dark:text-amber-400">
                                {{ $b->kode_barang }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">
                                {{ $b->nama_barang }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $b->jenis_barang === 'Zak' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/20' : 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/20' }}">
                                    {{ $b->jenis_barang }} ({{ $b->satuan_barang }})
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums text-slate-500 dark:text-slate-400">
                                Rp {{ number_format($b->harga_pokok, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-semibold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($b->harga_jual_standar, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums text-blue-600 dark:text-blue-400 font-medium">
                                + Rp {{ number_format($margin, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$b->kode_barang"
                                    labelSalin="Salin Kode"
                                    modulIzin="master_barang"
                                    aksiEdit="$dispatch('buka-edit-barang', '{{ $b->kode_barang }}')"
                                    labelEdit="Edit"
                                    aksiHapus="{{ route('master.barang.destroy', $b->kode_barang) }}"
                                    labelHapus="Hapus"
                                    pesanHapus="Apakah Anda yakin ingin menghapus produk semen {{ $b->nama_barang }} ({{ $b->kode_barang }})?"
                                />

                                <!-- Riwayat Terakhir Dibuat / Diedit Real-Time -->
                                <x-waktu-relatif :diperbaruiPada="$b->diperbarui_pada ?? null" :dibuatPada="$b->dibuat_pada ?? null" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Belum ada data produk semen yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Toolbar Paginasi & Baris per Halaman -->
        <x-paginasi-tabel :totalData="count($daftarBarang ?? [])" />
    </div>

    <!-- Modal Tambah Produk -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Produk Semen Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('master.barang.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Produk <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold px-1.5 py-0.5 bg-amber-50 dark:bg-amber-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_barang" value="{{ $kodeOtomatis }}" required placeholder="SMN-001"
                               class="w-full px-3 py-2 rounded-xl bg-amber-50/50 dark:bg-[#1C1E2A] border border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kemasan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="jenis_barang"
                            placeholder="-- Pilih Jenis --"
                            :opsi="$opsiJenis"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Produk Semen <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_barang" required placeholder="Semen Tonasa PCC 40 Kg"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="satuan_barang" required value="Zak" placeholder="Zak / Ton"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Beli Pabrik (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_pokok"
                            placeholder="58.000"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Jual Standar (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_jual_standar"
                            placeholder="64.500"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Produk -->
    <div x-show="bukaModalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalEdit = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Edit Produk: <span class="font-mono text-amber-600" x-text="editData.kode_barang"></span></h3>
                <button @click="bukaModalEdit = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('master/barang') }}/' + editData.kode_barang" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis Kemasan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="jenis_barang"
                            placeholder="-- Pilih Jenis --"
                            :opsi="$opsiJenis"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="editData.jenis_barang"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan <span class="text-rose-500">*</span></label>
                        <input type="text" name="satuan_barang" x-model="editData.satuan_barang" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Produk Semen <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_barang" x-model="editData.nama_barang" required
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Beli Pabrik (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_pokok"
                            modelBind="editData.harga_pokok"
                            placeholder="58.000"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Jual Standar (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="harga_jual_standar"
                            modelBind="editData.harga_jual_standar"
                            placeholder="64.500"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalEdit = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
