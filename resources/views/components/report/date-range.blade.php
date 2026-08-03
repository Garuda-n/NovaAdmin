@props([
    'preset' => 'this_month',
    'fromDate' => '',
    'toDate' => '',
    'presetOptions' => [],
])

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

    {{-- Preset Dropdown --}}
    <div>
        <label for="report_preset" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">Date Range</label>
        <select id="report_preset" name="preset" x-model="preset" @change="onPresetChange()"
                class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
            @foreach($presetOptions as $option)
                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
            @endforeach
        </select>
    </div>

    {{-- From Date --}}
    <div>
        <label for="report_from_date" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">From Date</label>
        <input type="date" id="report_from_date" name="from_date" x-model="from_date"
               @change="preset = 'custom'"
               class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    {{-- To Date --}}
    <div>
        <label for="report_to_date" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">To Date</label>
        <input type="date" id="report_to_date" name="to_date" x-model="to_date"
               @change="preset = 'custom'"
               class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-xs focus:ring-indigo-500 focus:border-indigo-500">
    </div>

</div>
