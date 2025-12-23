@props([
    'title' => null,
    'ogTitle' => null,
    'ogDescription' => null,
    'ogImage' => null,
])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}-{{ resolve('current.country')->code }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? config('app.name') }}</title>

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

  <x-layouts.partials.favicon />
  @vite(['resources/css/app.css'])
  @livewireStyles
  @fluxAppearance
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased print:bg-white" data-country="{{ resolve('current.country')->code }}">
<x-layouts.localized.header />
{{ $slot }}
<x-layouts.localized.footer />
<x-confirmation-modal />
<livewire:auth.auth-modal />
@persist('toast')
<flux:toast.group position="bottom end">
  <flux:toast />
</flux:toast.group>
@endpersist
@vite(['resources/js/app.js'])
@livewireScripts
@fluxScripts
<script>
  document.addEventListener('logout', function () {
    fetch('{{ route("logout") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    }).then(function () {
      window.location.reload();
    });
  });

  document.addEventListener('load-shopping-list', function (event) {
    if (window.Alpine && window.Alpine.store('shoppingList')) {
      const store = window.Alpine.store('shoppingList');
      store.clear();
      event.detail.items.forEach(function (item) {
        store.add(item.recipe_id);
        store.setServings(item.recipe_id, item.servings);
      });
    }
  });
</script>
</body>
</html>
