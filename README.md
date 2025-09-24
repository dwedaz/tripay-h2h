# Tripay H2H Laravel Package

Laravel package untuk integrasi dengan Tripay H2H PPOB API. Package ini menyediakan facade yang mudah digunakan untuk mengakses layanan Tripay seperti cek server, cek saldo, dan layanan prepaid.

## Requirements

- PHP ^8.1|^8.2|^8.3
- Laravel ^10.0|^11.0|^12.0

## Installation

Install package melalui Composer:

```bash
composer require dwedaz/tripay-h2h
```

### Auto-Discovery

Package ini menggunakan Laravel auto-discovery, sehingga service provider dan facade akan otomatis terdaftar.

### Manual Registration (Opsional)

Jika auto-discovery dinonaktifkan, tambahkan service provider secara manual di `config/app.php`:

```php
'providers' => [
    // ...
    Dwedaz\TripayH2H\Providers\TripayServiceProvider::class,
],

'aliases' => [
    // ...
    'Tripay' => Dwedaz\TripayH2H\Facades\Tripay::class,
],
```

## Configuration

Publish file konfigurasi:

```bash
php artisan vendor:publish --provider="Dwedaz\TripayH2H\Providers\TripayServiceProvider" --tag="tripay-config"
```

Atau publish semua file:

```bash
php artisan vendor:publish --provider="Dwedaz\TripayH2H\Providers\TripayServiceProvider"
```

Tambahkan environment variables di file `.env`:

```env
TRIPAY_API_KEY=your_api_key_here
TRIPAY_MODE=sandbox
```

## Usage

### Menggunakan Facade

```php
use Dwedaz\TripayH2H\Facades\Tripay;

// Cek status server
$serverStatus = Tripay::checkServer();
echo $serverStatus->getMessage();

// Cek saldo
$balance = Tripay::checkBalance();
echo "Saldo: " . $balance->getBalance();

// Menggunakan service chaining
$serverService = Tripay::server();
$balanceService = Tripay::balance();
$prepaidService = Tripay::prepaid();

// Layanan Prepaid
$operators = Tripay::prepaid()->getOperators();
$categories = Tripay::prepaid()->getCategories();
$products = Tripay::prepaid()->getProducts();
```

### Menggunakan Dependency Injection

```php
use Dwedaz\TripayH2H\Services\TripayService;
use Dwedaz\TripayH2H\Contracts\TripayServerInterface;
use Dwedaz\TripayH2H\Contracts\TripayBalanceInterface;
use Dwedaz\TripayH2H\Contracts\TripayPrepaidInterface;

class YourController extends Controller
{
    public function __construct(
        private TripayService $tripayService,
        private TripayServerInterface $serverService,
        private TripayBalanceInterface $balanceService,
        private TripayPrepaidInterface $prepaidService
    ) {}

    public function checkStatus()
    {
        $status = $this->serverService->checkServer();
        return response()->json($status->toArray());
    }
}
```

### Menggunakan Service Container

```php
// Menggunakan service container
$tripayService = app('tripay');
$serverService = app(TripayServerInterface::class);
$balanceService = app(TripayBalanceInterface::class);
$prepaidService = app(TripayPrepaidInterface::class);
```

## Available Methods

### Server Service
- `checkServer()` - Cek status server Tripay

### Balance Service  
- `checkBalance()` - Cek saldo akun

### Prepaid Service
- `getOperators(?string $operatorId = null, ?string $categoryId = null)` - Ambil daftar operator
- `getCategories(?string $categoryId = null)` - Ambil daftar kategori
- `getProducts(?string $categoryId = null, ?string $operatorId = null)` - Ambil daftar produk

## Configuration Options

File konfigurasi `config/tripay.php` menyediakan opsi berikut:

```php
return [
    'api_key' => env('TRIPAY_API_KEY', ''),
    'is_sandbox' => env('TRIPAY_MODE', 'sandbox') === 'sandbox',
    'base_urls' => [
        'sandbox' => env('TRIPAY_SANDBOX_URL', 'https://tripay.id/api-sandbox/v2'),
        'production' => env('TRIPAY_PRODUCTION_URL', 'https://tripay.id/api/v2'),
    ],
    'http' => [
        'timeout' => env('TRIPAY_TIMEOUT', 30),
        'retry_times' => env('TRIPAY_RETRY_TIMES', 3),
        'retry_sleep' => env('TRIPAY_RETRY_SLEEP', 100),
    ],
    'logging' => [
        'enabled' => env('TRIPAY_LOGGING', false),
        'channel' => env('TRIPAY_LOG_CHANNEL', 'daily'),
    ],
    'cache' => [
        'enabled' => env('TRIPAY_CACHE_ENABLED', true),
        'ttl' => env('TRIPAY_CACHE_TTL', 300),
        'prefix' => env('TRIPAY_CACHE_PREFIX', 'tripay_'),
    ],
];
```

## Testing

Jalankan test dengan:

```bash
composer test
```

## License

MIT License. Lihat file [LICENSE](LICENSE) untuk detail.

## Contributing

Kontribusi sangat diterima! Silakan buat pull request atau buka issue untuk saran dan perbaikan.

## Support

Jika Anda mengalami masalah atau memiliki pertanyaan, silakan buka issue di repository ini.