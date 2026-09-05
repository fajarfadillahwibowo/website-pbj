@extends('layouts.app')

@section('judul', 'Master Data Karyawan & Seluruh Pegawai')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false, 
    bukaModalEdit: false, 
    editData: {},
    petaKodeOtomatis: @js($kodePerJabatan),
    formTambah: {
        kode_karyawan: '{{ $kodeOtomatis }}',
        kategori_karyawan: 'staf',
        nama_karyawan: '',
        id_jabatan: '{{ $daftarJabatan->first()->id_jabatan ?? 2 }}',
        no_identitas: '',
        no_hp: '',
        status_karyawan: 'aktif',
        tanggal_bergabung: '{{ date('Y-m-d') }}',
        alamat: ''
    },
    sinkronkanKodeOtomatis() {
        if (this.formTambah.kategori_karyawan === 'driver') {
            if (this.petaKodeOtomatis && this.petaKodeOtomatis['driver']) {
                this.formTambah.kode_karyawan = this.petaKodeOtomatis['driver'];
            }
        } else {
            let jId = this.formTambah.id_jabatan;
            if (this.petaKodeOtomatis && this.petaKodeOtomatis[jId]) {
                this.formTambah.kode_karyawan = this.petaKodeOtomatis[jId];
            }
        }
    },
    init() {
        this.$watch('formTambah.id_jabatan', () => this.sinkronkanKodeOtomatis());
        this.$watch('formTambah.kategori_karyawan', () => this.sinkronkanKodeOtomatis());
    },
    semuaKaryawan: @js($daftarKaryawan->keyBy('kode_karyawan')),
    bukaEditKaryawan(kode) {
        if (this.semuaKaryawan && this.semuaKaryawan[kode]) {
            this.editData = Object.assign({}, this.semuaKaryawan[kode]);
            this.bukaModalEdit = true;
            window.dispatchEvent(new CustomEvent('set-nilai-kategori_karyawan_edit', { detail: this.editData.kategori_karyawan }));
            window.dispatchEvent(new CustomEvent('set-nilai-id_jabatan_edit', { detail: this.editData.id_jabatan }));
            window.dispatchEvent(new CustomEvent('set-nilai-status_karyawan_edit', { detail: this.editData.status_karyawan }));
        }
    }
}" @buka-edit-karyawan.window="bukaEditKaryawan($event.detail)">
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

    <!-- Header Modul -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Master Data · Dev 1</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Master Personil Karyawan & Driver</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Database terpadu seluruh pegawai: Staf Kantor, Driver Supir Tronton/Colt Diesel, Gudang, dan Teknisi.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Karyawan Baru
            </button>
        </div>
    </div>

    <!-- Filter Tab Kategori Karyawan & Pencarian -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarKaryawan ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Tab Bar Kategori -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 border-b border-[#E2E8F0] dark:border-[#252837] bg-[#F8FAFC] dark:bg-[#1C1E2A]">
            <div class="flex items-center gap-1.5 overflow-x-auto text-xs">
                <a href="{{ route('master.karyawan.index') }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ empty($filterKategori) ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Semua Karyawan ({{ $totalSemua }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'staf']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'staf' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Staf Kantor ({{ $totalStaf }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'driver']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'driver' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Driver Supir ({{ $totalDriver }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'gudang']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'gudang' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Gudang ({{ $totalGudang }})
                </a>
                <a href="{{ route('master.karyawan.index', ['kategori' => 'teknisi']) }}"
                   class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ $filterKategori === 'teknisi' ? 'bg-indigo-600 text-white font-semibold shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-[#252837]' }}">
                    Teknisi Bengkel ({{ $totalTeknisi }})
                </a>
            </div>

            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('master.karyawan.index') }}" class="relative w-full sm:w-64">
                @if($filterKategori)
                    <input type="hidden" name="kategori" value="{{ $filterKategori }}">
                @endif
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nama / kode / no hp..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
        </div>

        <!-- Tabel Data Karyawan Lengkap -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Karyawan</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Lengkap & Kontak</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jabatan Peran</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Identitas (KTP)</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Alamat Domisili</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarKaryawan as $karyawan)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400">
                                {{ $karyawan->kode_karyawan }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $karyawan->nama_karyawan }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $karyawan->no_hp }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400">
                                    {{ $karyawan->jabatan->nama_jabatan ?? 'Staf' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($karyawan->kategori_karyawan === 'driver')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 uppercase font-mono">Driver</span>
                                @elseif($karyawan->kategori_karyawan === 'staf')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 uppercase font-mono">Staf</span>
                                @elseif($karyawan->kategori_karyawan === 'gudang')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-50 dark:bg-teal-500/10 text-teal-700 dark:text-teal-400 uppercase font-mono">Gudang</span>
                                @elseif($karyawan->kategori_karyawan === 'teknisi')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 uppercase font-mono">Teknisi</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 uppercase font-mono">{{ $karyawan->kategori_karyawan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 dark:text-slate-400">{{ $karyawan->no_identitas }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-400 truncate max-w-xs">{{ $karyawan->alamat }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase font-mono {{ $karyawan->status_karyawan === 'aktif' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                                    {{ $karyawan->status_karyawan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$karyawan->kode_karyawan" 
                                    labelSalin="Salin ID"
                                    modulIzin="master_karyawan"
                                    aksiEdit="$dispatch('buka-edit-karyawan', '{{ $karyawan->kode_karyawan }}')"
                                    :aksiHapus="route('master.karyawan.destroy', $karyawan->kode_karyawan)"
                                    :pesanHapus="'Hapus karyawan ' . $karyawan->nama_karyawan . '?'"
                                />

                                <!-- Riwayat Terakhir Dibuat / Diedit Real-Time -->
                                <x-waktu-relatif :diperbaruiPada="$karyawan->diperbarui_pada ?? null" :dibuatPada="$karyawan->dibuat_pada ?? null" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada data karyawan yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarKaryawan ?? [])" />
    </div>
    @php
        $opsiKategoriKaryawan = [
            ['nilai' => 'staf', 'label' => 'Staf Kantor'],
            ['nilai' => 'driver', 'label' => 'Driver Supir'],
            ['nilai' => 'gudang', 'label' => 'Staf Gudang'],
            ['nilai' => 'teknisi', 'label' => 'Teknisi Bengkel'],
            ['nilai' => 'manajemen', 'label' => 'Manajemen / Direksi'],
        ];
        $opsiJabatanKaryawan = ($daftarJabatan ?? collect())->map(fn($j) => [
            'nilai' => $j->id_jabatan,
            'label' => $j->nama_jabatan,
            'sub'   => 'Kode: ' . $j->kode_jabatan
        ])->toArray();
        $opsiStatusKaryawan = [
            ['nilai' => 'aktif', 'label' => 'Aktif'],
            ['nilai' => 'kontrak', 'label' => 'Kontrak'],
            ['nilai' => 'tetap', 'label' => 'Tetap'],
            ['nilai' => 'non-aktif', 'label' => 'Non-Aktif'],
        ];
    @endphp

    <!-- Modal Tambah Karyawan -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Data Pegawai / Driver</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('master.karyawan.store') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Karyawan <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_karyawan" x-model="formTambah.kode_karyawan" required placeholder="STF-001"
                               class="w-full px-3 py-2 rounded-xl bg-indigo-50/50 dark:bg-[#1C1E2A] border border-indigo-200 dark:border-indigo-900/50 text-indigo-900 dark:text-indigo-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Karyawan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kategori_karyawan"
                            placeholder="-- Pilih Kategori --"
                            :opsi="$opsiKategoriKaryawan"
                            :wajib="true"
                            warnaFokus="indigo"
                            modelBind="formTambah.kategori_karyawan"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_karyawan" x-model="formTambah.nama_karyawan" required placeholder="Nama Lengkap Pegawai"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jabatan Sistem <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="id_jabatan"
                            placeholder="-- Pilih Jabatan --"
                            :opsi="$opsiJabatanKaryawan"
                            :wajib="true"
                            warnaFokus="indigo"
                            modelBind="formTambah.id_jabatan"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. KTP / Identitas <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_identitas" x-model="formTambah.no_identitas" required placeholder="321606xxxxxx0001" inputmode="numeric" data-hanya-angka="true" maxlength="16"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp" x-model="formTambah.no_hp" required placeholder="0812-xxxx-xxxx" inputmode="numeric" data-hanya-angka="true"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_karyawan"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusKaryawan"
                            :wajib="true"
                            warnaFokus="indigo"
                            modelBind="formTambah.status_karyawan"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Bergabung <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_bergabung" 
                            modelBind="formTambah.tanggal_bergabung" 
                            placeholder="Pilih Tanggal"
                            :wajib="true"
                            warnaFokus="indigo"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Domisili <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="alamat" x-model="formTambah.alamat" rows="2" placeholder="Jl. Raya ..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">Simpan Karyawan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Karyawan -->
    <div x-show="bukaModalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalEdit = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Edit Karyawan: <span class="font-mono text-indigo-600" x-text="editData.kode_karyawan"></span></h3>
                <button @click="bukaModalEdit = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('master/karyawan') }}/' + editData.kode_karyawan" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_karyawan" x-model="editData.nama_karyawan" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Karyawan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kategori_karyawan"
                            placeholder="-- Pilih Kategori --"
                            :opsi="$opsiKategoriKaryawan"
                            :wajib="true"
                            warnaFokus="indigo"
                            modelBind="editData.kategori_karyawan"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jabatan Sistem <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="id_jabatan"
                            placeholder="-- Pilih Jabatan --"
                            :opsi="$opsiJabatanKaryawan"
                            :wajib="true"
                            warnaFokus="indigo"
                            modelBind="editData.id_jabatan"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Kepegawaian <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_karyawan"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusKaryawan"
                            :wajib="true"
                            warnaFokus="indigo"
                            modelBind="editData.status_karyawan"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. KTP / Identitas <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_identitas" x-model="editData.no_identitas" required inputmode="numeric" data-hanya-angka="true" maxlength="16"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp" x-model="editData.no_hp" required inputmode="numeric" data-hanya-angka="true"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 font-mono">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Domisili <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <textarea name="alamat" x-model="editData.alamat" rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalEdit = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
