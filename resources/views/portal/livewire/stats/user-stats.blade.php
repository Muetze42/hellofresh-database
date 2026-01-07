<flux:main container class="space-y-section">
    <x-portal::email-not-verified />
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item :href="route('portal.stats.index')" wire:navigate>Statistics</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Users</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <div>
    <flux:heading size="xl">User Statistics</flux:heading>
    <flux:text class="mt-ui">Statistics about registered users and their engagement.</flux:text>
  </div>

  {{-- User Engagement --}}
  <div class="grid gap-section sm:grid-cols-2 xl:grid-cols-4">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 p-3">
          <flux:icon.users class="size-6 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Total Users</flux:text>
          <flux:heading size="xl">{{ number_format($this->userEngagement['total_users']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-emerald-100 dark:bg-emerald-900/30 p-3">
          <flux:icon.user-check class="size-6 text-emerald-600 dark:text-emerald-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Users with Lists</flux:text>
          <flux:heading size="xl">{{ number_format($this->userEngagement['users_with_lists']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-fuchsia-100 dark:bg-fuchsia-900/30 p-3">
          <flux:icon.list class="size-6 text-fuchsia-600 dark:text-fuchsia-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Recipe Lists</flux:text>
          <flux:heading size="xl">{{ number_format($this->userEngagement['total_lists']) }}</flux:heading>
        </div>
      </div>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-cyan-100 dark:bg-cyan-900/30 p-3">
          <flux:icon.bookmark class="size-6 text-cyan-600 dark:text-cyan-400" />
        </div>
        <div>
          <flux:text class="text-sm text-zinc-500">Saved Recipes</flux:text>
          <flux:heading size="xl">{{ number_format($this->userEngagement['total_recipes_in_lists']) }}</flux:heading>
        </div>
      </div>
    </flux:card>
  </div>

  {{-- Users by Country --}}
  <flux:card>
    <flux:heading size="lg">Users by Country</flux:heading>
    <flux:table class="mt-section">
      <flux:table.columns>
        <flux:table.column class="ui-text-subtle">Country</flux:table.column>
        <flux:table.column class="ui-text-subtle" align="end">Users</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @foreach($this->usersByCountry as $countryStat)
          <flux:table.row wire:key="user-country-{{ $countryStat->country_code ?? 'unknown' }}">
            <flux:table.cell>
              @if($countryStat->country_code)
                <x-flag :code="$countryStat->country_code" /> {{ __('country.' . $countryStat->country_code) }}
              @else
                <span class="text-zinc-400">Not set</span>
              @endif
            </flux:table.cell>
            <flux:table.cell align="end" class="tabular-nums">{{ number_format($countryStat->count) }}</flux:table.cell>
          </flux:table.row>
        @endforeach
      </flux:table.rows>
    </flux:table>
  </flux:card>

  <flux:text class="text-sm text-zinc-500 text-center">
    User statistics are not cached.
  </flux:text>
</flux:main>
