@php
    use Illuminate\Support\Str;

    $countries = __('country');
    asort($countries);
@endphp

<flux:select
    variant="listbox"
    searchable
    clearable
    :label="__('Country')"
    :placeholder="__('Select your country (optional)')"
    {{ $attributes }}
>
    @foreach ($countries as $code => $countryName)
        <flux:select.option :value="$code">
            {{ Str::countryFlag($code) }} {{ $countryName }}
        </flux:select.option>
    @endforeach
</flux:select>
