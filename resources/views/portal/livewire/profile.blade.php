<div class="space-y-section">
    <flux:heading size="xl">Profile</flux:heading>

    {{-- Profile Information --}}
    <flux:card>
        <flux:heading size="lg">Profile Information</flux:heading>
        <flux:text class="mt-ui">{{ __("Update your account's profile information and email address.") }}</flux:text>

        <form wire:submit="updateProfile" class="mt-section space-y-section">
            <flux:input
                wire:model="name"
                label="Name"
                placeholder="Your name"
                required
            />

            <flux:input
                wire:model="email"
                type="email"
                label="Email"
                placeholder="you@example.com"
                required
            />

            <x-country-select wire:model="country_code" />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    Save
                </flux:button>
            </div>
        </form>
    </flux:card>

    {{-- Update Password --}}
    <flux:card>
        <flux:heading size="lg">Update Password</flux:heading>
        <flux:text class="mt-ui">{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:text>

        <form wire:submit="updatePassword" class="mt-section space-y-section">
            <flux:input
                wire:model="current_password"
                type="password"
                label="Current Password"
                :placeholder="__('Your current password')"
                required
            />

            <flux:input
                wire:model="password"
                type="password"
                label="New Password"
                :placeholder="__('Your new password')"
                required
            />

            <flux:input
                wire:model="password_confirmation"
                type="password"
                label="Confirm Password"
                :placeholder="__('Confirm your new password')"
                required
            />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    Update Password
                </flux:button>
            </div>
        </form>
    </flux:card>

    {{-- Delete Account --}}
    <flux:card class="border-red-200 dark:border-red-800">
        <flux:heading size="lg" class="text-red-600 dark:text-red-400">Delete Account</flux:heading>
        <flux:text class="mt-ui">
            Once your account is deleted, all of its resources and data will be permanently deleted.
            This action cannot be undone.
        </flux:text>

        <form wire:submit="deleteAccount" class="mt-section space-y-section">
            <flux:input
                wire:model="delete_confirmation"
                label="Type DELETE to confirm"
                placeholder="DELETE"
                required
            />

            <div class="flex justify-end">
                <flux:button type="submit" variant="danger">
                    Delete Account
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
