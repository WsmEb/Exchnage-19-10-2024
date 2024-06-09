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
        Schema::create('detail_historiques_transferts', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('expediteur')->nullable();
            $table->string('recepteur')->nullable();
            $table->float('solde')->nullable();
            $table->foreign('expediteur')->references('username')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('recepteur')->references('username')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('detail_historiques_transferts');
    }
};
