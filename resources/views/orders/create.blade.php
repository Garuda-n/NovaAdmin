<x-app-layout>
    <div class="py-6 bg-slate-100 dark:bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <!-- Header Section -->
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                    Create Order
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                    Create a new order.
                </p>
            </div>

            <!-- Form Wrapper -->
            <form method="POST" action="#">
                @csrf
                {{-- Fields will be added here --}}
            </form>

        </div>
    </div>
</x-app-layout>
