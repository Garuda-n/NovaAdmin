<x-app-layout>

<div class="max-w-3xl mx-auto p-6">
    @if(session('success'))
        <div id="toast"
            class="fixed top-5 right-5 bg-green-500 text-white px-4 py-3 rounded shadow-lg z-50 animate-slide-in">
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

    <h2 class="text-2xl font-bold mb-6 text-white">Roles List</h2>
    <a href="{{ route('roles.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
        + Create Role
    </a>

    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">ID</th>
                <th class="border p-2">Role Name</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($roles as $role)
                <tr>
                    <td class="border p-3 text-center text-white">{{ $role->id }}</td>
                    <td class="border p-3 text-center text-white">{{ $role->name }}</td>
                    <td class="border p-2 text-center">
                        <a href="{{ route('roles.edit', $role->id) }}"
                        class="bg-yellow-500 text-white px-3 py-1 rounded">
                            Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="border p-3 text-center text-gray-500">
                        No roles found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

</x-app-layout>