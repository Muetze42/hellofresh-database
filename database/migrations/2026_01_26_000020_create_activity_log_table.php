<?php

/** @noinspection DuplicatedCode */

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
        Schema::connection(config('activitylog.database_connection'))
            ->create(config('activitylog.table_name'), function (Blueprint $table): void {
                $table->id();
                $table->string('log_name')->nullable();
                $table->text('description');
                // $table->nullableUuidMorphs('subject', 'subject');
                $table->nullableMorphs('subject', 'subject');
                $table->string('event')->nullable();
                // $table->nullableUuidMorphs('causer', 'causer');
                $table->nullableMorphs('causer', 'causer');
                $table->jsonb('properties')->nullable();
                $table->uuid('batch_uuid')->nullable();
                $table->index('log_name');
                $table->timestamp('created_at', precision: 3)->nullable();
            });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
};
