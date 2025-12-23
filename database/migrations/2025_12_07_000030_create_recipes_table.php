<?php

/** @noinspection DuplicatedCode */

use App\Models\Country;
use App\Models\Label;
use App\Models\Recipe;
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
        Schema::create('recipes', static function (Blueprint $table): void {
            $table->id();
            $table->string('hellofresh_id');
            $table->foreignIdFor(Country::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Label::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Recipe::class, 'canonical_id')
                ->nullable()->constrained('recipes')->nullOnDelete();
            $table->jsonb('name');
            $table->jsonb('headline')->nullable();
            $table->jsonb('description')->nullable();
            $table->unsignedTinyInteger('difficulty')->nullable();
            $table->unsignedInteger('prep_time')->nullable();
            $table->unsignedInteger('total_time')->nullable();
            $table->string('image_path')->nullable();
            $table->jsonb('card_link')->nullable();
            $table->jsonb('steps_primary')->nullable();
            $table->jsonb('steps_secondary')->nullable();
            $table->jsonb('nutrition_primary')->nullable();
            $table->jsonb('nutrition_secondary')->nullable();
            $table->jsonb('yields_primary')->nullable();
            $table->jsonb('yields_secondary')->nullable();
            $table->boolean('has_pdf');
            $table->timestamp('hellofresh_created_at', precision: 3)->nullable();
            $table->timestamp('hellofresh_updated_at', precision: 3)->nullable();
            $table->timestamps(precision: 3);
            $table->softDeletes(precision: 3);

            $table->unique(['hellofresh_id', new Country()->getForeignKey()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
