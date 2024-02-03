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
        Schema::create($prefix . 'families', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->unique();
            $table->uuid();
            $table->string('name');
            $table->string('slug');
            $table->string('type');
            $table->string('icon_link');
            $table->string('icon_path');
            $table->text('description')->nullable();
            $table->json('usage_by_country')->nullable();
            $table->unsignedInteger('priority');
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
        Schema::dropIfExists($prefix . 'families');
    }
};
