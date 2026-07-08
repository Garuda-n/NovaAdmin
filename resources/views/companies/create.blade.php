<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-slate-900 rounded-xl border border-slate-700 shadow-xl">

                <div class="p-6">

                    <h1 class="text-3xl font-bold text-white">
                        Create Company
                    </h1>

                    <p class="text-slate-400 mt-1 mb-8">
                        Add a new company to the system.
                    </p>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-500/10 border border-red-500 text-red-300 p-4">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('companies.store') }}" method="POST">

                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Company Name --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Company Name <span class="text-red-400">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            {{-- Company Code --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Company Code <span class="text-red-400">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="code"
                                    value="{{ old('code') }}"
                                    required
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            {{-- Address --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Address
                                </label>

                                <textarea
                                    name="address"
                                    rows="4"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('address') }}</textarea>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Status
                                </label>

                                <select
                                    name="status"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                                    <option value="1" {{ old('status',1)==1 ? 'selected' : '' }}>
                                        Active
                                    </option>

                                    <option value="0" {{ old('status')==='0' ? 'selected' : '' }}>
                                        Inactive
                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="flex justify-end gap-3 mt-8">

                            <a href="{{ route('companies.index') }}"
                                class="px-5 py-3 rounded-lg bg-slate-700 hover:bg-slate-600 text-white transition">
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="px-5 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition">

                                Save Company

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>