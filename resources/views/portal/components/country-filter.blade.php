@props([
    'countries',
])

<flux:select
    variant="listbox"
    placeholder="All Countries"
    clearable
    {{ $attributes->class(['sm:w-48']) }}
>
    @foreach($countries as $country)
        <flux:select.option :value="$country->id">
            <div class="flex items-center gap-ui">
                <x-flag :code="$country->code" /> {{ $country->code }}
            </div>
        </flux:select.option>
    @endforeach
</flux:select>
