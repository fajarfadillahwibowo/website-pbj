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

<div class="space-y-1 w-full" 
     @set-nilai-{{ $nama }}.window="nilaiMurni = $event.detail; formatKeTampilan(nilaiMurni);"
     x-data="{
    nilaiMurni: '{{ old($nama, $nilaiAwal) }}',
    nilaiTampil: '',
    init() {
        this.formatKeTampilan(this.nilaiMurni);
        
        @if($modelBind)
            try {
                let getter = new Function('try { return (typeof this.{{ $modelBind }} !== "undefined" ? this.{{ $modelBind }} : (typeof {{ $modelBind }} !== "undefined" ? {{ $modelBind }} : null)); } catch(e){ return null; }');
                let valAwal = getter.call(this);
                if (valAwal !== null && valAwal !== undefined && valAwal !== '') {
                    this.nilaiMurni = valAwal;
                    this.formatKeTampilan(this.nilaiMurni);
                }
            } catch(e) {}

            try {
                this.$watch('{{ $modelBind }}', (val) => {
                    if (String(val) !== String(this.nilaiMurni)) {
                        this.nilaiMurni = val || '0';
                        this.formatKeTampilan(this.nilaiMurni);
                    }
                });
            } catch(e) {}
        @endif
    },
    formatKeTampilan(angka) {
        if (angka === '' || angka === null || angka === undefined) {
            this.nilaiTampil = '';
            this.nilaiMurni = '';
            return;
        }

        let strAngka = String(angka).trim();

        // Tangani angka desimal dari MySQL/API (misal: "1200000.00" atau 1200000.00)
        // agar bagian desimal (".00") tidak terkonversi menjadi tambahan digit 00 (x100)
        if (typeof angka === 'number') {
            strAngka = Math.round(angka).toString();
        } else if (strAngka.includes('.')) {
            let bagianTitik = strAngka.split('.');
            // Format desimal database hanya memiliki 1 titik dan 1-2 digit di belakang titik
            if (bagianTitik.length === 2 && bagianTitik[1].length <= 2) {
                strAngka = Math.round(parseFloat(strAngka) || 0).toString();
            }
        }

        // Bersihkan dari selain angka
        let bersih = strAngka.replace(/[^0-9]/g, '');
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
                try {
                    let setter = new Function('val', 'try { this.{{ $modelBind }} = val; } catch(e){ try { {{ $modelBind }} = val; } catch(e2){} }');
                    setter.call(this, 0);
                } catch(e) {}
            @endif
            $dispatch('input', 0);
            $dispatch('change', 0);
            return;
        }
        let num = parseInt(bersih, 10);
        this.nilaiMurni = num;
        this.nilaiTampil = num.toLocaleString('id-ID');
        
        @if($modelBind)
            try {
                let setter = new Function('val', 'try { this.{{ $modelBind }} = val; } catch(e){ try { {{ $modelBind }} = val; } catch(e2){} }');
                setter.call(this, num);
            } catch(e) {}
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
    <input type="hidden" name="{{ $nama }}" :value="nilaiMurni">

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
