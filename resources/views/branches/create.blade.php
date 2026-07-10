<x-app-layout>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-slate-900 rounded-xl border border-slate-700 shadow-xl">

                <div class="p-6">

                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-white">
                            Create Branch
                        </h1>

                        <p class="text-slate-400 mt-1">
                            Add a new branch to your company.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg bg-red-100 border border-red-300 text-red-700 px-4 py-3">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('branches.store') }}" method="POST">

                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Company --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Company <span class="text-red-500">*</span>
                                </label>

                                <select
                                    name="company_id"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white focus:border-indigo-500 focus:ring-indigo-500">

                                    <option value="">Select Company</option>

                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Branch Name --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Branch Name <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('branch_name') }}"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white"
                                    placeholder="Coimbatore Branch">
                            </div>

                            {{-- Branch Code --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Branch Code <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="branch_code"
                                    value="{{ old('branch_code') }}"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white"
                                    placeholder="CBE001">
                            </div>

                            {{-- GST --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    GST Number
                                </label>

                                <input
                                    type="text"
                                    name="gst_number"
                                    value="{{ old('gst_number') }}"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white">
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
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white">
                            </div>

                        </div>

                        {{-- Address --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Address
                            </label>

                            <textarea
                                name="address"
                                rows="4"
                                class="w-full rounded-lg border border-slate-600 bg-slate-800 text-white">{{ old('address') }}</textarea>
                        </div>

                        {{-- Head Office --}}
                        <div class="mt-6 flex items-center">

                            <input
                                id="is_head_office"
                                type="checkbox"
                                name="is_head_office"
                                value="1"
                                {{ old('is_head_office') ? 'checked' : '' }}
                                class="rounded border-slate-600 text-indigo-600">

                            <label
                                for="is_head_office"
                                class="ml-2 text-slate-300">

                                Head Office

                            </label>

                        </div>

                        {{-- Buttons --}}
                        <div class="mt-8 flex justify-end gap-3">

                            <a href="{{ route('branches.index') }}"
                               class="px-5 py-2 rounded-lg bg-slate-600 hover:bg-slate-700 text-white">

                                Cancel

                            </a>

                            <button
                                type="submit"
                                class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white">

                                Save Branch

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>