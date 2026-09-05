@props([
    'diperbaruiPada' => null,
    'dibuatPada' => null,
    'class' => '',
])

@php
    $waktu = !empty($diperbaruiPada) ? $diperbaruiPada : (!empty($dibuatPada) ? $dibuatPada : null);
    $relatif = $waktu ? \Carbon\Carbon::parse($waktu)->locale('id')->diffForHumans() : 'Baru';
    $presisi = $waktu ? \Carbon\Carbon::parse($waktu)->format('d/m/Y H:i:s') : '-';
    $label = !empty($diperbaruiPada) ? 'Terakhir diperbarui: ' : 'Dibuat pada: ';
@endphp

<div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 flex items-center justify-center gap-1 font-mono cursor-help select-none {{ $class }}"
     title="{{ $label }}{{ $presisi }}">
    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ $relatif }}</span>
</div>
