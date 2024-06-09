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
        Schema::create('detail_historiques_operations', function (Blueprint $table) {
            $table->id();
            $table->string('comments')->nullable();
            $table->date('date')->nullable();
            $table->string('percentage')->nullable();
            $table->float('total')->nullable();
            $table->string('quantity')->nullable();
            $table->string('type_operation')->nullable();
            $table->string('ville')->nullable();
            $table->string('prix')->nullable();
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
        Schema::dropIfExists('detail_historiques_operations');
    }
};
