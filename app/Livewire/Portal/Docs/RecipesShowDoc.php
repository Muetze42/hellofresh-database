<?php

namespace App\Livewire\Portal\Docs;

class RecipesShowDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'Get Recipe';
    }

    protected function description(): string
    {
        return 'Retrieve a single recipe by ID with full details.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/recipes/{id}',
            ],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Recipe ID'],
            ['name' => 'canonical_id', 'type' => 'integer|null', 'description' => 'ID of the original recipe if this is a variant'],
            ['name' => 'url', 'type' => 'string', 'description' => 'URL to recipe on website'],
            ['name' => 'name', 'type' => 'string', 'description' => 'Recipe name (localized)'],
            ['name' => 'headline', 'type' => 'string', 'description' => 'Short description (localized)'],
            ['name' => 'description', 'type' => 'string', 'description' => 'Full description (localized)'],
            ['name' => 'difficulty', 'type' => 'integer', 'description' => 'Difficulty level (1-3)'],
            ['name' => 'prep_time', 'type' => 'integer', 'description' => 'Preparation time in minutes'],
            ['name' => 'total_time', 'type' => 'integer', 'description' => 'Total cooking time in minutes'],
            ['name' => 'pdf_url', 'type' => 'string|null', 'description' => 'URL to recipe PDF card'],
            ['name' => 'has_pdf', 'type' => 'boolean', 'description' => 'Whether recipe has a PDF card'],
            ['name' => 'nutrition', 'type' => 'object', 'description' => 'Nutritional information'],
            ['name' => 'label', 'type' => 'object|null', 'description' => 'Recipe label (e.g., Premium, Family)'],
            ['name' => 'tags', 'type' => 'array', 'description' => 'Recipe tags'],
            ['name' => 'allergens', 'type' => 'array', 'description' => 'Allergens in recipe'],
            ['name' => 'ingredients', 'type' => 'array', 'description' => 'Recipe ingredients'],
            ['name' => 'cuisines', 'type' => 'array', 'description' => 'Cuisine types'],
            ['name' => 'utensils', 'type' => 'array', 'description' => 'Required utensils'],
            ['name' => 'saved_in_lists', 'type' => 'array', 'description' => 'Lists where user saved this recipe'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/recipes/12345" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
