<?php

use App\Models\Recipe;
use App\Models\User;
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
        Schema::create($prefix . 'recipe_user', function (Blueprint $table) {
            $table->foreignIdFor(Recipe::class)->constrained((new Recipe())->getTable())->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained((new User())->getTable())->cascadeOnDelete();
            $table->primary([(new Recipe())->getForeignKey(), (new User())->getForeignKey()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'recipe_user');
    }
};
