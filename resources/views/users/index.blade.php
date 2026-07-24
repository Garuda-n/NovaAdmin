<x-app-layout>
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl">

                <div class="p-4 text-slate-800 dark:text-gray-100">
                <x-toast />
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white">
                                Users
                            </h1>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Manage all users in your application.
                            </p>
                        </div>
                        @can('users.create')
                        <a href="{{ route('users.create') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs transition duration-200">
                            <span class="text-sm">+</span>
                            <span>Add User</span>
                        </a>
                        @endcan
                    </div>

                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800">
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">ID</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Name</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Email</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Phone</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Role</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Status</th>
                                @if(Auth::user()->can('users.edit') || Auth::user()->can('users.delete'))
                                <th class="px-3 py-2 text-center text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <td class="px-3 py-2 text-slate-800 dark:text-slate-200">{{ $user->id }}</td>
                                    <td class="px-3 py-2 text-slate-800 dark:text-slate-200">{{ $user->name }}</td>
                                    <td class="px-3 py-2 text-slate-800 dark:text-slate-200">{{ $user->email }}</td>
                                    <td class="px-3 py-2 text-slate-800 dark:text-slate-200">{{ $user->phone }}</td>
                                    <td class="px-3 py-2 text-slate-800 dark:text-slate-200">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:border-indigo-500/30">
                                            {{ $user->role?->name ?? 'No Role' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-slate-800 dark:text-slate-200">
                                        @if ($user->status)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                                        @endif
                                    </td>
                                    @if(Auth::user()->can('users.edit') || Auth::user()->can('users.delete'))
                                    <td class="px-3 py-2 text-slate-200 text-center">
                                        @can('users.edit')
                                        <a href="{{ route('users.edit', $user->id) }}"
                                        class="inline-flex items-center px-2.5 py-1 text-xs rounded-md bg-amber-500 hover:bg-amber-600 text-white transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('users.status', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="px-2.5 py-1 rounded-md text-white text-xs
                                                {{ $user->status ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                                                {{ $user->status ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                    {{ $users->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>