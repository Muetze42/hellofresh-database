<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Shopping List') }} - hfresh.info</title>
    @php
        $ingredientStrings = array_map(
            fn($i) => ($i['amount'] !== '' ? $i['amount'] . ' ' : '') . $i['name'],
            $ingredients
        );
    @endphp
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Recipe',
        'name' => __('Shopping List'),
        'recipeIngredient' => $ingredientStrings,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
</head>
<body itemscope itemtype="https://schema.org/Recipe">
    <h1 itemprop="name">{{ __('Shopping List') }}</h1>
    <ul>
        @foreach ($ingredients as $ingredient)
            <li itemprop="recipeIngredient">
                @if ($ingredient['amount'] !== '')
                    {{ $ingredient['amount'] }} {{ $ingredient['name'] }}
                @else
                    {{ $ingredient['name'] }}
                @endif
            </li>
        @endforeach
    </ul>
</body>
</html>
