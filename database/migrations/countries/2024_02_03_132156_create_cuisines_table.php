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
        Schema::create($prefix . 'cuisines', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->unique();
            $table->string('type');
            $table->string('name');
            $table->string('icon_link');
            $table->string('icon_path');
            $table->unsignedInteger('usage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'cuisines');
    }
};