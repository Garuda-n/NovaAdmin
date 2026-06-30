<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 dark:text-gray-100">

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
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="border p-3">{{ $user->id }}</td>
                                    <td class="border p-3">{{ $user->name }}</td>
                                    <td class="border p-3">{{ $user->email }}</td>
                                    <td class="border p-3">{{ $user->phone }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>