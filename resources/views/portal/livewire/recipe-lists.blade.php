<div class="space-y-section">
    @if ($viewingListId && $this->viewingList)
        {{-- Viewing a specific list --}}
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <flux:button wire:click="backToLists" variant="ghost" icon="arrow-left" size="sm">
                    {{ __('Back to Lists') }}
                </flux:button>
                <flux:heading size="xl" class="truncate">{{ $this->viewingList->name }}</flux:heading>
            </div>
        </div>

        @if ($this->viewingList->description)
            <flux:text>{{ $this->viewingList->description }}</flux:text>
        @endif

        @if ($this->viewingList->recipes->isEmpty())
            <flux:card class="text-center py-12">
                <flux:icon.list class="mx-auto size-16 text-zinc-300 dark:text-zinc-600" />
                <flux:heading size="lg" class="mt-4">{{ __('This list is empty') }}</flux:heading>
            </flux:card>
        @else
            <flux:text variant="subtle">
                {{ trans_choice(':count recipe total|:count recipes total', $this->viewingList->recipes->count(), ['count' => $this->viewingList->recipes->count()]) }}
            </flux:text>

            @foreach ($this->recipesByCountry as $group)
                <div class="space-y-ui">
                    <div class="flex items-center gap-ui">
                        @if ($group['country'])
                            <img
                                src="{{ $group['country']->flag_url }}"
                                alt="{{ $group['country']->name }}"
                                class="h-5 w-auto rounded"
                            >
                            <flux:heading size="lg">{{ $group['country']->name }}</flux:heading>
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
                                            alt="{{ $recipe->name }}"
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
                                                onConfirm: () => $wire.removeRecipeFromList({{ $recipe->id }})
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
                                        {{ $recipe->name }}
                                    </flux:heading>

                                    @if ($recipe->headline)
                                        <flux:text class="mt-1 line-clamp-2">{{ $recipe->headline }}</flux:text>
                                    @endif
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @else
        {{-- List overview --}}
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
                    <flux:card
                        wire:key="list-{{ $list->id }}"
                        class="cursor-pointer hover:shadow-lg transition-shadow"
                        wire:click="viewList({{ $list->id }})"
                    >
                        <div class="flex-1 min-w-0">
                            <flux:heading size="lg" class="truncate">{{ $list->name }}</flux:heading>
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
    @endif
</div>
