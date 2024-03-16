<?php

use App\Models\Filter;
use App\Models\User;
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
        Schema::create($prefix . 'user_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Filter::class)
                ->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(string $prefix = ''): void
    {
        Schema::dropIfExists($prefix . 'user_filters');
    }
};
