<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->string("username")->primary();
            $table->string("nom")->nullable();
            $table->string("localisation")->nullable();
            $table->string("commentaire")->nullable();
            $table->string("password");
            $table->string("bloque");
            $table->timestamps();
        });
    }

 
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
