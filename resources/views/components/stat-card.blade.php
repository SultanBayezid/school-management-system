@props(['label', 'value', 'hint' => null, 'href' => null, 'color' => 'indigo'])
@php
$colors = [
    'indigo' => 'bg-indigo-500',
    'green'  => 'bg-emerald-500',
    'red'    => 'bg-rose-500',
    'amber'  => 'bg-amber-500',
    'sky'    => 'bg-sky-500',
    'violet' => 'bg-violet-500',
];
@endphp
<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 overflow-hidden rounded-xl shadow-sm border border-gray-200 dark:border-gray-700']) }}>
    <a @if ($href) href="{{ $href }}" @else href="#" onclick="return false" @endif class="block p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
        <div class="flex items-center justify-between">
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</div>
            <div class="h-2.5 w-2.5 rounded-full {{ $colors[$color] ?? $colors['indigo'] }}"></div>
        </div>
        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $value }}</div>
        @if ($hint)
            <div class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $hint }}</div>
        @endif
    </a>
</div>
