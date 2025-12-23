<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDevAlwaysToMail();
        $this->configureModels();

        $this->definingDefaultPasswordRules();

        // \Illuminate\Support\Facades\Date::use(\Carbon\CarbonImmutable::class);
    }

    /**
     * Configure the application's commands.
     */
    protected function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    /**
     * Configure the application's global email receiver for development environment.
     */
    protected function configureDevAlwaysToMail(): void
    {
        if (! $this->app->environment(['local', 'staging'])) {
            return;
        }

        if (! $address = config('mail.always_to')) {
            return;
        }

        if (is_string($address)) {
            Mail::alwaysTo($address);
        }
    }

    /**
     * Configure the application's models.
     */
    protected function configureModels(): void
    {
        // Model::automaticallyEagerLoadRelationships();
        Model::shouldBeStrict(! $this->app->isProduction());
    }

    /**
     * Specify the default validation rules for passwords.
     */
    protected function definingDefaultPasswordRules(): void
    {
        Password::defaults(static function () {
            return Password::min(8)
                // ->uncompromised()
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}
