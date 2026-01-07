<flux:main container class="space-y-section">
    <div class="text-center">
        <flux:heading size="lg">Verify your email</flux:heading>
        <flux:text class="mt-ui">
            We've sent a verification link to <strong>{{ auth()->user()?->email }}</strong>.
            Please check your inbox and click the link to verify your email address.
        </flux:text>
    </div>

    <flux:callout icon="information-circle" color="blue">
        <flux:callout.text>
            If you didn't receive the email, check your spam folder or click the button below to resend.
        </flux:callout.text>
    </flux:callout>

    <flux:button wire:click="resend" variant="primary" class="w-full">
        Resend verification email
    </flux:button>

    <div class="text-center">
        <flux:link :href="route('portal.dashboard')" variant="subtle" wire:navigate>
            Skip for now
        </flux:link>
    </div>
</flux:main>
