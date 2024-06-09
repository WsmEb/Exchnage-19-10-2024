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
        Schema::create('deposes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date("date_depose")->nullable();
            $table->string("client");
            $table->foreign("client")->references("username")->on("clients")->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("devise");
            $table->string("type");
            $table->foreign("devise")->references("symbol")->on("devises")->cascadeOnDelete()->cascadeOnUpdate();
            $table->float("amount")->nullable();
            $table->text("commentaire")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposes');
    }
};
