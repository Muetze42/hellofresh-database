<?php

namespace App\Livewire\Portal\Docs;

use Override;

class AllergensDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'Allergens API';
    }

    protected function description(): string
    {
        return 'Retrieve available allergen information for recipes.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/allergens',
                'description' => 'List all available allergens.',
            ],
        ];
    }

    #[Override]
    protected function queryParams(): array
    {
        return [
            ['name' => 'sort', 'type' => 'string', 'description' => 'Sort by: created_at (default) or updated_at, always descending'],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Allergen ID'],
            ['name' => 'name', 'type' => 'string', 'description' => 'Allergen name (localized)'],
            ['name' => 'icon_path', 'type' => 'string', 'description' => 'Path to allergen icon'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/allergens" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
