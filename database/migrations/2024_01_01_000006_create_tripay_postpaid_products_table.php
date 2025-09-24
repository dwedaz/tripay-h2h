<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tripay_postpaid_products', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->decimal('admin_fee', 15, 2);
            $table->integer('operator_id');
            $table->integer('category_id');
            $table->string('status');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('operator_id')->references('id')->on('tripay_postpaid_operators')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('tripay_postpaid_categories')->onDelete('cascade');
            
            // Indexes
            $table->index('status');
            $table->index('operator_id');
            $table->index('category_id');
            $table->index('code');
            $table->index('name');
            $table->index('admin_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tripay_postpaid_products');
    }
};