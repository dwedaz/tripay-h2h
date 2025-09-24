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
        Schema::create('tripay_prepaid_operators', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
            $table->string('status');
            $table->integer('category_id');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('category_id')->references('id')->on('tripay_prepaid_categories')->onDelete('cascade');
            
            // Indexes
            $table->index('status');
            $table->index('category_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tripay_prepaid_operators');
    }
};