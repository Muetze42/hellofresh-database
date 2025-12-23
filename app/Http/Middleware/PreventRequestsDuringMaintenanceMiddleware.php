<?php

namespace App\Http\Middleware;

use App\Exceptions\MaintenanceException;
use Closure;
use ErrorException;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Http\Request;
use Override;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PreventRequestsDuringMaintenanceMiddleware extends PreventRequestsDuringMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     *
     * @throws HttpException
     * @throws ErrorException
     */
    #[Override]
    public function handle($request, Closure $next): mixed
    {
        try {
            return parent::handle($request, $next);
        } catch (HttpException $httpException) {
            throw new MaintenanceException(
                $httpException->getStatusCode(),
                'Service Unavailable',
                $httpException,
                $httpException->getHeaders()
            );
        }
    }
}
