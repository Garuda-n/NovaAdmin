<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-100 leading-tight flex items-center gap-2">
                    <x-heroicon-o-camera class="w-6 h-6 text-indigo-500" />
                    Stock Item Images
                </h2>
                <nav class="flex text-xs text-gray-500 dark:text-gray-400 gap-1.5 items-center mt-1">
                    <span>Inventory</span>
                    <span>/</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Stock Item Images</span>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Search Item Card -->
            <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-6 border border-gray-200 dark:border-slate-700">
                <form id="searchForm" onsubmit="event.preventDefault(); searchStockItem();" class="flex flex-col sm:flex-row items-end gap-4 max-w-xl">
                    <div class="w-full">
                        <label for="item_code" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            Scan or Enter Item Code <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="item_code"
                            name="item_code"
                            placeholder="Enter Item Code (e.g. ITEM0001)"
                            class="w-full rounded-lg border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono uppercase"
                            required
                            autofocus>
                    </div>

                    <button
                        type="submit"
                        id="searchBtn"
                        class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition flex items-center justify-center gap-2 shadow-sm shrink-0">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        <span>Search</span>
                    </button>
                </form>
            </div>

            <!-- Loader Indicator -->
            <div id="loader" class="hidden py-12 flex justify-center items-center">
                <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold text-sm">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Fetching stock item...</span>
                </div>
            </div>

            <!-- Error Notification Alert -->
            <div id="errorAlert" class="hidden p-4 bg-rose-50 dark:bg-rose-955/20 border border-rose-200 dark:border-rose-800/40 rounded-lg text-rose-800 dark:text-rose-400 text-xs flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 shrink-0" />
                <span id="errorMessage">Item code not found.</span>
            </div>

            <!-- Workspace Container -->
            <div id="workspace-container">
                <div class="bg-white dark:bg-slate-800 shadow rounded-lg border border-gray-200 dark:border-slate-700 p-12 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-950/50 rounded-full text-indigo-500">
                            <x-heroicon-o-qr-code class="w-10 h-10" />
                        </div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Start by scanning or entering an item code</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md">
                            Once you enter a valid item code and search, the system will load its dynamic photo gallery workspace.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';

        async function searchStockItem() {
            const itemCode = document.getElementById('item_code').value.trim();
            if (!itemCode) return;

            showLoader(true);
            showError(false);

            try {
                const response = await fetch(`{{ route('stock-item-images.search') }}?item_code=${encodeURIComponent(itemCode)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    document.getElementById('workspace-container').innerHTML = data.html;
                } else {
                    showError(true, data.message || 'Failed to find item.');
                }
            } catch (err) {
                console.error(err);
                showError(true, 'An unexpected error occurred while searching.');
            } finally {
                showLoader(false);
            }
        }

        async function uploadStockImage(stockItemId) {
            const fileInput = document.getElementById('imageInput');
            if (!fileInput.files || fileInput.files.length === 0) return;

            showLoader(true);
            showError(false);

            const formData = new FormData();
            formData.append('_token', CSRF_TOKEN);
            formData.append('image', fileInput.files[0]);

            try {
                const response = await fetch(`/inventory/stock-item-images/${stockItemId}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    document.getElementById('workspace-container').innerHTML = data.html;
                    showToast('success', 'Image uploaded successfully!');
                } else {
                    showError(true, data.message || 'Failed to upload image.');
                }
            } catch (err) {
                console.error(err);
                showError(true, 'An unexpected error occurred during upload.');
            } finally {
                showLoader(false);
            }
        }

        async function setStockImageDefault(stockItemId, imageId) {
            showLoader(true);
            showError(false);

            try {
                const response = await fetch(`/inventory/stock-item-images/${stockItemId}/set-default/${imageId}`, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    document.getElementById('workspace-container').innerHTML = data.html;
                    showToast('success', 'Default image updated successfully.');
                } else {
                    showError(true, data.message || 'Failed to set default.');
                }
            } catch (err) {
                console.error(err);
                showError(true, 'An unexpected error occurred.');
            } finally {
                showLoader(false);
            }
        }

        async function deleteStockImage(stockItemId, imageId) {
            if (!confirm('Are you sure you want to delete this image?')) return;

            showLoader(true);
            showError(false);

            try {
                const response = await fetch(`/inventory/stock-item-images/${stockItemId}/delete/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    document.getElementById('workspace-container').innerHTML = data.html;
                    showToast('success', 'Image deleted successfully.');
                } else {
                    showError(true, data.message || 'Failed to delete image.');
                }
            } catch (err) {
                console.error(err);
                showError(true, 'An unexpected error occurred.');
            } finally {
                showLoader(false);
            }
        }

        function showLoader(show) {
            const loader = document.getElementById('loader');
            if (show) {
                loader.classList.remove('hidden');
            } else {
                loader.classList.add('hidden');
            }
        }

        function showError(show, msg = '') {
            const alert = document.getElementById('errorAlert');
            const messageSpan = document.getElementById('errorMessage');
            if (show) {
                messageSpan.textContent = msg;
                alert.classList.remove('hidden');
            } else {
                alert.classList.add('hidden');
            }
        }

        function showToast(type, message) {
            if (window.Alpine) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type, message } }));
            } else {
                alert(message);
            }
        }
    </script>
    @endpush
</x-app-layout>
