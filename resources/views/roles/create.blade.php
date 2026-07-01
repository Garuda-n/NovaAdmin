<x-app-layout>

<div class="max-w-xl mx-auto p-6">

    <h2 class="text-2xl font-bold mb-6 text-white">Create Role</h2>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        <input type="text"
            name="name"
            placeholder="Enter role name"
            class="w-full border p-2 rounded mb-4">

        @error('name')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Create Role
        </button>
    </form>

    <a href="{{ route('roles.index') }}" class="text-blue-600 mt-4 inline-block">
        ← Back to Roles List
    </a>

</div>

</x-app-layout>