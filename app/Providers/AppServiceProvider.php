<?php

namespace App\Providers;

use App\Console\Commands\Development\Illuminate\InstallCommand;
use App\Console\Commands\Development\MigrateCommand;
use App\Console\Commands\Development\RollbackCommand;
use App\Database\Migrations\Migrator;
use App\Models\User;
use App\Services\LengthAwarePaginator as CustomLengthAwarePaginator;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use NormanHuth\Library\Lib\MacroRegistry;
use NormanHuth\Library\Support\Macros\Carbon\ToUserTimezoneMacro;
use Spatie\Translatable\Facades\Translatable;

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

        Translatable::fallback(
            fallbackAny: true,
        );

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return countryRoute('show.reset.form', [
                'country_lang' => $this->countryLangPath(),
                'token' => $token,
            ]);
        });

        VerifyEmail::createUrlUsing(function ($notifiable): string {
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'country_lang' => $this->countryLangPath(),
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
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

    protected function countryLangPath(): string
    {
        $country = country();
        $locale = $this->app->getLocale();

        return $country && in_array($locale, $country->locales) ? Str::lower($country->code) . '-' . $locale : 'us-en';
    }

    /**
     * Register application macros.
     */
    protected function macros(): void
    {
        Carbon::macro(
            'publicFormatted',
            fn (Request $request) => $this->toUserTimezone($request)->translatedFormat('M j')
        );
        MacroRegistry::macros([
            ToUserTimezoneMacro::class => Carbon::class,
        ]);
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
