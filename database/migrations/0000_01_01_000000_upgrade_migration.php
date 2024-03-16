<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('at__recipe_user');
        Schema::dropIfExists('au__recipe_user');
        Schema::dropIfExists('be__recipe_user');
        Schema::dropIfExists('ca__recipe_user');
        Schema::dropIfExists('ch__recipe_user');
        Schema::dropIfExists('de__recipe_user');
        Schema::dropIfExists('dk__recipe_user');
        Schema::dropIfExists('fr__recipe_user');
        Schema::dropIfExists('gb__recipe_user');
        Schema::dropIfExists('it__recipe_user');
        Schema::dropIfExists('lu__recipe_user');
        Schema::dropIfExists('nl__recipe_user');
        Schema::dropIfExists('no__recipe_user');
        Schema::dropIfExists('nz__recipe_user');
        Schema::dropIfExists('se__recipe_user');
        Schema::dropIfExists('us__recipe_user');
        Schema::dropIfExists('at__recipe_notes');
        Schema::dropIfExists('au__recipe_notes');
        Schema::dropIfExists('be__recipe_notes');
        Schema::dropIfExists('ca__recipe_notes');
        Schema::dropIfExists('ch__recipe_notes');
        Schema::dropIfExists('de__recipe_notes');
        Schema::dropIfExists('dk__recipe_notes');
        Schema::dropIfExists('fr__recipe_notes');
        Schema::dropIfExists('gb__recipe_notes');
        Schema::dropIfExists('it__recipe_notes');
        Schema::dropIfExists('lu__recipe_notes');
        Schema::dropIfExists('nl__recipe_notes');
        Schema::dropIfExists('no__recipe_notes');
        Schema::dropIfExists('nz__recipe_notes');
        Schema::dropIfExists('se__recipe_notes');
        Schema::dropIfExists('us__recipe_notes');
        Schema::dropIfExists('verifications');
        Schema::dropIfExists('action_events');
        Schema::dropIfExists('nova_notifications');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('filter_requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
