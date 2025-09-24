<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Dwedaz\TripayH2H\Models\TripayPostpaidProduct;

/**
 * Tripay Postpaid Product CRUD Controller (Readonly)
 * 
 * This controller provides readonly access to Tripay postpaid products
 * through the Backpack admin interface.
 */
class TripayPostpaidProductCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     */
    public function setup(): void
    {
        CRUD::setModel(TripayPostpaidProduct::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin') . '/tripay/postpaid-products');
        CRUD::setEntityNameStrings('postpaid product', 'postpaid products');

        // Disable operations to make it readonly
        CRUD::denyAccess(['create', 'update', 'delete']);
    }

    /**
     * Define what happens when the List operation is loaded.
     */
    protected function setupListOperation(): void
    {
        CRUD::addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'code',
            'label' => 'Code',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'product_name',
            'label' => 'Product Name',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'brand',
            'label' => 'Brand',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'type',
            'label' => 'Type',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'seller_name',
            'label' => 'Seller',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry && isset($entry->price)) ? 'Rp ' . number_format($entry->price, 0, ',', '.') : 'Rp 0';
            },
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry && isset($entry->status) && $entry->status) ? 'Available' : 'Unavailable';
            },
        ]);

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Created At',
            'type' => 'datetime',
        ]);

        // Add filters with error handling
        CRUD::addFilter(
            [
                'type' => 'dropdown',
                'name' => 'category',
                'label' => 'Category',
            ],
            function () {
                try {
                    $categories = TripayPostpaidProduct::distinct()->pluck('category', 'category');
                    return $categories && $categories->count() > 0 ? $categories->toArray() : [];
                } catch (\Exception $e) {
                    return [];
                }
            },
            function ($value) {
                CRUD::addClause('where', 'category', $value);
            }
        );

        CRUD::addFilter(
            [
                'type' => 'dropdown',
                'name' => 'brand',
                'label' => 'Brand',
            ],
            function () {
                try {
                    $brands = TripayPostpaidProduct::distinct()->pluck('brand', 'brand');
                    return $brands && $brands->count() > 0 ? $brands->toArray() : [];
                } catch (\Exception $e) {
                    return [];
                }
            },
            function ($value) {
                CRUD::addClause('where', 'brand', $value);
            }
        );

        CRUD::addFilter(
            [
                'type' => 'dropdown',
                'name' => 'type',
                'label' => 'Type',
            ],
            function () {
                try {
                    $types = TripayPostpaidProduct::distinct()->pluck('type', 'type');
                    return $types && $types->count() > 0 ? $types->toArray() : [];
                } catch (\Exception $e) {
                    return [];
                }
            },
            function ($value) {
                CRUD::addClause('where', 'type', $value);
            }
        );

        CRUD::addFilter(
            [
                'type' => 'dropdown',
                'name' => 'status',
                'label' => 'Status',
            ],
            [
                1 => 'Available',
                0 => 'Unavailable',
            ],
            function ($value) {
                CRUD::addClause('where', 'status', $value);
            }
        );
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    protected function setupShowOperation(): void
    {
        CRUD::addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        CRUD::addColumn([
            'name' => 'code',
            'label' => 'Code',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'product_name',
            'label' => 'Product Name',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'brand',
            'label' => 'Brand',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'type',
            'label' => 'Type',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'seller_name',
            'label' => 'Seller',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry && isset($entry->price)) ? 'Rp ' . number_format($entry->price, 0, ',', '.') : 'Rp 0';
            },
        ]);

        CRUD::addColumn([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry && isset($entry->status) && $entry->status) ? 'Available' : 'Unavailable';
            },
        ]);

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Created At',
            'type' => 'datetime',
        ]);

        CRUD::addColumn([
            'name' => 'updated_at',
            'label' => 'Updated At',
            'type' => 'datetime',
        ]);
    }


}