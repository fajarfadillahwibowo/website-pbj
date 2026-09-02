@extends('layouts.app')

@section('judul', 'Buku Pembantu Piutang Pelanggan (AR)')

@section('konten')
<div class="space-y-5" x-data="{ 
    bukaModalTambah: false,
    bukaModalDetail: false,
    bukaModalEdit: false,
    bukaModalBayar: false,
    bukaModalHapus: false,
    piutangTerpilih: {},
    detailData: {},
    loadingDetail: false,
    nomorFakturOtomatis: '',
    modeKode: 'gap',
    sedangBuatKode: false,
    keteranganKode: 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)',

    // Data Form Edit
    formEdit: {
        id_piutang: '',
        nomor_faktur: '',
        nama_toko: '',
        tanggal_jatuh_tempo: '',
        status_piutang: 'belum_lunas',
        jumlah_piutang: 0
    },

    async buatKode(mode = 'gap') {
        this.modeKode = mode;
        this.sedangBuatKode = true;
        try {
            const res = await fetch(`{{ route('keuangan.ar.piutang.buat_kode') }}?mode=${mode}`);
            const data = await res.json();
            if (data.status === 'sukses') {
                this.nomorFakturOtomatis = data.kode_otomatis;
                this.keteranganKode = data.keterangan;
            }
        } catch (e) {
            console.error('Gagal generate nomor faktur', e);
        } finally {
            this.sedangBuatKode = false;
        }
    },

    async lihatDetail(id) {
        this.loadingDetail = true;
        this.bukaModalDetail = true;
        try {
            const res = await fetch(`{{ url('keuangan/ar/list-piutang') }}/${id}`);
            const data = await res.json();
            if (data.status === 'sukses') {
                this.detailData = data.piutang;
            }
        } catch (e) {
            console.error('Gagal mengambil detail piutang', e);
        } finally {
            this.loadingDetail = false;
        }
    },

    async bukaEdit(id) {
        this.loadingDetail = true;
        this.bukaModalEdit = true;
        try {
            const res = await fetch(`{{ url('keuangan/ar/list-piutang') }}/${id}`);
            const data = await res.json();
            if (data.status === 'sukses') {
                const p = data.piutang;
                this.formEdit = {
                    id_piutang: p.id_piutang,
                    nomor_faktur: p.nomor_faktur,
                    nama_toko: p.nama_toko_bangunan,
                    tanggal_jatuh_tempo: p.tanggal_jatuh_tempo,
                    status_piutang: p.status_piutang,
                    jumlah_piutang: p.jumlah_piutang
                };
            }
        } catch (e) {
            console.error('Gagal memuat data edit', e);
        } finally {
            this.loadingDetail = false;
        }
    },

    bukaBayar(item) {
        this.piutangTerpilih = item;
        this.bukaModalBayar = true;
    },

    setNominalBayar(persen) {
        const sisa = this.piutangTerpilih.sisa || 0;
        this.piutangTerpilih.jumlah_bayar = Math.round(sisa * (persen / 100));
    },

    bukaHapus(item) {
        this.piutangTerpilih = item;
        this.bukaModalHapus = true;
    }
}" x-init="buatKode('gap')">

    <!-- Flash Notifications -->
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

    <!-- Header Modul List Piutang -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-amber-600 dark:text-amber-400 font-semibold font-mono uppercase tracking-wider mb-1">Account Receivable · SPV Keuangan</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Buku Pembantu Piutang Pelanggan</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Monitoring saldo piutang toko mitra, pencatatan pembayaran cicilan, status jatuh tempo, dan kontrol plafon kredit.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaModalTambah = true; if(!nomorFakturOtomatis) buatKode('gap')" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Catat Piutang Baru
            </button>
        </div>
    </div>

    <!-- Ringkasan 5 Kartu Statistik Piutang -->
    <div class="wadah-bertingkat grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Piutang Terbit</div>
            <div class="text-base sm:text-lg font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Sisa Piutang Berjalan</div>
            <div class="text-base sm:text-lg font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono">Rp {{ number_format($totalSisa ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Total Terbayar / Lunas</div>
            <div class="text-base sm:text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837]">
            <div class="text-[10px] text-rose-500 font-semibold uppercase tracking-wider">Lewat Jatuh Tempo</div>
            <div class="text-base sm:text-lg font-bold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($totalTerlambat ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] col-span-2 sm:col-span-1">
            <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Toko Berpiutang Aktif</div>
            <div class="text-base sm:text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 font-mono">{{ $totalCustomerPiutang ?? 0 }} Toko</div>
        </div>
    </div>

    <!-- Filter & Tabel Data Piutang -->
    <div class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.ar.piutang') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterStatusPiutang = [
                    ['nilai' => '', 'label' => '-- Semua Status --'],
                    ['nilai' => 'belum_lunas', 'label' => 'Belum Lunas'],
                    ['nilai' => 'sebagian', 'label' => 'Cicilan Sebagian'],
                    ['nilai' => 'terlambat', 'label' => '⚠️ Lewat Jatuh Tempo'],
                    ['nilai' => 'lunas', 'label' => 'Lunas'],
                ];

                $opsiCustomerFilter = [['nilai' => '', 'label' => '-- Semua Customer Toko --']];
                foreach($daftarCustomer ?? [] as $cust) {
                    $opsiCustomerFilter[] = [
                        'nilai' => $cust->kode_customer,
                        'label' => $cust->nama_toko_bangunan . " ({$cust->kode_customer})"
                    ];
                }
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <div class="relative w-full sm:w-60">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari faktur, toko, pemilik..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-48">
                    <x-dropdown-kustom 
                        nama="status" 
                        :nilaiAwal="$filterStatus ?? ''" 
                        placeholder="-- Semua Status --" 
                        :opsi="$opsiFilterStatusPiutang" 
                        warnaFokus="amber"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                <div class="w-full sm:w-56">
                    <x-dropdown-kustom 
                        nama="customer" 
                        :nilaiAwal="$filterCustomer ?? ''" 
                        placeholder="-- Semua Customer Toko --" 
                        :opsi="$opsiCustomerFilter" 
                        warnaFokus="amber"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
            </div>
            <span class="text-xs text-slate-400 font-mono">Tabel: list_piutang & penjualan</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">No. Faktur</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Customer / Mitra Toko</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Total Piutang</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Sisa Piutang</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Status & Aging</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi & Riwayat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarPiutang ?? [] as $piutang)
                        @php
                            $aging = $piutang->status_aging;
                            $nomorFaktur = $piutang->penjualan->nomor_faktur ?? "ID-{$piutang->id_penjualan}";
                        @endphp
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td class="px-4 py-3 font-mono font-medium text-blue-600 dark:text-blue-400">
                                <div class="font-bold">{{ $nomorFaktur }}</div>
                                <div class="text-[10px] text-slate-400">Terbit: {{ $piutang->tanggal_terbit ? date('d/m/Y', strtotime($piutang->tanggal_terbit)) : '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $piutang->customer->nama_toko_bangunan ?? $piutang->kode_customer }}</div>
                                <div class="text-[11px] text-slate-400">Pemilik: {{ $piutang->customer->nama_pemilik ?? '-' }} · Telp: {{ $piutang->customer->no_hp ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums text-slate-600 dark:text-slate-400">
                                <div>Rp {{ number_format($piutang->jumlah_piutang, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-emerald-600 dark:text-emerald-400">Terbayar: Rp {{ number_format($piutang->jumlah_terbayar, 0, ',', '.') }} ({{ $piutang->persentase_terbayar }}%)</div>
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold {{ $piutang->sisa_piutang > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                                Rp {{ number_format($piutang->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center font-mono text-[11px]">
                                <div class="text-slate-700 dark:text-slate-300">{{ date('d/m/Y', strtotime($piutang->tanggal_jatuh_tempo)) }}</div>
                                <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[9px] font-semibold {{ $aging['badge'] }}">
                                    {{ $aging['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($piutang->status_piutang === 'lunas')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Lunas
                                    </span>
                                @elseif($piutang->status_piutang === 'sebagian')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20">
                                        Cicilan Sebagian
                                    </span>
                                @elseif($piutang->status_piutang === 'macet')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                        Macet
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Detail -->
                                    <button @click="lihatDetail({{ $piutang->id_piutang }})"
                                            class="p-1 text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                            title="Lihat Detail & Aging Piutang">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    <!-- Bayar Cicilan -->
                                    @if($piutang->sisa_piutang > 0)
                                        <button @click="bukaBayar({
                                                    id_piutang: {{ $piutang->id_piutang }},
                                                    nama_toko: '{{ addslashes($piutang->customer->nama_toko_bangunan ?? $piutang->kode_customer) }}',
                                                    faktur: '{{ $nomorFaktur }}',
                                                    sisa: {{ (float) $piutang->sisa_piutang }},
                                                    jumlah_bayar: {{ (float) $piutang->sisa_piutang }}
                                                })"
                                                class="px-2 py-1 text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 rounded-lg transition-colors border border-emerald-200 dark:border-emerald-500/20"
                                                title="Catat Pelunasan / Cicilan">
                                            Bayar
                                        </button>
                                    @endif

                                    <!-- Edit -->
                                    <button @click="bukaEdit({{ $piutang->id_piutang }})"
                                            class="p-1 text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                            title="Edit Data Piutang">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Hapus -->
                                    <button @click="bukaHapus({
                                                id_piutang: {{ $piutang->id_piutang }},
                                                faktur: '{{ $nomorFaktur }}',
                                                nama_toko: '{{ addslashes($piutang->customer->nama_toko_bangunan ?? $piutang->kode_customer) }}'
                                            })"
                                            class="p-1 text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors"
                                            title="Hapus Piutang">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                <div class="text-[9px] text-slate-400 font-mono mt-1" title="Waktu: {{ $piutang->terakhir_diedit_waktu }}">
                                    🕒 {{ $piutang->terakhir_diedit_relatif }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="font-medium">Tidak ada catatan piutang yang sesuai dengan kriteria.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 1: TAMBAH CATATAN PIUTANG BARU (CREATE) -->
    <!-- ============================================================== -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Catat Piutang Pelanggan Baru</h3>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form method="POST" action="{{ route('keuangan.ar.piutang.simpan') }}" class="p-5 space-y-3.5 text-xs">
                @csrf
                
                <!-- Nomor Faktur & Generator -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold text-slate-700 dark:text-slate-300">Nomor Faktur / Tagihan</label>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="buatKode('gap')" :disabled="sedangBuatKode"
                                    :class="modeKode === 'gap' ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                    class="text-[9px] font-semibold px-1.5 py-0.5 rounded transition-all">
                                🔵 Daur Ulang
                            </button>
                            <button type="button" @click="buatKode('acak')" :disabled="sedangBuatKode"
                                    :class="modeKode === 'acak' ? 'bg-purple-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                    class="text-[9px] font-semibold px-1.5 py-0.5 rounded transition-all">
                                🟣 Acak
                            </button>
                        </div>
                    </div>
                    <input type="text" name="nomor_faktur" x-model="nomorFakturOtomatis" required placeholder="INV-20260901-001"
                           class="w-full px-3 py-2 rounded-xl bg-amber-50/50 dark:bg-[#1C1E2A] border border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <span class="text-[9px] text-slate-400 font-mono mt-0.5 block" x-text="keteranganKode"></span>
                </div>

                <!-- Customer Toko Bangunan -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer / Toko Bangunan</label>
                    <x-dropdown-kustom 
                        nama="kode_customer"
                        placeholder="-- Pilih Toko Bangunan --"
                        :opsi="array_slice($opsiCustomerFilter, 1)"
                        :wajib="true"
                        warnaFokus="amber"
                    />
                </div>

                <!-- Tanggal Terbit & Jatuh Tempo -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Terbit</label>
                        <input type="date" name="tanggal_terbit" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Jatuh Tempo</label>
                        <input type="date" name="tanggal_jatuh_tempo" required value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                               class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    </div>
                </div>

                <!-- Nominal Piutang -->
                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Nominal Piutang (Rp)</label>
                    <input type="number" name="jumlah_piutang" required min="1000" step="50000" placeholder="contoh: 25000000"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono text-sm font-semibold">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="bukaModalTambah = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Catatan Piutang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 2: LIHAT DETAIL & AGING PIUTANG (READ) -->
    <!-- ============================================================== -->
    <div x-show="bukaModalDetail" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalDetail = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-lg overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Detail & Rincian Piutang Toko</h3>
                </div>
                <button @click="bukaModalDetail = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            
            <div class="p-5 space-y-4 text-xs">
                <!-- Loading State -->
                <div x-show="loadingDetail" class="py-8 text-center text-slate-400">
                    <div class="animate-spin w-6 h-6 border-2 border-amber-600 border-t-transparent rounded-full mx-auto mb-2"></div>
                    <span>Memuat informasi detail piutang...</span>
                </div>

                <div x-show="!loadingDetail" class="space-y-4">
                    <!-- Header Info Faktur & Toko -->
                    <div class="p-4 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837] space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400" x-text="detailData.nomor_faktur"></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold"
                                  :class="detailData.status_aging?.badge"
                                  x-text="detailData.status_aging?.label"></span>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900 dark:text-slate-100" x-text="detailData.nama_toko_bangunan"></div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                Pemilik: <span class="font-medium text-slate-700 dark:text-slate-300" x-text="detailData.nama_pemilik"></span> · 
                                No. Telp: <span class="font-mono text-slate-700 dark:text-slate-300" x-text="detailData.no_hp"></span>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                Alamat: <span class="text-slate-700 dark:text-slate-300" x-text="detailData.alamat"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar Pembayaran -->
                    <div class="space-y-1.5 p-3.5 bg-white dark:bg-[#14161F] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-500">Progres Pelunasan</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="(detailData.persentase_terbayar || 0) + '% Lunas'"></span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-300"
                                 :style="'width: ' + (detailData.persentase_terbayar || 0) + '%'"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-400 font-mono pt-0.5">
                            <span>Terbayar: <strong class="text-slate-700 dark:text-slate-300" x-text="detailData.jumlah_terbayar_rupiah"></strong></span>
                            <span>Sisa: <strong class="text-amber-600 dark:text-amber-400" x-text="detailData.sisa_piutang_rupiah"></strong></span>
                        </div>
                    </div>

                    <!-- Ringkasan Angka Finansial -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Total Nilai Tagihan</div>
                            <div class="text-sm font-bold text-slate-900 dark:text-slate-100 mt-0.5 font-mono" x-text="detailData.jumlah_piutang_rupiah"></div>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Sisa Piutang Berjalan</div>
                            <div class="text-sm font-bold text-amber-600 dark:text-amber-400 mt-0.5 font-mono" x-text="detailData.sisa_piutang_rupiah"></div>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Tanggal Terbit</div>
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5 font-mono" x-text="detailData.tanggal_terbit_format"></div>
                        </div>
                        <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Tanggal Jatuh Tempo</div>
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5 font-mono" x-text="detailData.tanggal_jatuh_tempo_format"></div>
                        </div>
                    </div>

                    <!-- Plafon & Saldo Customer -->
                    <div class="p-3 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 rounded-xl flex items-center justify-between">
                        <div>
                            <div class="text-[10px] text-amber-700 dark:text-amber-400 font-semibold">Plafon Kredit Toko</div>
                            <div class="font-bold text-slate-800 dark:text-slate-200 font-mono" x-text="detailData.plafon_piutang_rupiah"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] text-amber-700 dark:text-amber-400 font-semibold">Total Saldo Terpakai</div>
                            <div class="font-bold text-amber-700 dark:text-amber-300 font-mono" x-text="detailData.saldo_piutang_customer"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono pt-1">
                        <span>Terakhir Diedit: <strong class="text-slate-600 dark:text-slate-300" x-text="detailData.terakhir_diedit_relatif"></strong></span>
                        <span x-text="detailData.terakhir_diedit_waktu"></span>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="bukaModalDetail = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 3: EDIT DATA PIUTANG (UPDATE) -->
    <!-- ============================================================== -->
    <div x-show="bukaModalEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalEdit = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Edit Data Piutang</h3>
                <button @click="bukaModalEdit = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('keuangan/ar/list-piutang') }}/' + formEdit.id_piutang" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                @method('PUT')

                <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837] space-y-0.5">
                    <div class="text-[11px] text-slate-400">No Faktur: <span class="font-mono font-bold text-blue-600 dark:text-blue-400" x-text="formEdit.nomor_faktur"></span></div>
                    <div class="text-[11px] text-slate-400">Customer: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="formEdit.nama_toko"></span></div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Jatuh Tempo Baru</label>
                    <input type="date" name="tanggal_jatuh_tempo" required x-model="formEdit.tanggal_jatuh_tempo"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Nominal Piutang (Rp)</label>
                    <input type="number" name="jumlah_piutang" required min="1000" step="50000" x-model="formEdit.jumlah_piutang"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30 font-mono text-sm font-semibold">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Piutang</label>
                    <select name="status_piutang" x-model="formEdit.status_piutang" required
                            class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                        <option value="belum_lunas">Belum Lunas</option>
                        <option value="sebagian">Cicilan Sebagian</option>
                        <option value="lunas">Lunas Sepenuhnya</option>
                        <option value="macet">Piutang Macet</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="bukaModalEdit = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 4: CATAT PEMBAYARAN CICILAN / PELUNASAN -->
    <!-- ============================================================== -->
    <div x-show="bukaModalBayar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalBayar = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md overflow-hidden shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] dark:border-[#252837]">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Pelunasan / Cicilan Piutang</h3>
                <button @click="bukaModalBayar = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
            </div>
            <form :action="'{{ url('keuangan/ar/list-piutang') }}/' + piutangTerpilih.id_piutang + '/bayar'" method="POST" class="p-5 space-y-3.5 text-xs">
                @csrf
                <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837] space-y-1">
                    <div class="text-[11px] text-slate-400">Toko: <span class="font-bold text-slate-800 dark:text-slate-200" x-text="piutangTerpilih.nama_toko"></span></div>
                    <div class="text-[11px] text-slate-400">Faktur: <span class="font-mono text-blue-600 dark:text-blue-400" x-text="piutangTerpilih.faktur"></span></div>
                    <div class="text-[11px] text-slate-400">Sisa Piutang: <span class="font-mono font-bold text-amber-600 dark:text-amber-400">Rp <span x-text="new Intl.NumberFormat('id-ID').format(piutangTerpilih.sisa || 0)"></span></span></div>
                </div>

                <!-- Tombol Opsi Cepat -->
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="setNominalBayar(25)" class="px-2 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200">25%</button>
                    <button type="button" @click="setNominalBayar(50)" class="px-2 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200">50%</button>
                    <button type="button" @click="setNominalBayar(75)" class="px-2 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200">75%</button>
                    <button type="button" @click="setNominalBayar(100)" class="px-2 py-1 rounded-lg text-[10px] font-semibold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-200">100% (Lunas)</button>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Nominal Bayar (Rp)</label>
                    <input type="number" name="jumlah_bayar" :max="piutangTerpilih.sisa" required min="1" step="1000" x-model="piutangTerpilih.jumlah_bayar"
                           class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 font-mono font-semibold text-sm">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button @click="bukaModalBayar = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 5: KONFIRMASI HAPUS PIUTANG (DELETE) -->
    <!-- ============================================================== -->
    <div x-show="bukaModalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalHapus = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-sm overflow-hidden shadow-xl">
            <div class="p-5 text-center space-y-3">
                <div class="w-10 h-10 rounded-full bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Hapus Catatan Piutang?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Apakah Anda yakin ingin menghapus piutang <strong class="text-slate-700 dark:text-slate-200" x-text="piutangTerpilih.faktur"></strong> untuk <strong class="text-slate-700 dark:text-slate-200" x-text="piutangTerpilih.nama_toko"></strong>? Saldo piutang customer akan disesuaikan kembali.
                    </p>
                </div>
                <form :action="'{{ url('keuangan/ar/list-piutang') }}/' + piutangTerpilih.id_piutang" method="POST" class="pt-2">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center justify-center gap-2">
                        <button @click="bukaModalHapus = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                        <button type="submit" class="px-4 py-2 font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-sm">Ya, Hapus Piutang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
