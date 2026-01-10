<flux:main container class="space-y-section">
  <x-portal::email-not-verified />
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Resources</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <div>
    <flux:heading size="xl">Resources</flux:heading>
    <flux:text class="mt-ui">Browse and manage recipe resources in the {{ config('app.name') }} database.</flux:text>
  </div>

  <div class="grid gap-section md:grid-cols-2 lg:grid-cols-3">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3">
          <flux:icon.carrot class="size-6 text-amber-600 dark:text-amber-400" />
        </div>
        <div>
          <flux:heading size="lg">Ingredients</flux:heading>
          <flux:text class="text-sm text-zinc-500">Recipe ingredients</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.resources.ingredients')" wire:navigate variant="primary" class="mt-section w-full">
        View Ingredients
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-red-100 dark:bg-red-900/30 p-3">
          <flux:icon.triangle-alert class="size-6 text-red-600 dark:text-red-400" />
        </div>
        <div>
          <flux:heading size="lg">Allergens</flux:heading>
          <flux:text class="text-sm text-zinc-500">Food allergens</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.resources.allergens')" wire:navigate variant="primary" class="mt-section w-full">
        View Allergens
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-rose-100 dark:bg-rose-900/30 p-3">
          <flux:icon.tag class="size-6 text-rose-600 dark:text-rose-400" />
        </div>
        <div>
          <flux:heading size="lg">Tags</flux:heading>
          <flux:text class="text-sm text-zinc-500">Recipe tags</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.resources.tags')" wire:navigate variant="primary" class="mt-section w-full">
        View Tags
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
          <flux:icon.tags class="size-6 text-violet-600 dark:text-violet-400" />
        </div>
        <div>
          <flux:heading size="lg">Labels</flux:heading>
          <flux:text class="text-sm text-zinc-500">Recipe labels</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.resources.labels')" wire:navigate variant="primary" class="mt-section w-full">
        View Labels
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-teal-100 dark:bg-teal-900/30 p-3">
          <flux:icon.chef-hat class="size-6 text-teal-600 dark:text-teal-400" />
        </div>
        <div>
          <flux:heading size="lg">Cuisines</flux:heading>
          <flux:text class="text-sm text-zinc-500">Cuisine types</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.resources.cuisines')" wire:navigate variant="primary" class="mt-section w-full">
        View Cuisines
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-sky-100 dark:bg-sky-900/30 p-3">
          <flux:icon.cooking-pot class="size-6 text-sky-600 dark:text-sky-400" />
        </div>
        <div>
          <flux:heading size="lg">Utensils</flux:heading>
          <flux:text class="text-sm text-zinc-500">Kitchen utensils</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.resources.utensils')" wire:navigate variant="primary" class="mt-section w-full">
        View Utensils
      </flux:button>
    </flux:card>
  </div>
</flux:main>
