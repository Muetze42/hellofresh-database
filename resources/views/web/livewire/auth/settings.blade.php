<flux:main container class="space-y-section">
    <flux:heading size="xl">{{ __('Settings') }}</flux:heading>

    {{-- Profile Information --}}
    <flux:card>
        <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
        <flux:text class="mt-ui">{{ __("Update your account's profile information and email address.") }}</flux:text>

        <form wire:submit="updateProfile" class="mt-section space-y-section max-w-md">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                :placeholder="__('Your name')"
                required
            />

            <flux:input
                wire:model="email"
                type="email"
                :label="__('Email')"
                placeholder="you@example.com"
                required
            />

            <x-country-select wire:model="country_code" />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </flux:card>

    {{-- Support --}}
    <flux:card>
        <flux:heading size="lg">{{ __('Support') }}</flux:heading>
        <flux:text class="mt-ui">{{ __('Your User ID for support requests:') }}</flux:text>
        <flux:badge class="mt-ui font-mono" size="lg">{{ auth()->id() }}</flux:badge>
    </flux:card>

    {{-- Update Password --}}
    <flux:card>
        <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>
        <flux:text class="mt-ui">{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:text>

        <form wire:submit="updatePassword" class="mt-section space-y-section max-w-md">
            <flux:input
                wire:model="current_password"
                type="password"
                :label="__('Current Password')"
                :placeholder="__('Your current password')"
                required
            />

            <flux:input
                wire:model="password"
                type="password"
                :label="__('New Password')"
                :placeholder="__('Your new password')"
                required
            />

            <flux:input
                wire:model="password_confirmation"
                type="password"
                :label="__('Confirm Password')"
                :placeholder="__('Confirm your new password')"
                required
            />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ __('Update Password') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</flux:main>
