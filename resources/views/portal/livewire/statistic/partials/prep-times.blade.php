{{-- Average Prep Times by Country --}}
<flux:card>
  <flux:heading size="lg">Average Prep Times by Country</flux:heading>
  <flux:table class="mt-section">
    <flux:table.columns>
      <flux:table.column class="ui-text-subtle">Country</flux:table.column>
      <flux:table.column class="ui-text-subtle" align="end">Avg. Prep Time</flux:table.column>
      <flux:table.column class="ui-text-subtle" align="end">Avg. Total Time</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
      @foreach($this->avgPrepTimesByCountry as $country)
        <flux:table.row wire:key="prep-{{ $country->code }}">
          <flux:table.cell>
            <span class="font-medium">{{ Str::countryFlag($country->code) }} {{ $country->code }}</span>
          </flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">{{ $country->avg_prep }} min</flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">{{ $country->avg_total }} min</flux:table.cell>
        </flux:table.row>
      @endforeach
    </flux:table.rows>
  </flux:table>
</flux:card>
