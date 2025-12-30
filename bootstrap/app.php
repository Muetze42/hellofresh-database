<?php

use App\Http\Middleware\LocalizationMiddleware;
use App\Http\Middleware\PreventRequestsDuringMaintenanceMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        then: function (): void {
            Route::middleware(['api'])
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
            Route::middleware(['api', 'localized'])
                ->prefix('api')
                ->name('api-localized.')
                ->group(base_path('routes/api-localized.php'));

            Route::middleware(['web', 'localized:true'])
                ->name('localized.')
                ->prefix('{locale}-{country:code}')
                ->group(base_path('routes/web-localized.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->replace(
            PreventRequestsDuringMaintenance::class,
            PreventRequestsDuringMaintenanceMiddleware::class
        );
        $middleware->alias([
            'localized' => LocalizationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request): bool {
            return $request->expectsJson() || $request->is('api') || $request->is('api/*');
        });

        $exceptions->context(fn (): array => ['user_id' => auth()->user()?->id]);
    })->create();
