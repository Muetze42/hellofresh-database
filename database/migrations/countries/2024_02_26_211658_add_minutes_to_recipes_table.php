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
        Schema::table($prefix . 'recipes', function (Blueprint $table) {
            $table->unsignedInteger('minutes')->nullable()->after('prep_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::table($prefix . 'recipes', function (Blueprint $table) {
            $table->dropColumn('minutes');
        });
    }
};
