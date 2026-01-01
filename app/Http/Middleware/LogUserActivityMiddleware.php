<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Models\PersonalAccessTokenUsage;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            $this->logActivity($request, $user);
        }

        return $next($request);
    }

    protected function logActivity(Request $request, User $user): void
    {
        DB::table('users')
            ->where('id', $user->getKey())
            ->update(['active_at' => now()]);

        $token = $user->currentAccessToken();

        if (! $token instanceof PersonalAccessToken) {
            return;
        }

        $usage = new PersonalAccessTokenUsage([
            'host' => $request->getHost(),
            'path' => $request->path(),
        ]);
        $usage->token()->associate($token);
        $usage->user()->associate($user);
        $usage->save();
    }
}
