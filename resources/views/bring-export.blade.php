<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Shopping List') }} - hfresh.info</title>
</head>
<body>
    <div itemscope itemtype="http://schema.org/Recipe">
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
    </div>
</body>
</html>
