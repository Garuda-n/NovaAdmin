<x-app-layout>

<div class="max-w-xl mx-auto p-6">

    <h2 class="text-2xl font-bold mb-6 text-white">Edit Role</h2>

    <form method="POST" action="{{ route('roles.update', $role->id) }}">
        @csrf
        @method('PUT')

        <label class="block mb-2 font-medium text-white">Role Name</label>

        <input type="text"
            name="name"
            value="{{ old('name', $role->name) }}"
            class="w-full border p-2 rounded mb-4 text-gray-900 bg-white">

        @error('name')
            <p class="text-red-500 text-sm">{{ $message }}</p>
        @enderror

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Update Role
        </button>
    </form>

    <a href="{{ route('roles.index') }}"
        class="text-blue-600 mt-4 inline-block">
        ← Back to Roles
    </a>

</div>

</x-app-layout>