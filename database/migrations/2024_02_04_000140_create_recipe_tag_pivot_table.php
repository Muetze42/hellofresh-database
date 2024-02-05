<?php

use App\Models\Recipe;
use App\Models\Tag;
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
        Schema::create('recipe_tag', function (Blueprint $table) {
            $table->foreignIdFor(Recipe::class)->constrained((new Recipe())->getTable())->cascadeOnDelete();
            $table->foreignIdFor(Tag::class)->constrained((new Tag())->getTable())->cascadeOnDelete();
            $table->primary([(new Recipe())->getForeignKey(), (new Tag())->getForeignKey()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_tag');
    }
};
