<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Dwedaz\TripayH2H\Models\TripayPrepaidProduct;
use Dwedaz\TripayH2H\Models\TripayPrepaidOperator;
use Dwedaz\TripayH2H\Models\TripayPrepaidCategory;

/**
 * Tripay Prepaid Product CRUD Controller (Readonly)
 * 
 * This controller provides readonly access to Tripay prepaid products
 * through the Backpack admin interface.
 * 
 * Note: This controller requires Backpack CRUD to be installed.
 * Install with: composer require backpack/crud
 */
class TripayPrepaidProductCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Setup the CRUD controller
     */
    public function setup(): void
    {
        CRUD::setModel(TripayPrepaidProduct::class);
        CRUD::setRoute('admin/tripay/prepaid-products');
        CRUD::setEntityNameStrings('prepaid product', 'prepaid products');
        
        // Disable create, update, delete operations (readonly)
        CRUD::denyAccess(['create', 'update', 'delete']);
    }

    /**
     * Define what happens when the List operation is loaded.
     */
    protected function setupListOperation(): void
    {
        // Add eager loading for relationships
        CRUD::addClause('with', ['operator', 'category']);

        // Define columns for the list view
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Product Name',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'operator',
            'label' => 'Operator',
            'type' => 'closure',
            'function' => function($entry) {
                return ($entry && $entry->operator && isset($entry->operator->name)) ? $entry->operator->name : '-';
            }
        ]);

        CRUD::addColumn([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'closure',
            'function' => function($entry) {
                return ($entry && $entry->category && isset($entry->category->name)) ? $entry->category->name : '-';
            }
        ]);

        CRUD::addColumn([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'text',
        ]);

        // Add filters with null checks
        try {
            $operators = TripayPrepaidOperator::all();
            if ($operators && $operators->count() > 0) {
                CRUD::addFilter([
                    'name' => 'operator_id',
                    'type' => 'dropdown',
                    'label' => 'Operator'
                ], $operators->pluck('name', 'id')->toArray(), function($value) {
                    CRUD::addClause('where', 'operator_id', $value);
                });
            }
        } catch (\Exception $e) {
            // Skip operator filter if there's an error
        }

        try {
            $categories = TripayPrepaidCategory::all();
            if ($categories && $categories->count() > 0) {
                CRUD::addFilter([
                    'name' => 'category_id',
                    'type' => 'dropdown',
                    'label' => 'Category'
                ], $categories->pluck('name', 'id')->toArray(), function($value) {
                    CRUD::addClause('where', 'category_id', $value);
                });
            }
        } catch (\Exception $e) {
            // Skip category filter if there's an error
        }

        CRUD::addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], [
            'active' => 'Active',
            'inactive' => 'Inactive'
        ], function($value) {
            CRUD::addClause('where', 'status', $value);
        });
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    protected function setupShowOperation(): void
    {
        // Add eager loading for relationships
        CRUD::addClause('with', ['operator', 'category']);

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
            'name' => 'name',
            'label' => 'Product Name',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'closure',
            'function' => function ($entry) {
                $price = ($entry && isset($entry->price)) ? $entry->price : 0;
                return 'Rp ' . number_format($price, 0, ',', '.');
            },
        ]);

        CRUD::addColumn([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'text',
        ]);

        CRUD::addColumn([
            'name' => 'operator',
            'label' => 'Operator',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry && $entry->operator && isset($entry->operator->name)) ? $entry->operator->name : 'N/A';
            },
        ]);

        CRUD::addColumn([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry && $entry->category && isset($entry->category->name)) ? $entry->category->name : 'N/A';
            },
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'closure',
            'function' => function ($entry) {
                $status = ($entry && isset($entry->status)) ? $entry->status : false;
                return $status ? 'Available' : 'Unavailable';
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