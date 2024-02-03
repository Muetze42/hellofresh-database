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
        Schema::create($prefix . 'allergen_ingredient', function (Blueprint $table) use ($prefix) {
            $table->foreignId('allergen_id')->constrained($prefix . 'allergens')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained($prefix . 'ingredients')->cascadeOnDelete();
            $table->primary(['allergen_id', 'ingredient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'allergen_ingredient');
    }
};
