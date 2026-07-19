<?php

use App\Http\Controllers\Api\CustomerApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('/customers', [CustomerApiController::class, 'index']);
    Route::post('/customers', [CustomerApiController::class, 'store']);
    Route::get('/customers/{customer}', [CustomerApiController::class, 'show']);
    Route::put('/customers/{customer}', [CustomerApiController::class, 'update']);
});
