<?php

namespace App\Providers;

use App\Services\Database\Migrations\MigrationCreator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\MigrationServiceProvider as ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Override;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register the migration creator.
     */
    #[Override]
    protected function registerCreator(): void
    {
        $this->app->singleton('migration.creator', function (Application $app): MigrationCreator {
            return new MigrationCreator($app->make(Filesystem::class), $app->basePath('stubs'));
        });
    }

    /**
     * Register the command.
     */
    #[Override]
    protected function registerMigrateMakeCommand(): void
    {
        $this->app->singleton(function (Application $app): MigrateMakeCommand {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = new MigrationCreator($app->make(Filesystem::class), $app->basePath('stubs'));

            $composer = $app->make(Composer::class);

            return new MigrateMakeCommand($creator, $composer);
        });
    }
}
