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
        Schema::create('historiques_operations', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->text('commentaire')->nullable();
            $table->date('datehistorique');
            $table->float('valeur');
            $table->string('client')->nullable();
            $table->string('devise')->nullable();
            $table->foreign('client')->references('username')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('devise')->references('symbol')->on('devises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiques_operations');
    }
};
