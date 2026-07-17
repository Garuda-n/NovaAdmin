<x-app-layout>
    <div class="p-6">
        <x-toast />
        <h2 class="mb-6 text-2xl font-bold text-white">
            Edit Counter
        </h2>
        <div class="max-w-3xl rounded-xl border border-slate-700 bg-slate-900 p-6">
            <form method="POST" action="{{ route('counters.update', $counter) }}">
                @csrf
                @method('PUT')
                @include('counters._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('counters.index') }}" class="rounded-lg bg-slate-700 px-5 py-2 text-white">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-white">
                        Update Counter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>