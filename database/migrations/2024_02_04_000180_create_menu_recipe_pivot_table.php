<?php

use App\Models\Menu;
use App\Models\Recipe;
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
        Schema::create('menu_recipe', function (Blueprint $table) {
            $table->foreignIdFor(Menu::class)->constrained((new Menu())->getTable())->cascadeOnDelete();
            $table->foreignIdFor(Recipe::class)->constrained((new Recipe())->getTable())->cascadeOnDelete();
            $table->primary([(new Menu())->getForeignKey(), (new Recipe())->getForeignKey()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_recipe');
    }
};
