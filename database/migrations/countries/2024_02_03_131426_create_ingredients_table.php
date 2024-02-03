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
        Schema::create($prefix . 'ingredients', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->unique();
            $table->uuid();
            $table->string('slug');
            $table->string('type');
            $table->char('country', 2);
            $table->string('image_link');
            $table->string('image_path');
            $table->string('name');
            $table->string('internal_name');
            $table->text('description')->nullable();
            $table->string('has_duplicated_name')->nullable();
            $table->boolean('shipped');
            $table->unsignedInteger('usage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'ingredients');
    }
};
