{{-- Recipes per Month --}}
<flux:card>
  <flux:heading size="lg">Recipes Added (Last 12 Months)</flux:heading>
  @php
    $maxCount = $this->recipesPerMonth->max('count') ?: 1;
    $totalCount = $this->recipesPerMonth->sum('count') ?: 1;
  @endphp
  <flux:table class="mt-section">
    <flux:table.columns>
      <flux:table.column class="ui-text-subtle">Month</flux:table.column>
      <flux:table.column class="ui-text-subtle" align="end">Recipes</flux:table.column>
      <flux:table.column class="ui-text-subtle" align="end">Share</flux:table.column>
      <flux:table.column class="ui-text-subtle w-1/2">Distribution</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
      @foreach($this->recipesPerMonth as $month)
        @php
          $barWidth = ($month->count / $maxCount) * 100;
          $sharePercent = ($month->count / $totalCount) * 100;
        @endphp
        <flux:table.row wire:key="month-{{ $month->month }}">
          <flux:table.cell class="font-medium">{{ \Carbon\Carbon::parse($month->month . '-01')->format('M Y') }}</flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">{{ number_format($month->count) }}</flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums text-zinc-500">{{ number_format($sharePercent, 1) }}%</flux:table.cell>
          <flux:table.cell>
            <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2">
              <div class="bg-lime-500 dark:bg-lime-600 h-2 rounded-full" style="width: {{ $barWidth }}%"></div>
            </div>
          </flux:table.cell>
        </flux:table.row>
      @endforeach
    </flux:table.rows>
  </flux:table>
</flux:card>
