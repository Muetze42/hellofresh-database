<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_utensil', function (Blueprint $table) {
            $table->foreignId('recipe_id')->constrained('recipes')->cascadeOnDelete();
            $table->foreignId('utensil_id')->constrained('utensils')->cascadeOnDelete();
            $table->primary(['recipe_id', 'utensil_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_utensil');
    }
};
