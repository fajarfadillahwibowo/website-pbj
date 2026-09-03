@extends('layouts.app')

@section('judul', 'Data Karyawan (Driver) - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaDriver()" x-init="initDriver()" class="space-y-6">

    @php
        $opsiStatusDriver = [
            ['nilai' => 'aktif', 'label' => 'Aktif (Siap Operasi)'],
            ['nilai' => 'kontrak', 'label' => 'Kontrak'],
            ['nilai' => 'tetap', 'label' => 'Tetap'],
            ['nilai' => 'non-aktif', 'label' => 'Non-Aktif / Cuti'],
            ['nilai' => 'berhenti', 'label' => 'Berhenti / Resign'],
        ];
        $opsiStatusFilterDriver = array_merge([
            ['nilai' => 'semua', 'label' => 'Semua Status Karyawan']
        ], $opsiStatusDriver);

        $opsiJabatan = ($daftarJabatan ?? collect())->map(fn($jab) => [
            'nilai' => $jab->id_jabatan,
            'label' => $jab->nama_jabatan . ' (' . $jab->kode_jabatan . ')',
            'sub'   => 'Kode: ' . $jab->kode_jabatan
        ])->toArray();
    @endphp

    <!-- 1. Header Modul & Tombol Aksi -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <template x-if="jabatanAktif === 'SPV_OPERASIONAL'">
                    <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 font-mono">
                        SPV Operasional (Read-Only)
                    </span>
                </template>
                <template x-if="jabatanAktif !== 'SPV_OPERASIONAL'">
                    <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 font-mono">
                        Dispatcher & Pengawas Driver
                    </span>
                </template>
                <span class="text-xs text-slate-400 font-mono">Modul Operasional</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data Karyawan (Driver)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola data pengemudi armada truk semen terintegrasi database dengan manajemen berkas KTP dan dokumen kontrak.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <template x-if="jabatanAktif !== 'SPV_OPERASIONAL'">
                <button @click="bukaModalTambah()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 rounded-xl transition-all shadow-md shadow-blue-600/20">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Driver Baru</span>
                </button>
            </template>
            <template x-if="jabatanAktif === 'SPV_OPERASIONAL'">
                <span class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 shadow-xs">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Mode: Hanya Lihat (Read-Only)</span>
                </span>
            </template>
        </div>
    </div>

    <!-- Banner Khusus SPV Operasional -->
    <template x-if="jabatanAktif === 'SPV_OPERASIONAL'">
        <div class="animasi-masuk p-4 rounded-2xl bg-amber-50/80 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-amber-800 dark:text-amber-300 text-xs flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold">Hak Akses SPV Operasional (Hanya Driver):</span>
                    <span class="text-amber-700 dark:text-amber-300 ml-1">Anda hanya memiliki hak pantau khusus untuk data karyawan <strong>Driver / Pengemudi</strong> armada. Hak tambah, ubah, dan hapus driver dibatasi untuk Dispatcher & Pengawas Driver, dan karyawan kategori non-driver tidak ditampilkan pada modul ini.</span>
                </div>
            </div>
            <span class="px-2.5 py-1 text-[10px] font-mono font-bold uppercase rounded-lg bg-amber-200/70 dark:bg-amber-900/50 text-amber-800 dark:text-amber-200 shrink-0 ml-3">Read-Only</span>
        </div>
    </template>

    <!-- 2. Flash Message / Notifikasi Sukses & Error -->
    @if(session('sukses'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('sukses') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:text-rose-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm space-y-1">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3. Ringkasan Kartu KPI / Statistik -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Driver -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Total Supir</div>
                <div class="text-xl font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalDriver }} <span class="text-xs font-normal text-slate-400 font-sans">Orang</span></div>
            </div>
        </div>

        <!-- Driver Aktif & Tetap -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Driver Aktif</div>
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $driverAktif }} <span class="text-xs font-normal text-slate-400 font-sans">Siap Jalan</span></div>
            </div>
        </div>

        <!-- Driver Kontrak -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Status Kontrak</div>
                <div class="text-xl font-bold text-amber-600 dark:text-amber-400 font-mono mt-0.5">{{ $driverKontrak }} <span class="text-xs font-normal text-slate-400 font-sans">Karyawan</span></div>
            </div>
        </div>

        <!-- Driver Non-Aktif / Berhenti -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <div>
                <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Cuti / Non-Aktif</div>
                <div class="text-xl font-bold text-rose-600 dark:text-rose-400 font-mono mt-0.5">{{ $driverNonaktif }} <span class="text-xs font-normal text-slate-400 font-sans">Orang</span></div>
            </div>
        </div>
    </div>

    <!-- 4. Tabel Data Driver & Bar Pencarian -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('operasional.armada.driver') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-2xl">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari nama driver, kode, nomor HP, no KTP, alamat..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Status Filter Dropdown Kustom -->
                <div class="w-full sm:w-56">
                    <x-dropdown-kustom 
                        nama="status"
                        placeholder="-- Status Driver --"
                        :opsi="$opsiStatusFilterDriver"
                        :nilaiAwal="$statusFilter ?? 'semua'"
                        :submitOnChange="true"
                        warnaFokus="blue"
                    />
                </div>

                @if(!empty($kataKunci) || ($statusFilter !== 'semua' && !empty($statusFilter)))
                    <a href="{{ route('operasional.armada.driver') }}"
                       class="px-3 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset Filter
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono shrink-0">
                Menampilkan <strong class="text-slate-700 dark:text-slate-300 font-bold">{{ count($daftarDriver) }}</strong> Driver
            </div>
        </div>

        <!-- Tabel Data Driver -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-left text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Kode Supir</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Nama & Jabatan</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. KTP / NIK (16 Digit)</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">No. HP / WA</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider">Alamat</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Berkas</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarDriver as $driver)
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors group">
                            
                            <!-- Kode Karyawan -->
                            <td class="px-4 py-3.5 font-mono font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                <span class="px-2 py-1 rounded bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                                    {{ $driver->kode_karyawan }}
                                </span>
                            </td>

                            <!-- Nama & Jabatan -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 dark:text-slate-100 text-sm flex items-center gap-1.5">
                                    {{ $driver->nama_karyawan }}
                                </div>
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                                    <span class="px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium">
                                        {{ $driver->jabatan->nama_jabatan ?? 'Driver / Supir' }}
                                    </span>
                                    <span>•</span>
                                    <span class="font-mono">Tgl Masuk: {{ $driver->tanggal_mulai_kerja ? \Carbon\Carbon::parse($driver->tanggal_mulai_kerja)->format('d/m/Y') : '-' }}</span>
                                </div>
                            </td>

                            <!-- No. KTP / NIK (16 Digit Khas Indonesia) -->
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-[#1E212E] px-2.5 py-1 rounded-lg border border-slate-200 dark:border-[#252837]"
                                     title="Nomor Induk Kependudukan e-KTP Indonesia (16 Digit)">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                    <span>{{ $driver->no_ktp_format }}</span>
                                </div>
                            </td>

                            <!-- No. HP -->
                            <td class="px-4 py-3.5 font-mono text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $driver->no_hp) }}" target="_blank"
                                   class="text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
                                   title="Kirim Pesan WhatsApp">
                                    <span>{{ $driver->no_hp }}</span>
                                    <svg class="w-3 h-3 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </td>

                            <!-- Alamat -->
                            <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 max-w-xs truncate" title="{{ $driver->alamat }}">
                                {{ $driver->alamat }}
                            </td>

                            <!-- Berkas Foto KTP & Kontrak -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Foto KTP -->
                                    @if($driver->foto_ktp_url)
                                        <a href="{{ $driver->foto_ktp_url }}" target="_blank"
                                           class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 transition-colors"
                                           title="Lihat Foto KTP">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600" title="Foto KTP Belum Diunggah">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </span>
                                    @endif

                                    <!-- File Kontrak -->
                                    @if($driver->file_kontrak_url)
                                        <a href="{{ $driver->file_kontrak_url }}" target="_blank"
                                           class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-100 transition-colors"
                                           title="Unduh / Lihat Dokumen Kontrak">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600" title="Dokumen Kontrak Belum Diunggah">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Karyawan -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @php
                                    $warnaStatus = match($driver->status_karyawan) {
                                        'aktif', 'tetap' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                                        'kontrak' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                                        default => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase font-mono border {{ $warnaStatus }}">
                                    {{ $driver->status_karyawan }}
                                </span>
                            </td>

                            <!-- Tombol Aksi & Riwayat Diedit -->
                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Detail (Bisa diakses semua peran termasuk SPV Operasional) -->
                                    <button @click="bukaModalDetail('{{ $driver->kode_karyawan }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors"
                                            title="Lihat Detail Profil Driver">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <!-- Edit & Hapus (Disembunyikan untuk SPV Operasional) -->
                                    <template x-if="jabatanAktif !== 'SPV_OPERASIONAL'">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Edit -->
                                            <button @click="bukaModalEdit('{{ $driver->kode_karyawan }}')"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors"
                                                    title="Ubah Data Driver">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>

                                            <!-- Hapus -->
                                            <button @click="bukaModalHapus('{{ $driver->kode_karyawan }}', '{{ addslashes($driver->nama_karyawan) }}')"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                    title="Hapus Data Driver">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Riwayat Terakhir Diedit Real-Time -->
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help"
                                     title="Terakhir diperbarui: {{ $driver->diperbarui_pada ? \Carbon\Carbon::parse($driver->diperbarui_pada)->format('d/m/Y H:i:s') : '-' }}">
                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $driver->diperbarui_pada ? \Carbon\Carbon::parse($driver->diperbarui_pada)->locale('id')->diffForHumans() : 'Baru' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 mb-2">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-600 dark:text-slate-300">Tidak ada data driver ditemukan</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Coba ubah kata kunci pencarian atau tambah data driver baru.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Driver -->
    <div x-show="modalTambahTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Tambah Driver Pengemudi Baru</h3>
                <button @click="modalTambahTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>
            <form action="{{ route('operasional.armada.driver.simpan') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Driver <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_karyawan" x-model="formTambah.kode_karyawan" required placeholder="DRV-001"
                               class="w-full px-3 py-2 rounded-xl bg-blue-50/50 dark:bg-[#1C1E2A] border border-blue-200 dark:border-blue-900/50 text-blue-900 dark:text-blue-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jabatan Karyawan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="id_jabatan"
                            placeholder="-- Pilih Jabatan --"
                            :opsi="$opsiJabatan"
                            :wajib="true"
                            warnaFokus="blue"
                            modelBind="formTambah.id_jabatan"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Driver <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_karyawan" x-model="formTambah.nama_karyawan" required placeholder="Nama pengemudi"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Karyawan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_karyawan"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusDriver"
                            :wajib="true"
                            warnaFokus="blue"
                            modelBind="formTambah.status_karyawan"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">No. KTP / NIK <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] font-mono font-semibold"
                                  :class="hitungDigitKtp(formTambah.no_ktp) === 16 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                                <span x-text="hitungDigitKtp(formTambah.no_ktp)"></span>/16 Digit
                            </span>
                        </div>
                        <input type="text" name="no_ktp" x-model="formTambah.no_ktp" 
                               @input="formatInputKtp($event, 'formTambah')"
                               required placeholder="3201 0203 0405 0001" maxlength="19"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 font-mono tracking-wider">
                        <p class="text-[10px] text-slate-400 mt-1">Format khas 16 digit NIK/e-KTP Indonesia (otomatis berjarak 4 digit).</p>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Handphone / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp" x-model="formTambah.no_hp" required placeholder="0812-xxxx-xxxx" maxlength="25"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai Kerja <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_mulai_kerja" 
                            modelBind="formTambah.tanggal_mulai_kerja" 
                            placeholder="Pilih Tanggal Mulai"
                            :wajib="true"
                            warnaFokus="blue"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Berakhir <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                        <x-input-tanggal 
                            nama="tanggal_berhenti" 
                            modelBind="formTambah.tanggal_berhenti" 
                            placeholder="Pilih Tanggal Berakhir"
                            :wajib="false"
                            warnaFokus="blue"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Domisili Lengkap <span class="text-rose-500">*</span></label>
                    <textarea name="alamat" x-model="formTambah.alamat" required rows="2" placeholder="Jl. Raya Utama No. ..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
                </div>

                <!-- Upload Berkas Lampiran: Foto KTP & File Kontrak (Maks 2 MB) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <!-- Unggah Foto KTP -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Unggah Foto KTP</label>
                            <span class="text-[9px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 px-1.5 py-0.2 rounded font-mono">Maks 2 MB</span>
                        </div>
                        <input type="file" name="foto_ktp" accept="image/jpeg,image/png,image/jpg,image/webp" 
                               @change="pratinjauFotoTambah($event)"
                               class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-950/50 dark:file:text-blue-300 hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2 MB).</p>
                        
                        <!-- Pratinjau Foto KTP -->
                        <template x-if="pratinjauFotoUrl">
                            <div class="mt-2 relative inline-block">
                                <img :src="pratinjauFotoUrl" class="h-20 w-auto object-cover rounded-lg border border-blue-300 shadow-xs">
                                <button type="button" @click="pratinjauFotoUrl = null; $el.closest('div').previousElementSibling.previousElementSibling.value = ''"
                                        class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] hover:bg-rose-700 shadow-xs"
                                        title="Batal pilih foto">&times;</button>
                            </div>
                        </template>
                    </div>

                    <!-- Unggah File Kontrak -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Unggah File Kontrak</label>
                            <span class="text-[9px] font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50 px-1.5 py-0.2 rounded font-mono">Maks 2 MB</span>
                        </div>
                        <input type="file" name="file_kontrak" accept=".pdf,.doc,.docx,image/jpeg,image/png"
                               @change="validasiFileKontrak($event, 'tambah')"
                               class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950/50 dark:file:text-indigo-300 hover:file:bg-indigo-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Format: PDF, DOC, DOCX, JPG (Maksimal 2 MB).</p>

                        <template x-if="namaFileKontrakTambah">
                            <div class="mt-2 flex items-center gap-1.5 text-[11px] font-mono text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/40 px-2 py-1 rounded-md border border-indigo-200 dark:border-indigo-900/50">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="truncate max-w-[180px]" x-text="namaFileKontrakTambah"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="modalTambahTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">Simpan Data Driver</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Driver -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-xl my-8">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Driver: <span class="font-mono text-amber-600" x-text="formEdit.kode_karyawan"></span></h3>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none">&times;</button>
            </div>
            <form :action="'{{ url('operasional/armada/driver') }}/' + formEdit.kode_karyawan" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Driver <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_karyawan" x-model="formEdit.nama_karyawan" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jabatan Karyawan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="id_jabatan"
                            placeholder="-- Pilih Jabatan --"
                            :opsi="$opsiJabatan"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.id_jabatan"
                        />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Karyawan <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="status_karyawan"
                            placeholder="-- Pilih Status --"
                            :opsi="$opsiStatusDriver"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.status_karyawan"
                        />
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">No. KTP / NIK <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] font-mono font-semibold"
                                  :class="hitungDigitKtp(formEdit.no_ktp) === 16 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                                <span x-text="hitungDigitKtp(formEdit.no_ktp)"></span>/16 Digit
                            </span>
                        </div>
                        <input type="text" name="no_ktp" x-model="formEdit.no_ktp" 
                               @input="formatInputKtp($event, 'formEdit')"
                               required placeholder="3201 0203 0405 0001" maxlength="19"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono tracking-wider">
                        <p class="text-[10px] text-slate-400 mt-1">Format khas 16 digit NIK/e-KTP Indonesia (otomatis berjarak 4 digit).</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Handphone / WA <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp" x-model="formEdit.no_hp" required maxlength="25"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai Kerja <span class="text-rose-500">*</span></label>
                        <x-input-tanggal 
                            nama="tanggal_mulai_kerja" 
                            modelBind="formEdit.tanggal_mulai_kerja" 
                            placeholder="Pilih Tanggal Mulai"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Domisili Lengkap <span class="text-rose-500">*</span></label>
                    <textarea name="alamat" x-model="formEdit.alamat" required rows="2"
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <!-- Berkas Lampiran: Foto KTP & File Kontrak (Maks 2 MB) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <!-- Kelola Foto KTP -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Foto KTP / Identitas</label>
                            <span class="text-[9px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 px-1.5 py-0.2 rounded font-mono">Maks 2 MB</span>
                        </div>
                        
                        <!-- Pratinjau berkas yang saat ini tersimpan -->
                        <template x-if="formEdit.foto_ktp_url">
                            <div class="mb-2 p-2 rounded-lg bg-white dark:bg-[#14161F] border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <img :src="formEdit.foto_ktp_url" class="h-10 w-10 object-cover rounded-md border shrink-0">
                                    <div class="truncate">
                                        <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Foto KTP Tersimpan</div>
                                        <a :href="formEdit.foto_ktp_url" target="_blank" class="text-[10px] text-blue-600 hover:underline">Buka Gambar</a>
                                    </div>
                                </div>
                                <button type="button" @click="hapusBerkasDriver(formEdit.kode_karyawan, 'foto_ktp')"
                                        class="px-2 py-1 text-[10px] font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded transition-colors shrink-0"
                                        title="Hapus foto KTP tersimpan">
                                    Hapus
                                </button>
                            </div>
                        </template>

                        <input type="file" name="foto_ktp" accept="image/jpeg,image/png,image/jpg,image/webp"
                               @change="pratinjauFotoEdit($event)"
                               class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 dark:file:bg-amber-950/50 dark:file:text-amber-300 hover:file:bg-amber-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2 MB).</p>

                        <!-- Pratinjau Foto Baru yang Dipilih -->
                        <template x-if="pratinjauFotoEditUrl">
                            <div class="mt-2 relative inline-block">
                                <span class="text-[9px] text-amber-600 dark:text-amber-400 block font-semibold mb-0.5">Foto Baru Terpilih:</span>
                                <img :src="pratinjauFotoEditUrl" class="h-20 w-auto object-cover rounded-lg border border-amber-400 shadow-xs">
                                <button type="button" @click="pratinjauFotoEditUrl = null; $el.closest('div').previousElementSibling.previousElementSibling.value = ''"
                                        class="absolute top-3.5 -right-1.5 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] hover:bg-rose-700 shadow-xs"
                                        title="Batal ganti foto">&times;</button>
                            </div>
                        </template>
                    </div>

                    <!-- Kelola File Kontrak -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">File Kontrak Kerja</label>
                            <span class="text-[9px] font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 px-1.5 py-0.2 rounded font-mono">Maks 2 MB</span>
                        </div>

                        <!-- Berkas kontrak yang saat ini tersimpan -->
                        <template x-if="formEdit.file_kontrak_url">
                            <div class="mb-2 p-2 rounded-lg bg-white dark:bg-[#14161F] border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded bg-blue-50 dark:bg-blue-950/50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="truncate">
                                        <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Kontrak Tersimpan</div>
                                        <a :href="formEdit.file_kontrak_url" target="_blank" class="text-[10px] text-blue-600 hover:underline">Unduh Berkas</a>
                                    </div>
                                </div>
                                <button type="button" @click="hapusBerkasDriver(formEdit.kode_karyawan, 'file_kontrak')"
                                        class="px-2 py-1 text-[10px] font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded transition-colors shrink-0"
                                        title="Hapus berkas kontrak tersimpan">
                                    Hapus
                                </button>
                            </div>
                        </template>

                        <input type="file" name="file_kontrak" accept=".pdf,.doc,.docx,image/jpeg,image/png"
                               @change="validasiFileKontrak($event, 'edit')"
                               class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 dark:file:bg-amber-950/50 dark:file:text-amber-300 hover:file:bg-amber-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Format: PDF, DOC, DOCX, JPG (Maksimal 2 MB).</p>

                        <!-- Nama Berkas Baru yang Dipilih -->
                        <template x-if="namaFileKontrakEdit">
                            <div class="mt-2 flex items-center gap-1.5 text-[11px] font-mono text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 px-2 py-1 rounded-md border border-amber-200 dark:border-amber-900/50">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="truncate max-w-[180px]" x-text="namaFileKontrakEdit"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="modalEditTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Driver -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl my-8">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold font-mono text-xs">
                        <span x-text="detailDriver.kode_karyawan ? detailDriver.kode_karyawan.substring(0,3) : 'DRV'"></span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100" x-text="detailDriver.nama_karyawan"></h2>
                        <p class="text-[11px] text-slate-400 font-mono" x-text="detailDriver.kode_karyawan"></p>
                    </div>
                </div>
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Detail Isi Konten -->
            <div class="p-6 space-y-5 text-xs">
                
                <!-- Status & Posisi Grid -->
                <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837]">
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Jabatan</div>
                        <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailDriver.jabatan ? detailDriver.jabatan.nama_jabatan : 'Driver / Pengemudi'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Status Karyawan</div>
                        <div class="mt-0.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase font-mono bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20"
                                  x-text="detailDriver.status_karyawan"></span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider flex items-center justify-between">
                            <span>No. KTP / NIK</span>
                            <span class="text-[9px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-1.5 py-0.2 rounded font-mono">16 Digit KTP RI</span>
                        </div>
                        <div class="font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5 text-sm tracking-wide" x-text="detailDriver.no_ktp_format || detailDriver.no_identitas || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">No. Handphone</div>
                        <div class="font-mono font-bold text-blue-600 dark:text-blue-400 mt-0.5" x-text="detailDriver.no_hp || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Tanggal Masuk</div>
                        <div class="font-mono text-slate-700 dark:text-slate-300 mt-0.5" x-text="detailDriver.tanggal_mulai_kerja || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Tanggal Selesai</div>
                        <div class="font-mono text-slate-700 dark:text-slate-300 mt-0.5" x-text="detailDriver.tanggal_berhenti || '-'"></div>
                    </div>
                </div>

                <!-- Alamat Domisili -->
                <div>
                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Alamat Domisili</div>
                    <div class="p-3 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 leading-relaxed"
                         x-text="detailDriver.alamat || '-'"></div>
                </div>

                <!-- Lampiran Berkas KTP & Kontrak -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Preview Foto KTP -->
                    <div class="p-3.5 rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Foto KTP / SIM</div>
                        <template x-if="detailDriver.foto_ktp_url">
                            <div>
                                <img :src="detailDriver.foto_ktp_url" class="w-full h-28 object-cover rounded-lg border border-[#E2E8F0] dark:border-[#252837] mb-2 cursor-pointer hover:opacity-90"
                                     @click="window.open(detailDriver.foto_ktp_url, '_blank')">
                                <a :href="detailDriver.foto_ktp_url" target="_blank"
                                   class="text-[11px] font-semibold text-blue-600 hover:underline flex items-center gap-1">
                                    <span>Buka Gambar Penuh</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        </template>
                        <template x-if="!detailDriver.foto_ktp_url">
                            <div class="h-28 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 text-[11px]">
                                <span>Foto KTP belum diunggah</span>
                            </div>
                        </template>
                    </div>

                    <!-- Dokumen Kontrak -->
                    <div class="p-3.5 rounded-xl border border-[#E2E8F0] dark:border-[#252837] flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Dokumen Kontrak</div>
                            <template x-if="detailDriver.file_kontrak_url">
                                <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 text-blue-800 dark:text-blue-300">
                                    <div class="flex items-center gap-2 font-semibold text-xs mb-1">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Berkas Kontrak Terlampir</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Dokumen kerja legal driver tersimpan di server.</p>
                                </div>
                            </template>
                            <template x-if="!detailDriver.file_kontrak_url">
                                <div class="h-20 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-slate-400 text-[11px]">
                                    <span>Belum ada dokumen kontrak</span>
                                </div>
                            </template>
                        </div>
                        
                        <template x-if="detailDriver.file_kontrak_url">
                            <a :href="detailDriver.file_kontrak_url" target="_blank"
                               class="mt-3 w-full py-2 text-center text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors shadow-sm">
                                Unduh / Buka Dokumen
                            </a>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end px-6 py-3.5 border-t border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <button @click="modalDetailTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] hover:bg-slate-100 rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. MODAL KONFIRMASI HAPUS DRIVER -->
    <!-- ========================================================================= -->
    <div x-show="modalHapusTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false"
             class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-2xl p-6 text-center">
            
            <div class="w-12 h-12 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 mx-auto flex items-center justify-center mb-3.5">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Hapus Data Driver?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin menghapus data supir <strong class="text-slate-900 dark:text-slate-200 font-bold" x-text="hapusData.nama"></strong> (<span class="font-mono" x-text="hapusData.kode"></span>)?
                Tindakan ini juga akan menghapus berkas lampiran yang tersimpan di server.
            </p>

            <form :action="'{{ url('operasional/armada/driver') }}/' + hapusData.kode" method="POST" class="mt-6 flex items-center justify-center gap-2.5">
                @csrf
                @method('DELETE')

                <button type="button" @click="modalHapusTerbuka = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-xl transition-all shadow-md shadow-rose-600/20">
                    Ya, Hapus Data
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Script Alpine.js Logika CRUD Driver -->
<script>
    function kelolaDriver() {
        return {
            modalTambahTerbuka: false,
            modalEditTerbuka: false,
            modalDetailTerbuka: false,
            modalHapusTerbuka: false,

            pratinjauFotoUrl: null,
            namaFileKontrakTambah: '',
            pratinjauFotoEditUrl: null,
            namaFileKontrakEdit: '',

            formTambah: {
                kode_karyawan: '',
                nama_karyawan: '',
                id_jabatan: '{{ $daftarJabatan->where("kode_jabatan", "PENGAWAS_DRIVER")->first()->id_jabatan ?? ($daftarJabatan->first()->id_jabatan ?? "") }}',
                status_karyawan: 'aktif',
                no_ktp: '',
                no_hp: '',
                alamat: '',
                tanggal_mulai_kerja: new Date().toISOString().split('T')[0],
                tanggal_berhenti: ''
            },

            formEdit: {
                kode_karyawan: '',
                nama_karyawan: '',
                id_jabatan: '',
                status_karyawan: 'aktif',
                no_ktp: '',
                no_hp: '',
                alamat: '',
                tanggal_mulai_kerja: '',
                tanggal_berhenti: '',
                foto_ktp_url: null,
                file_kontrak_url: null
            },

            detailDriver: {},
            hapusData: {
                kode: '',
                nama: ''
            },

            keteranganKodeOtomatis: 'Mode: Daur Ulang Slot Kosong',

            hitungDigitKtp(val) {
                if (!val) return 0;
                return String(val).replace(/[^0-9]/g, '').length;
            },

            formatInputKtp(e, formKey) {
                let raw = e.target.value.replace(/[^0-9]/g, '').substring(0, 16);
                let chunked = raw.match(/.{1,4}/g);
                let formatted = chunked ? chunked.join(' ') : raw;
                this[formKey].no_ktp = formatted;
                e.target.value = formatted;
            },

            initDriver() {
                // Inisialisasi awal jika dibutuhkan
            },

            bukaModalTambah() {
                if (this.jabatanAktif === 'SPV_OPERASIONAL') {
                    alert('Akses Ditolak: SPV Operasional hanya memiliki hak akses Lihat Saja (Read-Only).');
                    return;
                }
                this.pratinjauFotoUrl = null;
                this.namaFileKontrakTambah = '';
                this.formTambah.nama_karyawan = '';
                this.formTambah.no_ktp = '';
                this.formTambah.no_hp = '';
                this.formTambah.alamat = '';
                this.buatKodeOtomatis('gap');
                this.modalTambahTerbuka = true;
            },

            async buatKodeOtomatis(mode = 'gap') {
                try {
                    const response = await fetch(`{{ route("operasional.armada.driver.buat_kode") }}?mode=${mode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        this.formTambah.kode_karyawan = hasil.kode_otomatis;
                        this.keteranganKodeOtomatis = hasil.keterangan || (mode === 'acak' ? 'Mode: Kode Acak Anti-Tebak' : 'Mode: Daur Ulang Slot Kosong');
                    }
                } catch (e) {
                    console.error('Gagal membuat kode otomatis:', e);
                }
            },

            pratinjauFotoTambah(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file foto KTP melebihi batas maksimal 2 MB!');
                        event.target.value = '';
                        this.pratinjauFotoUrl = null;
                        return;
                    }
                    this.pratinjauFotoUrl = URL.createObjectURL(file);
                }
            },

            validasiFileKontrak(event, formKey) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file kontrak melebihi batas maksimal 2 MB!');
                        event.target.value = '';
                        if (formKey === 'tambah') this.namaFileKontrakTambah = '';
                        if (formKey === 'edit') this.namaFileKontrakEdit = '';
                        return;
                    }
                    if (formKey === 'tambah') this.namaFileKontrakTambah = file.name;
                    if (formKey === 'edit') this.namaFileKontrakEdit = file.name;
                }
            },

            pratinjauFotoEdit(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file foto KTP melebihi batas maksimal 2 MB!');
                        event.target.value = '';
                        this.pratinjauFotoEditUrl = null;
                        return;
                    }
                    this.pratinjauFotoEditUrl = URL.createObjectURL(file);
                }
            },

            async bukaModalDetail(kode) {
                try {
                    const response = await fetch(`{{ url('operasional/armada/driver') }}/${kode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        this.detailDriver = hasil.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data detail driver.');
                }
            },

            async bukaModalEdit(kode) {
                if (this.jabatanAktif === 'SPV_OPERASIONAL') {
                    alert('Akses Ditolak: SPV Operasional hanya memiliki hak akses Lihat Saja (Read-Only).');
                    return;
                }
                this.pratinjauFotoEditUrl = null;
                this.namaFileKontrakEdit = '';
                try {
                    const response = await fetch(`{{ url('operasional/armada/driver') }}/${kode}`);
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        const d = hasil.data;
                        let rawNik = String(d.no_identitas || d.no_ktp || '').replace(/[^0-9]/g, '').substring(0, 16);
                        let chunkedNik = rawNik.match(/.{1,4}/g);
                        let nikFormat = chunkedNik ? chunkedNik.join(' ') : rawNik;
                        this.formEdit = {
                            kode_karyawan: d.kode_karyawan,
                            nama_karyawan: d.nama_karyawan,
                            id_jabatan: d.id_jabatan,
                            status_karyawan: d.status_karyawan,
                            no_ktp: nikFormat,
                            no_hp: d.no_hp,
                            alamat: d.alamat,
                            tanggal_mulai_kerja: d.tanggal_mulai_kerja || '',
                            tanggal_berhenti: d.tanggal_berhenti || '',
                            foto_ktp_url: d.foto_ktp_url,
                            file_kontrak_url: d.file_kontrak_url
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal mengambil data driver untuk diedit.');
                }
            },

            async hapusBerkasDriver(kode, jenisBerkas) {
                const label = jenisBerkas === 'foto_ktp' ? 'Foto KTP' : 'File Kontrak';
                if (!confirm(`Apakah Anda yakin ingin menghapus berkas ${label} ini?`)) {
                    return;
                }
                try {
                    const response = await fetch(`{{ url('operasional/armada/driver') }}/${kode}/berkas/${jenisBerkas}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const hasil = await response.json();
                    if (hasil.status === 'sukses') {
                        alert(hasil.pesan);
                        if (jenisBerkas === 'foto_ktp') {
                            this.formEdit.foto_ktp_url = null;
                            if (this.detailDriver && this.detailDriver.kode_karyawan === kode) {
                                this.detailDriver.foto_ktp_url = null;
                            }
                        } else {
                            this.formEdit.file_kontrak_url = null;
                            if (this.detailDriver && this.detailDriver.kode_karyawan === kode) {
                                this.detailDriver.file_kontrak_url = null;
                            }
                        }
                        window.location.reload();
                    } else {
                        alert(hasil.pesan || 'Gagal menghapus berkas.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat menghapus berkas.');
                }
            },

            bukaModalHapus(kode, nama) {
                if (this.jabatanAktif === 'SPV_OPERASIONAL') {
                    alert('Akses Ditolak: SPV Operasional hanya memiliki hak akses Lihat Saja (Read-Only).');
                    return;
                }
                this.hapusData.kode = kode;
                this.hapusData.nama = nama;
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
