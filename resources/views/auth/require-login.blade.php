<x-layouts.localized>
  <flux:main container>
    <flux:card class="text-center py-12">
      <flux:icon.lock class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
      <flux:heading size="lg" class="mt-4">{{ __('Login Required') }}</flux:heading>
      <flux:text class="mt-2">{{ __('Please log in to access this page.') }}</flux:text>
      <flux:button x-data x-on:click="$dispatch('require-auth')" variant="primary" class="mt-4">
        {{ __('Log In') }}
      </flux:button>
    </flux:card>
  </flux:main>
</x-layouts.localized>
