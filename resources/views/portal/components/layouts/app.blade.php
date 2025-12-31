@props([
    'title' => null,
])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? $title . ' - ' . config('app.name') . ' API' : config('app.name') . ' API Portal' }}</title>
    <x-web::layouts.partials.favicon />
    @vite(['resources/css/portal/app.css'], 'build-portal')
    @livewireStyles
    @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
<flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <flux:brand href="{{ route('portal.dashboard') }}" logo="earth" name="{{ config('app.name') }} API" class="px-2" />

    <flux:navlist variant="outline">
        @auth
            <flux:navlist.item icon="key" href="{{ route('portal.tokens.index') }}" :current="request()->routeIs('portal.tokens.*')">
                API Tokens
            </flux:navlist.item>
        @endauth

        <flux:navlist.group expandable heading="API Reference" class="mt-4">
            <flux:navlist.item href="{{ route('portal.docs.countries') }}" :current="request()->routeIs('portal.docs.countries')">
                Countries
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.recipes') }}" :current="request()->routeIs('portal.docs.recipes')">
                List Recipes
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.recipes-show') }}" :current="request()->routeIs('portal.docs.recipes-show')">
                Get Recipe
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.menus') }}" :current="request()->routeIs('portal.docs.menus')">
                List Menus
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.menus-show') }}" :current="request()->routeIs('portal.docs.menus-show')">
                Get Menu
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.tags') }}" :current="request()->routeIs('portal.docs.tags')">
                Tags
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.labels') }}" :current="request()->routeIs('portal.docs.labels')">
                Labels
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.allergens') }}" :current="request()->routeIs('portal.docs.allergens')">
                Allergens
            </flux:navlist.item>
            <flux:navlist.item href="{{ route('portal.docs.ingredients') }}" :current="request()->routeIs('portal.docs.ingredients')">
                Ingredients
            </flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer />

    @auth
        <flux:dropdown position="top" align="start" class="w-full">
            <flux:profile name="{{ auth()->user()->name }}" class="w-full cursor-pointer" />

            <flux:menu>
                <flux:menu.item icon="user" href="{{ route('portal.profile') }}">Profile</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item icon="log-out" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <form id="logout-form" action="{{ route('portal.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    @else
        <flux:navlist variant="outline">
            <flux:navlist.item icon="log-in" href="{{ route('portal.login') }}">
                Sign In
            </flux:navlist.item>
        </flux:navlist>
    @endauth
</flux:sidebar>

<flux:header sticky class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:brand href="{{ route('portal.dashboard') }}" logo="earth" name="{{ config('app.name') }} API" />
</flux:header>

<flux:main class="space-y-section" container>
    @auth
        @if(!auth()->user()->hasVerifiedEmail())
            <flux:callout icon="triangle-alert" color="amber">
                <flux:callout.heading>Email Not Verified</flux:callout.heading>
                <flux:callout.text>
                    Please verify your email address to access all API features.
                    <flux:link href="{{ route('portal.verification.notice') }}">Resend verification email</flux:link>
                </flux:callout.text>
            </flux:callout>
        @endif
    @endauth

    {{ $slot }}
</flux:main>

<flux:toast.group position="bottom end">
    <flux:toast />
</flux:toast.group>

@vite(['resources/js/portal/app.js'], 'build-portal')
@livewireScripts
@fluxScripts
</body>
</html>
