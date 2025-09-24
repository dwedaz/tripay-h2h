<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Providers;

use Illuminate\Support\ServiceProvider;
use Dwedaz\TripayH2H\Services\TripayService;
use Dwedaz\TripayH2H\Console\Commands\TripaySync;
use Dwedaz\TripayH2H\Services\TripayServerService;
use Dwedaz\TripayH2H\Services\TripayBalanceService;
use Dwedaz\TripayH2H\Services\TripayPrepaidService;
use Dwedaz\TripayH2H\Services\TripayPostpaidService;
use Dwedaz\TripayH2H\Contracts\TripayServerInterface;
use Dwedaz\TripayH2H\Contracts\TripayBalanceInterface;
use Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface;
use Dwedaz\TripayH2H\Contracts\TripayPostpaidInterface;
use Illuminate\Http\Client\Factory as HttpClientFactory;

class TripayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/tripay.php',
            'tripay'
        );

        // Register TripayServerService
        $this->app->bind(TripayServerInterface::class, function ($app) {
            return new TripayServerService(
                httpClient: $app->make(HttpClientFactory::class),
                apiKey: config('tripay.api_key', ''),
                isSandbox: config('tripay.is_sandbox', true)
            );
        });

        // Register TripayBalanceService
        $this->app->bind(TripayBalanceInterface::class, function ($app) {
            return new TripayBalanceService(
                httpClient: $app->make(HttpClientFactory::class),
                apiKey: config('tripay.api_key', ''),
                isSandbox: config('tripay.is_sandbox', true)
            );
        });

        // Register TripayPrepaidService
        $this->app->bind(TripayPrepaidInterface::class, function ($app) {
            return new TripayPrepaidService(
                httpClient: $app->make(HttpClientFactory::class),
                apiKey: config('tripay.api_key', ''),
                isSandbox: config('tripay.is_sandbox', true)
            );
        });

        // Register TripayPostpaidService
        $this->app->bind(TripayPostpaidInterface::class, function ($app) {
            return new TripayPostpaidService(
                httpClient: $app->make(HttpClientFactory::class),
                apiKey: config('tripay.api_key', ''),
                isSandbox: config('tripay.is_sandbox', true)
            );
        });

        // Register singleton for TripayServerService
        $this->app->singleton('tripay.server', function ($app) {
            return $app->make(TripayServerInterface::class);
        });

        // Register singleton for TripayBalanceService
        $this->app->singleton('tripay.balance', function ($app) {
            return $app->make(TripayBalanceInterface::class);
        });

        // Register singleton for TripayPrepaidService
        $this->app->singleton('tripay.prepaid', function ($app) {
            return $app->make(TripayPrepaidInterface::class);
        });

        // Register singleton for TripayPostpaidService
        $this->app->singleton('tripay.postpaid', function ($app) {
            return $app->make(TripayPostpaidInterface::class);
        });

        // Register main Tripay service
        $this->app->singleton('tripay', function ($app) {
            return new TripayService(
                $app->make(TripayServerInterface::class),
                $app->make(TripayBalanceInterface::class),
                $app->make(TripayPrepaidInterface::class),
                $app->make(TripayPostpaidInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if (class_exists('Backpack\CRUD\app\Http\Controllers\CrudController')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/backpack.php');
        }

        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/tripay.php' => config_path('tripay.php'),
            ], 'tripay-config');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'tripay-migrations');

            // Publish all files
            $this->publishes([
                __DIR__ . '/../../config/tripay.php' => config_path('tripay.php'),
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'tripay');

            
            // Register console commands
            $this->commands([
                TripaySync::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            TripayServerInterface::class,
            TripayBalanceInterface::class,
            TripayPrepaidInterface::class,
            TripayPostpaidInterface::class,
            'tripay.server',
            'tripay.balance',
            'tripay.prepaid',
            'tripay.postpaid',
            'tripay',
        ];
    }
}