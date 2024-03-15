<!DOCTYPE html>
@if(country())
<html lang="{{ config('app.locale', 'en') }}-{{ country()->code }}" class="scrollbar">
@else
<html lang="{{ config('app.locale', 'en') }}">
@endif
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel')  }}</title>
    <meta name="robots" content="index,follow">
    <meta property="og:type" content="website">
    <meta name="description" content="{{ __('HelloFresh database for 16 countries including features like filtering by ingredients, allergens, tags, etc.') }}">
    <meta property="og:description" content="{{ __('HelloFresh database for 16 countries including features like filtering by ingredients, allergens, tags, etc.') }}">
    <meta property="og:image" content="{{ \App\Services\Asset::socialPreview() }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta name="author" content="Norman Huth">
    <meta name="copyright" content="Norman Huth">
    <meta name="designer" content="Norman Huth">
    <meta name="rating" content="general">
    @include('app.favicon')
    @inertiaHead
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="antialiased">
@inertia
</body>
</html>
