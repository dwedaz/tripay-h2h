<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dwedaz\TripayH2H\Facades\Tripay;
use Illuminate\Http\Client\Factory as HttpClientFactory;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Dotenv\Dotenv;

echo "TRIPAY H2H API TEST\n";
echo "==================\n\n";

// Load .env file if exists
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $envApiKey = $_ENV['TRIPAY_API_KEY'] ?? getenv('TRIPAY_API_KEY') ?? '';
} else {
    $envApiKey = '';
}

// Configuration - use .env values as defaults
$apiKey = empty($envApiKey) 
    ? readline("Enter your Tripay API Key: ")
    : ($envApiKey === 'your_api_key_here' 
        ? readline("Enter your Tripay API Key: ")
        : $envApiKey);

// Hardcode sandbox mode to false (production)
$isSandbox = false;

if (empty($apiKey)) {
    echo "API Key is required!\n";
    exit(1);
}

// Setup Laravel Container and Facade for standalone usage
$container = new Container();
$httpClient = new HttpClientFactory();
$container->instance('http.client', $httpClient);

// Register Tripay service
$container->singleton('tripay', function () use ($apiKey, $isSandbox, $httpClient) {
    return new \Dwedaz\TripayH2H\Services\TripayService(
        new \Dwedaz\TripayH2H\Services\TripayServerService(
            $httpClient,
            $apiKey,
            $isSandbox
        ),
        new \Dwedaz\TripayH2H\Services\TripayBalanceService(
            $httpClient,
            $apiKey,
            $isSandbox
        ),
        new \Dwedaz\TripayH2H\Services\TripayPrepaidService(
            $httpClient,
            $apiKey,
            $isSandbox
        ),
        new \Dwedaz\TripayH2H\Services\TripayPostpaidService(
            $httpClient,
            $apiKey,
            $isSandbox
        )
    );
});

// Setup Facade
Facade::setFacadeApplication($container);
Container::setInstance($container);

// Register the Facade accessor
Tripay::swap($container->make('tripay'));

$products = Tripay::postpaid()->getProducts();
$operators = Tripay::postpaid()->getProductDetail('WATAPIN');
// print_r($products->toArray());
print_r($operators->toArray());
