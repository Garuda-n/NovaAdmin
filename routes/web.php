<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UomController;
use App\Http\Controllers\FinancialYearController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SubProductController;
use App\Http\Controllers\Inventory\StockInwardController;
use App\Http\Controllers\Inventory\ItemAllocationController;
use App\Http\Controllers\Inventory\AvailableStockController;
use App\Http\Controllers\Inventory\StockTransferController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\Reports\Inventory\StockRegisterController;
use App\Http\Controllers\Reports\Sales\SalesReportController;
use App\Http\Controllers\Reports\Purchase\PurchaseReportController;
use App\Http\Controllers\Reports\Customer\CustomerReportController;
use App\Http\Controllers\Reports\Supplier\SupplierReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class)
        ->only(['create', 'store'])
        ->middleware('permission:users.create');

    Route::resource('users', UserController::class)
        ->only(['index', 'show'])
        ->middleware('permission:users.view');

    Route::resource('users', UserController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:users.edit');

    Route::resource('users', UserController::class)
        ->only(['destroy'])
        ->middleware('permission:users.delete');

    Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])
        ->name('users.status')
        ->middleware('permission:users.edit');

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    Route::resource('roles', RoleController::class)
        ->only(['create', 'store'])
        ->middleware('permission:roles.create');

    Route::resource('roles', RoleController::class)
        ->only(['index', 'show'])
        ->middleware('permission:roles.view');

    Route::resource('roles', RoleController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:roles.edit');

    Route::resource('roles', RoleController::class)
        ->only(['destroy'])
        ->middleware('permission:roles.delete');

    /*
    |--------------------------------------------------------------------------
    | Companies
    |--------------------------------------------------------------------------
    */
    Route::resource('companies', CompanyController::class)
        ->only(['create', 'store'])
        ->middleware('permission:companies.create');

    Route::resource('companies', CompanyController::class)
        ->only(['index', 'show'])
        ->middleware('permission:companies.view');

    Route::resource('companies', CompanyController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:companies.edit');

    Route::resource('companies', CompanyController::class)
        ->only(['destroy'])
        ->middleware('permission:companies.delete');

    /*
    |--------------------------------------------------------------------------
    | Branches
    |--------------------------------------------------------------------------
    */
    Route::resource('branches', BranchController::class)
        ->only(['create', 'store'])
        ->middleware('permission:branches.create');

    Route::resource('branches', BranchController::class)
        ->only(['index', 'show'])
        ->middleware('permission:branches.view');

    Route::resource('branches', BranchController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:branches.edit');

    Route::resource('branches', BranchController::class)
        ->only(['destroy'])
        ->middleware('permission:branches.delete');

    /*
    |--------------------------------------------------------------------------
    | Uoms
    |--------------------------------------------------------------------------
    */
    Route::resource('uoms', UomController::class)
        ->only(['create', 'store'])
        ->middleware('permission:uoms.create');

    Route::resource('uoms', UomController::class)
        ->only(['index', 'show'])
        ->middleware('permission:uoms.view');

    Route::resource('uoms', UomController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:uoms.edit');

    Route::resource('uoms', UomController::class)
        ->only(['destroy'])
        ->middleware('permission:uoms.delete');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Financial Years
    |--------------------------------------------------------------------------
    */
    Route::resource('financial-years', FinancialYearController::class)
        ->only(['create', 'store'])
        ->middleware('permission:financial-years.create');

    Route::resource('financial-years', FinancialYearController::class)
        ->only(['index', 'show'])
        ->middleware('permission:financial-years.view');

    Route::resource('financial-years', FinancialYearController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:financial-years.edit');

    Route::resource('financial-years', FinancialYearController::class)
        ->only(['destroy'])
        ->middleware('permission:financial-years.delete');

    /*
    |--------------------------------------------------------------------------
    | Taxes
    |--------------------------------------------------------------------------
    */
    Route::resource('taxes', TaxController::class)
        ->only(['create', 'store'])
        ->middleware('permission:taxes.create');

    Route::resource('taxes', TaxController::class)
        ->only(['index', 'show'])
        ->middleware('permission:taxes.view');

    Route::resource('taxes', TaxController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:taxes.edit');

    Route::resource('taxes', TaxController::class)
        ->only(['destroy'])
        ->middleware('permission:taxes.delete');

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */
    Route::resource('categories', CategoryController::class)
        ->only(['create', 'store'])
        ->middleware('permission:categories.create');

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'show'])
        ->middleware('permission:categories.view');

    Route::resource('categories', CategoryController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:categories.edit');

    Route::resource('categories', CategoryController::class)
        ->only(['destroy'])
        ->middleware('permission:categories.delete');

    /*
    |--------------------------------------------------------------------------
    | Brands
    |--------------------------------------------------------------------------
    */
    Route::resource('brands', BrandController::class)
        ->only(['create', 'store'])
        ->middleware('permission:brands.create');

    Route::resource('brands', BrandController::class)
        ->only(['index', 'show'])
        ->middleware('permission:brands.view');

    Route::resource('brands', BrandController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:brands.edit');

    Route::resource('brands', BrandController::class)
        ->only(['destroy'])
        ->middleware('permission:brands.delete');

    /*
    |--------------------------------------------------------------------------
    | Counters
    |--------------------------------------------------------------------------
    */
    Route::resource('counters', CounterController::class)
        ->only(['create', 'store'])
        ->middleware('permission:counters.create');

    Route::resource('counters', CounterController::class)
        ->only(['index', 'show'])
        ->middleware('permission:counters.view');

    Route::resource('counters', CounterController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:counters.edit');

    Route::resource('counters', CounterController::class)
        ->only(['destroy'])
        ->middleware('permission:counters.delete');

    Route::post(
        '/counters/{counter}/branch-mapping',
        [CounterController::class, 'saveBranchMapping']
    )->name('counters.branch.mapping')->middleware('permission:counters.edit');

    /*
    |--------------------------------------------------------------------------
    | Menus
    |--------------------------------------------------------------------------
    */
    Route::resource('menus', MenuController::class)
        ->only(['create', 'store'])
        ->middleware('permission:menus.create');

    Route::resource('menus', MenuController::class)
        ->only(['index'])
        ->middleware('permission:menus.view');

    Route::resource('menus', MenuController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:menus.edit');

    Route::resource('menus', MenuController::class)
        ->only(['destroy'])
        ->middleware('permission:menus.delete');

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::post('products/filter', [ProductController::class, 'index'])
        ->name('products.filter')
        ->middleware('permission:products.view');

    Route::resource('products', ProductController::class)
        ->only(['create', 'store'])
        ->middleware('permission:products.create');

    Route::resource('products', ProductController::class)
        ->only(['index', 'show'])
        ->middleware('permission:products.view');

    Route::resource('products', ProductController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:products.edit');

    Route::resource('products', ProductController::class)
        ->only(['destroy'])
        ->middleware('permission:products.delete');

    /*
    |--------------------------------------------------------------------------
    | Sizes
    |--------------------------------------------------------------------------
    */
    Route::resource('sizes', SizeController::class)
        ->only(['create', 'store'])
        ->middleware('permission:sizes.create');

    Route::resource('sizes', SizeController::class)
        ->only(['index', 'show'])
        ->middleware('permission:sizes.view');

    Route::resource('sizes', SizeController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:sizes.edit');

    Route::resource('sizes', SizeController::class)
        ->only(['destroy'])
        ->middleware('permission:sizes.delete');

    /*
    |--------------------------------------------------------------------------
    | Sub Products
    |--------------------------------------------------------------------------
    */
    Route::resource('sub-products', SubProductController::class)
        ->only(['create', 'store'])
        ->middleware('permission:sub-products.create');

    Route::resource('sub-products', SubProductController::class)
        ->only(['index', 'show'])
        ->middleware('permission:sub-products.view');

    Route::resource('sub-products', SubProductController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:sub-products.edit');

    Route::resource('sub-products', SubProductController::class)
        ->only(['destroy'])
        ->middleware('permission:sub-products.delete');

    /*
    |--------------------------------------------------------------------------
    | Inventory Bulk Inward & Item Allocation
    |--------------------------------------------------------------------------
    */
    Route::match(['get', 'post'], 'inventory/item-allocation', [ItemAllocationController::class, 'index'])
        ->name('item-allocation.index')
        ->middleware('permission:stock-inwards.view');

    Route::match(['get', 'post'], 'inventory/item-allocation/filter', [ItemAllocationController::class, 'index'])
        ->name('item-allocation.filter')
        ->middleware('permission:stock-inwards.view');

    Route::match(['get', 'post'], 'inventory/available-stock', [AvailableStockController::class, 'index'])
        ->name('available-stock.index')
        ->middleware('permission:available-stock.view');

    Route::match(['get', 'post'], 'inventory/available-stock/filter', [AvailableStockController::class, 'index'])
        ->name('available-stock.filter')
        ->middleware('permission:available-stock.view');

    Route::get('inventory/stock-inwards/items/{stockInwardItem}/pending-info', [ItemAllocationController::class, 'pendingInfo'])
        ->name('stock-inwards.items.pending-info')
        ->middleware('permission:stock-inwards.view');

    Route::post('inventory/stock-inwards/allocate', [ItemAllocationController::class, 'store'])
        ->name('stock-inwards.allocate')
        ->middleware('permission:stock-inwards.edit');
    Route::resource('inventory/stock-inwards', StockInwardController::class)
        ->only(['create', 'store'])
        ->names('stock-inwards')
        ->middleware('permission:stock-inwards.create');

    Route::post('inventory/stock-inwards/filter', [StockInwardController::class, 'index'])
        ->name('stock-inwards.filter')
        ->middleware('permission:stock-inwards.view');

    Route::get('inventory/stock-inwards/{stockInward}/print', [StockInwardController::class, 'print'])
        ->name('stock-inwards.print')
        ->middleware('permission:stock-inwards.view');

    Route::resource('inventory/stock-inwards', StockInwardController::class)
        ->only(['index', 'show'])
        ->names('stock-inwards')
        ->middleware('permission:stock-inwards.view');

    Route::resource('inventory/stock-inwards', StockInwardController::class)
        ->only(['edit', 'update'])
        ->names('stock-inwards')
        ->middleware('permission:stock-inwards.edit');

    Route::resource('inventory/stock-inwards', StockInwardController::class)
        ->only(['destroy'])
        ->names('stock-inwards')
        ->middleware('permission:stock-inwards.delete');

    /*
    |--------------------------------------------------------------------------
    | Inventory Stock Transfer
    |--------------------------------------------------------------------------
    */
    Route::post('inventory/stock-transfers/filter', [StockTransferController::class, 'index'])
        ->name('stock-transfers.filter')
        ->middleware('permission:stock-transfer.view');

    Route::post('inventory/stock-transfers/{stockTransfer}/dispatch', [StockTransferController::class, 'dispatch'])
        ->name('stock-transfers.dispatch')
        ->middleware('permission:stock-transfer.dispatch');

    Route::get('inventory/stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receiveForm'])
        ->name('stock-transfers.receive-form')
        ->middleware('permission:stock-transfer.receive');

    Route::post('inventory/stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receive'])
        ->name('stock-transfers.receive')
        ->middleware('permission:stock-transfer.receive');

    Route::post('inventory/stock-transfers/{stockTransfer}/cancel', [StockTransferController::class, 'cancel'])
        ->name('stock-transfers.cancel')
        ->middleware('permission:stock-transfer.cancel');

    Route::get('inventory/stock-transfers/{stockTransfer}/print', [StockTransferController::class, 'print'])
        ->name('stock-transfers.print')
        ->middleware('permission:stock-transfer.view');

    Route::resource('inventory/stock-transfers', StockTransferController::class)
        ->names('stock-transfers');

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */
    Route::post('customers/filter', [CustomerController::class, 'index'])
        ->name('customers.filter')
        ->middleware('permission:customers.view');

    Route::resource('customers', CustomerController::class)
        ->only(['create', 'store'])
        ->middleware('permission:customers.create');

    Route::resource('customers', CustomerController::class)
        ->only(['index', 'show'])
        ->middleware('permission:customers.view');

    Route::resource('customers', CustomerController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:customers.edit');

    Route::resource('customers', CustomerController::class)
        ->only(['destroy'])
        ->middleware('permission:customers.delete');

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */
    Route::post('suppliers/filter', [SupplierController::class, 'index'])
        ->name('suppliers.filter')
        ->middleware('permission:suppliers.view');

    Route::resource('suppliers', SupplierController::class)
        ->only(['create', 'store'])
        ->middleware('permission:suppliers.create');

    Route::resource('suppliers', SupplierController::class)
        ->only(['index', 'show'])
        ->middleware('permission:suppliers.view');

    Route::resource('suppliers', SupplierController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:suppliers.edit');

    Route::resource('suppliers', SupplierController::class)
        ->only(['destroy'])
        ->middleware('permission:suppliers.delete');

    /*
    |--------------------------------------------------------------------------
    | Location Cascading Endpoints
    |--------------------------------------------------------------------------
    */
    Route::get('/locations/states/{country}', [LocationController::class, 'getStates'])
        ->name('locations.states');

    Route::get('/locations/cities/{state}', [LocationController::class, 'getCities'])
        ->name('locations.cities');

    /*
    |--------------------------------------------------------------------------
    | General Settings Master
    |--------------------------------------------------------------------------
    */
    Route::post('settings/filter', [SettingController::class, 'index'])
        ->name('settings.filter')
        ->middleware('permission:settings.view');

    Route::resource('settings', SettingController::class)
        ->only(['create', 'store'])
        ->middleware('permission:settings.create');

    Route::resource('settings', SettingController::class)
        ->only(['index', 'show'])
        ->middleware('permission:settings.view');

    Route::resource('settings', SettingController::class)
        ->only(['edit', 'update'])
        ->middleware('permission:settings.edit');

    Route::resource('settings', SettingController::class)
        ->only(['destroy'])
        ->middleware('permission:settings.delete');

    /*
    |--------------------------------------------------------------------------
    | Quotations Module
    |--------------------------------------------------------------------------
    */
    Route::post('quotations/filter', [QuotationController::class, 'index'])
        ->name('quotations.filter')
        ->middleware('permission:quotation.view');

    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])
        ->name('quotations.pdf')
        ->middleware('permission:quotation.print');

    Route::get('quotations', [QuotationController::class, 'index'])
        ->name('quotations.index')
        ->middleware('permission:quotation.view');

    Route::get('quotations/create', [QuotationController::class, 'create'])
        ->name('quotations.create')
        ->middleware('permission:quotation.create');

    Route::get('quotations/search-customers', [QuotationController::class, 'searchCustomers'])
        ->name('quotations.search-customers')
        ->middleware('permission:quotation.create');

    Route::post('quotations', [QuotationController::class, 'store'])
        ->name('quotations.store')
        ->middleware('permission:quotation.create');

    // Route::get('quotations/{quotation}', [QuotationController::class, 'show'])
    //     ->name('quotations.show')
    //     ->middleware('permission:quotation.view');

    Route::get('quotations/{quotation}/edit', [QuotationController::class, 'edit'])
        ->name('quotations.edit')
        ->middleware('permission:quotation.edit');

    Route::match(['put', 'patch'], 'quotations/{quotation}', [QuotationController::class, 'update'])
        ->name('quotations.update')
        ->middleware('permission:quotation.edit');

    /*
    |--------------------------------------------------------------------------
    | Sales Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index')->middleware('permission:sales.view');
        Route::get('/quotation/{quotation}/create', [SalesController::class, 'createFromQuotation'])->name('createFromQuotation')->middleware('permission:sales.create');
        Route::post('/quotation/{quotation}/convert', [SalesController::class, 'convert'])->name('convert')->middleware('permission:sales.create');
        Route::get('/{sale}', [SalesController::class, 'show'])->name('show')->middleware('permission:sales.view');
        Route::get('/{sale}/print', [SalesController::class, 'print'])->name('print')->middleware('permission:sales.print');
        Route::get('/{sale}/invoice/print', [SalesController::class, 'print'])->name('invoice.print')->middleware('permission:sales.print');
        Route::get('/{sale}/invoice/pdf', [SalesController::class, 'downloadPdf'])->name('invoice.pdf')->middleware('permission:sales.print');
        Route::post('/{sale}/cancel', [SalesController::class, 'cancel'])->name('cancel')->middleware('permission:sales.cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | Customer Receivables Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('receivables')->name('receivables.')->group(function () {
        Route::get('/', [ReceivableController::class, 'index'])->name('index')->middleware('permission:receivable.view');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::match(['get', 'post'], 'inventory', [StockRegisterController::class, 'index'])
            ->name('inventory.index')->middleware('permission:reports.inventory');
        Route::post('inventory/search', [StockRegisterController::class, 'search'])
            ->name('inventory.search')->middleware('permission:reports.inventory');

        Route::match(['get', 'post'], 'allocated-item-history', [StockRegisterController::class, 'allocatedItemHistory'])
            ->name('allocated-item-history.index')->middleware('permission:reports.allocated-item-history');
        Route::post('allocated-item-history/search', [StockRegisterController::class, 'searchAllocatedItemHistory'])
            ->name('allocated-item-history.search')->middleware('permission:reports.allocated-item-history');

        Route::match(['get', 'post'], 'sales', [SalesReportController::class, 'index'])
            ->name('sales.index')->middleware('permission:reports.sales');
        Route::post('sales/search', [SalesReportController::class, 'search'])
            ->name('sales.search')->middleware('permission:reports.sales');

        Route::match(['get', 'post'], 'purchase', [PurchaseReportController::class, 'index'])
            ->name('purchase.index')->middleware('permission:reports.purchase');

        Route::match(['get', 'post'], 'customer', [CustomerReportController::class, 'index'])
            ->name('customer.index')->middleware('permission:reports.customer');
        Route::post('customer/search', [CustomerReportController::class, 'search'])
            ->name('customer.search')->middleware('permission:reports.customer');
        Route::get('customer/{customer}/sales-modal', [CustomerReportController::class, 'salesModal'])
            ->name('customer.sales-modal')->middleware('permission:reports.customer');

        Route::match(['get', 'post'], 'supplier', [SupplierReportController::class, 'index'])
            ->name('supplier.index')->middleware('permission:reports.supplier');
        Route::post('supplier/search', [SupplierReportController::class, 'search'])
            ->name('supplier.search')->middleware('permission:reports.supplier');
        Route::get('supplier/{supplier}/inwards-modal', [SupplierReportController::class, 'inwardsModal'])
            ->name('supplier.inwards-modal')->middleware('permission:reports.supplier');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';