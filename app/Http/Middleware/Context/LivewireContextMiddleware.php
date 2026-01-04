<?php

namespace App\Http\Middleware\Context;

use Illuminate\Http\Request;

class LivewireContextMiddleware extends AbstractContextMiddleware
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
            'referer' => $request->header('Referer'),
            'user_agent' => $request->userAgent(),
            'origin' => $request->header('Origin'),
            'auth_class' => is_null($user) ? null : $user::class,
            'auth_id' => $user?->getKey(),
        ];
    }
}
