<?php

use App\Models\Recipe;
use App\Models\Utensil;
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
        Schema::create($prefix . 'recipe_utensil', function (Blueprint $table) {
            $table->foreignIdFor(Recipe::class)->constrained((new Recipe())->getTable())->cascadeOnDelete();
            $table->foreignIdFor(Utensil::class)->constrained((new Utensil())->getTable())->cascadeOnDelete();
            $table->primary([(new Recipe())->getForeignKey(), (new Utensil())->getForeignKey()]);
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
