@extends('layouts.app')

@section('judul', 'Aset & Inventaris Perusahaan')

@section('konten')
<div class="space-y-5" x-data="kelolaAsetPerusahaan()">
    <!-- Flash Notification -->
    @if(session('sukses'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('sukses') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-xs font-medium flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold">&times;</button>
        </div>
    @endif

    <!-- Header Modul Aset Perusahaan -->
    <div class="animasi-masuk flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-[#14161F] p-4 sm:p-5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-sm">
        <div>
            <div class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold font-mono uppercase tracking-wider mb-1">Buku Besar & Akuntansi · Aktiva Tetap</div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100">Master Aset Tetap & Depresiasi</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pengelolaan aktiva tetap (Tanah, Bangunan, Mesin Gudang, Truk, Alat Kantor) beserta amortisasi penyusutan bulanan.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="bukaTabRiwayat = !bukaTabRiwayat" type="button" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-[#F4F6F9] dark:bg-[#1C1E2A] hover:bg-slate-200 dark:hover:bg-[#252837] border border-[#E2E8F0] dark:border-[#252837] rounded-xl transition-all">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="bukaTabRiwayat ? 'Tutup Log Susut' : 'Log Penyusutan'">Log Penyusutan</span>
            </button>
            <button @click="bukaModalSusut = true" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Proses Tutup Buku Susut
            </button>
            <button @click="bukaModalTambah = true; aturKategori('AST-TRK')" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Aset Baru
            </button>
        </div>
    </div>

    <!-- 5 Kartu Indikator Finansial Aset -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs">
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Total Harga Perolehan</div>
            <div class="text-base font-extrabold text-slate-900 dark:text-white mt-0.5 font-mono">Rp {{ number_format($totalNilaiPerolehan ?? 0, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Nilai beli awal aktiva</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs">
            <div class="text-[10px] text-rose-600 dark:text-rose-400 font-bold uppercase tracking-wider">Akumulasi Penyusutan</div>
            <div class="text-base font-extrabold text-rose-600 dark:text-rose-400 mt-0.5 font-mono">Rp {{ number_format($totalAkumulasiSusut ?? 0, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Total depresiasi terpakai</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs">
            <div class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider">Nilai Buku Bersih</div>
            <div class="text-base font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5 font-mono">Rp {{ number_format($totalNilaiBuku ?? 0, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Nilai di Neraca Keuangan</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] shadow-xs">
            <div class="text-[10px] text-orange-600 dark:text-orange-400 font-bold uppercase tracking-wider">Beban Susut / Bulan</div>
            <div class="text-base font-extrabold text-orange-600 dark:text-orange-400 mt-0.5 font-mono">Rp {{ number_format($estimasiSusutBulanIni ?? 0, 0, ',', '.') }}</div>
            <div class="text-[10px] text-slate-400 mt-1">Amortisasi rutin bulanan</div>
        </div>
        <div class="bg-white dark:bg-[#14161F] p-3.5 rounded-2xl border border-[#E2E8F0] dark:border-[#252837] col-span-2 md:col-span-1 shadow-xs">
            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider">Total Entitas Aset</div>
            <div class="text-base font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5 font-mono">{{ $totalUnitAset ?? 0 }} Unit</div>
            <div class="text-[10px] text-slate-400 mt-1">Tanah, Gedung, Truk, dsb.</div>
        </div>
    </div>

    <!-- Panel Riwayat Penyusutan Terbaru (Toggleable) -->
    <div x-show="bukaTabRiwayat" x-cloak class="animasi-masuk bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3 border-b border-[#E2E8F0] dark:border-[#252837] pb-2">
            <div>
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Log Riwayat Penyusutan & Jurnal Akuntansi (10 Transaksi Terakhir)</h3>
                <p class="text-[11px] text-slate-400">Pencatatan beban depresiasi yang telah diposting otomatis ke Buku Besar.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] text-slate-500 border-b border-[#E2E8F0] dark:border-[#252837]">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">No. Bukti Susut</th>
                        <th class="px-3 py-2 text-left font-semibold">Nama Aset</th>
                        <th class="px-3 py-2 text-center font-semibold">Periode</th>
                        <th class="px-3 py-2 text-right font-semibold">Beban Susut</th>
                        <th class="px-3 py-2 text-right font-semibold">Sisa Nilai Buku</th>
                        <th class="px-3 py-2 text-center font-semibold">No. Jurnal</th>
                        <th class="px-3 py-2 text-left font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($riwayatTerbaru as $susut)
                        @php /** @var \App\Models\Keuangan\RiwayatPenyusutan $susut */ @endphp
                        <tr class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50">
                            <td class="px-3 py-2 font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $susut->nomor_penyusutan }}</td>
                            <td class="px-3 py-2 font-semibold text-slate-900 dark:text-slate-100">{{ $susut->aset->nama_aset ?? $susut->kode_aset }}</td>
                            <td class="px-3 py-2 text-center font-mono">{{ sprintf('%02d', $susut->periode_bulan) }}/{{ $susut->periode_tahun }}</td>
                            <td class="px-3 py-2 text-right font-mono font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format((float) ($susut->beban_penyusutan ?? 0), 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-mono font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format((float) ($susut->nilai_buku ?? 0), 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-center font-mono font-medium text-emerald-600 dark:text-emerald-400">{{ $susut->nomor_jurnal ?? '-' }}</td>
                            <td class="px-3 py-2 text-slate-500">{{ $susut->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-4 text-center text-slate-400">Belum ada transaksi penyusutan yang pernah diproses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Data Aset Tetap -->
    <div x-data="tabelPaginasi({ totalData: {{ count($daftarAset ?? []) }}, defaultBaris: 10 })" class="animasi-masuk tunda-2 bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl overflow-hidden shadow-sm">
        <form method="GET" action="{{ route('keuangan.akuntansi.aset') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            @php
                $opsiFilterJenisAset = array_merge([['nilai' => '', 'label' => '-- Semua Jenis Aset --']], ($daftarJenis ?? collect())->map(fn($j) => [
                    'nilai' => $j->kode_jenis_aset,
                    'label' => $j->jenis_aset
                ])->toArray());
            @endphp
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="cari" value="{{ $kataKunci ?? '' }}" placeholder="Cari nama aset / kode / no polisi..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-700 dark:text-slate-300 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="w-full sm:w-56">
                    <x-dropdown-kustom 
                        nama="jenis" 
                        :nilaiAwal="$filterJenis ?? ''" 
                        placeholder="-- Semua Jenis Aset --" 
                        :opsi="$opsiFilterJenisAset" 
                        warnaFokus="indigo"
                        classTombol="py-1.5"
                        :submitOnChange="true" 
                    />
                </div>
                @if(!empty($kataKunci) || !empty($filterJenis))
                    <a href="{{ route('keuangan.akuntansi.aset') }}" class="text-xs font-semibold text-rose-600 hover:underline">Reset</a>
                @endif
            </div>
            <span class="text-xs text-slate-400 font-mono">Master Aktiva Tetap & Nilai Buku</span>
        </form>

        <div class="overflow-x-auto">
            <table class="tabel-bertingkat w-full text-xs">
                <thead class="bg-[#F8FAFC] dark:bg-[#1C1E2A] border-b border-[#E2E8F0] dark:border-[#252837] text-slate-500">
                    <tr>
                        <th x-show="!apakahReadOnly('akun_aset')" class="w-10 px-3 py-2.5 text-center">
                            <input type="checkbox" 
                                   @change="togglePilihSemua({{ json_encode(($daftarAset ?? collect())->pluck('kode_aset')->toArray()) }})"
                                   :checked="apakahSemuaTerpilih({{ json_encode(($daftarAset ?? collect())->pluck('kode_aset')->toArray()) }})"
                                   class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-indigo-600 focus:ring-indigo-500/30 cursor-pointer">
                        </th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Kode Aset</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Nama Aset & Kategori</th>
                        <th class="px-4 py-2.5 text-left font-semibold uppercase tracking-wider">Penyusutan</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Harga Perolehan</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Akumulasi Susut</th>
                        <th class="px-4 py-2.5 text-right font-semibold uppercase tracking-wider">Nilai Buku Bersih</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider" title="Keterikatan fisik armada truk operasional di logistik">Armada Fisik Terhubung</th>
                        <th class="px-4 py-2.5 text-center font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EEF0F4] dark:divide-[#252837] text-slate-700 dark:text-slate-300">
                    @forelse($daftarAset as $aset)
                        @php /** @var \App\Models\Keuangan\AsetPerusahaan $aset */ @endphp
                        <tr x-show="apakahBarisTampil({{ $loop->index }})" 
                            :class="{ 'bg-indigo-50/50 dark:bg-indigo-950/20': apakahTerpilih('{{ $aset->kode_aset }}') }"
                            class="hover:bg-[#F8FAFC] dark:hover:bg-[#252837]/50 transition-colors">
                            <td x-show="!apakahReadOnly('akun_aset')" class="w-10 px-3 py-3 text-center">
                                <input type="checkbox" 
                                       :checked="apakahTerpilih('{{ $aset->kode_aset }}')"
                                       @change="togglePilih('{{ $aset->kode_aset }}')"
                                       class="w-4 h-4 rounded border-[#CBD5E1] dark:border-[#334155] text-indigo-600 focus:ring-indigo-500/30 cursor-pointer">
                            </td>
                            <td class="px-4 py-3 font-mono font-medium text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                {{ $aset->kode_aset }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $aset->nama_aset }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ $aset->jenisAset->jenis_aset ?? $aset->kode_jenis_aset }}
                                    </span>
                                    <span>· Beli: {{ $aset->tanggal_pembelian ? date('d/m/Y', strtotime($aset->tanggal_pembelian)) : '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($aset->metode_penyusutan === 'Tidak Disusutkan' || $aset->umur_manfaat == 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        Tanpa Penyusutan (Permanen)
                                    </span>
                                @else
                                    <div class="font-medium text-slate-700 dark:text-slate-300">{{ $aset->metode_penyusutan }} ({{ $aset->umur_manfaat }} Th / {{ $aset->tarif_penyusutan }}%)</div>
                                    <div class="text-[10px] text-amber-600 dark:text-amber-400 font-mono">Susut: Rp {{ number_format($aset->hitungPenyusutanBulanan(), 0, ',', '.') }}/bln</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                Rp {{ number_format((float) ($aset->harga_perolehan ?? $aset->harga_aset ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap">
                                Rp {{ number_format((float) ($aset->akumulasi_penyusutan ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono tabular-nums font-bold text-indigo-600 dark:text-indigo-400 whitespace-nowrap">
                                Rp {{ number_format((float) ($aset->nilai_buku ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($aset->dataKendaraan)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-blue-50 dark:bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-500/30 shadow-2xs"
                                          title="Tercatat di Master Armada Operasional: {{ $aset->dataKendaraan->merek_kendaraan ?? 'Truk' }} ({{ $aset->dataKendaraan->no_polisi }})">
                                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        <span>{{ $aset->dataKendaraan->kode_kendaraan }} ({{ $aset->dataKendaraan->no_polisi }})</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs font-mono" title="Aset non-kendaraan (Bukan armada jalan)">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <x-menu-aksi-tabel 
                                    :kodeSalin="$aset->kode_aset"
                                    labelSalin="Salin Kode"
                                    modulIzin="akun_aset"
                                    aksiDetail="bukaDetail('{{ $aset->kode_aset }}')"
                                    labelDetail="Detail"
                                    aksiEdit="bukaEdit('{{ $aset->kode_aset }}')"
                                    labelEdit="Edit"
                                    aksiHapus="{{ route('keuangan.akuntansi.aset.destroy', $aset->kode_aset) }}"
                                    labelHapus="Hapus"
                                    pesanHapus="Apakah Anda yakin ingin menghapus aset {{ $aset->nama_aset }} ({{ $aset->kode_aset }})?"
                                />

                                <!-- Riwayat Diedit Real-Time -->
                                <div class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 flex items-center justify-center gap-0.5 font-mono cursor-help"
                                     title="Terakhir diperbarui: {{ $aset->diperbarui_pada ? \Carbon\Carbon::parse($aset->diperbarui_pada)->format('d/m/Y H:i:s') : ($aset->dibuat_pada ? \Carbon\Carbon::parse($aset->dibuat_pada)->format('d/m/Y H:i:s') : '-') }}">
                                    <span>{{ $aset->diperbarui_pada ? \Carbon\Carbon::parse($aset->diperbarui_pada)->locale('id')->diffForHumans() : ($aset->dibuat_pada ? \Carbon\Carbon::parse($aset->dibuat_pada)->locale('id')->diffForHumans() : 'Baru') }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">Belum ada aktiva tetap terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-paginasi-tabel :totalData="count($daftarAset ?? [])" />

        <!-- Bar Aksi Massal (Multi-Select Floating Bar) -->
        <x-bar-aksi-massal labelItem="aset" warna="indigo" modulIzin="akun_aset" />
    </div>

    <!-- Modal 1: Tambah Aset Baru Komprehensif (Canvas Landscape) -->
    <div x-show="bukaModalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="bukaModalTambah = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-4xl lg:max-w-5xl overflow-hidden shadow-2xl my-6">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50/70 dark:bg-[#1C1E2A]/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Daftarkan Aset Tetap Baru</h3>
                        <p class="text-[11px] text-slate-500">Pencatatan aktiva tetap finansial, armada operasional, dan parameter depresiasi PSAK 16.</p>
                    </div>
                </div>
                <button @click="bukaModalTambah = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('keuangan.akuntansi.aset.store') }}" class="p-6 space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- KOLOM KIRI: Identitas, Finansial & Depresiasi PSAK 16 (5 Kolom) -->
                    <div class="lg:col-span-5 space-y-3.5 border-b lg:border-b-0 lg:border-r border-[#E2E8F0] dark:border-[#252837] pb-4 lg:pb-0 lg:pr-6">
                        <div class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>1. Data Pokok & Finansial Aset</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300">Kode Aset</label>
                                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/50 rounded-md">Auto</span>
                                </div>
                                <input type="text" name="kode_aset" value="{{ $kodeOtomatis }}" :disabled="jumlahUnit > 1" placeholder="AST-001"
                                       class="w-full px-3 py-2 rounded-xl bg-indigo-50/50 dark:bg-[#1C1E2A] border border-indigo-200 dark:border-indigo-900/50 text-indigo-900 dark:text-indigo-300 font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300">Jumlah Unit <span class="text-rose-500">*</span></label>
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold" x-show="jumlahUnit > 1" x-text="jumlahUnit + ' Unit'"></span>
                                </div>
                                <input type="number" name="jumlah_unit" x-model.number="jumlahUnit" @input="sinkronkanRincian()" min="1" max="50" required
                                       class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Golongan Kategori Aset <span class="text-rose-500">*</span></label>
                            <select name="kode_jenis_aset" x-model="pilihanJenis" @change="aturKategori($event.target.value)" required
                                    class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 font-medium">
                                <option value="AST-TRK">1. Kendaraan Armada (Truk / Tronton / Mobil Operasional)</option>
                                <option value="AST-TNH">2. Tanah & Bangunan Properti (Lahan, Kantor, Gudang Semen, Pos)</option>
                                <option value="AST-GDG">3. Mesin & Alat Gudang (Forklift / Genset / Conveyor / Timbangan)</option>
                                <option value="AST-OFC">4. Elektronik & Perabot Kantor (Komputer / Printer / AC / Meja)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Tipe / Model Aset <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_aset" required placeholder="Contoh: Hino Dutro 130 HD / Lahan & Gudang Semen Palembang / Forklift 3.5T"
                                   class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Entitas Pemilik / STNK <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_pemilik" value="PT Putra Balkom Jaya" required
                                       class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-medium">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                                <select name="status_aset" class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-medium">
                                    <option value="aktif">Aktif</option>
                                    <option value="dalam_perbaikan">Dalam Perbaikan</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="non-aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-rupiah nama="harga_perolehan" label="Harga Perolehan Satuan (Rp)" :wajib="true" placeholder="350.000.000" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Perolehan <span class="text-rose-500">*</span></label>
                                <x-input-tanggal nama="tanggal_pembelian" nilaiAwal="{{ date('Y-m-d') }}" placeholder="Pilih Tanggal Beli" :wajib="true" warnaFokus="indigo" />
                            </div>
                        </div>

                        <!-- Parameter Depresiasi Otomatis PSAK 16 -->
                        <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] rounded-xl space-y-2.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-800 dark:text-slate-200 text-[11px]">Parameter Penyusutan (PSAK 16)</span>
                                <span class="text-[10px] font-mono text-indigo-600 dark:text-indigo-400 font-semibold" x-text="'Metode: ' + metodeSusut"></span>
                            </div>
                            <input type="hidden" name="metode_penyusutan" :value="metodeSusut">

                            <!-- Khusus Tanah & Bangunan Properti: Pilihan Penyusutan -->
                            <template x-if="pilihanJenis === 'AST-TNH'">
                                <div class="space-y-2">
                                    <div class="text-[10px] text-slate-500">
                                        Tanah murni bebas penyusutan. Porsi bangunan gedung disusutkan 20 Tahun (5%/tahun).
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="umurManfaat = 20; tarifSusut = 5.0; metodeSusut = 'Garis Lurus'"
                                                :class="umurManfaat > 0 ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-[#14161F] text-slate-600 dark:text-slate-400'"
                                                class="p-2 rounded-lg border text-left text-[11px] font-semibold transition-all">
                                            <div>Ada Bangunan Gedung</div>
                                            <div class="text-[10px] font-normal opacity-80">Susut 20 Th / 5%</div>
                                        </button>
                                        <button type="button" @click="umurManfaat = 0; tarifSusut = 0; metodeSusut = 'Tidak Disusutkan'"
                                                :class="umurManfaat === 0 ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-[#14161F] text-slate-600 dark:text-slate-400'"
                                                class="p-2 rounded-lg border text-left text-[11px] font-semibold transition-all">
                                            <div>Tanah Kosong Saja</div>
                                            <div class="text-[10px] font-normal opacity-80">Bebas Penyusutan</div>
                                        </button>
                                    </div>
                                    <input type="hidden" name="umur_manfaat" :value="umurManfaat">
                                    <input type="hidden" name="tarif_penyusutan" :value="tarifSusut">
                                </div>
                            </template>

                            <!-- Kategori Selain Tanah -->
                            <template x-if="pilihanJenis !== 'AST-TNH'">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-0.5 text-[10px]">Masa Manfaat (Th)</label>
                                        <input type="number" name="umur_manfaat" x-model="umurManfaat" min="1" max="50" required
                                               class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-600 dark:text-slate-400 mb-0.5 text-[10px]">Tarif Susut / Th (%)</label>
                                        <input type="number" step="0.01" name="tarif_penyusutan" x-model="tarifSusut" min="0" max="100" required
                                               class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono text-xs">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: Spesifikasi Fisik Sesuai Diagram ERD (7 Kolom) -->
                    <div class="lg:col-span-7 space-y-3.5">
                        <div class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider flex items-center gap-1.5 mb-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>2. Spesifikasi Objek Fisik & Lapangan</span>
                        </div>

                        <!-- A. KONDISIONAL KENDARAAN ARMADA TRUK -->
                        <div x-show="pilihanJenis === 'AST-TRK'" x-cloak class="p-4 bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 rounded-2xl space-y-3">
                            <div class="flex items-center justify-between text-blue-900 dark:text-blue-300 font-bold text-xs">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    <span>Spesifikasi Fisik Armada Kendaraan Logistik (ERD)</span>
                                </div>
                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-mono" x-text="jumlahUnit + ' Unit Armada'"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis / Tipe Kendaraan <span class="text-rose-500">*</span></label>
                                    <select name="jenis_kendaraan" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-medium">
                                        <option value="Tronton Wingbox">Tronton Wingbox</option>
                                        <option value="Tronton Bak Terbuka">Tronton Bak Terbuka</option>
                                        <option value="Dump Truck">Dump Truck</option>
                                        <option value="Truk Trailer Gandeng 40ft">Truk Trailer Gandeng 40ft</option>
                                        <option value="Truk Tangki Semen Curah (Bulk)">Truk Tangki Semen Curah (Bulk)</option>
                                        <option value="Colt Diesel Double (CDD)">Colt Diesel Double (CDD)</option>
                                        <option value="Engkel Box (CDE)">Engkel Box (CDE)</option>
                                        <option value="Pick Up L300">Pick Up L300</option>
                                        <option value="Kendaraan Operasional Kantor">Kendaraan Operasional Kantor</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek Truk / Pabrikan</label>
                                    <input type="text" name="merek_aset" placeholder="Contoh: Hino / Mitsubishi Fuso / Isuzu"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan (Tonase / Sak)</label>
                                    <input type="text" name="muatan" placeholder="Contoh: 25 Ton / 500 Sak Semen"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pembuatan Kendaraan</label>
                                    <input type="number" name="tahun_pembuatan" placeholder="Contoh: 2023" min="1990" max="{{ date('Y') + 1 }}"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Masa Berlaku Uji KIR</label>
                                    <x-input-tanggal nama="tanggal_kir" placeholder="Pilih Tanggal KIR" :wajib="false" warnaFokus="blue" />
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jatuh Tempo Pajak STNK</label>
                                    <x-input-tanggal nama="tanggal_pajak" placeholder="Pilih Tanggal Pajak" :wajib="false" warnaFokus="blue" />
                                </div>
                            </div>

                            <!-- Input 1 Unit: Plat Nomor, Mesin, Rangka -->
                            <div x-show="jumlahUnit === 1" class="space-y-2.5 pt-1 border-t border-blue-200/60 dark:border-blue-900/40">
                                <x-input-plat-nomor nama="no_polisi" :wajib="false" label="Plat Nomor Polisi Kendaraan" />
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Mesin (Opsional)</label>
                                        <input type="text" name="no_mesin" placeholder="W04D-xxxxxx"
                                               class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono uppercase">
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Rangka (Opsional)</label>
                                        <input type="text" name="no_rangka" placeholder="MHK-xxxxxx"
                                               class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono uppercase">
                                    </div>
                                </div>
                            </div>

                            <!-- Input Multi-Unit: Rincian Plat & Identitas Per Unit -->
                            <div x-show="jumlahUnit > 1" class="space-y-2 max-h-56 overflow-y-auto pr-1 pt-1 border-t border-blue-200/60 dark:border-blue-900/40">
                                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Rincian Plat Nomor, Mesin, & Rangka per Unit Truk:</div>
                                <template x-for="(unit, index) in rincianUnit" :key="index">
                                    <div class="p-2.5 rounded-xl bg-white dark:bg-[#14161F] border border-blue-200 dark:border-blue-900/50 space-y-1.5 shadow-2xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-blue-700 dark:text-blue-400 font-mono text-[11px]" x-text="'Armada Unit #' + (index + 1)"></span>
                                            <span class="text-[10px] text-slate-400 font-mono" x-text="'Auto KND-' + (index + 1)"></span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Plat Nomor</label>
                                                <input type="text" :name="'rincian_unit[' + index + '][no_polisi]'" x-model="unit.no_polisi" placeholder="B 1234 PBJ"
                                                       class="w-full px-2.5 py-1.5 rounded-lg bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-slate-200 dark:border-slate-700 font-mono uppercase text-xs focus:ring-1 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">No. Mesin</label>
                                                <input type="text" :name="'rincian_unit[' + index + '][no_mesin]'" x-model="unit.no_mesin" placeholder="Opsional"
                                                       class="w-full px-2.5 py-1.5 rounded-lg bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-slate-200 dark:border-slate-700 font-mono uppercase text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 mb-0.5">No. Rangka</label>
                                                <input type="text" :name="'rincian_unit[' + index + '][no_rangka]'" x-model="unit.no_rangka" placeholder="Opsional"
                                                       class="w-full px-2.5 py-1.5 rounded-lg bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-slate-200 dark:border-slate-700 font-mono uppercase text-xs">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- B. KONDISIONAL TANAH & BANGUNAN PROPERTI -->
                        <div x-show="pilihanJenis === 'AST-TNH'" x-cloak class="p-4 bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/40 rounded-2xl space-y-3">
                            <div class="flex items-center gap-1.5 text-emerald-900 dark:text-emerald-300 font-bold text-xs">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Spesifikasi Aset Properti (Tanah & Bangunan Usaha)</span>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                    Keterangan Aset / Bangunan di Atas Tanah <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="keterangan" rows="3" placeholder="Jelaskan objek atau fasilitas yang berdiri di atas tanah ini, misal: Gedung Kantor Pusat 2 Lantai (Luas 450 m²), Bangunan Gudang Semen (Luas 1.200 m²), Pos Satpam & Lapangan Parkir Truk. Jika tanah kosong, tuliskan: 'Lahan Kosong Siap Bangun'."
                                          class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-emerald-500/30"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Luas Tanah / Bangunan</label>
                                    <input type="text" name="muatan" placeholder="Contoh: Tanah 2.500 m² / Gedung 1.200 m²"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Dokumen Legalitas & Sertifikat</label>
                                    <input type="text" name="no_mesin" placeholder="Contoh: SHM No. 1234 / HGB No. 5678"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                </div>
                            </div>
                        </div>

                        <!-- C. KONDISIONAL MESIN GUDANG ATAU ELEKTRONIK KANTOR -->
                        <div x-show="pilihanJenis === 'AST-GDG' || pilihanJenis === 'AST-OFC'" x-cloak class="p-4 bg-slate-50 dark:bg-[#1C1E2A] border border-slate-200 dark:border-[#252837] rounded-2xl space-y-3">
                            <div class="flex items-center gap-1.5 text-slate-800 dark:text-slate-200 font-bold text-xs">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Spesifikasi Teknis & Lokasi Penempatan</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek / Pabrikan</label>
                                    <input type="text" name="merek_aset" placeholder="Contoh: Toyota / Lenovo / Canon"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor Seri / Serial Number</label>
                                    <input type="text" name="no_mesin" placeholder="Contoh: SN-883921-X"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono uppercase">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Penempatan Gudang / Ruangan</label>
                                    <input type="text" name="muatan" placeholder="Contoh: Gudang Transit Semen / Kantor Lt. 2"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Pengadaan / Produksi</label>
                                    <input type="number" name="tahun_pembuatan" placeholder="2024" min="2000" max="{{ date('Y') }}"
                                           class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Kondisi / Garansi</label>
                                <textarea name="keterangan" rows="2" placeholder="Catatan spesifikasi teknis, garansi servis, atau kondisi aset."
                                          class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 text-xs"></textarea>
                            </div>
                        </div>

                        <!-- Tombol Submit & Batal di Sudut Kanan Bawah -->
                        <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#E2E8F0] dark:border-[#252837]">
                            <button @click="bukaModalTambah = false" type="button" class="px-5 py-2.5 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                            <button type="submit" class="px-6 py-2.5 font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-md shadow-indigo-600/20">Simpan & Daftarkan Aset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Konfirmasi Tutup Buku Penyusutan Bulanan -->
    <div x-show="bukaModalSusut" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="bukaModalSusut = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md shadow-xl p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Eksekusi Tutup Buku Penyusutan</h3>
                    <p class="text-[11px] text-slate-500">Posting ayat jurnal beban dan akumulasi depresiasi.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('keuangan.akuntansi.aset.penyusutan') }}" class="space-y-3.5 text-xs">
                @csrf
                <div class="p-3 bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] rounded-xl space-y-2">
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Total Aset Aktif Tersusut:</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">{{ $totalUnitAset ?? 0 }} Unit</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Estimasi Total Beban Bulan Ini:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">Rp {{ number_format($estimasiSusutBulanIni ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Bulan Periode</label>
                        <select name="periode_bulan" class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-medium">
                            @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
                                <option value="{{ $num }}" {{ (int)date('m') === $num ? 'selected' : '' }}>{{ $num }} - {{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Periode</label>
                        <input type="number" name="periode_tahun" value="{{ date('Y') }}" class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 font-mono">
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Sistem akan menghitung depresiasi masing-masing aset, memotong nilai buku, dan secara otomatis memposting jurnal berpasangan (Debit Beban Penyusutan, Kredit Akumulasi Penyusutan) ke Buku Besar.
                </p>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="bukaModalSusut = false" type="button" class="px-4 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">Jurnal Penyusutan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Aset (Canvas Landscape) -->
    <div x-show="modalDetailTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalDetailTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-3xl lg:max-w-4xl overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-xs">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100" x-text="detailAset.nama_aset"></h2>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase"
                                  :class="detailAset.status_aset === 'aktif' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 border border-amber-200 dark:border-amber-800'"
                                  x-text="detailAset.status_aset || 'aktif'"></span>
                        </div>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-mono font-semibold" x-text="detailAset.kode_aset"></p>
                    </div>
                </div>
                <button @click="modalDetailTerbuka = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 text-lg font-bold">
                    &times;
                </button>
            </div>
            <div class="p-6 space-y-4 text-xs">
                <!-- Grid Informasi Utama Finansial -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Golongan Aset</div>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.jenis_aset || detailAset.kode_jenis_aset"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Harga Perolehan</div>
                        <div class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 mt-0.5" x-text="'Rp ' + Number(detailAset.harga_aset || 0).toLocaleString('id-ID')"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Akumulasi Susut</div>
                        <div class="text-xs font-mono font-bold text-rose-600 dark:text-rose-400 mt-0.5" x-text="'Rp ' + Number(detailAset.akumulasi_penyusutan || 0).toLocaleString('id-ID')"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Nilai Buku Saat Ini</div>
                        <div class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mt-0.5" x-text="'Rp ' + Number(detailAset.nilai_buku || detailAset.harga_aset || 0).toLocaleString('id-ID')"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Tanggal Perolehan</div>
                        <div class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.tanggal_pembelian ? detailAset.tanggal_pembelian.substring(0, 10) : '-'"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Metode PSAK 16</div>
                        <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="(detailAset.metode_penyusutan || 'Garis Lurus') + ' (' + (detailAset.umur_manfaat || 0) + ' Th)'"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Tarif Depresiasi</div>
                        <div class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5" x-text="(detailAset.tarif_penyusutan || 0) + '% / Thn'"></div>
                    </div>
                    <div class="p-3 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]">
                        <div class="text-[10px] text-slate-400 font-semibold uppercase">Entitas Pemilik</div>
                        <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.nama_pemilik || 'PT Putra Balkom Jaya'"></div>
                    </div>
                </div>

                <!-- Spesifikasi Armada Truk (Jika Tersedia) -->
                <div class="p-4 bg-blue-50/60 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 rounded-2xl space-y-3"
                     x-show="detailAset.kode_jenis_aset === 'AST-TRK' || detailAset.no_polisi !== '-'">
                    <div class="flex items-center gap-1.5 text-blue-900 dark:text-blue-300 font-bold text-xs">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        <span>Data Spesifikasi Armada Fisik Kendaraan (ERD)</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Plat Nomor Polisi</div>
                            <div class="text-xs font-mono font-bold text-blue-700 dark:text-blue-400 mt-0.5" x-text="detailAset.no_polisi || '-'"></div>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Jenis / Tipe Armada</div>
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.jenis_kendaraan || '-'"></div>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Merek Truk</div>
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.merek_aset || '-'"></div>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Kapasitas Muatan</div>
                            <div class="text-xs font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.muatan || '-'"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Nomor Mesin</div>
                            <div class="text-xs font-mono font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.no_mesin || '-'"></div>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Nomor Rangka</div>
                            <div class="text-xs font-mono font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.no_rangka || '-'"></div>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Berlaku Uji KIR</div>
                            <div class="text-xs font-mono font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.tanggal_kir ? detailAset.tanggal_kir.substring(0, 10) : '-'"></div>
                        </div>
                        <div class="p-2.5 bg-white dark:bg-[#14161F] rounded-xl border border-blue-100 dark:border-blue-900/30">
                            <div class="text-[10px] text-slate-400 font-semibold uppercase">Jatuh Tempo Pajak</div>
                            <div class="text-xs font-mono font-semibold text-slate-800 dark:text-slate-200 mt-0.5" x-text="detailAset.tanggal_pajak ? detailAset.tanggal_pajak.substring(0, 10) : '-'"></div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan Tambahan / Fasilitas Bangunan di Atas Tanah -->
                <div class="p-3.5 bg-slate-50 dark:bg-[#1C1E2A] rounded-xl border border-[#E2E8F0] dark:border-[#252837]"
                     x-show="detailAset.keterangan">
                    <div class="text-[10px] text-slate-400 font-semibold uppercase mb-1">Keterangan Aset / Fasilitas Bangunan di Atas Tanah</div>
                    <div class="text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed" x-text="detailAset.keterangan"></div>
                </div>

                <div class="flex justify-end pt-2">
                    <button @click="modalDetailTerbuka = false" class="px-5 py-2 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Aset (Canvas Landscape) -->
    <div x-show="modalEditTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div @click.away="modalEditTerbuka = false" class="animasi-skala bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-3xl lg:max-w-4xl overflow-hidden shadow-2xl my-8">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] dark:border-[#252837] bg-slate-50 dark:bg-[#1C1E2A]">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Ubah Data Aset Perusahaan</h3>
                        <p class="text-[11px] text-slate-400">Pembaruan data inventaris aktiva tetap dan spesifikasi operasional.</p>
                    </div>
                </div>
                <button @click="modalEditTerbuka = false" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">&times;</button>
            </div>
            <form :action="'{{ url('keuangan/akuntansi/aset-perusahaan') }}/' + formEdit.kode_aset" method="POST" class="p-6 space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                    <!-- Sisi Kiri: Data Identitas & Finansial (6 Kolom) -->
                    <div class="lg:col-span-6 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Aset (Terkunci)</label>
                                <input type="text" :value="formEdit.kode_aset" disabled
                                       class="w-full px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 border border-[#E2E8F0] dark:border-[#252837] text-slate-500 font-mono font-semibold cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori Jenis <span class="text-rose-500">*</span></label>
                                <select name="kode_jenis_aset" x-model="formEdit.kode_jenis_aset" required
                                        class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                    <option value="AST-TRK">1. Kendaraan Armada Truk</option>
                                    <option value="AST-TNH">2. Tanah & Bangunan Properti</option>
                                    <option value="AST-GDG">3. Mesin & Alat Gudang</option>
                                    <option value="AST-OFC">4. Elektronik & Perabot Kantor</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Tipe / Model Aset <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_aset" x-model="formEdit.nama_aset" required
                                   class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Entitas Pemilik STNK</label>
                                <input type="text" name="nama_pemilik" x-model="formEdit.nama_pemilik" required
                                       class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Operasional</label>
                                <select name="status_aset" x-model="formEdit.status_aset"
                                        class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                    <option value="aktif">Aktif</option>
                                    <option value="dalam_perbaikan">Dalam Perbaikan</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="non-aktif">Non-Aktif</option>
                                    <option value="dijual">Dijual</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Pembelian <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal_pembelian" x-model="formEdit.tanggal_pembelian" required
                                       class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Harga Perolehan (Rp) <span class="text-rose-500">*</span></label>
                                <x-input-rupiah 
                                    nama="harga_aset"
                                    modelBind="formEdit.harga_aset"
                                    placeholder="350.000.000"
                                    :wajib="true"
                                    warnaFokus="indigo"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Spesifikasi Fisik & ERD (6 Kolom) -->
                    <div class="lg:col-span-6 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Plat Nomor Polisi</label>
                                <input type="text" name="no_polisi" x-model="formEdit.no_polisi" placeholder="B 1234 PBJ"
                                       class="w-full px-3 py-2 rounded-xl font-mono uppercase bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jenis / Tipe Kendaraan</label>
                                <select name="jenis_kendaraan" x-model="formEdit.jenis_kendaraan"
                                        class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Tronton Wingbox">Tronton Wingbox</option>
                                    <option value="Tronton Bak Terbuka">Tronton Bak Terbuka</option>
                                    <option value="Dump Truck">Dump Truck</option>
                                    <option value="Truk Trailer Gandeng 40ft">Truk Trailer Gandeng 40ft</option>
                                    <option value="Truk Tangki Semen Curah (Bulk)">Truk Tangki Semen Curah (Bulk)</option>
                                    <option value="Colt Diesel Double (CDD)">Colt Diesel Double (CDD)</option>
                                    <option value="Engkel Box (CDE)">Engkel Box (CDE)</option>
                                    <option value="Pick Up L300">Pick Up L300</option>
                                    <option value="Kendaraan Operasional Kantor">Kendaraan Operasional Kantor</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Merek Pabrikan</label>
                                <input type="text" name="merek_aset" x-model="formEdit.merek_aset" placeholder="Hino / Isuzu / Fuso"
                                       class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Kapasitas Muatan</label>
                                <input type="text" name="muatan" x-model="formEdit.muatan" placeholder="25 Ton / 500 Sak"
                                       class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Mesin</label>
                                <input type="text" name="no_mesin" x-model="formEdit.no_mesin" placeholder="W04D-xxx"
                                       class="w-full px-2.5 py-1.5 rounded-xl font-mono uppercase bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-700 dark:text-slate-300 mb-1">No. Rangka</label>
                                <input type="text" name="no_rangka" x-model="formEdit.no_rangka" placeholder="MHK-xxx"
                                       class="w-full px-2.5 py-1.5 rounded-xl font-mono uppercase bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Tahun Buat</label>
                                <input type="number" name="tahun_pembuatan" x-model="formEdit.tahun_pembuatan" placeholder="2023"
                                       class="w-full px-2.5 py-1.5 rounded-xl font-mono bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tanggal Uji KIR</label>
                                <input type="date" name="tanggal_kir" x-model="formEdit.tanggal_kir"
                                       class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Jatuh Tempo Pajak</label>
                                <input type="date" name="tanggal_pajak" x-model="formEdit.tanggal_pajak"
                                       class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Aset / Bangunan di Atas Tanah</label>
                            <textarea name="keterangan" x-model="formEdit.keterangan" rows="2" placeholder="Catatan fasilitas bangunan atau keterangan aset..."
                                      class="w-full px-3 py-2 rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0] dark:border-[#252837]">
                    <button @click="modalEditTerbuka = false" type="button" class="px-5 py-2.5 font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" class="px-6 py-2.5 font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded-xl transition-all shadow-md shadow-amber-600/20">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Aset -->
    <div x-show="modalHapusTerbuka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="modalHapusTerbuka = false" class="bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 text-center mb-1">Hapus Aset Perusahaan?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 text-center mb-4 leading-relaxed">
                Anda akan menghapus data aset <strong class="text-slate-800 dark:text-slate-200" x-text="hapusData.nama"></strong> (<span class="font-mono text-indigo-600" x-text="hapusData.kode"></span>). Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="'{{ url('keuangan/akuntansi/aset-perusahaan') }}/' + hapusData.kode" method="POST" class="flex items-center justify-center gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="modalHapusTerbuka = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-md shadow-rose-600/20">Ya, Hapus Aset</button>
            </form>
        </div>
    </div>
</div>

<script>
    function kelolaAsetPerusahaan() {
        return {
            bukaModalTambah: false,
            bukaModalSusut: false,
            bukaTabRiwayat: false,
            modalDetailTerbuka: false,
            modalEditTerbuka: false,
            modalHapusTerbuka: false,
            pilihanJenis: 'AST-TRK',
            umurManfaat: 8,
            tarifSusut: 12.5,
            metodeSusut: 'Garis Lurus',
            jumlahUnit: 1,
            rincianUnit: [{ no_polisi: '', no_mesin: '', no_rangka: '' }],
            detailAset: {},
            formEdit: {
                kode_aset: '',
                kode_jenis_aset: '',
                nama_aset: '',
                no_polisi: '',
                merek_aset: '',
                jenis_kendaraan: '',
                muatan: '',
                no_mesin: '',
                no_rangka: '',
                tahun_pembuatan: '',
                tanggal_kir: '',
                tanggal_pajak: '',
                nama_pemilik: '',
                tanggal_pembelian: '',
                harga_aset: 0,
                status_aset: 'aktif',
                keterangan: ''
            },
            hapusData: { kode: '', nama: '' },

            sinkronkanRincian() {
                if (!this.jumlahUnit || this.jumlahUnit < 1) this.jumlahUnit = 1;
                if (this.jumlahUnit > 50) this.jumlahUnit = 50;
                while (this.rincianUnit.length < this.jumlahUnit) {
                    this.rincianUnit.push({ no_polisi: '', no_mesin: '', no_rangka: '' });
                }
                if (this.rincianUnit.length > this.jumlahUnit) {
                    this.rincianUnit = this.rincianUnit.slice(0, this.jumlahUnit);
                }
            },

            aturKategori(kode) {
                this.pilihanJenis = kode;
                if (kode === 'AST-TNH') {
                    // Tanah & Bangunan: Default 20 tahun (5%) jika ada bangunan
                    this.umurManfaat = 20;
                    this.tarifSusut = 5.0;
                    this.metodeSusut = 'Garis Lurus';
                } else if (kode === 'AST-TRK' || kode === 'AST-GDG') {
                    this.umurManfaat = 8;
                    this.tarifSusut = 12.5;
                    this.metodeSusut = 'Garis Lurus';
                } else if (kode === 'AST-OFC') {
                    this.umurManfaat = 4;
                    this.tarifSusut = 25.0;
                    this.metodeSusut = 'Garis Lurus';
                }
            },

            async bukaDetail(kode) {
                try {
                    const res = await fetch(`{{ url('keuangan/akuntansi/aset-perusahaan') }}/${kode}`);
                    const json = await res.json();
                    if (json.status === 'sukses') {
                        this.detailAset = json.data;
                        this.modalDetailTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal memuat detail data aset.');
                }
            },

            async bukaEdit(kode) {
                try {
                    const res = await fetch(`{{ url('keuangan/akuntansi/aset-perusahaan') }}/${kode}`);
                    const json = await res.json();
                    if (json.status === 'sukses') {
                        const d = json.data;
                        this.formEdit = {
                            kode_aset: d.kode_aset,
                            kode_jenis_aset: d.kode_jenis_aset,
                            nama_aset: d.nama_aset,
                            no_polisi: d.no_polisi === '-' ? '' : (d.no_polisi || ''),
                            merek_aset: d.merek_aset === '-' ? '' : (d.merek_aset || ''),
                            jenis_kendaraan: d.jenis_kendaraan === '-' ? '' : (d.jenis_kendaraan || ''),
                            muatan: d.muatan === '-' ? '' : (d.muatan || ''),
                            no_mesin: d.no_mesin === '-' ? '' : (d.no_mesin || ''),
                            no_rangka: d.no_rangka === '-' ? '' : (d.no_rangka || ''),
                            tahun_pembuatan: d.tahun_pembuatan || '',
                            tanggal_kir: d.tanggal_kir ? d.tanggal_kir.substring(0, 10) : '',
                            tanggal_pajak: d.tanggal_pajak ? d.tanggal_pajak.substring(0, 10) : '',
                            nama_pemilik: d.nama_pemilik || 'PT Putra Balkom Jaya',
                            tanggal_pembelian: d.tanggal_pembelian ? d.tanggal_pembelian.substring(0, 10) : '',
                            harga_aset: d.harga_aset,
                            status_aset: d.status_aset || 'aktif',
                            keterangan: d.keterangan || ''
                        };
                        this.modalEditTerbuka = true;
                    }
                } catch (e) {
                    alert('Gagal memuat form edit data aset.');
                }
            },

            bukaHapus(kode, nama) {
                this.hapusData = { kode: kode, nama: nama };
                this.modalHapusTerbuka = true;
            }
        };
    }
</script>
@endsection
