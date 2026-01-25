<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('filter_shares', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('page', 20);
            $table->jsonb('filters');
            $table->timestamp('created_at', 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_shares');
    }
};
