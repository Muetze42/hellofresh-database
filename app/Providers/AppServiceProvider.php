<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureDevAlwaysToMail();
        $this->configureHttpClient();
        $this->configureModels();
        $this->configureRateLimiter();
        $this->configureUrlGenerator();
        $this->configureVitePrefetchingStrategy();

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
        if (! $this->app->environment('local')) {
            return;
        }

        if (! $address = config('mail.always_to')) {
            return;
        }

        Mail::alwaysTo($address);
    }

    /**
     * Configure the application's commands.
     */
    protected function configureHttpClient(): void
    {
        // \Illuminate\Support\Facades\Http::globalRequestMiddleware(
        //     fn (\GuzzleHttp\Psr7\Request $request) => $request->withHeader(
        //         'User-Agent', config('app.name') . ' ' . config('app.env'),
        //     )
        // );
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
     * Configure the application's Rate Limiter.
     */
    protected function configureRateLimiter(): void
    {
        // \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
        //     return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)
        //         ->by($request->user()?->id ?: $request->ip());
        // });
    }

    /**
     * Configure the application's URL Generator.
     */
    protected function configureUrlGenerator(): void
    {
        if (! $this->app->isLocal()) {
            URL::forceScheme('https');
        }
    }

    /**
     * Configure the application's Vite prefetching strategy.
     *
     * @see https://github.com/laravel/framework/pull/52462
     */
    protected function configureVitePrefetchingStrategy(): void
    {
        // Vite::useWaterfallPrefetching(10);
        Vite::useAggressivePrefetching();
    }

    /**
     * Specify the default validation rules for passwords.
     */
    protected function definingDefaultPasswordRules(): void
    {
        Password::defaults(static function () {
            return Password::min(12)
                // ->uncompromised()
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}
