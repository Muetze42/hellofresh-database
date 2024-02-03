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
        Schema::create($prefix . 'cuisine_recipe', function (Blueprint $table) use ($prefix) {
            $table->foreignId('cuisine_id')->constrained($prefix . 'cuisines')->cascadeOnDelete();
            $table->foreignId('recipe_id')->constrained($prefix . 'recipes')->cascadeOnDelete();
            $table->primary(['cuisine_id', 'recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'cuisine_recipe');
    }
};
