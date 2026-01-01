<div class="space-y-section">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-section">
        <div class="flex items-center gap-section">
            <flux:button icon="arrow-left" variant="ghost" :href="route('portal.admin.users')" wire:navigate />
            <div class="flex items-center gap-ui">
                <flux:avatar name="{{ $user->name }}" size="lg" />
                <div>
                    <flux:heading size="xl" class="flex items-center gap-ui">
                        {{ $user->name }}
                        @if($user->admin)
                            <flux:badge color="blue" size="sm">Admin</flux:badge>
                        @endif
                    </flux:heading>
                    <flux:text variant="subtle">{{ $user->email }}</flux:text>
                </div>
            </div>
        </div>
        <flux:select wire:model.live="period" variant="listbox" class="w-full sm:w-40">
            <flux:select.option value="7d">Last 7 days</flux:select.option>
            <flux:select.option value="30d">Last 30 days</flux:select.option>
            <flux:select.option value="90d">Last 90 days</flux:select.option>
            <flux:select.option value="all">All time</flux:select.option>
        </flux:select>
    </div>

    {{-- User Info Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-section">
        <flux:card>
            <flux:text variant="subtle">Registered</flux:text>
            <flux:heading size="lg">{{ $user->created_at->format('M d, Y') }}</flux:heading>
            <flux:text variant="subtle" size="sm">{{ $user->created_at->diffForHumans() }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:text variant="subtle">Last Active</flux:text>
            <flux:heading size="lg">{{ $user->active_at?->format('M d, Y') ?? 'Never' }}</flux:heading>
            @if($user->active_at)
                <flux:text variant="subtle" size="sm">{{ $user->active_at->diffForHumans() }}</flux:text>
            @endif
        </flux:card>
        <flux:card>
            <flux:text variant="subtle">Email Status</flux:text>
            @if($user->email_verified_at)
                <flux:heading size="lg" class="text-green-600 dark:text-green-400">Verified</flux:heading>
                <flux:text variant="subtle" size="sm">{{ $user->email_verified_at->format('M d, Y') }}</flux:text>
            @else
                <flux:heading size="lg" class="text-amber-600 dark:text-amber-400">Unverified</flux:heading>
            @endif
        </flux:card>
        <flux:card>
            <flux:text variant="subtle">API Requests</flux:text>
            <flux:heading size="xl" class="text-blue-600 dark:text-blue-400">{{ number_format($this->stats['total_requests']) }}</flux:heading>
        </flux:card>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-section">
        <flux:card>
            <flux:text variant="subtle">Total Tokens</flux:text>
            <flux:heading size="xl">{{ $this->stats['tokens_count'] }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text variant="subtle">Active Tokens</flux:text>
            <flux:heading size="xl" class="text-green-600 dark:text-green-400">{{ $this->stats['active_tokens'] }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text variant="subtle">Favorites</flux:text>
            <flux:heading size="xl">{{ $this->stats['favorites_count'] }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text variant="subtle">Recipe Lists</flux:text>
            <flux:heading size="xl">{{ $this->stats['recipe_lists_count'] }}</flux:heading>
        </flux:card>
    </div>

    {{-- Tokens & Top Endpoints --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-section">
        {{-- API Tokens --}}
        <flux:card>
            <flux:heading size="lg" class="mb-section">API Tokens</flux:heading>
            @if($this->tokens->isNotEmpty())
                <div class="space-y-ui">
                    @foreach($this->tokens as $token)
                        <div wire:key="token-{{ $token->id }}" class="flex items-center justify-between py-ui border-b border-zinc-200 dark:border-zinc-700 last:border-0">
                            <div>
                                <div class="font-medium">{{ $token->name }}</div>
                                <div class="flex items-center gap-ui text-sm text-zinc-500 dark:text-zinc-400">
                                    <span>Created {{ $token->created_at->format('M d, Y') }}</span>
                                    @if($token->expires_at)
                                        <span class="{{ $token->expires_at->isPast() ? 'text-red-600 dark:text-red-400' : '' }}">
                                            Expires {{ $token->expires_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-ui">
                                <flux:badge size="sm">{{ number_format($token->usages_count) }} requests</flux:badge>
                                @if($token->expires_at?->isPast())
                                    <flux:badge color="red" size="sm">Expired</flux:badge>
                                @elseif($token->last_used_at)
                                    <flux:badge color="green" size="sm">Active</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Unused</flux:badge>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <flux:text variant="subtle" class="text-center py-section">No API tokens created.</flux:text>
            @endif
        </flux:card>

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

    {{-- Recent API Requests --}}
    <flux:card>
        <flux:heading size="lg" class="mb-section">Recent API Requests</flux:heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Token</flux:table.column>
                <flux:table.column>Path</flux:table.column>
                <flux:table.column>Host</flux:table.column>
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
                            @if($log->token)
                                <span class="truncate max-w-32 inline-block" title="{{ $log->token->name }}">
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
                        <flux:table.cell colspan="4" class="text-center py-section">
                            <flux:text variant="subtle">No API requests in this period.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <flux:pagination :paginator="$this->usageLogs" class="mt-section" />
    </flux:card>
</div>
