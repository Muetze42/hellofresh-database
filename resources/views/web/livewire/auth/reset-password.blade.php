<flux:main container class="flex items-center justify-center min-h-[60vh]">
  <flux:card class="max-w-md w-full">
    <div class="flex flex-col gap-section">
      <div>
        <flux:heading size="lg">{{ __('Reset Password') }}</flux:heading>
        <flux:text class="mt-ui">{{ __('Enter your new password below.') }}</flux:text>
      </div>

      <form wire:submit="resetPassword" class="space-y-section">
        <flux:field>
          <flux:label>{{ __('Email') }}</flux:label>
          <flux:input wire:model="email" type="email" placeholder="{{ __('your@email.com') }}" />
          <flux:error name="email" />
        </flux:field>

        <flux:field>
          <flux:label>{{ __('New Password') }}</flux:label>
          <flux:input wire:model="password" type="password" />
          <flux:error name="password" />
        </flux:field>

        <flux:field>
          <flux:label>{{ __('Confirm Password') }}</flux:label>
          <flux:input wire:model="password_confirmation" type="password" />
          <flux:error name="password_confirmation" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full">
          {{ __('Reset Password') }}
        </flux:button>
      </form>
    </div>
  </flux:card>
</flux:main>
