<flux:main container class="space-y-section">
    <x-portal::email-not-verified />

  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Privacy Policy</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <flux:heading size="xl">Privacy Policy</flux:heading>

  <flux:card class="flex flex-col gap-section">
    {!! $this->content !!}
  </flux:card>
</flux:main>
