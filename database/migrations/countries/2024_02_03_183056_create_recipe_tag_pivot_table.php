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
        Schema::create($prefix . 'recipe_tag', function (Blueprint $table) use ($prefix) {
            $table->foreignId('recipe_id')->constrained($prefix . 'recipes')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained($prefix . 'tags')->cascadeOnDelete();
            $table->primary(['recipe_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'recipe_tag');
    }
};
