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
        Schema::create('permission_pages', function (Blueprint $table) {
            $table->string("page");
            $table->string("utilisateur");
            $table->string("type");
            $table->primary(['page', 'utilisateur', 'type']);
            $table->foreign('utilisateur')->references('username')->on('users')->onDelete('cascade')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_pages');
    }
};
