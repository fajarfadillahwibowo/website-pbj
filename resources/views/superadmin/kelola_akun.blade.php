@extends('layouts.app')

@section('judul', 'Kelola Akun & RBAC — Super Admin')

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
            <div class="text-xs text-purple-600 dark:text-purple-400 font-semibold font-mono uppercase tracking-wider mb-1">Kontrol Sistem · Super Admin</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Kelola Akun & Hak Akses RBAC (10 Aktor)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manajemen 10 Akun Pengguna/Aktor, Kontrol Password, Status Aktif, dan Matriks Hak Akses Granular.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Akun Pengguna
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Akun -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Akun Aktor</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">{{ $totalAkun ?? count($semuaAkun) }} Akun</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Status Aktif</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">{{ $totalAktif ?? 10 }} / {{ $totalAkun ?? 10 }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Super Administrator</div>
            <div class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-0.5 font-mono">{{ $totalSuper ?? 1 }} Akun</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Staf / Divisi</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">{{ $totalStaf ?? 9 }} Akun</div>
        </div>
    </div>

    <!-- Tabel Data 10 Akun Pengguna -->
    <div x-data="tabelPaginasi({ totalData: {{ count($semuaAkun ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('superadmin.kelola_akun') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-72">
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari username / nama staf..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel Relasi: super_account & account & jabatan</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider w-12">No</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Username</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Pegawai / Pemilik</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jabatan (Kode Role)</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($semuaAkun as $index => $row)
                        @php
                            $usr = is_object($row) ? $row->username : $row['username'];
                            $nama = is_object($row) ? $row->nama_pegawai : $row['nama'];
                            $jbt = is_object($row) ? $row->nama_jabatan : $row['jabatan'];
                            $kode = is_object($row) ? $row->kode_jabatan : $row['kode'];
                            $isSuper = is_object($row) ? ($row->is_super ?? false) : ($row['is_super'] ?? false);
                            $aktif = is_object($row) ? ($row->status_aktif ?? true) : ($row['status_aktif'] ?? true);
                        @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 text-center font-mono text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-medium {{ $isSuper ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-800 dark:text-slate-200' }}">
                                {{ $usr }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900 dark:text-slate-100">
                                {{ $nama }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold font-mono {{ $isSuper ? 'bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                    {{ $jbt }} ({{ $kode }})
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $aktif ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $aktif ? 'bg-emerald-600' : 'bg-rose-600' }}"></span>
                                    <span>{{ $aktif ? 'Aktif' : 'Non-Aktif' }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($isSuper)
                                    <x-menu-aksi-tabel 
                                        :kodeSalin="$usr" 
                                        labelSalin="Salin User"
                                        modulIzin="superadmin_akun"
                                    />
                                @else
                                    <x-menu-aksi-tabel 
                                        :kodeSalin="$usr" 
                                        labelSalin="Salin User"
                                        modulIzin="superadmin_akun"
                                    >
                                        <form method="POST" action="{{ route('superadmin.kelola_akun.reset_password') }}" onsubmit="return confirm('Reset password akun {{ $usr }} menjadi password123?')">
                                            @csrf
                                            <input type="hidden" name="username" value="{{ $usr }}">
                                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-purple-50 dark:hover:bg-purple-500/10 hover:text-purple-600 dark:hover:text-purple-400 transition-colors text-left">
                                                <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                </svg>
                                                <span>Reset Sandi</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('superadmin.kelola_akun.toggle_status') }}" onsubmit="return confirm('Ubah status aktif akun {{ $usr }}?')">
                                            @csrf
                                            <input type="hidden" name="username" value="{{ $usr }}">
                                            <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-semibold {{ $aktif ? 'text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10' : 'text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10' }} transition-colors text-left border-t border-slate-100 dark:border-[#252837]">
                                                @if($aktif)
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                    <span>Nonaktifkan</span>
                                                @else
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Aktifkan</span>
                                                @endif
                                            </button>
                                        </form>
                                    </x-menu-aksi-tabel>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">Tidak ada akun yang sesuai dengan kriteria pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginasi Terpadu -->
        <x-paginasi-tabel :totalData="count($semuaAkun ?? [])" />
    </div>

    <!-- Modal Tambah Akun Pengguna -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-visible shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Akun Pengguna Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('superadmin.kelola_akun.store') }}" class="p-5 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" required placeholder="contoh: staf_gudang_02"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                </div>
                @php
                    $opsiKaryawan = ($daftarKaryawan ?? collect())->map(fn($k) => [
                        'nilai' => $k->kode_karyawan, 
                        'label' => $k->nama_karyawan,
                        'sub' => $k->kode_karyawan . ' · ' . ucfirst($k->kategori_karyawan)
                    ])->toArray();

                    $opsiJabatan = ($daftarJabatan ?? collect())->map(fn($j) => [
                        'nilai' => $j->id_jabatan, 
                        'label' => $j->nama_jabatan,
                        'sub' => 'Kode Peran: ' . $j->kode_jabatan
                    ])->toArray();
                @endphp
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Karyawan Terkait <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="kode_karyawan"
                        placeholder="-- Pilih Karyawan --"
                        :opsi="$opsiKaryawan"
                        :wajib="true"
                        warnaFokus="purple"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Jabatan / Peran RBAC <span class="text-rose-500">*</span></label>
                    <x-dropdown-kustom 
                        nama="id_jabatan"
                        placeholder="-- Pilih Jabatan / Role --"
                        :opsi="$opsiJabatan"
                        :wajib="true"
                        warnaFokus="purple"
                    />
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Default <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required value="password123" placeholder="Minimal 6 karakter"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition-all shadow-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
