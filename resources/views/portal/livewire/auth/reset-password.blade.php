<div class="space-y-section">
    <div class="text-center">
        <flux:heading size="lg">Reset your password</flux:heading>
        <flux:text class="mt-ui">
            Enter your new password below.
        </flux:text>
    </div>

    <form wire:submit="resetPassword" class="space-y-section">
        <flux:input
            wire:model="email"
            type="email"
            label="Email"
            placeholder="you@example.com"
            required
            autofocus
        />

        <flux:input
            wire:model="password"
            type="password"
            label="New Password"
            placeholder="Your new password"
            required
        />

        <flux:input
            wire:model="password_confirmation"
            type="password"
            label="Confirm Password"
            placeholder="Confirm your new password"
            required
        />

        <flux:button type="submit" variant="primary" class="w-full">
            Reset password
        </flux:button>
    </form>
</div>
