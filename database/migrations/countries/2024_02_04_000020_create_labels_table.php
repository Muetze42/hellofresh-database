<?php

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
        Schema::create($prefix . 'labels', function (Blueprint $table) {
            $table->id();
            $table->string('handle')->nullable();
            $table->json('text');
            $table->string('foreground_color', 7)->nullable();
            $table->string('background_color', 7)->nullable();
            $table->boolean('display_label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'labels');
    }
};
