<!DOCTYPE html>
@if(country())
<html lang="{{ config('app.locale', 'en') }}-{{ country()->country }}">
@else
<html lang="{{ config('app.locale', 'en') }}">
@endif
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel')  }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="antialiased bg-gray-900 text-gray-100 tracking-tight">
@inertia
</body>
</html>
