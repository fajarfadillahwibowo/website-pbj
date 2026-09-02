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

    <!-- Header Modul Super Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
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
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
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
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('superadmin.kelola_akun') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-72">
                <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari username / nama staf..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel Relasi: super_account & account & jabatan</span>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider w-10">No</th>
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
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 text-center font-mono text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-medium {{ $isSuper ? 'text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-800 dark:text-slate-200' }}">
                                {{ $usr }}
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-slate-100">
                                {{ $nama }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold font-mono {{ $isSuper ? 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                                    {{ $jbt }} ({{ $kode }})
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($aktif)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($isSuper)
                                    <span class="text-purple-500 font-mono text-[11px] font-semibold">Utama</span>
                                @else
                                    <div class="inline-flex items-center gap-2">
                                        <form method="POST" action="{{ route('superadmin.kelola_akun.reset_password') }}" onsubmit="return confirm('Reset sandi akun {{ $usr }} ke password123?')">
                                            @csrf
                                            <input type="hidden" name="username" value="{{ $usr }}">
                                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Reset Sandi</button>
                                        </form>
                                        <span class="text-slate-300 dark:text-slate-700">|</span>
                                        <form method="POST" action="{{ route('superadmin.kelola_akun.toggle_status') }}" onsubmit="return confirm('Ubah status aktif akun {{ $usr }}?')">
                                            @csrf
                                            <input type="hidden" name="username" value="{{ $usr }}">
                                            <button type="submit" class="{{ $aktif ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} hover:underline font-medium">
                                                {{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
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
    </div>

    <!-- Modal Tambah Akun Pengguna -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Akun Pengguna Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('superadmin.kelola_akun.store') }}" class="p-5 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Username Login</label>
                    <input type="text" name="username" required placeholder="contoh: staf_gudang_02"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Karyawan Terkait</label>
                    <select name="kode_karyawan" required class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($daftarKaryawan ?? [] as $karyawan)
                            <option value="{{ $karyawan->kode_karyawan }}">{{ $karyawan->kode_karyawan }} - {{ $karyawan->nama_karyawan }} ({{ ucfirst($karyawan->kategori_karyawan) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Jabatan / Peran RBAC</label>
                    <select name="id_jabatan" required class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($daftarJabatan ?? [] as $jabatan)
                            <option value="{{ $jabatan->id_jabatan }}">{{ $jabatan->nama_jabatan }} ({{ $jabatan->kode_jabatan }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kata Sandi Default</label>
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
