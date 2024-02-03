<?php

use App\Models\Label;
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
        Schema::create($prefix . 'recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Label::class)->nullable();
            $table->uuid('external_id')->unique();
            $table->uuid()->unique();
            $table->string('name')->nullable();
            $table->string('canonical')->nullable();
            $table->string('canonical_link')->nullable();
            $table->string('card_link')->nullable();
            $table->string('cloned_from')->nullable();
            $table->string('headline');
            $table->string('image_link')->nullable();
            $table->string('image_path')->nullable();
            $table->string('total_time')->nullable();
            $table->string('prep_time')->nullable();
            $table->char('country', 2)->nullable();
            $table->text('comment')->nullable();
            $table->text('description')->nullable();
            $table->text('description_markdown')->nullable();
            $table->unsignedInteger('average_rating');
            $table->unsignedInteger('favorites_count');
            $table->unsignedInteger('ratings_count');
            $table->unsignedInteger('serving_size');
            $table->boolean('difficulty');
            $table->boolean('active');
            $table->boolean('is_addon');
            $table->json('nutrition');
            $table->json('steps');
            $table->json('yields');
            $table->timestamp('external_created_at')->nullable();
            $table->timestamp('external_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'recipes');
    }
};
