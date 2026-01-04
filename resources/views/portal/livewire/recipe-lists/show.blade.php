<div class="space-y-section">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item :href="route('portal.dashboard')" wire:navigate>Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item :href="route('portal.recipe-lists.index')" wire:navigate>{{ __('Recipe Lists') }}</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $recipeList->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">{{ $recipeList->name }}</flux:heading>

    @if ($recipeList->description)
        <flux:text>{{ $recipeList->description }}</flux:text>
    @endif

    @if ($recipeList->recipes->isEmpty())
        <flux:card class="text-center py-12">
            <flux:icon.list class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
            <flux:heading size="lg" class="mt-4">{{ __('This list is empty') }}</flux:heading>
        </flux:card>
    @else
        <flux:text variant="subtle">
            {{ trans_choice(':count recipe total|:count recipes total', $recipeList->recipes->count(), ['count' => $recipeList->recipes->count()]) }}
        </flux:text>

        @foreach ($this->recipesByCountry as $group)
            <div class="space-y-ui">
                <div class="flex items-center gap-ui">
                    @if ($group['country'])
                        <flux:heading size="lg">{{ Str::countryFlag($group['country']->code) }} {{ __('country.' . $group['country']->code) }}</flux:heading>
                    @else
                        <flux:heading size="lg">{{ __('Unknown Country') }}</flux:heading>
                    @endif
                    <flux:badge size="sm">{{ $group['recipes']->count() }}</flux:badge>
                </div>

                <div class="grid grid-cols-1 gap-section sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($group['recipes'] as $recipe)
                        <flux:card wire:key="recipe-{{ $recipe->id }}" class="overflow-hidden">
                            <div class="relative">
                                @if ($recipe->card_image_url)
                                    <img
                                        src="{{ $recipe->card_image_url }}"
                                        alt="{{ $recipe->name ?: $recipe->getFirstTranslation('name') }}"
                                        class="aspect-video w-full object-cover"
                                    >
                                @endif

                                <div class="absolute top-2 right-2">
                                    <button
                                        type="button"
                                        x-on:click.stop="$dispatch('confirm-action', {
                                            title: '{{ __('Remove from list') }}',
                                            message: '{{ __('Remove this recipe from the list?') }}',
                                            confirmText: '{{ __('Remove') }}',
                                            onConfirm: () => $wire.removeRecipe({{ $recipe->id }})
                                        })"
                                        class="rounded-full p-2 bg-red-500 text-white hover:bg-red-600 transition-colors"
                                        title="{{ __('Remove from list') }}"
                                    >
                                        <flux:icon.x variant="mini" />
                                    </button>
                                </div>
                            </div>

                            <div class="p-4">
                                <flux:heading size="lg" class="line-clamp-1">
                                    {{ $recipe->name ?: $recipe->getFirstTranslation('name') }}
                                </flux:heading>

                                @if ($recipe->headline ?: $recipe->getFirstTranslation('headline'))
                                    <flux:text class="mt-1 line-clamp-2">{{ $recipe->headline ?: $recipe->getFirstTranslation('headline') }}</flux:text>
                                @endif
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
