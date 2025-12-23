<?php

/** @noinspection DuplicatedCode */

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
        Schema::create('ingredients', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Country::class)->constrained()->cascadeOnDelete();
            $table->jsonb('name');
            $table->string('name_slug')->nullable();
            $table->jsonb('hellofresh_ids')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps(precision: 3);

            $table->index(['country_id', 'name_slug']);
        });

        DB::statement('CREATE INDEX ingredients_hellofresh_ids_gin ON ingredients USING GIN (hellofresh_ids)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
