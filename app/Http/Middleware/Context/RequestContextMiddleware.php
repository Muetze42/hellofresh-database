<?php

namespace App\Http\Middleware\Context;

use Illuminate\Http\Request;

class RequestContextMiddleware extends AbstractContextMiddleware
{
    /**
     * Handles request data extraction by fetching specific headers from the request.
     *
     * @return array<string, mixed>
     */
    protected function data(Request $request): array
    {
        $user = $request->user();

        return [
            'inputs' => $request->all(),
            'auth_class' => is_null($user) ? null : $user::class,
            'auth_id' => $user?->getKey(),
        ];
    }
}
