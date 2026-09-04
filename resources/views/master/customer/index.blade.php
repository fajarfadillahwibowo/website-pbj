@extends('layouts.app')

@section('judul', 'Master Data Customer (Entitas Pemilik & Finansial) - PT Putra Balkom Jaya')

@section('konten')
<div x-data="kelolaCustomer()" x-init="initCustomer()" class="space-y-6">

    <!-- 1. Header Modul & Tombol Tambah -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#14161F] p-4 sm:p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-md bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 font-mono">
                    Master Finansial
                </span>
                <span class="text-xs text-slate-400 font-mono">Entitas Legal & Plafon Kredit</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">Data Customer (Entitas Pemilik)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Kelola data profil pemilik, plafon limit kredit terpusat, saldo deposit, serta agregat seluruh cabang toko bangunan & proyek.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.toko_bangunan.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Kelola Cabang Toko</span>
            </a>
            <button @click="bukaModalTambah()" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 rounded-xl transition-all shadow-md shadow-blue-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Customer</span>
            </button>
        </div>
    </div>

    <!-- 2. Flash Message / Notifikasi -->
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

    @if(session('gagal') || session('error'))
        <div class="flex items-center justify-between p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('gagal') ?? session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:text-rose-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- 3. Ringkasan Kartu KPI -->
    <div class="wadah-bertingkat grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <!-- Total Customer -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Total Customer</div>
                <div class="text-lg font-bold text-slate-900 dark:text-slate-100 font-mono mt-0.5">{{ $totalCustomer }} <span class="text-xs font-normal text-slate-400 font-sans">Mitra</span></div>
            </div>
        </div>

        <!-- Total Toko Terhubung -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Toko & Proyek</div>
                <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">{{ $totalTokoSemua }} <span class="text-xs font-normal text-slate-400 font-sans">Titik</span></div>
            </div>
        </div>

        <!-- Total Plafon Kredit -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
            <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Total Plafon Kredit</div>
            <div class="text-base font-bold text-blue-600 dark:text-blue-400 font-mono mt-1">Rp {{ number_format($totalPlafon ?? 0, 0, ',', '.') }}</div>
        </div>

        <!-- Piutang Berjalan -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
            <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Piutang Berjalan</div>
            <div class="text-base font-bold text-amber-600 dark:text-amber-400 font-mono mt-1">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>

        <!-- Deposit Customer -->
        <div class="bg-white dark:bg-[#14161F] p-4 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
            <div class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Saldo Deposit Aktif</div>
            <div class="text-base font-bold text-emerald-600 dark:text-emerald-400 font-mono mt-1">Rp {{ number_format($totalDeposit ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- 4. Filter & Tabel Data Customer -->
    <div x-data="tabelPaginasi({ totalData: {{ $daftarCustomer->count() }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        
        <!-- Search & Filter Bar -->
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E2E8F0] dark:border-[#252837] flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('master.customer.index') }}" class="flex flex-wrap items-center gap-2.5 flex-1">
                <div class="relative flex-1 min-w-[220px]">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}"
                           placeholder="Cari kode customer, nama pemilik, nama badan usaha, no HP..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select name="wilayah" onchange="this.form.submit()" class="px-3 py-2 text-xs rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <option value="">-- Semua Wilayah Domisili --</option>
                    @foreach($daftarWilayah as $w)
                        <option value="{{ $w->kode_wilayah }}" {{ ($filterWilayah ?? '') == $w->kode_wilayah ? 'selected' : '' }}>
                            {{ $w->nama_wilayah }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-3.5 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl transition-colors">
                    Filter
                </button>
                @if(!empty($kataKunci) || !empty($filterWilayah))
                    <a href="{{ route('master.customer.index') }}" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline">
                        Reset
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-400 font-mono shrink-0">
                Total Data: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $daftarCustomer->count() }}</span>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th x-show="!apakahReadOnly('master_customer')" class="px-3 py-3 text-center w-10">
                            <input type="checkbox" 
                                   @change="togglePilihSemua({{ json_encode(($daftarCustomer ?? collect())->pluck('kode_customer')->toArray()) }})"
                                   :checked="apakahSemuaTerpilih({{ json_encode(($daftarCustomer ?? collect())->pluck('kode_customer')->toArray()) }})"
                                   class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-blue-600 focus:ring-blue-500/30 dark:bg-[#1C1E2A] cursor-pointer"
                                   title="Pilih Semua Customer">
                        </th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Nama Pemilik & Badan Usaha</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Cabang Toko / Proyek</th>
                        <th class="px-4 py-3 text-left font-semibold uppercase tracking-wider">Wilayah & Kontak</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Plafon Kredit</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Piutang Berjalan</th>
                        <th class="px-4 py-3 text-right font-semibold uppercase tracking-wider">Saldo Deposit</th>
                        <th class="px-4 py-3 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarCustomer as $cust)
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            :class="{ 'bg-blue-50/50 dark:bg-blue-950/20': apakahTerpilih('{{ $cust->kode_customer }}') }"
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td x-show="!apakahReadOnly('master_customer')" class="px-3 py-3 text-center">
                                <input type="checkbox" 
                                       :checked="apakahTerpilih('{{ $cust->kode_customer }}')"
                                       @change="togglePilih('{{ $cust->kode_customer }}')"
                                       class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-blue-600 focus:ring-blue-500/30 dark:bg-[#1C1E2A] cursor-pointer">
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-blue-600 dark:text-blue-400">
                                {{ $cust->kode_customer }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $cust->nama_pemilik }}</div>
                                <div class="text-[11px] text-slate-500">{{ $cust->nama_toko_bangunan }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button @click="bukaModalDetail('{{ $cust->kode_customer }}')" type="button"
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 hover:bg-emerald-100 transition-colors">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span>{{ $cust->toko_bangunan_count ?? 0 }} Toko/Proyek</span>
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $cust->wilayah->nama_wilayah ?? $cust->kode_wilayah }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $cust->no_hp }}</div>
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
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$cust->kode_customer"
                                    labelSalin="Salin ID"
                                    modulIzin="master_customer"
                                    aksiDetail="bukaModalDetail('{{ $cust->kode_customer }}')"
                                    labelDetail="Detail"
                                    aksiEdit="bukaModalEdit({{ json_encode($cust) }})"
                                    labelEdit="Edit"
                                    aksiHapus="{{ route('master.customer.destroy', $cust->kode_customer) }}"
                                    labelHapus="Hapus"
                                    pesanHapus="Hapus data customer {{ $cust->nama_pemilik }} ({{ $cust->kode_customer }})?"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data customer yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Toolbar Paginasi -->
        <x-paginasi-tabel :totalData="count($daftarCustomer ?? [])" />

        <!-- Bar Aksi Massal (Multi-Select Floating Bar) -->
        <x-bar-aksi-massal 
            labelItem="customer" 
            warna="blue" 
            modulIzin="master_customer" 
            ruteHapusMassal="{{ route('master.customer.hapus_massal') }}" 
            namaInputId="daftar_kode_customer" 
            pesanPeringatan="Customer dengan saldo piutang aktif tidak akan terhapus demi keamanan finansial." 
        />
    </div>

    <!-- ========================================================================= -->
    <!-- 5. MODAL FORM: TAMBAH CUSTOMER -->
    <!-- ========================================================================= -->
    <div x-show="modalTambahTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalTambahTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Tambah Customer Baru (Entitas Pemilik)</h2>
                        <p class="text-[11px] text-slate-400">Mendaftarkan pemilik & plafon limit kredit terpusat.</p>
                    </div>
                </div>
                <button @click="modalTambahTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('master.customer.store') }}" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Customer <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold px-1.5 py-0.5 bg-blue-50 dark:bg-blue-950/50 rounded-md">Otomatis</span>
                        </div>
                        <input type="text" name="kode_customer" x-model="formTambah.kode_customer" required placeholder="CUST-001"
                               class="w-full px-3 py-2 rounded-xl bg-blue-50/50 dark:bg-[#1C1E2A] border border-blue-200 dark:border-blue-900/50 text-blue-900 dark:text-blue-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah Domisili <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_wilayah"
                            placeholder="-- Pilih Wilayah --"
                            :opsi="$opsiWilayah"
                            :wajib="true"
                            warnaFokus="blue"
                            modelBind="formTambah.kode_wilayah"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik / Direktur <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_pemilik" x-model="formTambah.nama_pemilik" required placeholder="H. Anwar Sanusi"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Usaha / Grup <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_toko_bangunan" x-model="formTambah.nama_toko_bangunan" required placeholder="Grup TB Sumber Rezeki"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp" x-model="formTambah.no_hp" required placeholder="0812-xxxx-xxxx" inputmode="numeric" data-hanya-angka="true"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Plafon Limit Piutang (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="plafon_piutang"
                            modelBind="formTambah.plafon_piutang"
                            placeholder="50.000.000"
                            :wajib="true"
                            warnaFokus="blue"
                        />
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK / KTP Pemilik <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                    <input type="text" name="no_ktp" x-model="formTambah.no_ktp" placeholder="32160xxxxxxxxxx" inputmode="numeric" data-hanya-angka="true" maxlength="16"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Domisili / Kantor Pusat <span class="text-rose-500">*</span></label>
                    <textarea name="alamat" x-model="formTambah.alamat" rows="2" required placeholder="Jl. Raya Utama No. ..."
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="modalTambahTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-sm">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. MODAL FORM: EDIT CUSTOMER -->
    <!-- ========================================================================= -->
    <div x-show="modalEditTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-visible shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">Edit Customer: <span class="font-mono text-blue-600" x-text="formEdit.kode_customer"></span></h2>
                        <p class="text-[11px] text-slate-400">Perbarui profil pemilik atau ketentuan plafon kredit.</p>
                    </div>
                </div>
                <button @click="modalEditTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('master/customer') }}/' + formEdit.kode_customer" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik / Direktur <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_pemilik" x-model="formEdit.nama_pemilik" required
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Usaha / Grup <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_toko_bangunan" x-model="formEdit.nama_toko_bangunan" required
                                class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Wilayah Domisili <span class="text-rose-500">*</span></label>
                        <x-dropdown-kustom 
                            nama="kode_wilayah"
                            placeholder="-- Pilih Wilayah --"
                            :opsi="$opsiWilayah"
                            :wajib="true"
                            warnaFokus="amber"
                            modelBind="formEdit.kode_wilayah"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Plafon Limit Piutang (Rp) <span class="text-rose-500">*</span></label>
                        <x-input-rupiah 
                            nama="plafon_piutang"
                            modelBind="formEdit.plafon_piutang"
                            placeholder="50.000.000"
                            :wajib="true"
                            warnaFokus="amber"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">No. HP / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_hp" x-model="formEdit.no_hp" required inputmode="numeric" data-hanya-angka="true"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NIK / KTP Pemilik <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span></label>
                        <input type="text" name="no_ktp" x-model="formEdit.no_ktp" inputmode="numeric" data-hanya-angka="true" maxlength="16"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Alamat Domisili <span class="text-rose-500">*</span></label>
                    <textarea name="alamat" x-model="formEdit.alamat" rows="2" required
                              class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="modalEditTerbuka = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL DETAIL KINERJA 360 DERAJAT CUSTOMER -->
    <!-- ========================================================================= -->
    <div x-show="modalDetailTerbuka" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false"
             class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-3xl overflow-visible shadow-2xl my-8">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold font-mono text-sm shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="detailData.customer ? detailData.customer.nama_pemilik : ''"></h2>
                        <p class="text-[11px] text-slate-600 dark:text-slate-300 font-mono font-medium" x-text="detailData.customer ? (detailData.customer.kode_customer + ' · ' + detailData.customer.nama_toko_bangunan) : ''"></p>
                    </div>
                </div>
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5 text-xs">
                <!-- Ringkasan Finansial 4 Kotak -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/30">
                        <div class="text-[10px] text-blue-800 dark:text-blue-300 uppercase font-semibold">Plafon Kredit</div>
                        <div class="text-sm font-bold font-mono text-blue-700 dark:text-blue-400 mt-0.5" x-text="'Rp ' + (detailData.customer ? new Intl.NumberFormat('id-ID').format(detailData.customer.plafon_piutang) : '0')"></div>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30">
                        <div class="text-[10px] text-amber-800 dark:text-amber-300 uppercase font-semibold">Piutang Berjalan</div>
                        <div class="text-sm font-bold font-mono text-amber-700 dark:text-amber-400 mt-0.5" x-text="'Rp ' + (detailData.customer ? new Intl.NumberFormat('id-ID').format(detailData.customer.saldo_piutang) : '0')"></div>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30">
                        <div class="text-[10px] text-emerald-800 dark:text-emerald-300 uppercase font-semibold">Sisa Limit Kredit</div>
                        <div class="text-sm font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-0.5" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(detailData.sisa_limit_kredit || 0)"></div>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-900/30">
                        <div class="text-[10px] text-purple-800 dark:text-purple-300 uppercase font-semibold">Saldo Deposit</div>
                        <div class="text-sm font-bold font-mono text-purple-700 dark:text-purple-400 mt-0.5" x-text="'Rp ' + (detailData.customer ? new Intl.NumberFormat('id-ID').format(detailData.customer.saldo_deposit) : '0')"></div>
                    </div>
                </div>

                <!-- Daftar Cabang Toko Bangunan & Proyek yang Dimiliki -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>Daftar Cabang Toko Bangunan & Proyek (<span x-text="detailData.total_toko || 0"></span> Titik)</span>
                        </h3>
                    </div>

                    <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                        <template x-for="toko in detailData.toko_bangunan" :key="toko.kode_toko">
                            <div class="p-3 rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="toko.kode_toko"></span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200" x-text="toko.nama_toko_bangunan"></span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase"
                                              :class="toko.tipe_lokasi === 'proyek_kontraktor' ? 'bg-amber-100 text-amber-700' : (toko.tipe_lokasi === 'gudang_transit' ? 'bg-indigo-100 text-indigo-700' : 'bg-sky-100 text-sky-700')"
                                              x-text="toko.tipe_lokasi === 'proyek_kontraktor' ? 'Proyek' : (toko.tipe_lokasi === 'gudang_transit' ? 'Gudang' : 'Retail')"></span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5" x-text="'PIC: ' + toko.penanggung_jawab + ' (' + toko.no_hp_toko + ') · ' + toko.alamat_lengkap"></div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase"
                                      :class="toko.status_toko === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600'"
                                      x-text="toko.status_toko"></span>
                            </div>
                        </template>
                        <div x-show="!detailData.toko_bangunan || detailData.toko_bangunan.length === 0" class="p-4 text-center text-slate-400 bg-slate-50 dark:bg-slate-900/50 rounded-xl">
                            Belum ada cabang toko bangunan atau proyek yang didaftarkan untuk customer ini.
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button type="button" @click="modalDetailTerbuka = false" class="px-5 py-2 font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function kelolaCustomer() {
    return {
        modalTambahTerbuka: false,
        modalEditTerbuka: false,
        modalDetailTerbuka: false,
        formTambah: {
            kode_customer: '{{ $kodeOtomatis }}',
            kode_wilayah: '',
            nama_pemilik: '',
            nama_toko_bangunan: '',
            no_hp: '',
            no_ktp: '',
            plafon_piutang: 50000000,
            alamat: '',
        },
        formEdit: {
            kode_customer: '',
            kode_wilayah: '',
            nama_pemilik: '',
            nama_toko_bangunan: '',
            no_hp: '',
            no_ktp: '',
            plafon_piutang: 0,
            alamat: '',
        },
        detailData: {},

        initCustomer() {
            // Inisialisasi
        },

        bukaModalTambah() {
            this.modalTambahTerbuka = true;
        },

        bukaModalEdit(cust) {
            this.formEdit = Object.assign({}, cust);
            this.modalEditTerbuka = true;
        },

        async bukaModalDetail(kodeCust) {
            try {
                const respon = await fetch('{{ url("master/customer") }}/' + kodeCust + '/detail');
                const data = await respon.json();
                if (data.status === 'sukses') {
                    this.detailData = data;
                    this.modalDetailTerbuka = true;
                }
            } catch (error) {
                console.error('Gagal memuat detail kinerja customer:', error);
            }
        }
    }
}
</script>
@endsection
