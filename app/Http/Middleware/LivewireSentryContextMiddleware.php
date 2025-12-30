<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sentry\State\Scope;
use Symfony\Component\HttpFoundation\Response;

use function Sentry\configureScope;

class LivewireSentryContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->addLivewireContext($request);

        return $next($request);
    }

    /**
     * Extract Livewire context from request and add to Sentry.
     */
    protected function addLivewireContext(Request $request): void
    {
        if (! function_exists('Sentry\configureScope')) {
            return;
        }

        $components = $request->input('components', []);

        if ($components === []) {
            return;
        }

        $livewireContext = [];

        foreach ($components as $index => $component) {
            $snapshot = $this->decodeSnapshot($component['snapshot'] ?? '');

            if ($snapshot === null) {
                continue;
            }

            $memo = $snapshot['memo'] ?? [];
            $calls = $component['calls'] ?? [];

            // Detaillierte Child-Komponenten
            $children = array_map(static function ($childData) {
                return is_array($childData) ? ($childData[1] ?? $childData[0] ?? 'unknown') : $childData;
            }, $memo['children'] ?? []);

            $livewireContext['component_' . $index] = [
                'name' => $memo['name'] ?? 'unknown',
                'id' => $memo['id'] ?? null,
                'path' => $memo['path'] ?? null,
                'method' => $memo['method'] ?? null,
                'calls' => $calls,
                'children' => $children,
                'release' => $memo['release'] ?? null,
            ];
        }

        configureScope(function (Scope $scope) use ($livewireContext, $request): void {
            $scope->setContext('livewire', $livewireContext);
            $scope->setContext('livewire_request', [
                'referer' => $request->header('Referer'),
                'user_agent' => $request->userAgent(),
                'origin' => $request->header('Origin'),
            ]);
        });
    }

    /**
     * Decode the Livewire snapshot.
     *
     * @return array<string, mixed>|null
     *
     * @noinspection JsonEncodingApiUsageInspection
     */
    protected function decodeSnapshot(string $snapshot): ?array
    {
        if ($snapshot === '') {
            return null;
        }

        $decoded = json_decode($snapshot, true);

        if (! is_array($decoded)) {
            return null;
        }

        return $decoded;
    }
}
