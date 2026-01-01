<?php

/** @noinspection DuplicatedCode */

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\PersonalAccessToken;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personal_access_token_usages', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(PersonalAccessToken::class, 'token_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->string('host');
            $table->string('path');
            $table->timestamp('created_at', precision: 3);
            $table->softDeletes(precision: 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_token_usages');
    }
};
