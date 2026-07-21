<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-6 text-slate-800 dark:text-gray-100">

                    @if(session('success'))
                        <div class="mb-4 p-2 bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-400 rounded-lg border border-green-300 dark:border-green-500/30">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h2 class="text-3xl font-bold mb-8 text-slate-900 dark:text-white">Create Role</h2>

                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div class="mb-8">
                            <label class="block mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Role Name</label>
                            <input type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter role name"
                                class="w-full px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                            @error('name')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Permissions --}}
                        <div class="mb-8">
                            <label class="block mb-4 text-sm font-medium text-slate-700 dark:text-slate-300">Permissions</label>

                            <div class="space-y-6">
                                @foreach($permissions as $group => $groupPermissions)
                                    <div class="bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
                                        <div class="mb-3">
                                            <h3 class="text-slate-900 dark:text-white font-semibold text-base">{{ $group }}</h3>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            @foreach($groupPermissions as $permission)
                                                <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition">
                                                    <input type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        data-group="{{ $group }}"
                                                        class="permission-checkbox rounded border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"
                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <span>{{ $permission->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-200">
                                Create Role
                            </button>
                            <a href="{{ route('roles.index') }}"
                                class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-white font-medium rounded-lg transition duration-200">
                                ← Back to Roles
                            </a>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>

    <script src="{{ asset('js/roles/permission_toggle.js') }}" defer></script>
</x-app-layout>