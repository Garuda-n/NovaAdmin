<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create Category
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg">

                <form action="{{ route('categories.store') }}" method="POST" class="p-6">
                    @csrf

                    @include('categories._form')

                </form>

            </div>
        </div>
    </div>
</x-app-layout>