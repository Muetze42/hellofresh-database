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

            $componentName = $snapshot['memo']['name'] ?? 'unknown';
            $calls = $component['calls'] ?? [];
            $methods = array_column($calls, 'method');

            $livewireContext['component_' . $index] = [
                'name' => $componentName,
                'id' => $snapshot['memo']['id'] ?? null,
                'path' => $snapshot['memo']['path'] ?? null,
                'methods_called' => $methods,
                'children_count' => count($snapshot['memo']['children'] ?? []),
            ];
        }

        configureScope(function (Scope $scope) use ($livewireContext): void {
            $scope->setContext('livewire', $livewireContext);
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
