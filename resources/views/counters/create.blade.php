<x-app-layout>

    <div class="p-6">

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-white">
                Add Counter
            </h2>

            <p class="text-sm text-gray-400 mt-1">
                Create a new counter.
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-700 rounded-xl shadow-lg p-6">

            <form action="{{ route('counters.store') }}" method="POST">

                @csrf

                @include('counters._form')

            </form>

        </div>

    </div>

</x-app-layout>