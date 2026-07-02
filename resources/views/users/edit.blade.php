<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-slate-900 rounded-xl border border-slate-700 shadow-xl">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h2 class="text-3xl font-bold mb-8 text-white">
                        Edit User
                    </h2>

                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        @if(session('error'))
                            <div class="mb-6 p-4 bg-red-900 border border-red-700 text-red-200 rounded-lg">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-slate-300">Name</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ $user->name }}"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @error('name')
                                    <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
                                @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-slate-300">Phone</label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ $user->phone }}"
                                class="w-full px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @error('phone')
                                    <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
                                @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-slate-300">Email</label>
                            <input type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-slate-400 cursor-not-allowed"
                                readonly>
                        </div>

                        <div class="mb-8">
                            <label class="block mb-2 text-sm font-medium text-slate-300">Role</label>
                            <select name="role_id" class="w-full px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="text-red-400 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                onclick="this.innerText='Updating...'"
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition duration-200">
                                Update User
                            </button>
                            <a href="{{ route('users.index') }}"
                                class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-lg transition duration-200">
                                Cancel
                            </a>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>