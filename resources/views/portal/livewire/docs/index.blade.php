<flux:main container class="space-y-section">
  <x-portal::email-not-verified />
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>API Reference</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <div>
    <flux:heading size="xl">API Reference</flux:heading>
    <flux:text class="mt-ui">Complete documentation for the {{ config('app.name') }} API.</flux:text>
  </div>

  {{-- Getting Started --}}
  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-emerald-100 dark:bg-emerald-900/30 p-3">
        <flux:icon.rocket class="size-6 text-emerald-600 dark:text-emerald-400" />
      </div>
      <div class="flex-1">
        <flux:heading size="lg">Get Started</flux:heading>
        <flux:text class="text-sm text-zinc-500">Authentication, rate limiting, and basic usage</flux:text>
      </div>
      <flux:button :href="route('portal.docs.get-started')" wire:navigate variant="primary">
        Read Guide
      </flux:button>
    </div>
  </flux:card>

  {{-- Endpoints --}}
  <flux:heading size="lg">Endpoints</flux:heading>

  <div class="grid gap-section sm:grid-cols-2 lg:grid-cols-3">
    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
          <flux:icon.globe class="size-6 text-violet-600 dark:text-violet-400" />
        </div>
        <div>
          <flux:heading size="lg">Countries</flux:heading>
          <flux:text class="text-sm text-zinc-500">Available countries and locales</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.countries')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-lime-100 dark:bg-lime-900/30 p-3">
          <flux:icon.utensils class="size-6 text-lime-600 dark:text-lime-400" />
        </div>
        <div>
          <flux:heading size="lg">List Recipes</flux:heading>
          <flux:text class="text-sm text-zinc-500">Search and filter recipes</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.recipes')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-lime-100 dark:bg-lime-900/30 p-3">
          <flux:icon.book-open class="size-6 text-lime-600 dark:text-lime-400" />
        </div>
        <div>
          <flux:heading size="lg">Get Recipe</flux:heading>
          <flux:text class="text-sm text-zinc-500">Single recipe with details</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.recipes-show')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-sky-100 dark:bg-sky-900/30 p-3">
          <flux:icon.calendar class="size-6 text-sky-600 dark:text-sky-400" />
        </div>
        <div>
          <flux:heading size="lg">List Menus</flux:heading>
          <flux:text class="text-sm text-zinc-500">Browse weekly menus</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.menus')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-sky-100 dark:bg-sky-900/30 p-3">
          <flux:icon.calendar-days class="size-6 text-sky-600 dark:text-sky-400" />
        </div>
        <div>
          <flux:heading size="lg">Get Menu</flux:heading>
          <flux:text class="text-sm text-zinc-500">Single menu with recipes</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.menus-show')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-rose-100 dark:bg-rose-900/30 p-3">
          <flux:icon.tag class="size-6 text-rose-600 dark:text-rose-400" />
        </div>
        <div>
          <flux:heading size="lg">Tags</flux:heading>
          <flux:text class="text-sm text-zinc-500">Recipe tags and categories</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.tags')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
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
      <flux:button :href="route('portal.docs.labels')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-red-100 dark:bg-red-900/30 p-3">
          <flux:icon.triangle-alert class="size-6 text-red-600 dark:text-red-400" />
        </div>
        <div>
          <flux:heading size="lg">Allergens</flux:heading>
          <flux:text class="text-sm text-zinc-500">Allergen information</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.allergens')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>

    <flux:card>
      <div class="flex items-center gap-section">
        <div class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3">
          <flux:icon.carrot class="size-6 text-amber-600 dark:text-amber-400" />
        </div>
        <div>
          <flux:heading size="lg">Ingredients</flux:heading>
          <flux:text class="text-sm text-zinc-500">Available ingredients</flux:text>
        </div>
      </div>
      <flux:button :href="route('portal.docs.ingredients')" wire:navigate variant="primary" class="mt-section w-full">
        View Documentation
      </flux:button>
    </flux:card>
  </div>
</flux:main>
