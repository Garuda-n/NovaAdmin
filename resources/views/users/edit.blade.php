<x-app-layout>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h2 class="text-2xl font-bold mb-6">
                        Edit User
                    </h2>

                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        @if(session('error'))
                            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="block mb-2">Name</label>
                            <input
                                type="text"
                                name="name"
                                value="{{ $user->name }}"
                                class="w-full border border-gray-300 rounded p-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('name')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Phone</label>
                            <input
                                type="text"
                                name="phone"
                                value="{{ $user->phone }}"
                                class="w-full border border-gray-300 rounded p-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('phone')
                                    <div class="text-red-500 text-sm">{{ $message }}</div>
                                @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2">Email</label>
                            <input type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full border border-gray-300 rounded p-2 text-gray-900 bg-white"
                                readonly>
                        </div>

                        <button type="submit"
                            onclick="this.innerText='Updating...'"
                            class="bg-blue-600 text-white px-4 py-2 rounded">
                            Update User
                        </button>
                        <a href="{{ route('users.index') }}"
                            class="bg-gray-400 text-white px-4 py-2 rounded">
                            Cancel
                        </a>
                    </form>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>