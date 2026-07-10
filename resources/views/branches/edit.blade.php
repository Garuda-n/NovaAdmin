<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Branch
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6">

                <form method="POST" action="{{ route('branches.update', $branch->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" name="company_id" value="{{ $branch->company_id }}">

                        <!-- Branch Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Branch Name
                            </label>

                            <input type="text"
                                name="name"
                                value="{{ old('name', $branch->name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-slate-700 dark:text-white"
                                required>

                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Branch Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Branch Code
                            </label>

                            <input type="text"
                                name="branch_code"
                                value="{{ old('branch_code', $branch->branch_code) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-slate-700 dark:text-white"
                                required>

                            @error('branch_code')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Phone
                            </label>

                            <input type="text"
                                name="phone"
                                value="{{ old('phone', $branch->phone) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-slate-700 dark:text-white">

                            @error('phone')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Email
                            </label>

                            <input type="email"
                                name="email"
                                value="{{ old('email', $branch->email) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-slate-700 dark:text-white">

                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Address -->
                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Address
                            </label>

                            <textarea
                                name="address"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-slate-700 dark:text-white">{{ old('address', $branch->address) }}</textarea>

                            @error('address')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror

                        </div>


                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status
                            </label>

                            <select name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-slate-700 dark:text-white">

                                <option value="1"
                                    {{ $branch->status == 1 ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="0"
                                    {{ $branch->status == 0 ? 'selected' : '' }}>
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="mt-6 flex justify-end gap-3">

                        <a href="{{ route('branches.index') }}"
                           class="px-4 py-2 bg-gray-500 text-white rounded-md">
                            Cancel
                        </a>


                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Branch
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>