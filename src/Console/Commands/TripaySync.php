<?php

declare(strict_types=1);

namespace Dwedaz\TripayH2H\Console\Commands;

use Dwedaz\TripayH2H\Facades\Tripay;
use Dwedaz\TripayH2H\Models\TripayPrepaidCategory;
use Dwedaz\TripayH2H\Models\TripayPrepaidOperator;
use Dwedaz\TripayH2H\Models\TripayPrepaidProduct;
use Dwedaz\TripayH2H\Models\TripayPostpaidCategory;
use Dwedaz\TripayH2H\Models\TripayPostpaidOperator;
use Dwedaz\TripayH2H\Models\TripayPostpaidProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class TripaySync extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tripay:sync {--force : Force sync without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Sync Tripay data (categories, operators, products) for both prepaid and postpaid services';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Tripay data synchronization...');

        if (!$this->option('force') && !$this->confirm('This will clear all existing data and sync from Tripay API. Continue?')) {
            $this->info('Synchronization cancelled.');
            return self::SUCCESS;
        }

        try {
            DB::beginTransaction();

            // Clear all tables first
            $this->clearTables();

            // Sync prepaid data
            $this->syncPrepaidData();

            // Sync postpaid data
            $this->syncPostpaidData();

            DB::commit();

            $this->info('✅ Tripay data synchronization completed successfully!');
            return self::SUCCESS;

        } catch (Exception $e) {
            DB::rollBack();
            $this->error('❌ Synchronization failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return self::FAILURE;
        }
    }

    /**
     * Clear all Tripay tables
     */
    private function clearTables(): void
    {
        $this->info('🗑️  Clearing existing data...');

        // Clear in correct order to respect foreign key constraints
        TripayPrepaidProduct::query()->delete();
        TripayPrepaidOperator::query()->delete();
        TripayPrepaidCategory::query()->delete();

        TripayPostpaidProduct::query()->delete();
        TripayPostpaidOperator::query()->delete();
        TripayPostpaidCategory::query()->delete();

        $this->info('✅ All tables cleared.');
    }

    /**
     * Sync prepaid data
     */
    private function syncPrepaidData(): void
    {
        $this->info('📱 Syncing prepaid data...');

        // Sync prepaid categories
        $this->syncPrepaidCategories();

        // Sync prepaid operators
        $this->syncPrepaidOperators();

        // Sync prepaid products
        $this->syncPrepaidProducts();

        $this->info('✅ Prepaid data synced successfully.');
    }

    /**
     * Sync postpaid data
     */
    private function syncPostpaidData(): void
    {
        $this->info('💳 Syncing postpaid data...');

        // Sync postpaid categories
        $this->syncPostpaidCategories();

        // Sync postpaid operators
        $this->syncPostpaidOperators();

        // Sync postpaid products
        $this->syncPostpaidProducts();

        $this->info('✅ Postpaid data synced successfully.');
    }

    /**
     * Sync prepaid categories
     */
    private function syncPrepaidCategories(): void
    {
        $this->info('  📂 Syncing prepaid categories...');

        $response = Tripay::prepaid()->getCategories();
        $categories = $response->data;

        $bar = $this->output->createProgressBar(count($categories));
        $bar->start();

        $skippedCount = 0;
        $syncedCount = 0;

        foreach ($categories as $categoryDto) {
            try {
                TripayPrepaidCategory::createOrUpdateFromDto($categoryDto);
                $syncedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed') || 
                    str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->warn("⚠️  Skipped category ID {$categoryDto->id} ({$categoryDto->name}): Duplicate entry");
                    $skippedCount++;
                } else {
                    throw $e;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced {$syncedCount} prepaid categories.");
        if ($skippedCount > 0) {
            $this->warn("  ⚠️  Skipped {$skippedCount} categories due to constraint violations.");
        }
    }

    /**
     * Sync prepaid operators
     */
    private function syncPrepaidOperators(): void
    {
        $this->info('  🏢 Syncing prepaid operators...');

        $response = Tripay::prepaid()->getOperators();
        $operators = $response->data;

        $bar = $this->output->createProgressBar(count($operators));
        $bar->start();

        $skippedCount = 0;
        $syncedCount = 0;

        foreach ($operators as $operatorDto) {
            try {
                TripayPrepaidOperator::createOrUpdateFromDto($operatorDto);
                $syncedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'FOREIGN KEY constraint failed') || 
                    str_contains($e->getMessage(), 'foreign key constraint fails')) {
                    $this->warn("⚠️  Skipped operator ID {$operatorDto->id} ({$operatorDto->name}): Missing category_id {$operatorDto->categoryId}");
                    $skippedCount++;
                } elseif (str_contains($e->getMessage(), 'UNIQUE constraint failed') || 
                         str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->warn("⚠️  Skipped operator ID {$operatorDto->id} ({$operatorDto->name}): Duplicate entry");
                    $skippedCount++;
                } else {
                    throw $e;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced {$syncedCount} prepaid operators.");
        if ($skippedCount > 0) {
            $this->warn("  ⚠️  Skipped {$skippedCount} operators due to constraint violations.");
        }
    }

    /**
     * Sync prepaid products
     */
    private function syncPrepaidProducts(): void
    {
        $this->info('  📦 Syncing prepaid products...');

        $response = Tripay::prepaid()->getProducts();
        $products = $response->data;

        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        $skippedCount = 0;
        $syncedCount = 0;

        foreach ($products as $productDto) {
            try {
                TripayPrepaidProduct::createOrUpdateFromDto($productDto);
                $syncedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a foreign key constraint violation
                if (str_contains($e->getMessage(), 'FOREIGN KEY constraint failed') || 
                    str_contains($e->getMessage(), 'foreign key constraint fails')) {
                    $this->warn("⚠️  Skipped product ID {$productDto->id} ({$productDto->name}): Missing operator_id {$productDto->operatorId} or category_id {$productDto->categoryId}");
                    $skippedCount++;
                } elseif (str_contains($e->getMessage(), 'UNIQUE constraint failed') || 
                         str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->warn("⚠️  Skipped product ID {$productDto->id} ({$productDto->name}): Duplicate code '{$productDto->code}'");
                    $skippedCount++;
                } else {
                    // Re-throw other database exceptions
                    throw $e;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced {$syncedCount} prepaid products.");
        if ($skippedCount > 0) {
            $this->warn("  ⚠️  Skipped {$skippedCount} products due to missing foreign key references.");
        }
    }

    /**
     * Sync postpaid categories
     */
    private function syncPostpaidCategories(): void
    {
        $this->info('  📂 Syncing postpaid categories...');

        $response = Tripay::postpaid()->getCategories();
        $categories = $response->data;

        $bar = $this->output->createProgressBar(count($categories));
        $bar->start();

        $skippedCount = 0;
        $syncedCount = 0;

        foreach ($categories as $categoryDto) {
            try {
                TripayPostpaidCategory::createOrUpdateFromDto($categoryDto);
                $syncedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed') || 
                    str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->warn("⚠️  Skipped category ID {$categoryDto->id} ({$categoryDto->name}): Duplicate entry");
                    $skippedCount++;
                } else {
                    throw $e;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced {$syncedCount} postpaid categories.");
        if ($skippedCount > 0) {
            $this->warn("  ⚠️  Skipped {$skippedCount} categories due to constraint violations.");
        }
    }

    /**
     * Sync postpaid operators
     */
    private function syncPostpaidOperators(): void
    {
        $this->info('  🏢 Syncing postpaid operators...');

        $response = Tripay::postpaid()->getOperators();
        $operators = $response->data;

        $bar = $this->output->createProgressBar(count($operators));
        $bar->start();

        $skippedCount = 0;
        $syncedCount = 0;

        foreach ($operators as $operatorDto) {
            try {
                TripayPostpaidOperator::createOrUpdateFromDto($operatorDto);
                $syncedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'FOREIGN KEY constraint failed') || 
                    str_contains($e->getMessage(), 'foreign key constraint fails')) {
                    $this->warn("⚠️  Skipped operator ID {$operatorDto->id} ({$operatorDto->name}): Missing category_id {$operatorDto->categoryId}");
                    $skippedCount++;
                } elseif (str_contains($e->getMessage(), 'UNIQUE constraint failed') || 
                         str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->warn("⚠️  Skipped operator ID {$operatorDto->id} ({$operatorDto->name}): Duplicate entry");
                    $skippedCount++;
                } else {
                    throw $e;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced {$syncedCount} postpaid operators.");
        if ($skippedCount > 0) {
            $this->warn("  ⚠️  Skipped {$skippedCount} operators due to constraint violations.");
        }
    }

    /**
     * Sync postpaid products
     */
    private function syncPostpaidProducts(): void
    {
        $this->info('  📦 Syncing postpaid products...');

        $response = Tripay::postpaid()->getProducts();
        $products = $response->data;

        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        $skippedCount = 0;
        $syncedCount = 0;

        foreach ($products as $productDto) {
            try {
                TripayPostpaidProduct::createOrUpdateFromDto($productDto);
                $syncedCount++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Check if it's a foreign key constraint violation
                if (str_contains($e->getMessage(), 'FOREIGN KEY constraint failed') || 
                    str_contains($e->getMessage(), 'foreign key constraint fails')) {
                    $this->warn("⚠️  Skipped product ID {$productDto->id} ({$productDto->name}): Missing operator_id {$productDto->operatorId} or category_id {$productDto->categoryId}");
                    $skippedCount++;
                } elseif (str_contains($e->getMessage(), 'UNIQUE constraint failed') || 
                         str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->warn("⚠️  Skipped product ID {$productDto->id} ({$productDto->name}): Duplicate code '{$productDto->code}'");
                    $skippedCount++;
                } else {
                    // Re-throw other database exceptions
                    throw $e;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced {$syncedCount} postpaid products.");
        if ($skippedCount > 0) {
            $this->warn("  ⚠️  Skipped {$skippedCount} products due to missing foreign key references.");
        }
    }
}