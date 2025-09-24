<?php

require_once 'vendor/autoload.php';

use Dwedaz\TripayH2H\Providers\TripayServiceProvider;
use Dwedaz\TripayH2H\Facades\Tripay;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

echo "=== Testing Laravel Integration ===\n\n";

try {
    // Create a container instance
    $container = new Container();
    
    // Set the container as the global instance
    Container::setInstance($container);
    
    // Set the facade application
    Facade::setFacadeApplication($container);
    
    // Mock config service
    $container->singleton('config', function () {
        return new class {
            private $config = [
                'tripay' => [
                    'api_key' => 'test_key',
                    'is_sandbox' => true,
                    'base_urls' => [
                        'sandbox' => 'https://tripay.id/api-sandbox/v2',
                        'production' => 'https://tripay.id/api/v2',
                    ],
                    'http' => [
                        'timeout' => 30,
                        'retry_times' => 3,
                        'retry_sleep' => 100,
                    ],
                    'logging' => [
                        'enabled' => false,
                        'channel' => 'daily',
                    ],
                    'cache' => [
                        'enabled' => true,
                        'ttl' => 300,
                        'prefix' => 'tripay_',
                    ],
                ]
            ];
            
            public function get($key, $default = null) {
                $keys = explode('.', $key);
                $value = $this->config;
                
                foreach ($keys as $k) {
                    if (!isset($value[$k])) {
                        return $default;
                    }
                    $value = $value[$k];
                }
                
                return $value;
            }
            
            public function set($key, $value) {
                $this->config[$key] = $value;
            }
        };
    });
    
    // Register the service provider
    $serviceProvider = new TripayServiceProvider($container);
    
    echo "✓ Service Provider created successfully\n";
    
    // Register services
    $serviceProvider->register();
    echo "✓ Services registered successfully\n";
    
    // Check if services are bound
    $services = [
        'tripay',
        'tripay.server', 
        'tripay.balance',
        'tripay.prepaid',
        \Dwedaz\TripayH2H\Contracts\TripayServerInterface::class,
        \Dwedaz\TripayH2H\Contracts\TripayBalanceInterface::class,
        \Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface::class,
    ];
    
    foreach ($services as $service) {
        if ($container->bound($service)) {
            echo "✓ Service '$service' is bound\n";
        } else {
            echo "✗ Service '$service' is NOT bound\n";
        }
    }
    
    // Test facade accessor
    $reflection = new ReflectionClass(Tripay::class);
    $method = $reflection->getMethod('getFacadeAccessor');
    $method->setAccessible(true);
    $facadeAccessor = $method->invoke(null);
    echo "✓ Facade accessor: '$facadeAccessor'\n";
    
    // Test if we can resolve the main service
    if ($container->bound($facadeAccessor)) {
        $tripayService = $container->make($facadeAccessor);
        echo "✓ Main Tripay service resolved successfully\n";
        echo "  Service class: " . get_class($tripayService) . "\n";
    } else {
        echo "✗ Cannot resolve main Tripay service\n";
    }
    
    echo "\n=== Integration Test Completed Successfully ===\n";
    
} catch (Exception $e) {
    echo "✗ Error during integration test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}