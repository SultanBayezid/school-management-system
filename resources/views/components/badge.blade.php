@props(['type' => 'gray'])
@php
$types = [
    'green'  => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'red'    => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'amber'  => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    'sky'    => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
    'violet' => 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
    'gray'   => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$types[$type]]) }}>
    {{ $slot }}
</span>
