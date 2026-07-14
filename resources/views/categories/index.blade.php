<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <x-toast />
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Category Master
            </h2>

            <a href="{{ route('categories.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                + Add Category
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-800 shadow rounded-lg overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">

                        <thead class="bg-gray-100 dark:bg-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Short Code</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Tax</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-slate-700">

                            @forelse($categories as $category)

                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700">

                                    <td class="px-6 py-4 text-slate-200">{{ $category->code }}</td>

                                    <td class="px-6 py-4 text-slate-200">{{ $category->name }}</td>

                                    <td class="px-6 py-4 text-slate-200">
                                        {{ $category->tax?->name ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-left text-slate-200">

                                        @if($category->status)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                                Inactive
                                            </span>
                                        @endif

                                    </td>

                                    <td class="px-6 py-4 text-left">

                                        <a href="{{ route('categories.edit',$category) }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">

                                            <x-heroicon-o-pencil-square class="w-4 h-4 mr-1"/>

                                            Edit

                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-500">
                                        No Categories Found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>