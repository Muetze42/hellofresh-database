<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LowerCaseUrlsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() != 'GET') {
            return $next($request);
        }

        $path = $request->path();

        if ($path && $path !== Str::lower($path)) {
            return redirect(Str::lower($path));
        }

        return $next($request);
    }
}
