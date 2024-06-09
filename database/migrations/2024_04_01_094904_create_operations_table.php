<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->string('comments')->nullable();
            $table->date('date')->nullable();
            $table->string('client')->nullable();
            $table->string('percentage')->nullable();
            $table->float('total')->nullable();
            $table->string('quantity')->nullable();
            $table->string('devise')->nullable();
            $table->string('type_operation')->nullable();
            $table->string('ville')->nullable();
            $table->string('prix')->nullable();
            $table->foreign('client')->references('username')->on('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('devise')->references('symbol')->on('devises')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
