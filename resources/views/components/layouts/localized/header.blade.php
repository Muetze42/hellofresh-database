<flux:header sticky class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 print:hidden">
    <flux:brand :href="route('region.select')">
        <x-slot name="logo">
            <flux:icon name="earth" variant="micro" />
        </x-slot>
    </flux:brand>

    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />

    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item icon="book-open" :href="localized_route('localized.recipes.index')" wire:navigate>
            {{ __('Recipes') }}
        </flux:navbar.item>

        <flux:navbar.item icon="calendar" :href="localized_route('localized.menus.index')" wire:navigate>
            {{ __('Menu') }}
        </flux:navbar.item>

        <flux:navbar.item icon="shopping-basket" :href="localized_route('localized.shopping-list.index')" wire:navigate>
            {{ __('Shopping List') }}
            <span
                x-data
                x-show="$store.shoppingList && $store.shoppingList.count > 0"
                x-text="$store.shoppingList ? $store.shoppingList.count : ''"
                class="ml-1 inline-flex items-center justify-center size-5 text-xs font-medium rounded-full bg-green-500 text-white"
            ></span>
        </flux:navbar.item>
    </flux:navbar>

    <flux:spacer />

    <flux:navbar class="gap-ui">
        {{-- Dark Mode Toggle --}}
        <flux:dropdown x-data align="end">
            <flux:button variant="subtle" square class="group" aria-label="{{ __('Appearance') }}">
                <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="text-zinc-500 dark:text-white" />
                <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
                <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
            </flux:button>

            <flux:menu>
                <flux:menu.radio.group x-model="$flux.appearance">
                    <flux:menu.radio icon="sun" value="light">{{ __('Light') }}</flux:menu.radio>
                    <flux:menu.radio icon="moon" value="dark">{{ __('Dark') }}</flux:menu.radio>
                    <flux:menu.radio icon="monitor" value="system">{{ __('System') }}</flux:menu.radio>
                </flux:menu.radio.group>
            </flux:menu>
        </flux:dropdown>

        {{-- User Menu --}}
        @auth
            <flux:dropdown position="bottom" align="end">
                <flux:button variant="subtle" square>
                    <flux:icon.user variant="mini" />
                </flux:button>

                <flux:menu>
                    <flux:menu.item icon="list" :href="localized_route('localized.lists')" wire:navigate>
                        {{ __('My Lists') }}
                    </flux:menu.item>
                    <flux:menu.item icon="bookmark" :href="localized_route('localized.saved-shopping-lists')" wire:navigate>
                        {{ __('Saved Shopping Lists') }}
                    </flux:menu.item>
                    <flux:menu.item icon="settings" :href="localized_route('localized.settings')" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>

                    <flux:menu.separator />

                    <flux:menu.item icon="log-out" href="#" x-on:click.prevent="$dispatch('logout')">
                        {{ __('Logout') }}
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        @else
            <flux:button variant="subtle" x-on:click="$dispatch('require-auth')">
                {{ __('Login') }}
            </flux:button>
        @endauth
    </flux:navbar>
</flux:header>

<flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.header>
        <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
    </flux:sidebar.header>

    <flux:sidebar.nav>
        <flux:sidebar.item icon="earth" :href="route('region.select')">
            {{ __('Select your region') }}
        </flux:sidebar.item>

        <flux:separator class="my-ui" />

        <flux:sidebar.item icon="book-open" :href="localized_route('localized.recipes.index')" wire:navigate>
            {{ __('Recipes') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="calendar" :href="localized_route('localized.menus.index')" wire:navigate>
            {{ __('Menu') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="shopping-basket" :href="localized_route('localized.shopping-list.index')" wire:navigate>
            {{ __('Shopping List') }}
        </flux:sidebar.item>

        @auth
            <flux:sidebar.spacer />

            <flux:sidebar.item icon="list" :href="localized_route('localized.lists')" wire:navigate>
                {{ __('My Lists') }}
            </flux:sidebar.item>
            <flux:sidebar.item icon="bookmark" :href="localized_route('localized.saved-shopping-lists')" wire:navigate>
                {{ __('Saved Shopping Lists') }}
            </flux:sidebar.item>
            <flux:sidebar.item icon="settings" :href="localized_route('localized.settings')" wire:navigate>
                {{ __('Settings') }}
            </flux:sidebar.item>
        @endauth
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    <flux:sidebar.nav>
        {{-- Dark Mode Toggle for Mobile --}}
        <div class="px-3 py-2">
            <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="w-full">
                <flux:radio value="light" icon="sun" />
                <flux:radio value="dark" icon="moon" />
                <flux:radio value="system" icon="computer-desktop" />
            </flux:radio.group>
        </div>

        @guest
            <flux:sidebar.item icon="arrow-right-end-on-rectangle" x-on:click="$dispatch('require-auth')">
                {{ __('Login') }}
            </flux:sidebar.item>
        @else
            <flux:sidebar.item icon="arrow-right-start-on-rectangle" x-on:click="$dispatch('logout')">
                {{ __('Logout') }}
            </flux:sidebar.item>
        @endguest
    </flux:sidebar.nav>
</flux:sidebar>
