@props([
    'title' => null,
])
@php
  $pageTitle = $title ? $title . ' - ' . config('app.name') . ' API' : config('app.name') . ' API Portal';
@endphp
  <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $pageTitle }}</title>

  {{-- Open Graph / Social Media Meta Tags --}}
  <meta property="og:title" content="{{ $pageTitle }}">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ route('og.generic', ['title' => $pageTitle, 'font' => 'mono']) }}">
  <meta property="og:site_name" content="{{ config('app.name') }}">

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $pageTitle }}">
  <meta name="twitter:image" content="{{ route('og.generic', ['title' => $pageTitle, 'font' => 'mono']) }}">

  <x-partials.favicon />
  @vite(['resources/css/portal/app.css'], 'build-portal')
  @livewireStyles
  @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
<flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
  <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

  <flux:sidebar.brand
    :href="route('portal.dashboard')"
    :logo="asset('assets/favicons/' . app()->environment() . '.png')"
    name="{{ config('app.name') }} API"
  />

  <flux:sidebar.nav>
    @auth
      <flux:sidebar.item icon="key" :href="route('portal.tokens.index')" :current="request()->routeIs('portal.tokens.*')">
        API Tokens
      </flux:sidebar.item>
    @endauth

    <flux:sidebar.group expandable heading="API Reference">
      <flux:sidebar.item :href="route('portal.docs.get-started')" :current="request()->routeIs('portal.docs.get-started')" wire:navigate>
        Get Started
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.countries')" :current="request()->routeIs('portal.docs.countries')" wire:navigate>
        Countries
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.recipes')" :current="request()->routeIs('portal.docs.recipes')" wire:navigate>
        List Recipes
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.recipes-show')" :current="request()->routeIs('portal.docs.recipes-show')" wire:navigate>
        Get Recipe
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.menus')" :current="request()->routeIs('portal.docs.menus')" wire:navigate>
        List Menus
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.menus-show')" :current="request()->routeIs('portal.docs.menus-show')" wire:navigate>
        Get Menu
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.tags')" :current="request()->routeIs('portal.docs.tags')" wire:navigate>
        Tags
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.labels')" :current="request()->routeIs('portal.docs.labels')" wire:navigate>
        Labels
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.allergens')" :current="request()->routeIs('portal.docs.allergens')" wire:navigate>
        Allergens
      </flux:sidebar.item>
      <flux:sidebar.item :href="route('portal.docs.ingredients')" :current="request()->routeIs('portal.docs.ingredients')" wire:navigate>
        Ingredients
      </flux:sidebar.item>
    </flux:sidebar.group>
      @if(auth()->user()?->admin)
        <flux:sidebar.group expandable :expanded="false" heading="Admin" class="grid">
          <flux:sidebar.item icon="users" :href="route('portal.admin.users')" :current="request()->routeIs('portal.admin.users')" wire:navigate>
            Users
          </flux:sidebar.item>
          <flux:sidebar.item icon="activity" :href="route('portal.admin.api-usage')" :current="request()->routeIs('portal.admin.api-usage')" wire:navigate>
            API Usage
          </flux:sidebar.item>
        </flux:sidebar.group>
      @endif
  </flux:sidebar.nav>

  <flux:spacer />

  <flux:sidebar.nav>
    <flux:sidebar.item icon="chart-pie" :href="route('portal.stats')" :current="request()->routeIs('portal.stats')" wire:navigate>
      Statistics
    </flux:sidebar.item>
    <flux:sidebar.item icon="home" href="{{ config('app.url') }}" :current="false">
      {{ config('app.name') }}
    </flux:sidebar.item>
  </flux:sidebar.nav>

  @auth
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
      <flux:sidebar.profile name="{{ auth()->user()->name }}" />

      <flux:menu>
        <flux:menu.item icon="user" href="{{ route('portal.profile') }}">Profile</flux:menu.item>
        <flux:menu.item icon="home" href="{{ config('app.url') }}">{{ config('app.name') }}</flux:menu.item>
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
    <flux:sidebar.nav>
      <flux:sidebar.item icon="log-in" :href="route('portal.login')">
        Sign In
      </flux:sidebar.item>
    </flux:sidebar.nav>
  @endauth
  <div>
    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="w-full" size="sm">
      <flux:radio value="light" icon="sun" />
      <flux:radio value="dark" icon="moon" />
      <flux:radio value="system" icon="computer-desktop" />
    </flux:radio.group>
  </div>
</flux:sidebar>

<flux:header class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
  <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

  <flux:spacer />

  @auth
    <flux:dropdown position="top" align="end">
      <flux:profile name="{{ auth()->user()->name }}" />

      <flux:menu>
        <flux:menu.item icon="user" href="{{ route('portal.profile') }}">Profile</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item icon="log-out" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          Logout
        </flux:menu.item>
      </flux:menu>
    </flux:dropdown>
  @else
    <flux:button icon="log-in" variant="ghost" :href="route('portal.login')">Sign In</flux:button>
  @endauth
</flux:header>

<flux:main class="space-y-section sm:mb-16" container>
  @auth
    @if(!auth()->user()->hasVerifiedEmail())
      <flux:callout icon="triangle-alert" color="amber">
        <flux:callout.heading>Email Not Verified</flux:callout.heading>
        <flux:callout.text>
          Please verify your email address to access all API features.
          <flux:link :href="route('portal.verification.notice')" wire:navigate>Resend verification email</flux:link>
        </flux:callout.text>
      </flux:callout>
    @endif
  @endauth

  {{ $slot }}
</flux:main>
<flux:footer class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 !py-3 text-sm flex max-sm:flex-col justify-end gap-y-ui gap-x-4 sm:fixed w-full bottom-0">
  <flux:text variant="subtle" class="max-sm:text-center">
    API Version: {{ config('api.version') }}
  </flux:text>
  <div class="flex max-sm:flex-col items-center gap-y-ui gap-x-4">
    <flux:link href="https://github.com/Muetze42/hellofresh-database" target="_blank" class="inline-flex items-center gap-1 hover:text-zinc-700 dark:hover:text-zinc-200">
      <flux:icon.github variant="micro" />
      <span>GitHub</span>
    </flux:link>
    <div>
      {{ __('Made with') }} <span class="text-red-500">&hearts;</span> {{ __('by') }}
      <flux:link href="https://huth.it" target="_blank" class="font-medium hover:text-zinc-700 dark:hover:text-zinc-200">
        Norman Huth
      </flux:link>
    </div>
  </div>
</flux:footer>

@persist('toast')
<flux:toast.group position="bottom end">
  <flux:toast />
</flux:toast.group>
@endpersist

<x-confirmation-modal />

@vite(['resources/js/portal/app.js'], 'build-portal')
@livewireScripts
@fluxScripts
</body>
</html>
