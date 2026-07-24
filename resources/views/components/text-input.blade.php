@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'px-2.5 py-1.5 text-xs sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}>
