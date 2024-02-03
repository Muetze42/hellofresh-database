<?php

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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->char('country', 2);
            $table->char('locale', 2);
            $table->string('domain');
            $table->json('data')->nullable();
            $table->unsignedTinyInteger('take');
            $table->unsignedInteger('recipes')->nullable();
            $table->unsignedInteger('ingredients')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();

            $table->index(['country', 'locale']);
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
