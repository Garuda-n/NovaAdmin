<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-6 text-slate-800 dark:text-gray-100">
                    @if(session('success'))
                        <div id="toast"
                            class="fixed top-5 right-5 bg-green-500 text-white px-4 py-3 rounded shadow-lg z-50
                                animate-slide-in">
                            {{ session('success') }}
                        </div>
                        <script src="{{ asset('js/common/toast.js') }}" defer></script>
                        <style>
                            @keyframes slide-in {
                                from {
                                    transform: translateX(100%);
                                    opacity: 0;
                                }
                                to {
                                    transform: translateX(0);
                                    opacity: 1;
                                }
                            }
                            .animate-slide-in {
                                animation: slide-in 0.4s ease-out;
                            }
                            .opacity-0 {
                                transition: opacity 0.5s ease;
                                opacity: 0;
                            }
                        </style>
                    @endif

                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                                Roles
                            </h1>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">
                                Manage all roles in your application.
                            </p>
                        </div>
                        @can('roles.create')
                        <a href="{{ route('roles.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition duration-200">
                            <span class="text-lg">+</span>
                            <span>Add Role</span>
                        </a>
                        @endcan
                    </div>

                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Role Name</th>
                                @if(Auth::user()->can('roles.edit') || Auth::user()->can('roles.delete'))
                                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($roles as $role)
                                <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">{{ $role->id }}</td>
                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200">{{ $role->name }}</td>
                                    @if(Auth::user()->can('roles.edit') || Auth::user()->can('roles.delete'))
                                    <td class="px-6 py-4 text-slate-800 dark:text-slate-200 text-center">
                                        @can('roles.edit')
                                        <a href="{{ route('roles.edit', $role->id) }}"
                                        class="inline-flex items-center px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition">
                                            Edit
                                        </a>
                                        @endcan
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr class="border-t border-slate-200 dark:border-slate-700">
                                    <td colspan="3" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">
                                        No roles found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>