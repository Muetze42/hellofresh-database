<flux:main container class="space-y-section">
  <x-portal::email-not-verified />
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item :href="route('portal.resources.index')" wire:navigate>Resources</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Tags</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <flux:heading size="xl">Tags</flux:heading>

  {{-- Filters --}}
  <flux:card>
    <div class="flex flex-col sm:flex-row gap-section">
      <flux:select wire:model.live="countryId" variant="listbox" placeholder="All Countries" clearable class="sm:w-48">
        @foreach($this->countries as $country)
          <flux:select.option :value="$country->id">
            <div class="flex items-center gap-ui">
              <x-flag :code="$country->code" /> {{ $country->code }}
            </div>
          </flux:select.option>
        @endforeach
      </flux:select>
      <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search tags..."
        icon="search"
        class="flex-1"
      />
    </div>
  </flux:card>

  {{-- Table --}}
  <flux:card>
    <flux:table>
      <flux:table.columns>
        <flux:table.column class="w-id-col" sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" wire:click="sort('id')">ID</flux:table.column>
        <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
        <flux:table.column class="w-1">Country</flux:table.column>
        <flux:table.column sortable class="w-1" :sorted="$sortBy === 'cached_recipes_count'" :direction="$sortDirection" wire:click="sort('cached_recipes_count')">Recipes</flux:table.column>
        <flux:table.column class="w-24" sortable :sorted="$sortBy === 'active'" :direction="$sortDirection" wire:click="sort('active')">Status</flux:table.column>
        <flux:table.column class="w-1" sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created</flux:table.column>
        <flux:table.column class="w-1" sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">Updated</flux:table.column>
      </flux:table.columns>
      <flux:table.rows>
        @forelse($this->resources as $resource)
          <flux:table.row wire:key="resource-{{ $resource->id }}">
            <flux:table.cell align="end">{{ $resource->id }}</flux:table.cell>
            <flux:table.cell>{{ $resource->getTranslation('name', $resource->country->locales[0] ?? 'en') }}</flux:table.cell>
            <flux:table.cell><x-flag :code="$resource->country->code" /> {{ $resource->country->code ?? '—' }}</flux:table.cell>
            <flux:table.cell align="end">{{ Number::format($resource->cached_recipes_count ?? 0) }}</flux:table.cell>
            <flux:table.cell align="center">
              @if($resource->active)
                <flux:badge color="green" size="sm">Active</flux:badge>
              @else
                <flux:badge color="zinc" size="sm">Inactive</flux:badge>
              @endif
            </flux:table.cell>
            <flux:table.cell align="end">{{ $resource->created_at->toDateTimeString() }}</flux:table.cell>
            <flux:table.cell align="end">{{ $resource->updated_at->toDateTimeString() }}</flux:table.cell>
          </flux:table.row>
        @empty
          <flux:table.row>
            <flux:table.cell colspan="7" class="text-center py-section">
              <flux:text variant="subtle">No tags found.</flux:text>
            </flux:table.cell>
          </flux:table.row>
        @endforelse
      </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$this->resources" class="mt-section" />
  </flux:card>

  <flux:text class="text-sm text-zinc-500 text-center">
    Recipe counts are cached and updated periodically.
  </flux:text>
</flux:main>
