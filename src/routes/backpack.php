<?php

use Illuminate\Support\Facades\Route;
use Dwedaz\TripayH2H\Http\Controllers\Admin\TripayPrepaidProductCrudController;
use Dwedaz\TripayH2H\Http\Controllers\Admin\TripayPostpaidProductCrudController;

/*
|--------------------------------------------------------------------------
| Tripay H2H Backpack Admin Routes
|--------------------------------------------------------------------------
|
| Here are the admin routes for Tripay H2H package using Backpack CRUD.
| These routes provide readonly views for prepaid and postpaid products.
|
*/

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'Dwedaz\TripayH2H\Http\Controllers\Admin',
], function () {
    
    // Tripay Prepaid Products CRUD routes
    Route::crud('tripay-prepaid-products', TripayPrepaidProductCrudController::class);
    
    // Tripay Postpaid Products CRUD routes  
    Route::crud('tripay-postpaid-products', TripayPostpaidProductCrudController::class);
    
});