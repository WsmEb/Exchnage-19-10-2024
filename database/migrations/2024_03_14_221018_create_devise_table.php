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
        Schema::create('devises', function (Blueprint $table) {
            // The symbol of the devise, used as the primary key
            $table->string("symbol")->primary();

            // A description of the devise (optional)
            $table->string("description")->nullable();

            // The base value of the devise, stored as a float
            // You may want to consider decimal for precise calculations
            $table->float("base")->default;

            // Timestamps for creation and updates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devises');
    }
};
