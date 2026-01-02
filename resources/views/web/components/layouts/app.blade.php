@props([
    'title' => null,
    'ogTitle' => null,
    'ogDescription' => null,
    'ogImage' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? config('app.name') }}</title>
  @if($ogDescription)
    <meta name="description" content="{{ $ogDescription }}">
  @endif

  {{-- Open Graph / Social Media Meta Tags --}}
  <meta property="og:title" content="{{ $ogTitle ?? $title ?? config('app.name') }}">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ $ogImage ?? route('og.generic', ['title' => $ogTitle ?? $title ?? config('app.name')]) }}">
  @if($ogDescription)
    <meta property="og:description" content="{{ $ogDescription }}">
  @endif
  <meta property="og:site_name" content="{{ config('app.name') }}">

  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $ogTitle ?? $title ?? config('app.name') }}">
  @if($ogDescription)
    <meta name="twitter:description" content="{{ $ogDescription }}">
  @endif
  <meta name="twitter:image" content="{{ $ogImage ?? route('og.generic', ['title' => $ogTitle ?? $title ?? config('app.name')]) }}">

  <x-partials.favicon />
  @vite(['resources/css/web/app.css'])
  @livewireStyles
  @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">
{{ $slot }}
@persist('toast')
<flux:toast.group position="bottom end">
    <flux:toast />
</flux:toast.group>
@endpersist
@vite(['resources/js/web/app.js'])
@livewireScripts
@fluxScripts
</body>
</html>
