<?php

use App\Models\Country;
use App\Models\Family;
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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Country::class)->constrained();
            $table->foreignIdFor(Family::class)->nullable()->constrained();
            $table->uuid('external_id')->unique();
            $table->uuid();
            $table->json('name');
            $table->string('type');
            $table->string('image_link')->nullable();
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
        Schema::dropIfExists('ingredients');
    }
};
