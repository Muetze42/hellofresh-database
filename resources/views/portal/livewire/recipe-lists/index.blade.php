<flux:main container class="space-y-section">
    <x-portal::email-not-verified />
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Recipe Lists') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">{{ __('My Recipe Lists') }}</flux:heading>

    <flux:callout icon="info" color="sky">
        {{ __('Here you can view all your recipe lists with recipes from all countries. On the main site, recipes are filtered by the currently selected country.') }}
    </flux:callout>

    @if ($this->recipeLists->isEmpty())
        <flux:card class="text-center py-12">
            <flux:icon.list class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="lg" class="mt-4">{{ __('No lists yet') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create lists on the main site to organize your recipes.') }}</flux:text>
        </flux:card>
    @else
        <div class="grid grid-cols-1 gap-section sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->recipeLists as $list)
                <flux:card wire:key="list-{{ $list->id }}">
                    <div class="flex-1 min-w-0">
                        <flux:heading size="lg" class="truncate">
                            <flux:link :href="route('portal.recipe-lists.show', $list)" wire:navigate>
                                {{ $list->name }}
                            </flux:link>
                        </flux:heading>
                        @if ($list->description)
                            <flux:text class="mt-1 line-clamp-2">{{ $list->description }}</flux:text>
                        @endif
                        <flux:badge class="mt-2" size="sm">
                            {{ trans_choice(':count Recipe|:count Recipes', $list->recipes_count, ['count' => $list->recipes_count]) }}
                        </flux:badge>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</flux:main>
