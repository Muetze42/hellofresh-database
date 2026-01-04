<div class="space-y-section">
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Admin</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Users</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <flux:heading size="xl">Users</flux:heading>

  {{-- Statistics Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-section">
    <flux:card>
      <flux:text variant="subtle">Total Users</flux:text>
      <flux:heading size="xl">{{ number_format($this->stats['total']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">Verified</flux:text>
      <flux:heading size="xl" class="text-green-600 dark:text-green-400">{{ number_format($this->stats['verified']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">Unverified</flux:text>
      <flux:heading size="xl" class="text-amber-600 dark:text-amber-400">{{ number_format($this->stats['unverified']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">Admins</flux:text>
      <flux:heading size="xl" class="text-blue-600 dark:text-blue-400">{{ number_format($this->stats['admins']) }}</flux:heading>
    </flux:card>
    <flux:card>
      <flux:text variant="subtle">With Tokens</flux:text>
      <flux:heading size="xl" class="text-purple-600 dark:text-purple-400">{{ number_format($this->stats['with_tokens']) }}</flux:heading>
    </flux:card>
  </div>

  {{-- Filters --}}
  <flux:card>
    <div class="flex flex-col sm:flex-row gap-section">
      <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search users..."
        icon="search"
        class="flex-1"
      />
      <flux:select wire:model.live="filter" variant="listbox" class="sm:w-48">
        <flux:select.option value="all">All Users</flux:select.option>
        <flux:select.option value="verified">Verified</flux:select.option>
        <flux:select.option value="unverified">Unverified</flux:select.option>
        <flux:select.option value="admins">Admins</flux:select.option>
        <flux:select.option value="with_tokens">With Tokens</flux:select.option>
      </flux:select>
    </div>
  </flux:card>

  {{-- Users Table --}}
  <flux:card>
    <flux:table>
      <flux:table.columns>
        <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" wire:click="sort('id')">ID</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">User</flux:table.column>
        <flux:table.column class="ui-text-subtle">Status</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'country_code'" :direction="$sortDirection" wire:click="sort('country_code')">Country</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'tokens_count'" :direction="$sortDirection" wire:click="sort('tokens_count')">Tokens</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'active_at'" :direction="$sortDirection" wire:click="sort('active_at')">Last Active</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Registered</flux:table.column>
        <flux:table.column class="w-16"></flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @forelse($this->users as $user)
          <flux:table.row wire:key="user-{{ $user->id }}">
            <flux:table.cell align="end">
              {{ $user->id }}
            </flux:table.cell>
            <flux:table.cell>
              <div class="flex items-center gap-ui">
                <flux:avatar name="{{ $user->name }}" size="sm" />
                <div>
                  <div class="font-medium flex items-center gap-ui">
                    {{ $user->name }}
                    @if($user->admin)
                      <flux:badge color="blue" size="sm">Admin</flux:badge>
                    @endif
                  </div>
                  <flux:text variant="subtle" size="sm">{{ $user->email }}</flux:text>
                </div>
              </div>
            </flux:table.cell>
            <flux:table.cell>
              @if($user->email_verified_at)
                <flux:badge color="green" size="sm">Verified</flux:badge>
              @else
                <flux:badge color="amber" size="sm">Unverified</flux:badge>
              @endif
            </flux:table.cell>
            <flux:table.cell>
              @if($user->country_code)
                <span title="{{ $user->countryName() }}">{{ $user->countryFlag() }} {{ $user->country_code }}</span>
              @else
                <flux:text variant="subtle">—</flux:text>
              @endif
            </flux:table.cell>
            <flux:table.cell>
              <flux:badge size="sm">{{ $user->tokens_count }}</flux:badge>
            </flux:table.cell>
            <flux:table.cell align="right">
              {{ $user->active_at?->toDateTimeString() ?? 'Never' }}
            </flux:table.cell>
            <flux:table.cell align="right">
              {{ $user->created_at->toDateTimeString() }}
            </flux:table.cell>
            <flux:table.cell align="right">
              <flux:button
                icon="eye"
                variant="ghost"
                size="sm"
                :href="route('portal.admin.users.show', $user)"
                wire:navigate
              />
            </flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="8" class="text-center py-section">
              <flux:text variant="subtle">No users found.</flux:text>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$this->users" class="mt-section" />
  </flux:card>
</div>
