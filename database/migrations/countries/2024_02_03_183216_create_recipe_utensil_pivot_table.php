<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(string $prefix = ''): void
    {
        Schema::create($prefix . 'recipe_utensil', function (Blueprint $table) use ($prefix) {
            $table->foreignId('recipe_id')->constrained($prefix . 'recipes')->cascadeOnDelete();
            $table->foreignId('utensil_id')->constrained($prefix . 'utensils')->cascadeOnDelete();
            $table->primary(['recipe_id', 'utensil_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'recipe_utensil');
    }
};
