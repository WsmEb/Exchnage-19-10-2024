<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->string('titre')->primary();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('base_devise');
            $table->foreign('base_devise')->references('symbol')->on('devises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('entreprise');
    }
};
