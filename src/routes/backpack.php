<?php

use Illuminate\Support\Facades\Route;
use Tripay\H2H\Http\Controllers\Admin\TripayPrepaidProductCrudController;
use Tripay\H2H\Http\Controllers\Admin\TripayPostpaidProductCrudController;

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
if (class_exists('Backpack\CRUD\CrudServiceProvider')) {
    Route::group([
        'prefix' => config('backpack.base.route_prefix', 'admin'),
        'middleware' => array_merge(
            (array) config('backpack.base.web_middleware', 'web'),
            (array) config('backpack.base.middleware_key', 'admin')
        ),
        'namespace' => 'Tripay\H2H\Http\Controllers\Admin',
    ], function () {
        // Tripay Prepaid Products - Read Only
        Route::get('tripay/prepaid-products', [TripayPrepaidProductCrudController::class, 'index'])
            ->name('tripay.prepaid-products.index');
        Route::get('tripay/prepaid-products/{id}', [TripayPrepaidProductCrudController::class, 'show'])
            ->name('tripay.prepaid-products.show');

        // Tripay Postpaid Products - Read Only
        Route::get('tripay/postpaid-products', [TripayPostpaidProductCrudController::class, 'index'])
            ->name('tripay.postpaid-products.index');
        Route::get('tripay/postpaid-products/{id}', [TripayPostpaidProductCrudController::class, 'show'])
            ->name('tripay.postpaid-products.show');
    });
}