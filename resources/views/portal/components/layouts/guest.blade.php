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
  {{ Vite::useHotFile(public_path('hot-portal'))->useBuildDirectory('build-portal')->withEntryPoints(['resources/css/portal/app.css']) }}
  @livewireStyles
  @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 antialiased">
<flux:main class="min-h-screen flex flex-col items-center justify-center p-section">
  <flux:heading class="mb-4 inline-flex gap-1 items-center text-xl">
    <flux:icon.earth />
    {{ config('app.name') }} API
  </flux:heading>

  <flux:card class="w-full max-w-md">
    {{ $slot }}
  </flux:card>
</flux:main>

<flux:toast.group position="bottom end">
  <flux:toast />
</flux:toast.group>

{{ Vite::useHotFile(public_path('hot-portal'))->useBuildDirectory('build-portal')->withEntryPoints(['resources/js/portal/app.js']) }}
@livewireScripts
@fluxScripts
</body>
</html>
