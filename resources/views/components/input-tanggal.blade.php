@props([
    'nama',
    'nilaiAwal' => '',
    'placeholder' => 'Pilih Tanggal...',
    'wajib' => false,
    'warnaFokus' => 'blue',
    'classInput' => '',
    'modelBind' => null,
    'min' => null,
    'max' => null,
])

<div class="relative w-full" x-data="{
    buka: false,
    nilai: '{{ old($nama, $nilaiAwal) }}',
    tahunTampil: new Date().getFullYear(),
    bulanTampil: new Date().getMonth(),
    modePilih: 'hari', // 'hari', 'bulan', 'tahun'
    namaBulan: [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ],
    namaBulanSingkat: [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ],
    namaHari: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
    daftarHari: [],

    init() {
        if (this.nilai) {
            this.nilai = String(this.nilai).split('T')[0];
            this.sinkronkanTanggalDariNilai(this.nilai);
        } else {
            let sekarang = new Date();
            this.tahunTampil = sekarang.getFullYear();
            this.bulanTampil = sekarang.getMonth();
        }
        this.buatKalender();

        @if($modelBind)
            this.$watch('{{ $modelBind }}', (val) => {
                let bersih = val ? String(val).split('T')[0] : '';
                if (bersih !== this.nilai) {
                    this.nilai = bersih;
                    if (this.nilai) {
                        this.sinkronkanTanggalDariNilai(this.nilai);
                    }
                    this.buatKalender();
                }
            });
            if (typeof {{ $modelBind }} !== 'undefined' && {{ $modelBind }} !== null && {{ $modelBind }} !== '') {
                this.nilai = String({{ $modelBind }}).split('T')[0];
                this.sinkronkanTanggalDariNilai(this.nilai);
                this.buatKalender();
            }
        @endif
    },

    sinkronkanTanggalDariNilai(tglStr) {
        if (!tglStr) return;
        let bersih = String(tglStr).split('T')[0];
        this.nilai = bersih;
        let bagian = bersih.split('-');
        if (bagian.length === 3) {
            let y = parseInt(bagian[0], 10);
            let m = parseInt(bagian[1], 10) - 1;
            if (!isNaN(y) && !isNaN(m) && m >= 0 && m <= 11) {
                this.tahunTampil = y;
                this.bulanTampil = m;
            }
        }
    },

    formatTampilan() {
        if (!this.nilai) return '';
        let bersih = String(this.nilai).split('T')[0];
        let bagian = bersih.split('-');
        if (bagian.length === 3) {
            let y = bagian[0];
            let m = parseInt(bagian[1], 10) - 1;
            let d = bagian[2];
            if (this.namaBulanSingkat[m]) {
                return `${d} ${this.namaBulanSingkat[m]} ${y}`;
            }
        }
        return bersih;
    },

    buatKalender() {
        this.daftarHari = [];
        let tahun = this.tahunTampil;
        let bulan = this.bulanTampil;

        let hariPertama = new Date(tahun, bulan, 1).getDay();
        let totalHariBulanIni = new Date(tahun, bulan + 1, 0).getDate();
        let totalHariBulanLalu = new Date(tahun, bulan, 0).getDate();

        let sekarang = new Date();
        let tglSekarang = sekarang.getFullYear() + '-' + 
            String(sekarang.getMonth() + 1).padStart(2, '0') + '-' + 
            String(sekarang.getDate()).padStart(2, '0');

        // Hari dari bulan sebelumnya untuk mengisi slot awal minggu
        for (let i = hariPertama - 1; i >= 0; i--) {
            let tgl = totalHariBulanLalu - i;
            let bln = bulan === 0 ? 11 : bulan - 1;
            let thn = bulan === 0 ? tahun - 1 : tahun;
            let tglLengkap = `${thn}-${String(bln + 1).padStart(2, '0')}-${String(tgl).padStart(2, '0')}`;
            this.daftarHari.push({
                tanggal: tgl,
                bulanTipe: 'lalu',
                tglLengkap: tglLengkap,
                isHariIni: tglLengkap === tglSekarang,
                isTerpilih: tglLengkap === this.nilai
            });
        }

        // Hari di bulan aktif
        for (let i = 1; i <= totalHariBulanIni; i++) {
            let tglLengkap = `${tahun}-${String(bulan + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            this.daftarHari.push({
                tanggal: i,
                bulanTipe: 'ini',
                tglLengkap: tglLengkap,
                isHariIni: tglLengkap === tglSekarang,
                isTerpilih: tglLengkap === this.nilai
            });
        }

        // Hari di bulan berikutnya untuk melengkapi baris grid
        let sisaSlot = 35 - this.daftarHari.length;
        if (sisaSlot < 0) {
            sisaSlot = 42 - this.daftarHari.length;
        }
        for (let i = 1; i <= sisaSlot; i++) {
            let bln = bulan === 11 ? 0 : bulan + 1;
            let thn = bulan === 11 ? tahun + 1 : tahun;
            let tglLengkap = `${thn}-${String(bln + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            this.daftarHari.push({
                tanggal: i,
                bulanTipe: 'depan',
                tglLengkap: tglLengkap,
                isHariIni: tglLengkap === tglSekarang,
                isTerpilih: tglLengkap === this.nilai
            });
        }
    },

    bulanSebelumnya() {
        if (this.bulanTampil === 0) {
            this.bulanTampil = 11;
            this.tahunTampil--;
        } else {
            this.bulanTampil--;
        }
        this.buatKalender();
    },

    bulanBerikutnya() {
        if (this.bulanTampil === 11) {
            this.bulanTampil = 0;
            this.tahunTampil++;
        } else {
            this.bulanTampil++;
        }
        this.buatKalender();
    },

    pilihTanggal(item) {
        this.nilai = item.tglLengkap;
        @if($modelBind)
            {{ $modelBind }} = this.nilai;
        @endif
        $dispatch('input', this.nilai);
        $dispatch('change', this.nilai);
        this.buatKalender();
        this.buka = false;
    },

    pilihHariIni() {
        let sekarang = new Date();
        let tglSekarang = sekarang.getFullYear() + '-' + 
            String(sekarang.getMonth() + 1).padStart(2, '0') + '-' + 
            String(sekarang.getDate()).padStart(2, '0');
        this.tahunTampil = sekarang.getFullYear();
        this.bulanTampil = sekarang.getMonth();
        this.nilai = tglSekarang;
        @if($modelBind)
            {{ $modelBind }} = this.nilai;
        @endif
        $dispatch('input', this.nilai);
        $dispatch('change', this.nilai);
        this.buatKalender();
        this.buka = false;
    },

    kosongkanTanggal() {
        this.nilai = '';
        @if($modelBind)
            {{ $modelBind }} = '';
        @endif
        $dispatch('input', '');
        $dispatch('change', '');
        this.buatKalender();
        this.buka = false;
    },

    bukaPopover() {
        if (this.nilai) {
            this.sinkronkanTanggalDariNilai(this.nilai);
        }
        this.modePilih = 'hari';
        this.buatKalender();
        this.buka = !this.buka;
    },

    ambilWarnaAktif() {
        const peta = {
            'blue': 'bg-blue-600 text-white shadow-sm shadow-blue-600/30 font-bold',
            'red': 'bg-red-600 text-white shadow-sm shadow-red-600/30 font-bold',
            'amber': 'bg-amber-600 text-white shadow-sm shadow-amber-600/30 font-bold',
            'emerald': 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30 font-bold',
            'purple': 'bg-purple-600 text-white shadow-sm shadow-purple-600/30 font-bold',
            'rose': 'bg-rose-600 text-white shadow-sm shadow-rose-600/30 font-bold',
            'indigo': 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30 font-bold',
            'sky': 'bg-sky-600 text-white shadow-sm shadow-sky-600/30 font-bold',
            'teal': 'bg-teal-600 text-white shadow-sm shadow-teal-600/30 font-bold'
        };
        return peta['{{ $warnaFokus }}'] || peta['blue'];
    },

    ambilWarnaHariIni() {
        const peta = {
            'blue': 'text-blue-600 dark:text-blue-400 border border-blue-500/50 bg-blue-50/50 dark:bg-blue-950/30 font-bold',
            'red': 'text-red-600 dark:text-red-400 border border-red-500/50 bg-red-50/50 dark:bg-red-950/30 font-bold',
            'amber': 'text-amber-600 dark:text-amber-400 border border-amber-500/50 bg-amber-50/50 dark:bg-amber-950/30 font-bold',
            'emerald': 'text-emerald-600 dark:text-emerald-400 border border-emerald-500/50 bg-emerald-50/50 dark:bg-emerald-950/30 font-bold',
            'purple': 'text-purple-600 dark:text-purple-400 border border-purple-500/50 bg-purple-50/50 dark:bg-purple-950/30 font-bold',
            'rose': 'text-rose-600 dark:text-rose-400 border border-rose-500/50 bg-rose-50/50 dark:bg-rose-950/30 font-bold',
            'indigo': 'text-indigo-600 dark:text-indigo-400 border border-indigo-500/50 bg-indigo-50/50 dark:bg-indigo-950/30 font-bold',
            'sky': 'text-sky-600 dark:text-sky-400 border border-sky-500/50 bg-sky-50/50 dark:bg-sky-950/30 font-bold',
            'teal': 'text-teal-600 dark:text-teal-400 border border-teal-500/50 bg-teal-50/50 dark:bg-teal-950/30 font-bold'
        };
        return peta['{{ $warnaFokus }}'] || peta['blue'];
    },

    ambilTeksAksen() {
        const peta = {
            'blue': 'text-blue-600 dark:text-blue-400 hover:text-blue-700',
            'red': 'text-red-600 dark:text-red-400 hover:text-red-700',
            'amber': 'text-amber-600 dark:text-amber-400 hover:text-amber-700',
            'emerald': 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-700',
            'purple': 'text-purple-600 dark:text-purple-400 hover:text-purple-700',
            'rose': 'text-rose-600 dark:text-rose-400 hover:text-rose-700',
            'indigo': 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-700',
            'sky': 'text-sky-600 dark:text-sky-400 hover:text-sky-700',
            'teal': 'text-teal-600 dark:text-teal-400 hover:text-teal-700'
        };
        return peta['{{ $warnaFokus }}'] || peta['blue'];
    }
}" @click.away="buka = false">

    <!-- Hidden Input untuk dikirim ke Backend Form -->
    <input type="hidden" name="{{ $nama }}" :value="nilai" {{ $wajib ? 'required' : '' }}>

    <!-- Trigger Button (Identik 100% dengan Dropdown Kustom) -->
    <button type="button" 
            @click="bukaPopover()"
            class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-{{ $warnaFokus }}-500/30 transition-all text-left cursor-pointer {{ $classInput }}">
        
        <!-- Teks Tampilan Tanggal -->
        <span class="truncate font-medium font-mono" :class="{ 'text-slate-400 dark:text-slate-500 font-normal font-sans': !nilai }" x-text="formatTampilan() || '{{ $placeholder }}'"></span>

        <!-- Ikon Kalender SVG Vektor Kustom -->
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-1.5 transition-transform duration-200" :class="{ 'text-{{ $warnaFokus }}-500': buka }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </button>

    <!-- Floating Compact Calendar Popover -->
    <div x-show="buka" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         x-cloak
         class="absolute z-50 left-0 mt-1 w-56 sm:w-60 rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] shadow-xl shadow-slate-900/15 dark:shadow-black/60 p-2 text-xs select-none">
        
        <!-- Header Bulan & Tahun Navigator -->
        <div class="flex items-center justify-between mb-1.5 pb-1.5 border-b border-[#E2E8F0] dark:border-[#252837]">
            <button type="button" 
                    @click="bulanSebelumnya()" 
                    class="p-1 rounded-lg text-slate-500 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-[#1C1E2A] transition-colors"
                    title="Bulan Sebelumnya">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Judul Bulan & Tahun (Klik untuk Ganti Mode) -->
            <div class="flex items-center gap-1">
                <button type="button" 
                        @click="modePilih = modePilih === 'bulan' ? 'hari' : 'bulan'"
                        class="px-1.5 py-0.5 text-[11px] font-bold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-[#1C1E2A] rounded-md transition-colors flex items-center gap-0.5">
                    <span x-text="namaBulanSingkat[bulanTampil]"></span>
                    <svg class="w-2.5 h-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <button type="button" 
                        @click="modePilih = modePilih === 'tahun' ? 'hari' : 'tahun'"
                        class="px-1.5 py-0.5 text-[11px] font-mono font-bold text-slate-800 dark:text-slate-100 hover:bg-slate-100 dark:hover:bg-[#1C1E2A] rounded-md transition-colors flex items-center gap-0.5">
                    <span x-text="tahunTampil"></span>
                    <svg class="w-2.5 h-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <button type="button" 
                    @click="bulanBerikutnya()" 
                    class="p-1 rounded-lg text-slate-500 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-[#1C1E2A] transition-colors"
                    title="Bulan Berikutnya">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Mode 1: Tampilan Grid Hari (Default) -->
        <template x-if="modePilih === 'hari'">
            <div>
                <!-- Baris Nama Hari -->
                <div class="grid grid-cols-7 gap-0.5 mb-1">
                    <template x-for="hari in namaHari" :key="hari">
                        <div class="text-center font-semibold text-[9px] text-slate-400 dark:text-slate-500 py-0.5" x-text="hari"></div>
                    </template>
                </div>

                <!-- Grid Tanggal -->
                <div class="grid grid-cols-7 gap-0.5">
                    <template x-for="(item, idx) in daftarHari" :key="idx">
                        <button type="button"
                                @click="pilihTanggal(item)"
                                class="h-6.5 w-6.5 mx-auto flex items-center justify-center rounded-lg text-[11px] font-medium transition-all"
                                :class="[
                                    item.isTerpilih ? ambilWarnaAktif() : (
                                        item.isHariIni ? ambilWarnaHariIni() : (
                                            item.bulanTipe === 'ini' 
                                                ? 'text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#1C1E2A]' 
                                                : 'text-slate-300 dark:text-slate-600 hover:bg-slate-50 dark:hover:bg-[#1C1E2A]/50'
                                        )
                                    )
                                ]"
                                :title="item.tglLengkap">
                            <span x-text="item.tanggal"></span>
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Mode 2: Pemilih Bulan Cepat -->
        <template x-if="modePilih === 'bulan'">
            <div class="grid grid-cols-3 gap-1 py-1">
                <template x-for="(bln, idx) in namaBulanSingkat" :key="idx">
                    <button type="button"
                            @click="bulanTampil = idx; modePilih = 'hari'; buatKalender()"
                            class="py-1.5 px-1 rounded-lg text-[11px] font-semibold text-center transition-all"
                            :class="bulanTampil === idx ? ambilWarnaAktif() : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#1C1E2A]'">
                        <span x-text="bln"></span>
                    </button>
                </template>
            </div>
        </template>

        <!-- Mode 3: Pemilih Tahun Cepat -->
        <template x-if="modePilih === 'tahun'">
            <div class="grid grid-cols-3 gap-1 py-1 max-h-40 overflow-y-auto custom-scrollbar">
                <template x-for="thn in Array.from({length: 25}, (_, i) => new Date().getFullYear() - 10 + i)" :key="thn">
                    <button type="button"
                            @click="tahunTampil = thn; modePilih = 'hari'; buatKalender()"
                            class="py-1.5 px-1 rounded-lg text-[11px] font-mono font-semibold text-center transition-all"
                            :class="tahunTampil === thn ? ambilWarnaAktif() : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#1C1E2A]'">
                        <span x-text="thn"></span>
                    </button>
                </template>
            </div>
        </template>

        <!-- Footer Tombol Cepat (Hari Ini & Kosongkan) -->
        <div class="flex items-center justify-between pt-1.5 mt-1.5 border-t border-[#E2E8F0] dark:border-[#252837] text-[10px]">
            <button type="button" 
                    @click="kosongkanTanggal()" 
                    class="font-semibold text-slate-400 hover:text-rose-500 transition-colors">
                Hapus
            </button>
            <button type="button" 
                    @click="pilihHariIni()" 
                    class="font-semibold"
                    :class="ambilTeksAksen()">
                Hari Ini
            </button>
        </div>
    </div>
</div>
