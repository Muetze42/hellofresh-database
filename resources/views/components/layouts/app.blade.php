<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="theme-accent-green">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>{{ $title ?? 'Page Title' }}</title>
  @include('partials.head')
</head>
<body>
{{ $slot }}
@fluxScripts
</body>
</html>
