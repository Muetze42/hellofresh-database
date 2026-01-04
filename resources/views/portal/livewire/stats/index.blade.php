<div class="space-y-section">
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Statistics</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <div>
    <flux:heading size="xl">Statistics</flux:heading>
    <flux:text class="mt-ui">Explore statistics about the {{ config('app.name') }} database and API usage.</flux:text>
  </div>

  <div class="grid gap-section sm:grid-cols-3">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-lime-100 dark:bg-lime-900/30 p-3">
          <flux:icon.utensils class="size-6 text-lime-600 dark:text-lime-400" />
        </div>
        <div>
          <flux:heading size="lg">Recipes</flux:heading>
          <flux:text class="text-sm text-zinc-500">Recipe database statistics</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.stats.recipes')" wire:navigate variant="primary" class="mt-section w-full">
        View Recipe Stats
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-indigo-100 dark:bg-indigo-900/30 p-3">
          <flux:icon.users class="size-6 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
          <flux:heading size="lg">Users</flux:heading>
          <flux:text class="text-sm text-zinc-500">User engagement statistics</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.stats.users')" wire:navigate variant="primary" class="mt-section w-full">
        View User Stats
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-blue-100 dark:bg-blue-900/30 p-3">
          <flux:icon.activity class="size-6 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <flux:heading size="lg">API</flux:heading>
          <flux:text class="text-sm text-zinc-500">API usage statistics</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.stats.api')" wire:navigate variant="primary" class="mt-section w-full">
        View API Stats
      </flux:button>
    </flux:card>
  </div>
</div>
