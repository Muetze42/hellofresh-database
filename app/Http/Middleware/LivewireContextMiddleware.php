<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;

use function Sentry\configureScope;

class LivewireContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $data = [
            'referer' => $request->header('Referer'),
            'user_agent' => $request->userAgent(),
            'origin' => $request->header('Origin'),
        ];

        Context::add($data);

        configureScope(function (Scope $scope) use ($data): void {
            $scope->setContext('livewire-context-mw', $data);
        });

        return $next($request);
    }
}
