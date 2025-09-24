<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Http\Controllers\Admin;

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
class TripayPrepaidProductCrudController
{
    /** @var mixed */
    protected $crud;

    /** @var array */
    protected $data = [];

    public function __construct()
    {
        // Initialize only if Backpack is available
        if (class_exists('Backpack\CRUD\app\Http\Controllers\CrudController')) {
            $this->initializeBackpack();
        }
    }

    /**
     * Initialize Backpack functionality
     */
    private function initializeBackpack(): void
    {
        if (class_exists('Backpack\CRUD\app\Library\CrudPanel\CrudPanel')) {
            $crudPanelClass = 'Backpack\CRUD\app\Library\CrudPanel\CrudPanel';
            $this->crud = new $crudPanelClass();
            $this->setup();
        }
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     */
    public function setup(): void
    {
        if (!$this->crud || !method_exists($this->crud, 'setModel')) {
            return;
        }

        $this->crud->setModel(TripayPrepaidProduct::class);
        $this->crud->setRoute(config('backpack.base.route_prefix', 'admin') . '/tripay/prepaid-products');
        $this->crud->setEntityNameStrings('prepaid product', 'prepaid products');
        
        // Add eager loading for relationships
        if (method_exists($this->crud, 'addClause')) {
            $this->crud->addClause('with', ['operator', 'category']);
        }

        // Disable operations to make it readonly
        if (method_exists($this->crud, 'denyAccess')) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
        }

        $this->setupListOperation();
        $this->setupShowOperation();
    }

    /**
     * Define what happens when the List operation is loaded.
     */
    protected function setupListOperation(): void
    {
        if (!$this->crud || !method_exists($this->crud, 'addColumn')) {
            return;
        }

        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'code',
            'label' => 'Code',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Product Name',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'closure',
            'function' => function ($entry) {
                return 'Rp ' . number_format($entry->price ?? 0, 0, ',', '.');
            },
        ]);

        $this->crud->addColumn([
            'name' => 'operator_name',
            'label' => 'Operator',
            'type' => 'closure',
            'function' => function ($entry) {
                return $entry->operator ? $entry->operator->name : 'N/A';
            },
        ]);

        $this->crud->addColumn([
            'name' => 'category_name',
            'label' => 'Category',
            'type' => 'closure',
            'function' => function ($entry) {
                return $entry->category ? $entry->category->name : 'N/A';
            },
        ]);

        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry->status ?? false) ? 'Available' : 'Unavailable';
            },
        ]);

        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Created At',
            'type' => 'datetime',
        ]);

        // Add filters with method existence checks
        if (method_exists($this->crud, 'addFilter')) {
            $this->crud->addFilter(
                [
                    'type' => 'dropdown',
                    'name' => 'operator_id',
                    'label' => 'Operator',
                ],
                function () {
                    try {
                        return TripayPrepaidOperator::pluck('name', 'id')->toArray();
                    } catch (\Exception $e) {
                        return [];
                    }
                },
                function ($value) {
                    if (method_exists($this->crud, 'addClause')) {
                        $this->crud->addClause('where', 'operator_id', $value);
                    }
                }
            );

            $this->crud->addFilter(
                [
                    'type' => 'dropdown',
                    'name' => 'category_id',
                    'label' => 'Category',
                ],
                function () {
                    try {
                        return TripayPrepaidCategory::pluck('name', 'id')->toArray();
                    } catch (\Exception $e) {
                        return [];
                    }
                },
                function ($value) {
                    if (method_exists($this->crud, 'addClause')) {
                        $this->crud->addClause('where', 'category_id', $value);
                    }
                }
            );

            $this->crud->addFilter(
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
                    if (method_exists($this->crud, 'addClause')) {
                        $this->crud->addClause('where', 'status', $value);
                    }
                }
            );
        }
    }

    /**
     * Define what happens when the Show operation is loaded.
     */
    protected function setupShowOperation(): void
    {
        if (!$this->crud || !method_exists($this->crud, 'addColumn')) {
            return;
        }

        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'code',
            'label' => 'Code',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Product Name',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'closure',
            'function' => function ($entry) {
                return 'Rp ' . number_format($entry->price ?? 0, 0, ',', '.');
            },
        ]);

        $this->crud->addColumn([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'textarea',
        ]);

        $this->crud->addColumn([
            'name' => 'operator_name',
            'label' => 'Operator',
            'type' => 'closure',
            'function' => function ($entry) {
                return $entry->operator ? $entry->operator->name : 'N/A';
            },
        ]);

        $this->crud->addColumn([
            'name' => 'category_name',
            'label' => 'Category',
            'type' => 'closure',
            'function' => function ($entry) {
                return $entry->category ? $entry->category->name : 'N/A';
            },
        ]);

        $this->crud->addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'closure',
            'function' => function ($entry) {
                return ($entry->status ?? false) ? 'Available' : 'Unavailable';
            },
        ]);

        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Created At',
            'type' => 'datetime',
        ]);

        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Updated At',
            'type' => 'datetime',
        ]);
    }

    /**
     * Handle list operation
     */
    public function index()
    {
        if (!class_exists('Backpack\CRUD\app\Http\Controllers\CrudController')) {
            return response()->json([
                'error' => 'Backpack CRUD is not installed. Please install it with: composer require backpack/crud'
            ], 500);
        }

        if (!$this->crud || !method_exists($this->crud, 'hasAccessOrFail')) {
            return response()->json(['error' => 'CRUD panel not properly initialized'], 500);
        }

        $this->crud->hasAccessOrFail('list');
        $this->data['crud'] = $this->crud;
        $this->data['title'] = (method_exists($this->crud, 'getTitle') ? $this->crud->getTitle() : null) 
            ?? 'List ' . ($this->crud->entity_name_plural ?? 'items');

        return view('crud::list', $this->data);
    }

    /**
     * Handle show operation
     */
    public function show($id)
    {
        if (!class_exists('Backpack\CRUD\app\Http\Controllers\CrudController')) {
            return response()->json([
                'error' => 'Backpack CRUD is not installed. Please install it with: composer require backpack/crud'
            ], 500);
        }

        if (!$this->crud || !method_exists($this->crud, 'hasAccessOrFail')) {
            return response()->json(['error' => 'CRUD panel not properly initialized'], 500);
        }

        $this->crud->hasAccessOrFail('show');
        
        if (method_exists($this->crud, 'getEntry')) {
            $this->data['entry'] = $this->crud->getEntry($id);
        }
        
        $this->data['crud'] = $this->crud;
        $this->data['title'] = 'Show ' . ($this->crud->entity_name ?? 'item');

        return view('crud::show', $this->data);
    }
}