<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use NormanHuth\Library\Http\Middleware\PreventRequestsDuringMaintenanceMiddleware;
use NormanHuth\Library\Lib\CommandRegistry;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        then: function (): void {
            Route::middleware(['api', 'auth:sanctum'])
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->replace(
            PreventRequestsDuringMaintenance::class,
            PreventRequestsDuringMaintenanceMiddleware::class
        );
    })
    ->withCommands(CommandRegistry::devCommands())
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request): bool {
            return $request->expectsJson() || $request->is('api') || $request->is('api/*');
        });
    })->create();
