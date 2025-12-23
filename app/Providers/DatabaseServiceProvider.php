<?php

namespace App\Providers;

use App\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        DB::connection('pgsql')->setQueryGrammar(
            new PostgresGrammar(DB::connection('pgsql'))
        );
    }
}
