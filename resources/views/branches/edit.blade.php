<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-6">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200 dark:border-slate-700">
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                                Edit Branch
                            </h1>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                                Update existing branch details.
                            </p>
                        </div>

                        <a href="{{ route('branches.index') }}"
                           class="px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-slate-200 text-sm font-medium transition">
                            ← Back to List
                        </a>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('branches.update', $branch->id) }}">
                        @csrf
                        @method('PUT')

                        @include('branches._form', ['branch' => $branch])

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <a href="{{ route('branches.index') }}"
                               class="px-5 py-2.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 font-medium">
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium transition shadow-lg">
                                Update Branch
                            </button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>