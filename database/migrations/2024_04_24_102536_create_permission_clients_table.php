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
        Schema::create('permission_clients', function (Blueprint $table) {
            $table->string("client");
            $table->string("utilisateur");
            $table->primary(['client', 'utilisateur']);
            $table->foreign('client')->references('username')->on('clients')->onDelete('cascade')->cascadeOnUpdate();
            $table->foreign('utilisateur')->references('username')->on('users')->onDelete('cascade')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_clients');
    }
};
