<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight flex items-center gap-2">
                    <x-heroicon-o-camera class="w-6 h-6 text-indigo-500" />
                    Manage Images — {{ $stockItem->item_code }}
                </h2>
                <nav class="flex text-xs text-gray-500 dark:text-gray-400 gap-1.5 items-center mt-1">
                    <span>Inventory</span>
                    <span>/</span>
                    <a href="{{ route('available-stock.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Available Stock</a>
                    <span>/</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Manage Images</span>
                </nav>
            </div>
            <div>
                <a href="{{ route('available-stock.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg hover:bg-slate-300 transition">
                    ← Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-toast />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Item Information -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-[#182035] rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Item Details</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ $stockItem->product->name }}</h3>
                            <span class="text-xs text-slate-500 font-mono">{{ $stockItem->item_code }}</span>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Category:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $stockItem->product->category->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Sub Product:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $stockItem->subProduct->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Size:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $stockItem->size->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Branch:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $stockItem->branch->name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Counter:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $stockItem->counter->counter_name ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-100 dark:border-slate-800/60 pt-3">
                                <span class="text-slate-500 dark:text-slate-400">Allocated Date:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $stockItem->allocated_at ? $stockItem->allocated_at->format('d M Y, h:i A') : '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Left Column: Active Thumbnail -->
                    <div class="bg-white dark:bg-[#182035] rounded-xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Preview Image</span>
                        <div class="aspect-square w-full rounded-lg overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 flex items-center justify-center">
                            @if($stockItem->image_url)
                                <img src="{{ $stockItem->image_url }}" alt="Preview" class="w-full h-full object-cover">
                            @else
                                <div class="text-center p-6 text-slate-400">
                                    <x-heroicon-o-photo class="w-12 h-12 mx-auto stroke-1" />
                                    <span class="text-xs mt-2 block">No Active Image Available</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Gallery & Uploads -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Upload Form Card -->
                    <div class="bg-white dark:bg-[#182035] rounded-xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Upload New Image</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Select or drop a photo to associate with this individual stock item. Files will be saved in `storage/public/stockitemimg`.</p>

                        <form action="{{ route('available-stock.upload-image', $stockItem->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-lg cursor-pointer bg-slate-50 dark:bg-slate-900/40 hover:bg-slate-100 dark:hover:bg-slate-900/60 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <x-heroicon-o-arrow-up-tray class="w-8 h-8 text-slate-400 mb-2" />
                                        <p class="text-xs text-slate-500 dark:text-slate-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-[10px] text-slate-400 mt-1">PNG, JPG, JPEG, WEBP (Max 5MB)</p>
                                    </div>
                                    <input type="file" name="image" required class="hidden" accept="image/*" onchange="this.form.submit()">
                                </label>
                            </div>
                        </form>
                    </div>

                    <!-- Gallery List Card -->
                    <div class="bg-white dark:bg-[#182035] rounded-xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Item Photos Gallery</h3>

                        @if($stockItem->images->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($stockItem->images as $image)
                                    <div class="group relative rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-900 flex flex-col justify-between">
                                        <div class="aspect-video w-full bg-black/5 dark:bg-black/20 flex items-center justify-center relative overflow-hidden">
                                            <img src="{{ $image->url }}" alt="Stock Photo" class="w-full h-full object-cover">
                                            
                                            @if($image->is_default)
                                                <div class="absolute top-2 left-2 px-2.5 py-0.5 bg-emerald-500 text-white text-[10px] font-bold rounded-full shadow-sm">
                                                    Default
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-3 bg-slate-50/50 dark:bg-slate-900/60 flex items-center justify-between border-t border-slate-200/60 dark:border-slate-850">
                                            <div>
                                                @if(!$image->is_default)
                                                    <form action="{{ route('available-stock.set-default-image', [$stockItem->id, $image->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                            Make Default
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                                        Active Default
                                                    </span>
                                                @endif
                                            </div>

                                            <form action="{{ route('available-stock.delete-image', [$stockItem->id, $image->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-rose-500 transition" title="Delete Image">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl bg-slate-50/40 dark:bg-slate-900/20">
                                <x-heroicon-o-photo class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 stroke-1" />
                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 block mt-2">No Custom Images Uploaded</span>
                                <span class="text-xs text-slate-400 block mt-1">Upload a photo above to get started.</span>
                            </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
