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
        Schema::create('tripay_postpaid_categories', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
            $table->string('status');
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tripay_postpaid_categories');
    }
};