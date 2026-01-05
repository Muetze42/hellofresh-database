@php
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
      <div class="flex items-center gap-2">
        <x-flag :code="$code" class="inline-block align-middle"/> {{ $countryName }}
      </div>

    </flux:select.option>
  @endforeach
</flux:select>
