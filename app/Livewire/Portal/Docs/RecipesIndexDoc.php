<?php

namespace App\Livewire\Portal\Docs;

use Override;

class RecipesIndexDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'List Recipes';
    }

    protected function description(): string
    {
        return 'Retrieve a paginated list of recipes with optional filtering.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/recipes',
            ],
        ];
    }

    #[Override]
    protected function queryParams(): array
    {
        return [
            ['name' => 'search', 'type' => 'string', 'description' => 'Filter recipes by name (case-insensitive)'],
            ['name' => 'tag', 'type' => 'string', 'description' => 'Filter by tag slug'],
            ['name' => 'label', 'type' => 'string', 'description' => 'Filter by label slug'],
            ['name' => 'difficulty', 'type' => 'integer', 'description' => 'Filter by difficulty level (1-3)'],
            ['name' => 'has_pdf', 'type' => 'boolean', 'description' => 'Filter recipes that have a PDF card'],
            ['name' => 'per_page', 'type' => 'integer', 'description' => 'Results per page (' . config('api.pagination.per_page_min') . '-' . config('api.pagination.per_page_max') . ', default ' . config('api.pagination.per_page_default') . ')'],
            ['name' => 'sort', 'type' => 'string', 'description' => 'Sort by: created_at (default) or updated_at, always descending'],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Recipe ID'],
            ['name' => 'url', 'type' => 'string', 'description' => 'URL to recipe on website'],
            ['name' => 'name', 'type' => 'string', 'description' => 'Recipe name (localized)'],
            ['name' => 'headline', 'type' => 'string', 'description' => 'Short description (localized)'],
            ['name' => 'difficulty', 'type' => 'integer', 'description' => 'Difficulty level (1-3)'],
            ['name' => 'prep_time', 'type' => 'integer', 'description' => 'Preparation time in minutes'],
            ['name' => 'total_time', 'type' => 'integer', 'description' => 'Total cooking time in minutes'],
            ['name' => 'has_pdf', 'type' => 'boolean', 'description' => 'Whether recipe has a PDF card'],
            ['name' => 'label', 'type' => 'object|null', 'description' => 'Recipe label'],
            ['name' => 'tags', 'type' => 'array', 'description' => 'Recipe tags'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/recipes?search=pasta&per_page=10" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
