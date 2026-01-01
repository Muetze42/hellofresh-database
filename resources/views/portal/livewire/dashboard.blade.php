<div class="space-y-section">
  <flux:heading size="xl">{{ config('app.name') }} API</flux:heading>
  <flux:text>Access recipe data, menus, ingredients, and more through our REST API.</flux:text>

  <img
    src="https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F73c8366b-1d79-4a0d-b540-214c968135b3%3Fdate%3D1%26commit%3D1&style=plastic&t={{ now()->timestamp }}"
    alt="Laravel Forge Site Deployment Status"
  />

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

  @if($isAuthenticated && $usageStats && $usageStats['total'] > 0)
    <flux:card>
      <flux:heading size="lg">API Usage</flux:heading>
      <div class="mt-section grid gap-section md:grid-cols-3">
        <div>
          <flux:text class="text-sm text-zinc-500">Today</flux:text>
          <flux:heading size="xl">{{ number_format($usageStats['today']) }}</flux:heading>
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Last 7 Days</flux:text>
          <flux:heading size="xl">{{ number_format($usageStats['week']) }}</flux:heading>
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">All Time</flux:text>
          <flux:heading size="xl">{{ number_format($usageStats['total']) }}</flux:heading>
        </div>
      </div>

      <div class="mt-section">
        <flux:chart :value="collect($usageStats['chartData'])" class="aspect-[4/1]">
          <flux:chart.svg gutter="4 0 20 0">
            <flux:chart.line field="requests" class="text-lime-500" />
            <flux:chart.area field="requests" class="text-lime-500/20" />

            <flux:chart.axis axis="x" field="date">
              <flux:chart.axis.tick />
            </flux:chart.axis>
            <flux:chart.axis axis="y">
              <flux:chart.axis.grid />
              <flux:chart.axis.tick />
            </flux:chart.axis>

          </flux:chart.svg>
          <flux:chart.tooltip>
            <flux:chart.tooltip.heading field="date" />
            <flux:chart.tooltip.value field="requests" label="Requests" />
          </flux:chart.tooltip>
        </flux:chart>
      </div>

      @if(count($usageStats['topEndpoints']) > 0)
        <div class="mt-section">
          <flux:text class="text-sm text-zinc-500 mb-ui">Top Endpoints</flux:text>
          <div class="space-y-ui">
            @foreach($usageStats['topEndpoints'] as $endpoint)
              <div wire:key="endpoint-{{ $loop->index }}" class="flex justify-between items-center text-sm">
                <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-ui py-0.5 rounded truncate max-w-[70%]">{{ $endpoint['path'] }}</code>
                <span class="text-zinc-500">{{ number_format($endpoint['count']) }}</span>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </flux:card>
  @endif

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
