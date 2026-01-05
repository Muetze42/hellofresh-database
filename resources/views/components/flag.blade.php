@props([
    'code',
    'squared' => false,
])

<span {{ $attributes->class(['fi', 'fi-' . strtolower($code), 'fis' => $squared]) }}></span>
