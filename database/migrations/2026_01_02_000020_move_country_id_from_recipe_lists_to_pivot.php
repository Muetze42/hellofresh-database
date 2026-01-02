<?php

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add country_id to pivot table (nullable first)
        Schema::table('recipe_recipe_list', static function (Blueprint $table): void {
            $table->foreignIdFor(Country::class)->nullable()->after('recipe_list_id')->constrained()->cascadeOnDelete();
        });

        // Step 2: Copy country_id from recipe_lists to pivot entries
        DB::statement('
            UPDATE recipe_recipe_list
            SET country_id = (
                SELECT country_id
                FROM recipe_lists
                WHERE recipe_lists.id = recipe_recipe_list.recipe_list_id
            )
        ');

        // Step 3: Make country_id NOT NULL
        Schema::table('recipe_recipe_list', static function (Blueprint $table): void {
            $table->bigInteger('country_id')->nullable(false)->change();
        });

        // Step 4: Remove country_id from recipe_lists
        Schema::table('recipe_lists', static function (Blueprint $table): void {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Add country_id back to recipe_lists (nullable first)
        Schema::table('recipe_lists', static function (Blueprint $table): void {
            $table->foreignIdFor(Country::class)->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });

        // Step 2: Copy country_id from first pivot entry back to recipe_lists
        // (Note: This may lose data if list has recipes from multiple countries)
        DB::statement('
            UPDATE recipe_lists
            SET country_id = (
                SELECT country_id
                FROM recipe_recipe_list
                WHERE recipe_recipe_list.recipe_list_id = recipe_lists.id
                LIMIT 1
            )
        ');

        // Step 3: Make country_id NOT NULL
        Schema::table('recipe_lists', static function (Blueprint $table): void {
            $table->bigInteger('country_id')->nullable(false)->change();
        });

        // Step 4: Remove country_id from pivot table
        Schema::table('recipe_recipe_list', static function (Blueprint $table): void {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
