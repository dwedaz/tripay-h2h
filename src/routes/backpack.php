<?php

use Illuminate\Support\Facades\Route;
use Dwedaz\TripayH2H\Http\Controllers\Admin\TripayPrepaidProductCrudController;
use Dwedaz\TripayH2H\Http\Controllers\Admin\TripayPostpaidProductCrudController;

/*
|--------------------------------------------------------------------------
| Backpack Routes
|--------------------------------------------------------------------------
|
| This file contains routes for Backpack CRUD controllers.
| These routes provide read-only access to Tripay products.
|
*/

// Only register routes if Backpack is installed
if (class_exists('Backpack\CRUD\BackpackServiceProvider')) {
    Route::group([
        'prefix' => config('backpack.base.route_prefix', 'admin'),
        'middleware' => array_merge(
            (array) config('backpack.base.web_middleware', 'web'),
            (array) config('backpack.base.middleware_key', 'admin')
        ),
        'namespace' => 'Dwedaz\TripayH2H\Http\Controllers\Admin',
    ], function () {
        // Tripay Prepaid Products - Read Only CRUD
        Route::crud('tripay/prepaid-products', 'TripayPrepaidProductCrudController');

        // Tripay Postpaid Products - Read Only CRUD
        Route::crud('tripay/postpaid-products', 'TripayPostpaidProductCrudController');
    });
}