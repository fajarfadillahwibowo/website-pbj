@extends('layouts.app')

@section('judul', 'Kelola Akun & RBAC — Super Admin')

@section('konten')
<div class="space-y-5">
    <!-- Header Modul Super Admin -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-purple-600 dark:text-purple-400 font-semibold font-mono uppercase tracking-wider mb-1">Kontrol Sistem · Super Admin</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Kelola Akun & Hak Akses RBAC (10 Aktor)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manajemen 10 Akun Pengguna/Aktor, Kontrol Password, Status Aktif, dan Matriks Hak Akses Granular.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Akun Pengguna
            </button>
        </div>
    </div>

    <!-- Ringkasan Statistik Akun -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Akun Aktor</div>
            <div class="text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">10 Akun</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Status Aktif</div>
            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">10 / 10</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Super Administrator</div>
            <div class="text-lg font-bold text-purple-600 dark:text-purple-400 mt-0.5 font-mono">1 Akun</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Staf / Divisi</div>
            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">9 Akun</div>
        </div>
    </div>

    <!-- Tabel Data 10 Akun Pengguna -->
    <div class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari username / nama pegawai..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/30">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel Relasi: super_account & account & jabatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider w-10">No</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Username</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Pegawai / Pemilik</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Jabatan (Kode Role)</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Hak Akses Modul Utamanya</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @php
                        $listDefault = [
                            ['no' => 1, 'username' => 'superadmin', 'nama' => 'Administrator Kontrol Akun', 'jabatan' => 'Super Admin', 'kode' => 'SUPER_ADMIN', 'modul' => 'Kontrol Akun, RBAC, System Log', 'is_super' => true],
                            ['no' => 2, 'username' => 'spv_keuangan', 'nama' => 'Siti Rahmawati', 'jabatan' => 'SPV Keuangan', 'kode' => 'SPV_KEUANGAN', 'modul' => 'Penjualan, Toko Bangunan, Piutang, COA, Customer', 'is_super' => false],
                            ['no' => 3, 'username' => 'staff_ar', 'nama' => 'Dewi Anggraeni', 'jabatan' => 'Staff AR', 'kode' => 'STAFF_AR', 'modul' => 'Penjualan, Piutang, Toko Bangunan, Deposit', 'is_super' => false],
                            ['no' => 4, 'username' => 'staff_ap', 'nama' => 'Rian Hidayat', 'jabatan' => 'Staff AP', 'kode' => 'STAFF_AP', 'modul' => 'Pengeluaran, Rilisan, Pembelian SO, List Gudang SO', 'is_super' => false],
                            ['no' => 5, 'username' => 'dispatcher', 'nama' => 'Bambang Wijaya', 'jabatan' => 'Dispatcher', 'kode' => 'DISPATCHER', 'modul' => 'Pengiriman, Data Kendaraan, Driver, Ongkos Angkut', 'is_super' => false],
                            ['no' => 6, 'username' => 'pengawas_driver', 'nama' => 'Agus Suryanto', 'jabatan' => 'Pengawas Driver', 'kode' => 'PENGAWAS_DRIVER', 'modul' => 'Data Karyawan (Driver) & Status Kesiapan', 'is_super' => false],
                            ['no' => 7, 'username' => 'spv_gudang', 'nama' => 'Hendra Gunawan', 'jabatan' => 'SPV Gudang', 'kode' => 'SPV_GUDANG', 'modul' => 'Data Gudang & Stok Semen, Stok Opname Gudang', 'is_super' => false],
                            ['no' => 8, 'username' => 'direktur', 'nama' => 'Ahmad Supriyadi', 'jabatan' => 'Direktur & Manager', 'kode' => 'DIREKTUR_MANAGER', 'modul' => 'Laporan Neraca, Laporan Laba Rugi Eksekutif', 'is_super' => false],
                            ['no' => 9, 'username' => 'spv_operasional', 'nama' => 'Wahyu Pratama', 'jabatan' => 'SPV Operasional', 'kode' => 'SPV_OPERASIONAL', 'modul' => 'Ongkos Angkut, Opname, Driver, Truk, KSO', 'is_super' => false],
                            ['no' => 10, 'username' => 'pengawas_kendaraan', 'nama' => 'Doni Kurniawan', 'jabatan' => 'Pengawas Kendaraan', 'kode' => 'PENGAWAS_KENDARAAN', 'modul' => 'Perbaikan Kendaraan (SPK), Sparepart', 'is_super' => false],
                        ];

                        $items = isset($semuaAkun) && count($semuaAkun) > 0 ? $semuaAkun : $listDefault;
                    @endphp

                    @foreach($items as $index => $row)
                        @php
                            $usr = is_object($row) ? $row->username : $row['username'];
                            $nama = is_object($row) ? $row->nama_pegawai : $row['nama'];
                            $jbt = is_object($row) ? $row->nama_jabatan : $row['jabatan'];
                            $kode = is_object($row) ? $row->kode_jabatan : $row['kode'];
                            $isSuper = is_object($row) ? ($row->is_super ?? false) : ($row['is_super'] ?? false);
                            $modulKet = is_object($row) ? 'Akses Modul RBAC' : $row['modul'];
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
                            <td class="px-4 py-3 text-slate-500 text-[11px]">
                                {{ $modulKet }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center space-x-1.5">
                                @if($isSuper)
                                    <span class="text-purple-500 font-mono text-[11px] font-semibold">Utama</span>
                                @else
                                    <button class="text-blue-600 dark:text-blue-400 hover:underline font-medium">Reset Sandi</button>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <button class="text-red-600 dark:text-red-400 hover:underline font-medium">Nonaktifkan</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
