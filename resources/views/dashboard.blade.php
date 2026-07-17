<x-app-layout>
    <div class="flex flex-col h-[calc(100vh-64px)]">
        <!-- Header -->
        <div class="px-6 pt-6">
            <h1 class="text-3xl font-bold text-white">
                Welcome to NovaAdmin
            </h1>
        </div>
        <!-- Dashboard Image -->
        <div class="flex-1 flex justify-center items-center p-6 overflow-hidden">
            <img
                src="{{ asset('images/dashboard.png') }}"
                alt="Dashboard"
                class="w-full h-full object-cover rounded-lg">
        </div>
    </div>
</x-app-layout>