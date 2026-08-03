@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 shadow rounded-lg p-5 border border-gray-200 dark:border-slate-700 ' . $class]) }}>
    {{ $slot }}
</div>
