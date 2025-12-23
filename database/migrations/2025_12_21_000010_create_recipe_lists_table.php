<?php

use App\Models\Country;
use App\Models\Recipe;
use App\Models\RecipeList;
use App\Models\User;
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
        Schema::create('recipe_lists', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Country::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('recipe_recipe_list', static function (Blueprint $table): void {
            $table->foreignIdFor(Recipe::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(RecipeList::class)->constrained()->cascadeOnDelete();
            $table->timestamp('added_at')->useCurrent();
            $table->primary([
                new Recipe()->getForeignKey(),
                new RecipeList()->getForeignKey(),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_recipe_list');
        Schema::dropIfExists('recipe_lists');
    }
};
