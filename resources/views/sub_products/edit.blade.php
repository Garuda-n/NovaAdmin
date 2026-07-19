<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Sub Product — {{ $subProduct->name }} ({{ $subProduct->code }})
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6">
                <form action="{{ route('sub-products.update', $subProduct) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('sub_products._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
