# Tripay H2H Backpack Integration

This package includes optional Backpack Laravel admin panel integration for viewing Tripay prepaid and postpaid products in a readonly interface.

## Requirements

- Laravel Backpack CRUD package
- Laravel ^10.0|^11.0|^12.0
- PHP ^8.1|^8.2|^8.3

## Installation

### 1. Install Backpack CRUD

First, install Laravel Backpack CRUD if you haven't already:

```bash
composer require backpack/crud
php artisan backpack:install
```

### 2. Register Backpack Service Provider

Add the Tripay Backpack service provider to your `config/app.php` file:

```php
'providers' => [
    // ... other providers
    Dwedaz\TripayH2H\Providers\TripayBackpackServiceProvider::class,
],
```

Or if you're using Laravel auto-discovery, the service provider will be automatically registered when Backpack is detected.

### 3. Run Migrations

Make sure you have run the Tripay H2H migrations:

```bash
php artisan migrate
```

### 4. Sync Data

Sync your Tripay data using the provided command:

```bash
php artisan tripay:sync
```

## Features

### Readonly Views

The Backpack integration provides readonly views for:

- **Prepaid Products** (`/admin/tripay-prepaid-products`)
- **Postpaid Products** (`/admin/tripay-postpaid-products`)

### Available Columns

#### Prepaid Products
- ID
- Code
- Product Name
- Price (formatted in Rupiah)
- Description
- Operator Name
- Category Name
- Status (Available/Unavailable)
- Created At
- Updated At

#### Postpaid Products
- ID
- Code
- Product Name
- Admin Fee (formatted in Rupiah)
- Operator Name
- Category Name
- Status (Available/Unavailable)
- Created At
- Updated At

### Filters

Both views include filters for:
- Operator
- Category
- Status

Postpaid products also include an additional filter for:
- Admin Fee (With/Without Admin Fee)

### Security

The controllers are configured as readonly, which means:
- ✅ List operation (view all products)
- ✅ Show operation (view individual product details)
- ❌ Create operation (disabled)
- ❌ Update operation (disabled)
- ❌ Delete operation (disabled)

## Accessing the Views

Once installed and configured, you can access the views through your Backpack admin panel:

1. Login to your Backpack admin panel (usually `/admin`)
2. Navigate to:
   - **Tripay Prepaid Products**: `/admin/tripay-prepaid-products`
   - **Tripay Postpaid Products**: `/admin/tripay-postpaid-products`

## Customization

### Adding to Sidebar

To add the Tripay views to your Backpack sidebar, add the following to your `resources/views/vendor/backpack/base/inc/sidebar_content.blade.php`:

```php
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-credit-card"></i> Tripay H2H</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('tripay-prepaid-products') }}">
                <i class="nav-icon la la-mobile"></i> Prepaid Products
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('tripay-postpaid-products') }}">
                <i class="nav-icon la la-file-invoice"></i> Postpaid Products
            </a>
        </li>
    </ul>
</li>
```

### Extending Controllers

If you need to customize the controllers, you can extend them in your application:

```php
<?php

namespace App\Http\Controllers\Admin;

use Dwedaz\TripayH2H\Http\Controllers\Admin\TripayPrepaidProductCrudController as BaseTripayPrepaidProductCrudController;

class TripayPrepaidProductCrudController extends BaseTripayPrepaidProductCrudController
{
    protected function setupListOperation()
    {
        parent::setupListOperation();
        
        // Add your custom columns or modifications here
    }
}
```

## Troubleshooting

### Routes Not Working

If the routes are not working, make sure:

1. Backpack is properly installed and configured
2. The `TripayBackpackServiceProvider` is registered
3. You have run `php artisan route:cache` if using route caching

### No Data Showing

If no data is showing in the views:

1. Make sure you have run the migrations: `php artisan migrate`
2. Sync the data: `php artisan tripay:sync`
3. Check that your Tripay API credentials are correctly configured

### Permission Issues

The views respect Backpack's authentication and authorization. Make sure:

1. You are logged in to the Backpack admin panel
2. Your user has the necessary permissions to access the routes
3. Check your Backpack middleware configuration

## Support

For issues related to the Backpack integration, please check:

1. [Laravel Backpack Documentation](https://backpackforlaravel.com/docs/)
2. [Tripay H2H Package Issues](https://github.com/your-repo/issues)

## License

This Backpack integration follows the same MIT license as the main Tripay H2H package.