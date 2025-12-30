<div>
    <flux:modal name="global-search" variant="bare" class="w-full max-w-lg my-[12vh] max-h-screen overflow-y-hidden">
        <div class="bg-white dark:bg-zinc-700 rounded-xl shadow-lg overflow-hidden flex flex-col max-h-[76vh]">
            {{-- Search Input --}}
            <div class="relative border-b border-zinc-200 dark:border-zinc-600">
                <flux:icon.search class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-zinc-400" />
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search recipes...') }}"
                    class="w-full pl-12 pr-12 py-3 bg-transparent text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none"
                    autofocus
                />
                <button
                    type="button"
                    x-on:click="$flux.modal('global-search').close()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                >
                    <flux:icon.x class="size-5" />
                </button>
            </div>

            {{-- Results --}}
            <div class="overflow-y-auto p-1">
                @if (blank($search))
                    <div class="px-3 py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Type to search for recipes...') }}
                    </div>
                @elseif ($this->recipes->isEmpty())
                    <div class="px-3 py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('No results found.') }}
                    </div>
                @else
                    @foreach ($this->recipes as $recipe)
                        <button
                            type="button"
                            wire:key="recipe-{{ $recipe->id }}"
                            wire:click="selectRecipe({{ $recipe->id }})"
                            class="w-full flex items-center gap-3 px-2 py-2 rounded-lg text-left hover:bg-zinc-100 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white"
                        >
                            <img
                                src="{{ $recipe->card_image_url }}"
                                alt=""
                                class="size-10 rounded object-cover shrink-0"
                            />
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="truncate">{{ $recipe->name }}</span>
                                @if ($recipe->menus->isNotEmpty())
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 truncate">
                                        {{ $recipe->menus->map(fn ($menu) => __('Week') . ' ' . substr((string) $menu->year_week, -2))->join(', ') }}
                                    </span>
                                @endif
                            </div>
                        </button>
                    @endforeach
                @endif
            </div>
        </div>
    </flux:modal>
</div>
