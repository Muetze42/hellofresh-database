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
        Schema::table('countries', static function (Blueprint $table): void {
            $table->unsignedInteger('recipes_with_pdf_count')
                ->nullable()
                ->after('recipes_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', static function (Blueprint $table): void {
            $table->dropColumn('recipes_with_pdf_count');
        });
    }
};
