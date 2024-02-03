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
        Schema::create($prefix . 'tags', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->unique();
            $table->string('type');
            $table->string('name');
            $table->string('slug');
            $table->string('color_handle');
            $table->json('preferences');
            $table->boolean('display_label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'tags');
    }
};
