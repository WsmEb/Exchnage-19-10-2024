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
        Schema::create('detail_historiques_convertes', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string("client_username");
            $table->string("convertedSymbol");
            $table->foreign('convertedSymbol')->references("symbol")->on('devises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->float('amount');
            $table->text('devise')->references('symbol')->on('devises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('commentaire')->nullable();
            $table->foreign('client_username')->references('username')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('id_historique')->nullable();
            $table->foreign('id_historique')->references('id')->on('historiques_operations')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_historiques_convertes');
    }
};

