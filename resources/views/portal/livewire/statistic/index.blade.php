<div class="space-y-section">
  <div>
    <flux:heading size="xl">Database Statistics</flux:heading>
    <flux:text class="mt-ui">Real-time statistics about the {{ config('app.name') }} database.</flux:text>
  </div>

  @include('portal::livewire.statistic.partials.global-stats')
  @include('portal::livewire.statistic.partials.quality-health')
  @include('portal::livewire.statistic.partials.user-engagement')
  @include('portal::livewire.statistic.partials.country-stats')
  @include('portal::livewire.statistic.partials.top-lists')
  @include('portal::livewire.statistic.partials.recipes-per-month')
  @include('portal::livewire.statistic.partials.prep-times')
  @include('portal::livewire.statistic.partials.difficulty')
  @include('portal::livewire.statistic.partials.newest-recipes')

  <flux:text class="text-sm text-zinc-500 text-center">
    Statistics are cached and refresh every hour.
  </flux:text>
</div>
