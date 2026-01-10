<flux:main container class="space-y-section">
    <x-portal::email-not-verified />
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item :href="route('portal.stats.index')" wire:navigate>Statistics</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>API</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-section">
    <div>
      <flux:heading size="xl">API Statistics</flux:heading>
      <flux:text class="mt-ui">Usage statistics for the {{ config('app.name') }} API.</flux:text>
    </div>

    <flux:select wire:model.live="period" class="sm:w-40" variant="listbox">
      <flux:select.option value="24h">Last 24 hours</flux:select.option>
      <flux:select.option value="7d">Last 7 days</flux:select.option>
      <flux:select.option value="30d">Last 30 days</flux:select.option>
      <flux:select.option value="90d">Last 90 days</flux:select.option>
    </flux:select>
  </div>

  {{-- Overview Stats --}}
  <div class="grid gap-section sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-blue-100 dark:bg-blue-900/30 p-3">
          <flux:icon.activity class="size-6 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Total Requests</flux:text>
          <flux:heading size="xl">{{ Number::format($this->stats['total_requests']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-emerald-100 dark:bg-emerald-900/30 p-3">
          <flux:icon.users class="size-6 text-emerald-600 dark:text-emerald-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Unique Users</flux:text>
          <flux:heading size="xl">{{ Number::format($this->stats['unique_users']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3">
          <flux:icon.key class="size-6 text-amber-600 dark:text-amber-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Unique Tokens</flux:text>
          <flux:heading size="xl">{{ Number::format($this->stats['unique_tokens']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
          <flux:icon.key-round class="size-6 text-violet-600 dark:text-violet-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Total Tokens</flux:text>
          <flux:heading size="xl">{{ Number::format($this->stats['total_tokens']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-green-100 dark:bg-green-900/30 p-3">
          <flux:icon.circle-check class="size-6 text-green-600 dark:text-green-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Active Tokens</flux:text>
          <flux:heading size="xl">{{ Number::format($this->stats['active_tokens']) }}</flux:heading>
        </div>
      </div>
    </flux:card>
  </div>

  {{-- Daily Requests Chart --}}
  @if($this->chartData)
    <flux:card>
      <flux:heading size="lg">Daily Requests</flux:heading>
      <flux:chart :value="$this->chartData" class="aspect-[3/1] mt-section">
        <flux:chart.svg>
          <flux:chart.line field="requests" class="text-blue-500 dark:text-blue-400" />
          <flux:chart.area field="requests" class="text-blue-200/50 dark:text-blue-400/20" />
          <flux:chart.point field="requests" class="text-blue-500 dark:text-blue-400" />
          <flux:chart.axis axis="x" field="date">
            <flux:chart.axis.line />
            <flux:chart.axis.tick />
          </flux:chart.axis>
          <flux:chart.axis axis="y">
            <flux:chart.axis.grid />
            <flux:chart.axis.tick />
          </flux:chart.axis>
          <flux:chart.cursor />
        </flux:chart.svg>
        <flux:chart.tooltip>
          <flux:chart.tooltip.heading field="date" :format="['year' => 'numeric', 'month' => 'short', 'day' => 'numeric']" />
          <flux:chart.tooltip.value field="requests" label="Requests" />
        </flux:chart.tooltip>
      </flux:chart>
    </flux:card>
  @endif

  {{-- Top Endpoints --}}
  <flux:card>
    <flux:heading size="lg">Top 10 Endpoints</flux:heading>
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Endpoint</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Requests</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @forelse($this->topEndpoints as $endpoint)
          <flux:table.row wire:key="endpoint-{{ $loop->index }}">
            <flux:table.cell>
              <code class="text-sm bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">{{ $endpoint->path }}</code>
            </flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ Number::format($endpoint->count) }}</flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="2" class="text-center text-zinc-500">
              No API requests in the selected period.
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>
  </flux:card>

  <flux:text class="text-sm text-zinc-500 text-center">
    API statistics are not cached and reflect real-time data.
  </flux:text>
</flux:main>
