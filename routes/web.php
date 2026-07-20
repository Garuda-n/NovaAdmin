<?php

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
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware('permission:dashboard.view');

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
    | Inventory Bulk Inward
    |--------------------------------------------------------------------------
    */
    Route::resource('inventory/stock-inwards', StockInwardController::class)
        ->only(['create', 'store'])
        ->names('stock-inwards')
        ->middleware('permission:stock-inwards.create');

    Route::post('inventory/stock-inwards/filter', [StockInwardController::class, 'index'])
        ->name('stock-inwards.filter')
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
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';