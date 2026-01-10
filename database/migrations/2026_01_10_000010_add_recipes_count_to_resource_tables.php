<?php

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
        Schema::table('ingredients', static function (Blueprint $table): void {
            $table->unsignedInteger('cached_recipes_count')->nullable()->after('active');
        });

        Schema::table('allergens', static function (Blueprint $table): void {
            $table->unsignedInteger('cached_recipes_count')->nullable()->after('active');
        });

        Schema::table('tags', static function (Blueprint $table): void {
            $table->unsignedInteger('cached_recipes_count')->nullable()->after('display_label');
        });

        Schema::table('labels', static function (Blueprint $table): void {
            $table->unsignedInteger('cached_recipes_count')->nullable()->after('display_label');
        });

        Schema::table('cuisines', static function (Blueprint $table): void {
            $table->unsignedInteger('cached_recipes_count')->nullable()->after('active');
        });

        Schema::table('utensils', static function (Blueprint $table): void {
            $table->unsignedInteger('cached_recipes_count')->nullable()->after('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', static function (Blueprint $table): void {
            $table->dropColumn('cached_recipes_count');
        });

        Schema::table('allergens', static function (Blueprint $table): void {
            $table->dropColumn('cached_recipes_count');
        });

        Schema::table('tags', static function (Blueprint $table): void {
            $table->dropColumn('cached_recipes_count');
        });

        Schema::table('labels', static function (Blueprint $table): void {
            $table->dropColumn('cached_recipes_count');
        });

        Schema::table('cuisines', static function (Blueprint $table): void {
            $table->dropColumn('cached_recipes_count');
        });

        Schema::table('utensils', static function (Blueprint $table): void {
            $table->dropColumn('cached_recipes_count');
        });
    }
};
