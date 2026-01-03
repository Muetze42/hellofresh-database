{{-- Global Stats --}}
<div class="grid gap-section sm:grid-cols-2 lg:grid-cols-4">
  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-lime-100 dark:bg-lime-900/30 p-3">
        <flux:icon.utensils class="size-6 text-lime-600 dark:text-lime-400" />
      </div>
      <div>
        <flux:text class="text-sm text-zinc-500">Total Recipes</flux:text>
        <flux:heading size="xl">{{ number_format($this->globalStats['recipes']) }}</flux:heading>
      </div>
    </div>
  </flux:card>

  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-amber-100 dark:bg-amber-900/30 p-3">
        <flux:icon.carrot class="size-6 text-amber-600 dark:text-amber-400" />
      </div>
      <div>
        <flux:text class="text-sm text-zinc-500">Ingredients</flux:text>
        <flux:heading size="xl">{{ number_format($this->globalStats['ingredients']) }}</flux:heading>
      </div>
    </div>
  </flux:card>

  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-sky-100 dark:bg-sky-900/30 p-3">
        <flux:icon.calendar class="size-6 text-sky-600 dark:text-sky-400" />
      </div>
      <div>
        <flux:text class="text-sm text-zinc-500">Weekly Menus</flux:text>
        <flux:heading size="xl">{{ number_format($this->globalStats['menus']) }}</flux:heading>
      </div>
    </div>
  </flux:card>

  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-violet-100 dark:bg-violet-900/30 p-3">
        <flux:icon.globe class="size-6 text-violet-600 dark:text-violet-400" />
      </div>
      <div>
        <flux:text class="text-sm text-zinc-500">Countries</flux:text>
        <flux:heading size="xl">{{ number_format($this->globalStats['countries']) }}</flux:heading>
      </div>
    </div>
  </flux:card>
</div>

{{-- Additional Stats --}}
<div class="grid gap-section sm:grid-cols-3">
  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-rose-100 dark:bg-rose-900/30 p-3">
        <flux:icon.tag class="size-6 text-rose-600 dark:text-rose-400" />
      </div>
      <div>
        <flux:text class="text-sm text-zinc-500">Tags</flux:text>
        <flux:heading size="lg">{{ number_format($this->globalStats['tags']) }}</flux:heading>
      </div>
    </div>
  </flux:card>

  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-red-100 dark:bg-red-900/30 p-3">
        <flux:icon.triangle-alert class="size-6 text-red-600 dark:text-red-400" />
      </div>
      <div>
        <flux:text class="text-sm text-zinc-500">Allergens</flux:text>
        <flux:heading size="lg">{{ number_format($this->globalStats['allergens']) }}</flux:heading>
      </div>
    </div>
  </flux:card>

  <flux:card>
    <div class="flex items-center gap-section">
      <div class="rounded-lg bg-teal-100 dark:bg-teal-900/30 p-3">
        <flux:icon.chef-hat class="size-6 text-teal-600 dark:text-teal-400" />
      </div>
      <div>
        <flux:text class="text-sm text-zinc-500">Cuisines</flux:text>
        <flux:heading size="lg">{{ number_format($this->globalStats['cuisines']) }}</flux:heading>
      </div>
    </div>
  </flux:card>
</div>
