<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create Bulk Stock Inward
            </h2>
            <a href="{{ route('stock-inwards.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-lg transition">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('stock-inwards.store') }}" method="POST">
                @csrf
                @include('inventory.stock_inwards._form')
            </form>
        </div>
    </div>
</x-app-layout>
