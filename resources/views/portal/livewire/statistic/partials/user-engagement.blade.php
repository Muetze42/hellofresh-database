{{-- User Engagement --}}
<div class="grid gap-section sm:grid-cols-2 lg:grid-cols-4">
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
