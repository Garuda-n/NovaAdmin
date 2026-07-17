<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-slate-900 rounded-xl border border-slate-700 shadow-xl">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('success'))
                        <div class="mb-4 p-2 bg-green-500/20 text-green-400 rounded-lg border border-green-500/30">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h2 class="text-3xl font-bold mb-8 text-white">Create Role</h2>

                    <form method="POST" action="{{ route('roles.store') }}">
                        @csrf

                        <div class="mb-8">
                            <label class="block mb-2 text-sm font-medium text-slate-300">Role Name</label>
                            <input type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter role name"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">

                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Permissions --}}
                        <div class="mb-8">
                            <label class="block mb-4 text-sm font-medium text-slate-300">Permissions</label>

                            <div class="space-y-6">
                                @foreach($permissions as $group => $groupPermissions)
                                    <div class="bg-slate-800 rounded-lg border border-slate-700 p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="text-white font-semibold text-base">{{ $group }}</h3>
                                            <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-400 hover:text-slate-200 transition">
                                                <input type="checkbox"
                                                    class="select-all-group rounded border-slate-500 bg-slate-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0"
                                                    data-group="{{ $group }}"
                                                    onchange="toggleGroup(this, '{{ $group }}')">
                                                <span>Select All</span>
                                            </label>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            @foreach($groupPermissions as $permission)
                                                <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-300 hover:text-white transition">
                                                    <input type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        data-group="{{ $group }}"
                                                        class="permission-checkbox rounded border-slate-500 bg-slate-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-0"
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
                                class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition duration-200">
                                ← Back to Roles
                            </a>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>

    <script>
        function toggleGroup(selectAllCheckbox, group) {
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-group="${group}"]`);
            checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        }
    </script>
</x-app-layout>