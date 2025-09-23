<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Providers;

use Dwedaz\TripayH2H\Contracts\TripayServerInterface;
use Dwedaz\TripayH2H\Contracts\TripayBalanceInterface;
use Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface;
use Dwedaz\TripayH2H\Services\TripayServerService;
use Dwedaz\TripayH2H\Services\TripayBalanceService;
use Dwedaz\TripayH2H\Services\TripayPrepaidService;
use Dwedaz\TripayH2H\Services\TripayService;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Support\ServiceProvider;

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
                isSandbox: config('tripay.sandbox', true)
            );
        });

        // Register TripayBalanceService
        $this->app->bind(TripayBalanceInterface::class, function ($app) {
            return new TripayBalanceService(
                httpClient: $app->make(HttpClientFactory::class),
                apiKey: config('tripay.api_key', ''),
                isSandbox: config('tripay.sandbox', true)
            );
        });

        // Register TripayPrepaidService
        $this->app->bind(TripayPrepaidInterface::class, function ($app) {
            return new TripayPrepaidService(
                httpClient: $app->make(HttpClientFactory::class),
                apiKey: config('tripay.api_key', ''),
                isSandbox: config('tripay.sandbox', true)
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

        // Register unified TripayService for facade
        $this->app->singleton('tripay', function ($app) {
            return new TripayService(
                serverService: $app->make(TripayServerInterface::class),
                balanceService: $app->make(TripayBalanceInterface::class),
                prepaidService: $app->make(TripayPrepaidInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/tripay.php' => config_path('tripay.php'),
            ], 'tripay-config');

            // Publish all package assets
            $this->publishes([
                __DIR__ . '/../../config/tripay.php' => config_path('tripay.php'),
            ], 'tripay');
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
            'tripay.server',
            'tripay.balance',
            'tripay.prepaid',
            'tripay',
        ];
    }
}