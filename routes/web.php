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
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';