<?php

use App\Application;
use App\Http\Middleware\CountryMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LowerCaseUrlsMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('');
        $middleware->web(append: [
            LowerCaseUrlsMiddleware::class,
            HandleInertiaRequests::class,
        ], prepend: CountryMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
