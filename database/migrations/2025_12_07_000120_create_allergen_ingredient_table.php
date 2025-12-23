<?php

/** @noinspection DuplicatedCode */

use App\Models\Allergen;
use App\Models\Ingredient;
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
        Schema::create('allergen_ingredient', static function (Blueprint $table): void {
            $table->foreignIdFor(Ingredient::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Allergen::class)->constrained()->cascadeOnDelete();

            $table->primary([new Allergen()->getForeignKey(), new Ingredient()->getForeignKey()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergen_ingredient');
    }
};
