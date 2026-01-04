<div class="space-y-section">
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Admin</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>API Usage</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-section">
    <flux:heading size="xl">API Usage</flux:heading>
    <flux:select wire:model.live="period" variant="listbox" class="w-full sm:w-40">
      <flux:select.option value="24h">Last 24 hours</flux:select.option>
      <flux:select.option value="7d">Last 7 days</flux:select.option>
      <flux:select.option value="30d">Last 30 days</flux:select.option>
      <flux:select.option value="90d">Last 90 days</flux:select.option>
    </flux:select>
  </div>

  {{-- Statistics Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-section">
    <flux:card>
      <flux:text variant="subtle">Total Requests</flux:text>
      <flux:heading size="xl">{{ number_format($this->stats['total_requests']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">Active Tokens</flux:text>
      <flux:heading size="xl" class="text-green-600 dark:text-green-400">{{ number_format($this->stats['active_tokens']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">Total Tokens</flux:text>
      <flux:heading size="xl">{{ number_format($this->stats['total_tokens']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">Unique Tokens Used</flux:text>
      <flux:heading size="xl" class="text-blue-600 dark:text-blue-400">{{ number_format($this->stats['unique_tokens']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">Unique Users</flux:text>
      <flux:heading size="xl" class="text-purple-600 dark:text-purple-400">{{ number_format($this->stats['unique_users']) }}</flux:heading>
    </flux:card>
  </div>

  {{-- Top Endpoints and Users --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-section">
    {{-- Top Endpoints --}}
    <flux:card>
      <flux:heading size="lg" class="mb-section">Top Endpoints</flux:heading>
      @if($this->topEndpoints->isNotEmpty())
        <div class="space-y-ui">
          @foreach($this->topEndpoints as $endpoint)
            <div wire:key="endpoint-{{ $loop->index }}" class="flex items-center justify-between">
              <flux:text class="font-mono text-sm truncate flex-1 mr-ui">{{ $endpoint->path }}</flux:text>
              <flux:badge size="sm">{{ number_format($endpoint->count) }}</flux:badge>
            </div>
          @endforeach
        </div>
      @else
        <flux:text variant="subtle" class="text-center py-section">No API usage in this period.</flux:text>
      @endif
    </flux:card>

    {{-- Top Users --}}
    <flux:card>
      <flux:heading size="lg" class="mb-section">Most Active Users</flux:heading>
      @if($this->topUsers->isNotEmpty())
        <div class="space-y-ui">
          @foreach($this->topUsers as $topUser)
            <div wire:key="top-user-{{ $topUser->id }}" class="flex items-center justify-between">
              <div class="flex items-center gap-ui flex-1 min-w-0">
                <flux:avatar name="{{ $topUser->name }}" size="sm" />
                <div class="min-w-0 flex-1">
                  <div class="font-medium truncate">{{ $topUser->name }}</div>
                  <flux:text variant="subtle" size="sm" class="truncate">{{ $topUser->email }}</flux:text>
                </div>
              </div>
              <flux:badge size="sm">{{ number_format($topUser->request_count) }}</flux:badge>
            </div>
          @endforeach
        </div>
      @else
        <flux:text variant="subtle" class="text-center py-section">No API usage in this period.</flux:text>
      @endif
    </flux:card>
  </div>

  {{-- Daily Usage Chart --}}
  @if($this->dailyUsage->isNotEmpty())
    <flux:card>
      <flux:heading size="lg" class="mb-section">Daily Requests</flux:heading>
      @php
        $maxCount = $this->dailyUsage->max('count');
        $yAxisSteps = [100, 75, 50, 25, 0];
      @endphp
      <div class="flex gap-ui">
        {{-- Y-Axis --}}
        <div class="flex flex-col justify-between h-48 text-xs text-zinc-500 dark:text-zinc-400 text-right shrink-0 w-8">
          @foreach($yAxisSteps as $percent)
            <span>{{ number_format($maxCount * $percent / 100) }}</span>
          @endforeach
        </div>

        {{-- Chart --}}
        <div class="flex flex-col gap-ui flex-1">
          <div class="h-48 flex items-end gap-1 border-l border-b border-zinc-200 dark:border-zinc-700">
            @foreach($this->dailyUsage as $day)
              <div
                wire:key="day-{{ $day->date }}"
                class="flex-1 max-w-12 bg-blue-500 dark:bg-blue-600 rounded-t transition-all hover:bg-blue-600 dark:hover:bg-blue-500 relative group"
                style="height: {{ $maxCount > 0 ? max(($day->count / $maxCount * 100), 4) : 4 }}%"
              >
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-xs px-2 py-1 rounded whitespace-nowrap z-10">
                  {{ \Carbon\Carbon::parse($day->date)->format('M d') }}: {{ number_format($day->count) }}
                </div>
              </div>
            @endforeach
          </div>
          <div class="flex text-xs text-zinc-500 dark:text-zinc-400" style="gap: 1px">
            @foreach($this->dailyUsage as $day)
              <div wire:key="label-{{ $day->date }}" class="flex-1 max-w-12 text-center truncate">
                {{ \Carbon\Carbon::parse($day->date)->format('d') }}
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </flux:card>
  @endif

  {{-- Recent Usage Logs --}}
  <flux:card>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-section mb-section">
      <flux:heading size="lg">Recent Requests</flux:heading>
      <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search logs..."
        icon="search"
        class="w-full sm:w-64"
      />
    </div>

    <flux:table>
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Time</flux:table.column>
        <flux:table.column class="ui-text-subtle">User</flux:table.column>
        <flux:table.column class="ui-text-subtle">Token</flux:table.column>
        <flux:table.column class="ui-text-subtle">Path</flux:table.column>
        <flux:table.column class="ui-text-subtle">Host</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @forelse($this->usageLogs as $log)
          <flux:table.row wire:key="log-{{ $log->id }}">
            <flux:table.cell>
              <span title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                {{ $log->created_at->diffForHumans() }}
              </span>
            </flux:table.cell>
            <flux:table.cell>
              @if($log->user)
                <div class="flex items-center gap-ui">
                  <flux:avatar name="{{ $log->user->name }}" size="xs" />
                  <span>{{ $log->user->name }}</span>
                </div>
              @else
                <flux:text variant="subtle">Deleted</flux:text>
              @endif
            </flux:table.cell>
            <flux:table.cell>
              @if($log->token)
                <span class="truncate max-w-24 inline-block" title="{{ $log->token->name }}">
                  {{ $log->token->name }}
                </span>
              @else
                <flux:text variant="subtle">Deleted</flux:text>
              @endif
            </flux:table.cell>
            <flux:table.cell>
              <span class="font-mono text-sm">{{ $log->path }}</span>
            </flux:table.cell>
            <flux:table.cell>
              <flux:text variant="subtle" size="sm">{{ $log->host }}</flux:text>
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="5" class="text-center py-section">
              <flux:text variant="subtle">No API usage found.</flux:text>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$this->usageLogs" class="mt-section" />
  </flux:card>
</div>
