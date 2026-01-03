{{-- Users by Country --}}
<flux:card>
  <flux:heading size="lg">Users by Country</flux:heading>
  <flux:table class="mt-section">
    <flux:table.columns>
      <flux:table.column class="ui-text-subtle">Country</flux:table.column>
      <flux:table.column class="ui-text-subtle" align="end">Users</flux:table.column>
    </flux:table.columns>
    <flux:table.rows>
      @foreach($this->usersByCountry as $countryStat)
        <flux:table.row wire:key="user-country-{{ $countryStat->country_code ?? 'unknown' }}">
          <flux:table.cell>
            @if($countryStat->country_code)
              {{ Str::countryFlag($countryStat->country_code) }} {{ __('country.' . $countryStat->country_code) }}
            @else
              🌐 Not set
            @endif
          </flux:table.cell>
          <flux:table.cell align="end" class="tabular-nums">{{ number_format($countryStat->count) }}</flux:table.cell>
        </flux:table.row>
      @endforeach
    </flux:table.rows>
  </flux:table>
</flux:card>
