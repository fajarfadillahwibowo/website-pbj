@props([
    'nama',
    'nilaiAwal' => '',
    'placeholder' => '-- Pilih Opsi --',
    'opsi' => [],
    'wajib' => false,
    'warnaFokus' => 'blue',
    'classTombol' => '',
    'modelBind' => null,
    'submitOnChange' => false,
])

@php
    $daftarPilihan = [];
    foreach ($opsi as $k => $v) {
        if (is_array($v) || is_object($v)) {
            $daftarPilihan[] = [
                'nilai' => is_object($v) ? ($v->nilai ?? $v->kode ?? $k) : ($v['nilai'] ?? $v['kode'] ?? $k),
                'label' => is_object($v) ? ($v->label ?? $v->nama ?? '') : ($v['label'] ?? $v['nama'] ?? ''),
                'sub'   => is_object($v) ? ($v->sub ?? null) : ($v['sub'] ?? null),
            ];
        } else {
            $daftarPilihan[] = [
                'nilai' => $k,
                'label' => $v,
                'sub'   => null
            ];
        }
    }
@endphp

<div class="relative w-full" x-data="{
    buka: false,
    terpilih: '{{ old($nama, $nilaiAwal) }}',
    labelTerpilih: '',
    daftar: {{ json_encode($daftarPilihan) }},
    submitOtomatis: {{ $submitOnChange ? 'true' : 'false' }},
    init() {
        @if($modelBind)
            this.$watch('{{ $modelBind }}', (val) => {
                this.terpilih = val;
                this.sinkronkanLabel();
            });
            this.terpilih = {{ $modelBind }} || this.terpilih;
        @endif
        this.sinkronkanLabel();
    },
    sinkronkanLabel() {
        if (this.terpilih !== null && this.terpilih !== '') {
            let item = this.daftar.find(d => String(d.nilai) === String(this.terpilih));
            this.labelTerpilih = item ? item.label : this.terpilih;
        } else {
            this.labelTerpilih = '';
        }
    },
    pilihItem(nilai, label) {
        this.terpilih = nilai;
        this.labelTerpilih = label;
        @if($modelBind)
            {{ $modelBind }} = nilai;
        @endif
        this.buka = false;
        $dispatch('input', nilai);
        $dispatch('change', nilai);
        if (this.submitOtomatis) {
            $nextTick(() => {
                if ($el.closest('form')) $el.closest('form').submit();
            });
        }
    }
}" @click.away="buka = false">

    <!-- Input hidden untuk submit form -->
    <input type="hidden" name="{{ $nama }}" :value="terpilih" {{ $wajib ? 'required' : '' }}>

    <!-- Tombol Trigger Dropdown Modern & Compact -->
    <button type="button" 
            @click="buka = !buka"
            class="w-full flex items-center justify-between px-3 py-2 text-xs rounded-xl bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-{{ $warnaFokus }}-500/30 transition-all text-left {{ $classTombol }}">
        <span class="truncate font-medium" :class="{ 'text-slate-400 dark:text-slate-500 font-normal': !terpilih }" x-text="labelTerpilih || '{{ $placeholder }}'"></span>
        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 ml-1.5 transition-transform duration-200" :class="{ 'rotate-180 text-{{ $warnaFokus }}-500': buka }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Menu List Dropdown Mengambang (Floating Popover Modern) -->
    <div x-show="buka" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         x-cloak
         class="absolute z-50 left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-xl bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] shadow-xl shadow-slate-900/10 dark:shadow-black/50 py-1 text-xs divide-y divide-slate-100/50 dark:divide-[#252837]/30 custom-scrollbar">
        
        <template x-for="item in daftar" :key="item.nilai">
            <div @click="pilihItem(item.nilai, item.label)"
                 class="px-3 py-2 cursor-pointer flex items-center justify-between gap-2 hover:bg-slate-50 dark:hover:bg-[#1C1E2A] transition-colors"
                 :class="{ 'bg-{{ $warnaFokus }}-50/60 dark:bg-{{ $warnaFokus }}-500/10 text-{{ $warnaFokus }}-600 dark:text-{{ $warnaFokus }}-400 font-semibold': String(terpilih) === String(item.nilai) }">
                <div class="flex flex-col truncate">
                    <span class="truncate" x-text="item.label"></span>
                    <template x-if="item.sub">
                        <span class="text-[10px] text-slate-400 font-normal" x-text="item.sub"></span>
                    </template>
                </div>
                <template x-if="String(terpilih) === String(item.nilai)">
                    <svg class="w-3.5 h-3.5 text-{{ $warnaFokus }}-600 dark:text-{{ $warnaFokus }}-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
            </div>
        </template>
    </div>
</div>
