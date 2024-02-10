<?php

use App\Models\Cuisine;
use App\Models\Recipe;
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
        Schema::create($prefix . 'cuisine_recipe', function (Blueprint $table) {
            $table->foreignIdFor(Cuisine::class)->constrained((new Cuisine())->getTable())->cascadeOnDelete();
            $table->foreignIdFor(Recipe::class)->constrained((new Recipe())->getTable())->cascadeOnDelete();
            $table->primary([(new Cuisine())->getForeignKey(), (new Recipe())->getForeignKey()]);
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
