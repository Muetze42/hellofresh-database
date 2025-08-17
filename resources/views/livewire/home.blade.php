<flux:main container>
  <flux:card class="flex flex-col gap-4">
    <div>
      <flux:button>Default</flux:button>
      <flux:button variant="primary">Primary</flux:button>
      <flux:button variant="filled">Filled</flux:button>
      <flux:button variant="danger">Danger</flux:button>
      <flux:button variant="ghost">Ghost</flux:button>
      <flux:button variant="subtle">Subtle</flux:button>
    </div>
    <div>
      <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="inline-flex">
        <flux:radio value="light" icon="sun" />
        <flux:radio value="dark" icon="moon" />
        <flux:radio value="system" icon="computer-desktop" />
      </flux:radio.group>
    </div>
  </flux:card>
</flux:main>
