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

        foreach ($categories as $categoryDto) {
            TripayPrepaidCategory::createOrUpdateFromDto($categoryDto);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced " . count($categories) . " prepaid categories.");
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

        foreach ($operators as $operatorDto) {
            TripayPrepaidOperator::createOrUpdateFromDto($operatorDto);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced " . count($operators) . " prepaid operators.");
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

        foreach ($products as $productDto) {
            TripayPrepaidProduct::createOrUpdateFromDto($productDto);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced " . count($products) . " prepaid products.");
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

        foreach ($categories as $categoryDto) {
            TripayPostpaidCategory::createOrUpdateFromDto($categoryDto);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced " . count($categories) . " postpaid categories.");
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

        foreach ($operators as $operatorDto) {
            TripayPostpaidOperator::createOrUpdateFromDto($operatorDto);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced " . count($operators) . " postpaid operators.");
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

        foreach ($products as $productDto) {
            TripayPostpaidProduct::createOrUpdateFromDto($productDto);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✅ Synced " . count($products) . " postpaid products.");
    }
}