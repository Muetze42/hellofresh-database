<?php

namespace App\Livewire\Portal\Docs;

use Override;

class MenusIndexDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'List Menus';
    }

    protected function description(): string
    {
        return 'Retrieve a paginated list of weekly menus with optional filtering.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/menus',
            ],
        ];
    }

    #[Override]
    protected function queryParams(): array
    {
        return [
            ['name' => 'include_recipes', 'type' => 'boolean', 'description' => 'Include recipes in response'],
            ['name' => 'from', 'type' => 'date', 'description' => 'Filter menus starting from this date'],
            ['name' => 'to', 'type' => 'date', 'description' => 'Filter menus up to this date'],
            ['name' => 'per_page', 'type' => 'integer', 'description' => 'Results per page (' . config('api.pagination.per_page_min') . '-' . config('api.pagination.per_page_max') . ', default ' . config('api.pagination.per_page_default') . ')'],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Menu ID'],
            ['name' => 'year_week', 'type' => 'integer', 'description' => 'Year and week number (YYYYWW format)'],
            ['name' => 'start', 'type' => 'date', 'description' => 'Menu start date'],
            ['name' => 'recipes', 'type' => 'array', 'description' => 'Array of recipe objects (when include_recipes=true)'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/menus?include_recipes=true&per_page=10" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
