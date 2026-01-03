{{-- Recipe Quality & Data Health --}}
<div class="grid gap-section lg:grid-cols-2">
  <flux:card>
    <flux:heading size="lg">Recipe Quality</flux:heading>
    <div class="mt-section space-y-4">
      <div class="flex justify-between items-center">
        <flux:text>Recipes with PDF</flux:text>
        <div class="text-right">
          <flux:heading size="lg" class="tabular-nums inline-flex gap-1 items-baseline">
            <flux:text class="text-sm text-zinc-500">{{ $this->recipeQuality['pdf_percentage'] }}%</flux:text>
            {{ number_format($this->recipeQuality['with_pdf']) }}
          </flux:heading>
        </div>
      </div>
      <div class="flex justify-between items-center">
        <flux:text>Recipes without Image</flux:text>
        <flux:heading size="lg" class="tabular-nums {{ $this->recipeQuality['without_image'] > 0 ? 'text-amber-600 dark:text-amber-400' : '' }}">
          {{ number_format($this->recipeQuality['without_image']) }}
        </flux:heading>
      </div>
      <div class="flex justify-between items-center">
        <flux:text>Recipes without Nutrition</flux:text>
        <flux:heading size="lg" class="tabular-nums {{ $this->recipeQuality['without_nutrition'] > 0 ? 'text-amber-600 dark:text-amber-400' : '' }}">
          {{ number_format($this->recipeQuality['without_nutrition']) }}
        </flux:heading>
      </div>
    </div>
  </flux:card>

  <flux:card>
    <flux:heading size="lg">Data Health</flux:heading>
    <div class="mt-section space-y-4">
      <div class="flex justify-between items-center">
        <flux:text>Orphan Ingredients</flux:text>
        <flux:heading size="lg" class="tabular-nums {{ $this->dataHealth['orphan_ingredients'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-green-600 dark:text-green-400' }}">
          {{ number_format($this->dataHealth['orphan_ingredients']) }}
        </flux:heading>
      </div>
      <div class="flex justify-between items-center">
        <flux:text>Inactive Countries</flux:text>
        <flux:heading size="lg" class="tabular-nums">{{ number_format($this->dataHealth['inactive_countries']) }}</flux:heading>
      </div>
      <div class="flex justify-between items-center">
        <flux:text>Recipes without Tags</flux:text>
        <flux:heading size="lg" class="tabular-nums {{ $this->dataHealth['recipes_without_tags'] > 0 ? 'text-amber-600 dark:text-amber-400' : '' }}">
          {{ number_format($this->dataHealth['recipes_without_tags']) }}
        </flux:heading>
      </div>
    </div>
  </flux:card>
</div>
