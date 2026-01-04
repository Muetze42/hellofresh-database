<?php

namespace App\Http\Middleware\Context;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;

use function Sentry\configureScope;

abstract class AbstractContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Context::add($this->data($request));

        configureScope(function (Scope $scope) use ($request): void {
            $scope->setContext('livewire-context-mw', $this->data($request));
        });

        return $next($request);
    }

    /**
     * Handles request data extraction by fetching specific headers from the request.
     *
     * @return array<string, mixed>
     */
    abstract protected function data(Request $request): array;
}
