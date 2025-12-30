<?php

/** @noinspection DuplicatedCode */

use App\Models\User;
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
        Schema::create('email_verifications', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->timestamp('verified_at', precision: 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_verifications');
    }
};
