<div class="space-y-section">
  <flux:breadcrumbs>
    <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>Terms of Use</flux:breadcrumbs.item>
  </flux:breadcrumbs>

  <flux:heading size="xl">Terms of Use</flux:heading>

  <flux:card class="flex flex-col gap-section">
    {!! $this->content !!}
  </flux:card>
</div>
