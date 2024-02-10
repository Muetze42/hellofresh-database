<?php

namespace App\Providers;

use App\Console\Commands\Development\Illuminate\InstallCommand;
use App\Console\Commands\Development\MigrateCommand;
use App\Console\Commands\Development\RollbackCommand;
use App\Database\Migrations\Migrator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerCountryMigrators();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    protected function registerCountryMigrators(): void
    {
        $this->app->singleton(MigrateCommand::class, function (Application $app) {
            $table = $app['config']['database.migrations'];
            $repository = new DatabaseMigrationRepository($app['db'], $table);
            $migrator = new Migrator($repository, $app['db'], $app['files'], $app['events']);

            return new MigrateCommand($migrator, $app[Dispatcher::class]);
        });
        $this->app->singleton(InstallCommand::class, function (Application $app) {
            return new InstallCommand($app['migration.repository']);
        });
        $this->app->singleton(RollbackCommand::class, function (Application $app) {
            $table = $app['config']['database.migrations'];
            $repository = new DatabaseMigrationRepository($app['db'], $table);
            $migrator = new Migrator($repository, $app['db'], $app['files'], $app['events']);

            return new RollbackCommand($migrator);
        });
    }
}
