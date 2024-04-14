<?php

use App\Application;
use App\Http\Middleware\CountryMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LowerCaseUrlsMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use NormanHuth\Library\Commands\UpdateDisposableEmailDomainsCommand;
use NormanHuth\Library\Http\Middleware\LogUserActivityMiddleware;
use NormanHuth\Library\Lib\CommandRegistry;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '*/logout',
        ]);
        $middleware->redirectGuestsTo('');
        $middleware->web(append: [
            LowerCaseUrlsMiddleware::class,
            HandleInertiaRequests::class,
            LogUserActivityMiddleware::class,
        ], prepend: CountryMiddleware::class);
    })
    ->withCommands(array_merge(CommandRegistry::devCommands(), [
        UpdateDisposableEmailDomainsCommand::class,
    ]))
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
