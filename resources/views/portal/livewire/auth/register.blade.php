<div class="space-y-section">
    <div class="text-center">
        <flux:heading size="lg">Create your account</flux:heading>
        <flux:text class="mt-ui">
            Already have an account? <flux:link :href="route('portal.login')" wire:navigate>Sign in</flux:link>
        </flux:text>
    </div>

    <form wire:submit="register" class="space-y-section">
        <flux:input
            wire:model="name"
            type="text"
            label="Name"
            placeholder="Your name"
            required
            autofocus
        />

        <flux:input
            wire:model="email"
            type="email"
            label="Email"
            placeholder="you@example.com"
            required
        />

        <x-country-select wire:model="country_code" />

        <flux:input
            wire:model="password"
            type="password"
            label="Password"
            placeholder="Create a password"
            required
        />

        <flux:input
            wire:model="password_confirmation"
            type="password"
            label="Confirm Password"
            placeholder="Confirm your password"
            required
        />

        <flux:field>
            <div class="flex items-start gap-ui">
                <flux:checkbox wire:model="acceptPrivacy" id="accept-privacy" />
                <flux:label for="accept-privacy" class="text-sm inline-flex gap-1 flex-wrap">
                    {{ __('I accept the') }}
                    <flux:link :href="route('portal.privacy')" target="_blank">{{ __('Privacy Policy') }}</flux:link>
                    {{ __('and') }}
                    <flux:link :href="route('portal.terms')" target="_blank">{{ __('Terms of Use') }}</flux:link>
                </flux:label>
            </div>
            <flux:error name="acceptPrivacy" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full">
            Create account
        </flux:button>
    </form>
</div>
