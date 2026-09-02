@props([
    'nama' => 'no_polisi',
    'nilaiAwal' => '',
    'wajib' => true,
    'modelBind' => null,
    'label' => 'Nomor Polisi (Plat Kendaraan)',
    'keterangan' => 'Format 3 kolom: [Wilayah] [Nomor] [Seri Belakang]'
])

<div class="space-y-1.5" x-data="{
    wilayah: '',
    nomor: '',
    seri: '',
    platLengkap: '',
    init() {
        this.uraiPlatNomor('{{ old($nama, $nilaiAwal) }}');
        
        @if($modelBind)
            this.$watch('{{ $modelBind }}', (val) => {
                if (val !== this.platLengkap) {
                    this.uraiPlatNomor(val || '');
                }
            });
        @endif
        
        this.$watch('wilayah', () => this.gabungPlat());
        this.$watch('nomor', () => this.gabungPlat());
        this.$watch('seri', () => this.gabungPlat());
    },
    uraiPlatNomor(str) {
        if (!str) {
            this.wilayah = '';
            this.nomor = '';
            this.seri = '';
            this.platLengkap = '';
            return;
        }
        
        // Pembersihan & Regex pemisah plat nomor Indonesia (contoh: 'B 9283 TDF' atau 'L1996YZ')
        let bersihkan = str.trim().toUpperCase();
        let pola = /^([A-Z]{1,2})\s*(\d{1,4})\s*([A-Z]{0,3})$/;
        let cocok = bersihkan.match(pola);
        
        if (cocok) {
            this.wilayah = cocok[1] || '';
            this.nomor = cocok[2] || '';
            this.seri = cocok[3] || '';
        } else {
            // Fallback split spasi sederhana
            let bagian = bersihkan.split(/\s+/);
            this.wilayah = bagian[0] ? bagian[0].replace(/[^A-Z]/g, '').substring(0, 2) : '';
            this.nomor = bagian[1] ? bagian[1].replace(/[^0-9]/g, '').substring(0, 4) : '';
            this.seri = bagian[2] ? bagian[2].replace(/[^A-Z]/g, '').substring(0, 3) : '';
        }
        this.gabungPlat();
    },
    gabungPlat() {
        // Format otomatis huruf kapital & digit
        this.wilayah = this.wilayah.replace(/[^A-Za-z]/g, '').toUpperCase().substring(0, 2);
        this.nomor = this.nomor.replace(/[^0-9]/g, '').substring(0, 4);
        this.seri = this.seri.replace(/[^A-Za-z]/g, '').toUpperCase().substring(0, 3);
        
        if (this.wilayah || this.nomor || this.seri) {
            let hasil = `${this.wilayah} ${this.nomor} ${this.seri}`.replace(/\s+/g, ' ').trim();
            this.platLengkap = hasil;
        } else {
            this.platLengkap = '';
        }
        
        @if($modelBind)
            {{ $modelBind }} = this.platLengkap;
        @endif
        
        $dispatch('input', this.platLengkap);
        $dispatch('change', this.platLengkap);
    },
    pindahKeNomor(e) {
        if (this.wilayah.length >= 2 || (e.key === ' ' || e.key === 'Enter')) {
            if (e.key === ' ') e.preventDefault();
            this.$refs.inputNomor.focus();
        }
    },
    pindahKeSeri(e) {
        if (this.nomor.length >= 4 || (e.key === ' ' || e.key === 'Enter')) {
            if (e.key === ' ') e.preventDefault();
            this.$refs.inputSeri.focus();
        }
    },
    mundurKeWilayah(e) {
        if (e.key === 'Backspace' && this.nomor.length === 0) {
            this.$refs.inputWilayah.focus();
        }
    },
    mundurKeNomor(e) {
        if (e.key === 'Backspace' && this.seri.length === 0) {
            this.$refs.inputNomor.focus();
        }
    }
}">
    <!-- Label Header -->
    <div class="flex items-center justify-between">
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($wajib)
                <span class="text-rose-500">*</span>
            @endif
        </label>
        
        <!-- Live Visual Plate Badge (Ala Plat Nomor Asli) -->
        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#18181B] border border-slate-700 text-white font-mono font-bold text-[10px] tracking-widest shadow-xs">
            <span class="text-slate-400 text-[8px]">PLAT</span>
            <span x-text="platLengkap || 'B ____ ___'" class="text-emerald-400"></span>
        </div>
    </div>

    <!-- Input Tersembunyi (Dikirim ke Controller Backend) -->
    <input type="hidden" name="{{ $nama }}" :value="platLengkap" {{ $wajib ? 'required' : '' }}>

    <!-- Wadah 3 Kolom Input Plat Nomor -->
    <div class="grid grid-cols-12 gap-2">
        <!-- 1. Kolom Kode Wilayah Depan (1-2 Huruf, misal: B, L, AB) -->
        <div class="col-span-3">
            <div class="relative">
                <input type="text" 
                       x-ref="inputWilayah"
                       x-model="wilayah"
                       @input="pindahKeNomor($event)"
                       @keydown.space.prevent="$refs.inputNomor.focus()"
                       maxlength="2" 
                       placeholder="B"
                       class="w-full text-center px-2 py-2 text-xs font-mono font-bold uppercase rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                <span class="absolute -bottom-3.5 left-0 right-0 text-center text-[9px] text-slate-400 font-medium">Wilayah</span>
            </div>
        </div>

        <!-- 2. Kolom Nomor Polisi Tengah (1-4 Angka, misal: 1996, 9283) -->
        <div class="col-span-5">
            <div class="relative">
                <input type="text" 
                       x-ref="inputNomor"
                       x-model="nomor"
                       @input="pindahKeSeri($event)"
                       @keydown="mundurKeWilayah($event)"
                       @keydown.space.prevent="$refs.inputSeri.focus()"
                       maxlength="4" 
                       inputmode="numeric"
                       placeholder="1996"
                       class="w-full text-center px-2 py-2 text-xs font-mono font-bold tracking-wider rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                <span class="absolute -bottom-3.5 left-0 right-0 text-center text-[9px] text-slate-400 font-medium">Nomor Seri</span>
            </div>
        </div>

        <!-- 3. Kolom Seri Huruf Belakang (1-3 Huruf, misal: YZ, PBJ) -->
        <div class="col-span-4">
            <div class="relative">
                <input type="text" 
                       x-ref="inputSeri"
                       x-model="seri"
                       @keydown="mundurKeNomor($event)"
                       maxlength="3" 
                       placeholder="YZ"
                       class="w-full text-center px-2 py-2 text-xs font-mono font-bold uppercase rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                <span class="absolute -bottom-3.5 left-0 right-0 text-center text-[9px] text-slate-400 font-medium">Seri Huruf</span>
            </div>
        </div>
    </div>

    <!-- Spacer untuk keterangan sublabel -->
    <div class="pt-2"></div>
</div>
