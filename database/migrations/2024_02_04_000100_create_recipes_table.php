<?php

use App\Models\Category;
use App\Models\Country;
use App\Models\Label;
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
        Schema::create('recipes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Country::class)->constrained();
            $table->foreignIdFor(Label::class)->nullable()->constrained();
            $table->foreignIdFor(Category::class)->nullable()->constrained();
            $table->uuid()->nullable();
            $table->json('name')->nullable();
            $table->string('card_link')->nullable();
            $table->string('cloned_from')->nullable();
            $table->json('headline');
            $table->string('image_path')->nullable();
            $table->string('total_time')->nullable();
            $table->string('prep_time')->nullable();
            $table->json('description')->nullable();
            $table->unsignedInteger('average_rating');
            $table->unsignedInteger('favorites_count');
            $table->unsignedInteger('ratings_count');
            $table->unsignedInteger('serving_size');
            $table->boolean('difficulty');
            $table->boolean('active');
            $table->boolean('is_addon');
            $table->json('nutrition')->nullable();
            $table->json('steps')->nullable();
            $table->json('yields')->nullable();
            $table->timestamp('external_created_at')->nullable();
            $table->timestamp('external_updated_at')->nullable();
            $table->timestamps();
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
