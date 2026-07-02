<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-slate-900 rounded-xl border border-slate-700 shadow-xl">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h2 class="text-3xl font-bold mb-8 text-white">Edit Role</h2>

                    <form method="POST" action="{{ route('roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-8">
                            <label class="block mb-2 text-sm font-medium text-slate-300">Role Name</label>
                            <input type="text"
                                name="name"
                                value="{{ old('name', $role->name) }}"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-200">
                                Update Role
                            </button>
                            <a href="{{ route('roles.index') }}"
                                class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition duration-200">
                                ← Back to Roles
                            </a>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>