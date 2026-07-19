<x-app-layout>

    <div class="py-6 bg-[#0f1422] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-[#111827] rounded-2xl border border-[#1f293d] shadow-2xl p-6 sm:p-8">

                <x-toast />

                <!-- Header -->
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-700/60">
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            Edit Setting: <span class="font-mono text-indigo-400">{{ $setting->key }}</span>
                        </h1>
                        <p class="text-slate-400 text-sm mt-1">
                            Update setting configuration value and group details.
                        </p>
                    </div>

                    <a href="{{ route('settings.index') }}"
                       class="px-4 py-2 rounded-lg bg-[#27334d] hover:bg-[#32415f] text-slate-200 text-sm font-medium transition">
                        ← Back to List
                    </a>
                </div>

                <!-- Form -->
                <form action="{{ route('settings.update', $setting) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('settings._form')

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-slate-700/60">
                        <a href="{{ route('settings.index') }}" class="px-5 py-2.5 rounded-lg bg-slate-700 text-slate-200 hover:bg-slate-600 font-medium text-sm">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-lg bg-[#5851ea] hover:bg-[#4b43e0] text-white font-medium text-sm transition shadow-lg">
                            Update Setting
                        </button>
                    </div>
                </form>

            </div>

        </div>
    </div>

</x-app-layout>
