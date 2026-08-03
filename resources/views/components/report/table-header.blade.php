@props([
    'columns' => [],
])

<thead class="bg-slate-50 dark:bg-slate-700/50">
    <tr>
        @foreach($columns as $col)
            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider {{ $col['class'] ?? '' }}">
                {{ $col['label'] }}
            </th>
        @endforeach
    </tr>
</thead>
