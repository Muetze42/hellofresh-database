<div class="space-y-section">
    <div class="text-center">
        <flux:heading size="lg">Forgot your password?</flux:heading>
        <flux:text class="mt-ui">
            Enter your email address and we'll send you a link to reset your password.
        </flux:text>
    </div>

    @if ($linkSent)
        <flux:callout variant="success" icon="check-circle">
            We have emailed your password reset link. Please check your inbox.
        </flux:callout>

        <div class="text-center">
            <flux:link :href="route('portal.login')" wire:navigate>Back to login</flux:link>
        </div>
    @else
        <form wire:submit="sendResetLink" class="space-y-section">
            <flux:input
                wire:model="email"
                type="email"
                label="Email"
                placeholder="you@example.com"
                required
                autofocus
            />

            <flux:button type="submit" variant="primary" class="w-full">
                Send reset link
            </flux:button>
        </form>

        <div class="text-center">
            <flux:link :href="route('portal.login')" wire:navigate>Back to login</flux:link>
        </div>
    @endif
</div>
