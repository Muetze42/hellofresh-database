<div class="space-y-section">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>API Reference</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div>
        <flux:heading size="xl">API Reference</flux:heading>
        <flux:text class="mt-ui">Complete documentation for the {{ config('app.name') }} API.</flux:text>
    </div>

    {{-- Getting Started --}}
    <flux:card>
        <div class="flex items-center gap-section">
            <div class="rounded-lg bg-emerald-100 dark:bg-emerald-900/30 p-3">
                <flux:icon.rocket class="size-6 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div class="flex-1">
                <flux:heading size="lg">Get Started</flux:heading>
                <flux:text class="text-sm text-zinc-500">Authentication, rate limiting, and basic usage</flux:text>
            </div>
            <flux:button :href="route('portal.docs.get-started')" wire:navigate variant="primary">
                Read Guide
            </flux:button>
        </div>
    </flux:card>

    {{-- Endpoints --}}
    <flux:heading size="lg">Endpoints</flux:heading>

    <div class="grid gap-section sm:grid-cols-2 lg:grid-cols-3">
        <flux:card>
            <flux:heading size="lg">Countries</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">List available countries and locales</flux:text>
            <flux:button :href="route('portal.docs.countries')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">List Recipes</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">Search and filter recipes</flux:text>
            <flux:button :href="route('portal.docs.recipes')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Get Recipe</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">Retrieve a single recipe with details</flux:text>
            <flux:button :href="route('portal.docs.recipes-show')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">List Menus</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">Browse weekly menus</flux:text>
            <flux:button :href="route('portal.docs.menus')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Get Menu</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">Retrieve a single menu with recipes</flux:text>
            <flux:button :href="route('portal.docs.menus-show')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Tags</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">List recipe tags and categories</flux:text>
            <flux:button :href="route('portal.docs.tags')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Labels</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">List recipe labels</flux:text>
            <flux:button :href="route('portal.docs.labels')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Allergens</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">List allergen information</flux:text>
            <flux:button :href="route('portal.docs.allergens')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>

        <flux:card>
            <flux:heading size="lg">Ingredients</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-ui">List available ingredients</flux:text>
            <flux:button :href="route('portal.docs.ingredients')" wire:navigate variant="ghost" class="mt-section w-full">
                View Documentation
            </flux:button>
        </flux:card>
    </div>
</div>
