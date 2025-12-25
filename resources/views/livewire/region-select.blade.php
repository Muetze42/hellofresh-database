<flux:main container>
  <flux:heading size="xl" class="mb-section flex items-center gap-ui/2 justify-center">
    <flux:icon.hellofresh class="text-accent" />
    {{ __('Select your region') }}
  </flux:heading>

  <div class="grid grid-cols-1 gap-section sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($this->countries as $country)
      <flux:card wire:key="country-{{ $country->id }}" size="sm">
        <div class="flex items-center justify-center gap-ui mb-ui">
          <span class="text-2xl">{{ $this->getFlagEmoji($country->code) }}</span>
          <flux:heading size="lg">
            {{ __('country.' . $country->code) }}
          </flux:heading>
        </div>

        <div class="flex flex-wrap justify-center gap-ui">
          @foreach ($country->locales as $locale)
            <flux:button
              wire:key="locale-{{ $country->id }}-{{ $locale }}"
              :href="localized_route('localized.recipes.index', country: $country, locale: $locale)"
              variant="primary"
              size="sm"
              :lang="$locale . '-' . $country->code"
              wire:navigate
            >
              {{ __('language.' . $locale, [], $locale) }}
            </flux:button>
          @endforeach
        </div>
      </flux:card>
    @endforeach
  </div>
</flux:main>
