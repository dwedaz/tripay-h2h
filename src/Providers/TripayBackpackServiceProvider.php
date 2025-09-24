<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Tripay Backpack Service Provider
 * 
 * This service provider registers Backpack routes and controllers
 * for the Tripay H2H package when Backpack is available.
 */
class TripayBackpackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register services if needed
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only load routes if Backpack is installed
        if (class_exists('Backpack\CRUD\CrudServiceProvider')) {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/backpack.php');
        }
    }
}