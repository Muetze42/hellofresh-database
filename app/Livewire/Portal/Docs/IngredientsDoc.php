<?php

namespace App\Livewire\Portal\Docs;

use Override;

class IngredientsDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'Ingredients API';
    }

    protected function description(): string
    {
        return 'Retrieve available ingredients used in recipes.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/ingredients',
                'description' => 'List all available ingredients with pagination.',
            ],
        ];
    }

    #[Override]
    protected function queryParams(): array
    {
        return [
            ['name' => 'search', 'type' => 'string', 'description' => 'Filter ingredients by name'],
            ['name' => 'per_page', 'type' => 'integer', 'description' => 'Results per page (' . config('api.pagination.per_page_min') . '-' . config('api.pagination.per_page_max') . ', default ' . config('api.pagination.per_page_default') . ')'],
            ['name' => 'sort', 'type' => 'string', 'description' => 'Sort by: created_at (default) or updated_at, always descending'],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Ingredient ID'],
            ['name' => 'name', 'type' => 'string', 'description' => 'Ingredient name (localized)'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/ingredients" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
