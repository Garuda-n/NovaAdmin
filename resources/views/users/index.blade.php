<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100">

                   @if(session('success'))
                        <div id="toast"
                            class="fixed top-5 right-5 bg-green-500 text-white px-4 py-3 rounded shadow-lg z-50
                                animate-slide-in">
                            {{ session('success') }}
                        </div>
                        <script>
                            setTimeout(() => {
                                const toast = document.getElementById('toast');
                                if (toast) {
                                    toast.classList.add('opacity-0');
                                    setTimeout(() => toast.remove(), 500);
                                }
                            }, 2500);
                        </script>
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
                    <h2 class="text-2xl font-bold mb-6">
                        Users List
                    </h2>

                    <table class="min-w-full border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="border p-3 text-left">ID</th>
                                <th class="border p-3 text-left">Name</th>
                                <th class="border p-3 text-left">Email</th>
                                <th class="border p-3 text-left">Phone</th>
                                <th class="border p-3 text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="border p-3">{{ $user->id }}</td>
                                    <td class="border p-3">{{ $user->name }}</td>
                                    <td class="border p-3">{{ $user->email }}</td>
                                    <td class="border p-3">{{ $user->phone }}</td>
                                    <td class="border p-3 text-center">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>