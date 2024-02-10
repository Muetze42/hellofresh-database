<?php

use App\Models\Family;
use App\Models\Ingredient;
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
        Schema::create((new Ingredient())->getTable(), function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Family::class)->nullable()
                ->constrained((new Family())->getTable());
            $table->uuid();
            $table->json('name');
            $table->string('type');
            $table->string('image_path')->nullable();
            $table->json('description')->nullable();
            $table->boolean('shipped');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Ingredient())->getTable());
    }
};
