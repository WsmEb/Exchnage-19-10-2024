<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transferts', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('expediteur')->nullable();
            $table->string('recepteur')->nullable();
            $table->string('client')->nullable();
            $table->string('devise')->nullable();
            $table->float('solde')->nullable();
            $table->foreign('expediteur')->references('username')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('recepteur')->references('username')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('devise')->references('symbol')->on('devises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferts');
    }
};