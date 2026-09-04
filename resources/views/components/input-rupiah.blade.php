@props([
    'nama',
    'nilaiAwal' => '',
    'placeholder' => '0',
    'wajib' => false,
    'modelBind' => null,
    'min' => 0,
    'label' => null,
    'prefix' => 'Rp',
    'suffix' => null,
    'warnaFokus' => 'blue',
    'classTambahan' => '',
    'disabled' => false,
    'readonly' => false,
    'id' => null,
])

@php
    $petaWarnaFokus = [
        'blue'    => 'focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/30',
        'emerald' => 'focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/30',
        'amber'   => 'focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-500/30',
        'rose'    => 'focus-within:border-rose-500 focus-within:ring-2 focus-within:ring-rose-500/30',
        'violet'  => 'focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-500/30',
        'sky'     => 'focus-within:border-sky-500 focus-within:ring-2 focus-within:ring-sky-500/30',
        'indigo'  => 'focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/30',
        'teal'    => 'focus-within:border-teal-500 focus-within:ring-2 focus-within:ring-teal-500/30',
        'orange'  => 'focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/30',
    ];
    $kelasFokus = ($readonly || $disabled) ? '' : ($petaWarnaFokus[$warnaFokus] ?? $petaWarnaFokus['blue']);
@endphp

<div class="space-y-1 w-full" x-data="{
    nilaiMurni: '{{ old($nama, $nilaiAwal) }}',
    nilaiTampil: '',
    init() {
        this.formatKeTampilan(this.nilaiMurni);
        
        @if($modelBind)
            this.$watch('{{ $modelBind }}', (val) => {
                if (String(val) !== String(this.nilaiMurni)) {
                    this.nilaiMurni = (val !== null && val !== undefined) ? val : '';
                    this.formatKeTampilan(this.nilaiMurni);
                }
            });
            try {
                let nilaiAwalModel = {{ $modelBind }};
                if (typeof nilaiAwalModel !== 'undefined' && nilaiAwalModel !== null && nilaiAwalModel !== '') {
                    this.nilaiMurni = nilaiAwalModel;
                    this.formatKeTampilan(this.nilaiMurni);
                }
            } catch (err) {}
        @endif
    },
    formatKeTampilan(angka) {
        if (angka === '' || angka === null || angka === undefined) {
            this.nilaiTampil = '';
            this.nilaiMurni = '';
            return;
        }
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
        @if($readonly || $disabled)
            return;
        @endif
        let inputVal = e.target.value;
        let bersih = inputVal.replace(/[^0-9]/g, '');
        if (!bersih) {
            this.nilaiMurni = '';
            this.nilaiTampil = '';
            @if($modelBind)
                try { {{ $modelBind }} = 0; } catch (err) {}
            @endif
            this.$dispatch('input', 0);
            this.$dispatch('change', 0);
            return;
        }
        let num = parseInt(bersih, 10);
        this.nilaiMurni = num;
        this.nilaiTampil = num.toLocaleString('id-ID');
        
        @if($modelBind)
            try { {{ $modelBind }} = num; } catch (err) {}
        @endif
        
        this.$dispatch('input', num);
        this.$dispatch('change', num);
    }
}">
    @if($label)
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if($wajib)
                <span class="text-rose-500">*</span>
            @else
                <span class="text-slate-400 font-normal text-[10px]">(Opsional)</span>
            @endif
        </label>
    @endif

    <!-- Hidden Raw Value untuk Submit Form Backend -->
    <input type="hidden" name="{{ $nama }}" :value="nilaiMurni" {{ $wajib ? 'required' : '' }} @if($id) id="{{ $id }}" @endif>

    <!-- Input Display Teks Terformat Titik Otomatis -->
    <div class="relative flex items-center rounded-xl {{ ($readonly || $disabled) ? 'bg-slate-100/80 dark:bg-[#1C1E2A]/60 opacity-90 cursor-not-allowed' : 'bg-[#F8FAFC] dark:bg-[#1C1E2A]' }} border border-[#E2E8F0] dark:border-[#252837] {{ $kelasFokus }} transition-all overflow-hidden {{ $classTambahan }}">
        @if($prefix)
            <span class="px-3 py-2 text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-[#252837] border-r border-[#E2E8F0] dark:border-[#252837] select-none font-mono shrink-0">
                {{ $prefix }}
            </span>
        @endif
        <input type="text" 
               inputmode="numeric"
               :value="nilaiTampil"
               @input="ketikInput($event)"
               @keydown="if(!/^[0-9]$/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End', 'Enter'].includes($event.key) && !$event.ctrlKey && !$event.metaKey) { $event.preventDefault(); }"
               placeholder="{{ $placeholder }}"
               {{ $disabled ? 'disabled' : '' }}
               {{ $readonly ? 'readonly' : '' }}
               data-input-rupiah="true"
               class="w-full px-3 py-2 text-xs font-mono font-bold text-slate-900 dark:text-slate-100 placeholder-slate-400 bg-transparent border-none focus:outline-none focus:ring-0 text-left {{ ($readonly || $disabled) ? 'cursor-not-allowed text-slate-700 dark:text-slate-300' : '' }}">
        @if($suffix)
            <span class="px-3 py-2 text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-[#252837] border-l border-[#E2E8F0] dark:border-[#252837] select-none font-mono shrink-0">
                {{ $suffix }}
            </span>
        @endif
    </div>
</div>
