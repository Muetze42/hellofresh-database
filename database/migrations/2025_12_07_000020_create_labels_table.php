<?php

/** @noinspection DuplicatedCode */

use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('labels', static function (Blueprint $table): void {
            $table->id();
            $table->jsonb('handles');
            $table->foreignIdFor(Country::class)->constrained()->cascadeOnDelete();
            $table->jsonb('name');
            $table->string('foreground_color')->nullable();
            $table->string('background_color')->nullable();
            $table->boolean('display_label');
            $table->boolean('active')->default(false);
            $table->timestamps(precision: 3);
        });

        DB::statement('CREATE INDEX labels_handles_gin ON labels USING GIN (handles)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labels');
    }
};
