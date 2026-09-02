@props([
    'nama',
    'nilaiAwal' => '',
    'placeholder' => '0',
    'wajib' => false,
    'modelBind' => null,
    'min' => 0,
    'label' => null,
    'prefix' => 'Rp',
    'classTambahan' => '',
    'disabled' => false,
])

<div class="space-y-1 w-full" x-data="{
    nilaiMurni: '{{ old($nama, $nilaiAwal) }}',
    nilaiTampil: '',
    init() {
        this.formatKeTampilan(this.nilaiMurni);
        
        @if($modelBind)
            this.$watch('{{ $modelBind }}', (val) => {
                if (String(val) !== String(this.nilaiMurni)) {
                    this.nilaiMurni = val || '0';
                    this.formatKeTampilan(this.nilaiMurni);
                }
            });
            if (typeof {{ $modelBind }} !== 'undefined' && {{ $modelBind }} !== null && {{ $modelBind }} !== '') {
                this.nilaiMurni = {{ $modelBind }};
                this.formatKeTampilan(this.nilaiMurni);
            }
        @endif
    },
    formatKeTampilan(angka) {
        if (angka === '' || angka === null || angka === undefined) {
            this.nilaiTampil = '';
            this.nilaiMurni = '';
            return;
        }
        // Bersihkan dari selain angka
        let bersih = String(angka).replace(/[^0-9]/g, '');
        if (!bersih) {
            this.nilaiTampil = '';
            this.nilaiMurni = '';
            return;
        }
        let num = parseInt(bersih, 10);
        this.nilaiMurni = num;
        this.nilaiTampil = num.toLocaleString('id-ID');
    },
    ketikInput(e) {
        let inputVal = e.target.value;
        let bersih = inputVal.replace(/[^0-9]/g, '');
        if (!bersih) {
            this.nilaiMurni = '';
            this.nilaiTampil = '';
            @if($modelBind)
                {{ $modelBind }} = 0;
            @endif
            $dispatch('input', 0);
            $dispatch('change', 0);
            return;
        }
        let num = parseInt(bersih, 10);
        this.nilaiMurni = num;
        this.nilaiTampil = num.toLocaleString('id-ID');
        
        @if($modelBind)
            {{ $modelBind }} = num;
        @endif
        
        $dispatch('input', num);
        $dispatch('change', num);
    }
}">
    @if($label)
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($wajib)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <!-- Hidden Raw Value untuk Submit Form Backend -->
    <input type="hidden" name="{{ $nama }}" :value="nilaiMurni" {{ $wajib ? 'required' : '' }}>

    <!-- Input Display Teks Terformat Titik Otomatis -->
    <div class="relative flex items-center rounded-xl bg-[#F8FAFC] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/30 transition-all overflow-hidden {{ $classTambahan }}">
        @if($prefix)
            <span class="px-3 py-2 text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-[#252837] border-r border-[#E2E8F0] dark:border-[#252837] select-none font-mono shrink-0">
                {{ $prefix }}
            </span>
        @endif
        <input type="text" 
               inputmode="numeric"
               :value="nilaiTampil"
               @input="ketikInput($event)"
               placeholder="{{ $placeholder }}"
               {{ $disabled ? 'disabled' : '' }}
               class="w-full px-3 py-2 text-xs font-mono font-bold text-slate-900 dark:text-slate-100 placeholder-slate-400 bg-transparent border-none focus:outline-none focus:ring-0 text-left">
    </div>
</div>
