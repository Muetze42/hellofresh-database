<div class="space-y-section">
    <flux:heading size="xl">{{ config('app.name') }} API</flux:heading>
    <flux:text>Access recipe data, menus, ingredients, and more through our REST API.</flux:text>

    <div class="grid gap-section md:grid-cols-2 lg:grid-cols-3">
        @if($isAuthenticated)
            <flux:card>
                <flux:heading size="lg">API Tokens</flux:heading>
                <flux:text class="mt-ui">
                    You have <strong>{{ $tokenCount }}</strong> active API {{ Str::plural('token', $tokenCount) }}.
                </flux:text>
                <div class="mt-section">
                    <flux:button href="{{ route('portal.tokens.index') }}" variant="primary" wire:navigate>
                        Manage Tokens
                    </flux:button>
                </div>
            </flux:card>
        @else
            <flux:card>
                <flux:heading size="lg">Get Started</flux:heading>
                <flux:text class="mt-ui">
                    Create an account to get your API tokens and start integrating with our API.
                </flux:text>
                <div class="mt-section flex gap-ui">
                    <flux:button href="{{ route('portal.register') }}" variant="primary" wire:navigate>
                        Sign Up
                    </flux:button>
                    <flux:button href="{{ route('portal.login') }}" wire:navigate>
                        Sign In
                    </flux:button>
                </div>
            </flux:card>
        @endif

        <flux:card>
            <flux:heading size="lg">API Documentation</flux:heading>
            <flux:text class="mt-ui">
                Learn how to authenticate and make your first API request.
            </flux:text>
            <div class="mt-section">
                <flux:button href="{{ route('portal.docs.recipes') }}" wire:navigate>
                    View API Docs
                </flux:button>
            </div>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">API Base URL</flux:heading>
            <flux:text class="mt-ui font-mono text-sm bg-zinc-100 dark:bg-zinc-800 p-ui rounded">
                https://{{ config('api.domain_name') }}/{locale}-{country}
            </flux:text>
            <flux:text class="mt-ui text-xs text-zinc-500">
                Replace <code>{locale}-{country}</code> with your target market, e.g., <code>de-DE</code>
            </flux:text>
        </flux:card>
    </div>

    @if($isAuthenticated && $tokens->isNotEmpty())
        <flux:card>
            <flux:heading size="lg">Recent Tokens</flux:heading>
            <flux:table class="mt-section">
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                    <flux:table.column>Last Used</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($tokens as $token)
                        <flux:table.row wire:key="token-{{ $token->id }}">
                            <flux:table.cell>{{ $token->name }}</flux:table.cell>
                            <flux:table.cell>{{ $token->created_at->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell>{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif

    <flux:card>
        <flux:heading size="lg">Authentication</flux:heading>
        <flux:text class="mt-ui">
            All API requests must include your token in the Authorization header:
        </flux:text>
        <pre class="mt-ui p-section bg-zinc-900 text-zinc-100 rounded-lg text-sm overflow-x-auto"><code>Authorization: Bearer YOUR_API_TOKEN</code></pre>
    </flux:card>
</div>
