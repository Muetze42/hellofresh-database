<?php

/** @noinspection DuplicatedCode */

use App\Models\Recipe;
use App\Models\Utensil;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_utensil', static function (Blueprint $table): void {
            $table->foreignIdFor(Recipe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Utensil::class)->constrained()->cascadeOnDelete();

            $table->primary([new Recipe()->getForeignKey(), new Utensil()->getForeignKey()]);
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
