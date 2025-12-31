<div class="space-y-section">
    <div>
        <flux:heading size="xl">Get Started</flux:heading>
        <flux:text class="mt-ui">Everything you need to start using the {{ config('app.name') }} API.</flux:text>
    </div>

    <flux:card>
        <flux:heading size="lg">Base URL</flux:heading>
        <flux:text class="mt-ui">All API requests should be made to:</flux:text>
        <pre class="mt-section p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>{{ $baseUrl }}</code></pre>
    </flux:card>

    <flux:card>
        <flux:heading size="lg">Authentication</flux:heading>
        <flux:text class="mt-ui">
            The API uses Bearer token authentication. Include your API token in the <code class="text-sm bg-zinc-100 dark:bg-zinc-700 px-1 rounded">Authorization</code> header of every request.
        </flux:text>
        <pre class="mt-section p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>Authorization: Bearer YOUR_API_TOKEN</code></pre>
        <flux:text class="mt-section text-sm">
            @auth
                You can manage your API tokens in the <flux:link href="{{ route('portal.tokens.index') }}">Token Management</flux:link> section.
            @else
                <flux:link href="{{ route('portal.login') }}">Sign in</flux:link> to create and manage your API tokens.
            @endauth
        </flux:text>
    </flux:card>

    <flux:card>
        <flux:heading size="lg">Localization</flux:heading>
        <flux:text class="mt-ui">
            Most endpoints require a locale and country prefix in the URL path. This determines the language and region for the response data.
        </flux:text>
        <pre class="mt-section p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>GET {{ $baseUrl }}/{locale}-{country}/recipes</code></pre>
        <flux:text class="mt-section text-sm">
            Example: <code class="bg-zinc-100 dark:bg-zinc-700 px-1 rounded">de-DE</code> for German (Germany), <code class="bg-zinc-100 dark:bg-zinc-700 px-1 rounded">en-GB</code> for English (Great Britain).
        </flux:text>
        <flux:text class="mt-ui text-sm">
            Use the <flux:link href="{{ route('portal.docs.countries') }}">Countries endpoint</flux:link> to get a list of available countries.
        </flux:text>
    </flux:card>

    <flux:card>
        <flux:heading size="lg">Rate Limiting</flux:heading>
        <flux:text class="mt-ui">
            API requests are limited to <strong>{{ $rateLimit }} requests per minute</strong> per user. If you exceed this limit, you will receive a <code class="text-sm bg-zinc-100 dark:bg-zinc-700 px-1 rounded">429 Too Many Requests</code> response.
        </flux:text>
        <flux:text class="mt-section text-sm">
            Rate limit headers are included in every response:
        </flux:text>
        <pre class="mt-ui p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>X-RateLimit-Limit: {{ $rateLimit }}
X-RateLimit-Remaining: {{ $rateLimit - 1 }}</code></pre>
        <flux:text class="mt-section text-sm text-zinc-500 dark:text-zinc-400">
            Note: The rate limit may be adjusted in the future. Always check the <code class="bg-zinc-100 dark:bg-zinc-700 px-1 rounded">X-RateLimit-Limit</code> header for the current limit.
        </flux:text>
    </flux:card>

    <flux:card>
        <flux:heading size="lg">Pagination</flux:heading>
        <flux:text class="mt-ui">
            List endpoints return paginated results. Use the <code class="text-sm bg-zinc-100 dark:bg-zinc-700 px-1 rounded">per_page</code> query parameter to control the number of results ({{ $paginationMin }}-{{ $paginationMax }}, default {{ $paginationDefault }}).
        </flux:text>
        <pre class="mt-section p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>GET {{ $baseUrl }}/de-DE/recipes?per_page=25&page=2</code></pre>
        <flux:text class="mt-section text-sm">
            Paginated responses include a <code class="bg-zinc-100 dark:bg-zinc-700 px-1 rounded">meta</code> object with pagination information:
        </flux:text>
        <pre class="mt-ui p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>{
    "data": [...],
    "meta": {
        "current_page": 2,
        "from": 26,
        "last_page": 10,
        "per_page": 25,
        "to": 50,
        "total": 250
    }
}</code></pre>
    </flux:card>

    <flux:card>
        <flux:heading size="lg">Response Format</flux:heading>
        <flux:text class="mt-ui">
            All responses are returned in JSON format. Include the <code class="text-sm bg-zinc-100 dark:bg-zinc-700 px-1 rounded">Accept: application/json</code> header in your requests.
        </flux:text>
        <flux:text class="mt-section text-sm">
            Successful responses return HTTP status <code class="bg-zinc-100 dark:bg-zinc-700 px-1 rounded">200 OK</code>. Error responses include a message explaining what went wrong:
        </flux:text>
        <pre class="mt-ui p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>{
    "message": "Unauthenticated."
}</code></pre>
    </flux:card>

    <flux:card>
        <flux:heading size="lg">Example Request</flux:heading>
        <pre class="mt-section p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>curl -X GET "{{ $baseUrl }}/de-DE/recipes?per_page=10" \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</code></pre>
    </flux:card>

    @if($isPreRelease)
        <flux:callout icon="triangle-alert" color="amber">
            <flux:callout.heading>Pre-Release API (v{{ $version }})</flux:callout.heading>
            <flux:callout.text>
                This API is currently in pre-release. Endpoints, response formats, and features may change without notice until version 1.0.0 is released. Use in production at your own risk.
            </flux:callout.text>
        </flux:callout>
    @endif
</div>
