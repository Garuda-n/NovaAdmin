<x-app-layout>
    <div class="p-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">
            Create User
        </h2>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-slate-700 dark:text-white mb-2">
                    Name
                </label>
                <input
                    type="text"
                    name="name"
                    class="w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block text-slate-700 dark:text-white mb-2">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    class="w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block text-slate-700 dark:text-white mb-2">
                    Mobile Number
                </label>
                <input
                    type="text"
                    name="phone"
                    class="w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block text-slate-700 dark:text-white mb-2">
                    Role
                </label>
                <select
                    name="role_id"
                    class="w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                    <option value="">-- Select Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-slate-700 dark:text-white mb-2">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    class="w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
            <div class="mb-4">
                <label class="block text-slate-700 dark:text-white mb-2">
                    Confirm Password
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    class="w-full rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
            </div>
            <button
                class="bg-green-600 hover:bg-green-700 px-5 py-2 rounded-lg text-white">
                Create User
            </button>
        </form>
    </div>
</x-app-layout>