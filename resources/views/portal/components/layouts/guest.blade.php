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
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 antialiased">
<div class="min-h-screen flex flex-col items-center justify-center p-section">
  <flux:heading class="mb-4 inline-flex gap-1 items-center text-xl">
    <flux:icon.earth />
    {{ config('app.name') }} API
  </flux:heading>

  <flux:card class="w-full max-w-md">
    {{ $slot }}
  </flux:card>
</div>

<flux:toast.group position="bottom end">
  <flux:toast />
</flux:toast.group>

@vite(['resources/js/portal/app.js'], 'build-portal')
@livewireScripts
@fluxScripts
</body>
</html>
