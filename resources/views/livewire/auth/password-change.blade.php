<flux:main container class="space-y-section">
  <flux:heading size="xl">{{ __('Settings') }}</flux:heading>

  <flux:card>
    <flux:heading size="lg" class="mb-section">{{ __('Account Settings') }}</flux:heading>

    <form wire:submit="updateAccount" class="space-y-section max-w-md">
    <flux:input
      wire:model="email"
      type="email"
      :label="__('Email')"
      required
    />

    <flux:separator />

    <flux:input
      wire:model="password"
      type="password"
      :label="__('New Password')"
      :description="__('Leave empty to keep current password')"
    />

    <flux:input
      wire:model="password_confirmation"
      type="password"
      :label="__('Confirm New Password')"
    />

    <flux:separator />

    <flux:input
      wire:model="current_password"
      type="password"
      :label="__('Current Password')"
      :description="__('Required to save changes')"
      required
    />

    <flux:button type="submit" variant="primary">
      {{ __('Save Changes') }}
    </flux:button>
  </form>
  </flux:card>
</flux:main>
