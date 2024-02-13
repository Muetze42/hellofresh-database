<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SentryTunnelController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (!app()->isProduction()) {
            return response()->json('only in production mode', 204);
        }

        $envelope = $request->getContent();
        $headers = array_map(
            fn ($line) => json_decode($line, true),
            preg_split('/\r\n|\r|\n/', $envelope)
        )[0];

        if (empty($headers['dsn']) || $headers['dsn'] != config('sentry.dsn')) {
            return response()->json(null, 401);
        }

        $parsed = parse_url(config('sentry.dsn'));
        $url = sprintf(
            'https://%s.ingest.sentry.io/api/%d/envelope/',
            explode('.', $parsed['host'])[0],
            last(explode('/', rtrim($parsed['path'], '/')))
        );

        $response = Http::withBody($envelope, 'application/x-sentry-envelope')->post($url);

        return response()->json($response->json(), $response->status());
    }
}
