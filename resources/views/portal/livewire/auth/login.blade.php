<div class="space-y-section">
    <div class="text-center">
        <flux:heading size="lg">Sign in to your account</flux:heading>
        <flux:text class="mt-ui">
            Or <flux:link :href="route('portal.register')" wire:navigate>create a new account</flux:link>
        </flux:text>
    </div>

    <form wire:submit="login" class="space-y-section">
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
            label="Password"
            placeholder="Your password"
            required
        />

        <flux:checkbox wire:model="remember" label="Remember me" />

        <flux:button type="submit" variant="primary" class="w-full">
            Sign in
        </flux:button>
    </form>
</div>
