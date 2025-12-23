<?php

/** @noinspection DuplicatedCode */

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
        Schema::create('countries', static function (Blueprint $table): void {
            $table->id();
            $table->char('code', 2)->unique();
            $table->string('domain');
            $table->jsonb('locales');
            $table->unsignedInteger('prep_min')->nullable();
            $table->unsignedInteger('prep_max')->nullable();
            $table->unsignedSmallInteger('total_min')->nullable();
            $table->unsignedSmallInteger('total_max')->nullable();
            $table->unsignedInteger('recipes_count')->nullable();
            $table->unsignedInteger('ingredients_count')->nullable();
            $table->unsignedTinyInteger('take');
            $table->boolean('active')->default(false);
            $table->boolean('has_allergens')->default(false);
            $table->boolean('has_cuisines')->default(false);
            $table->boolean('has_labels')->default(false);
            $table->boolean('has_tags')->default(false);
            $table->boolean('has_utensil')->default(false);
            $table->timestamps(precision: 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
