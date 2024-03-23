<?php

namespace App\Providers;

use App\Console\Commands\Development\Illuminate\InstallCommand;
use App\Console\Commands\Development\MigrateCommand;
use App\Console\Commands\Development\RollbackCommand;
use App\Database\Migrations\Migrator;
use App\Models\User;
use App\Services\LengthAwarePaginator as CustomLengthAwarePaginator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
        $this->binds();
        $this->macros();

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            $country = country();
            $parameter = $country ? Str::lower(country()?->code) . '-' . $this->app->getLocale() : 'us-en';

            return route('show.reset.form', [
                'country_lang' => $parameter,
                'token' => $token,
            ]);
        });

        Password::defaults(static function () {
            return Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }

    /**
     * Register application macros.
     */
    protected function macros(): void
    {
        Carbon::macro(
            'publicFormatted',
            fn () => $this->translatedFormat('M j')
        );
    }

    /**
     * Register application bindings.
     */
    protected function binds(): void
    {
        $this->app->bind(
            LengthAwarePaginator::class,
            CustomLengthAwarePaginator::class
        );
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
