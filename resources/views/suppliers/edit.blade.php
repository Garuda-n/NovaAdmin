<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-slate-900 rounded-xl border border-slate-700 shadow-xl">

                <div class="p-6">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-700">
                        <div>
                            <h1 class="text-2xl font-bold text-white">
                                Edit Supplier: {{ $supplier->supplier_name }}
                            </h1>
                            <p class="text-slate-400 text-sm mt-1">
                                Supplier ID: <span class="font-mono text-indigo-400 font-bold">#{{ $supplier->id }}</span>
                            </p>
                        </div>

                        <a href="{{ route('suppliers.index') }}"
                           class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-medium transition">
                            ← Back to List
                        </a>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('suppliers._form')

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-700">
                            <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 rounded-lg bg-slate-700 text-slate-200 hover:bg-slate-600 font-medium">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 font-medium transition shadow-lg">
                                Update Supplier
                            </button>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>
