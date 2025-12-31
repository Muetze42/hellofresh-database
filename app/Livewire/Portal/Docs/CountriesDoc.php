<?php

namespace App\Livewire\Portal\Docs;

use Override;

class CountriesDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'Countries API';
    }

    protected function description(): string
    {
        return 'Retrieve available countries supported by the API.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/countries',
            ],
        ];
    }

    #[Override]
    protected function queryParams(): array
    {
        return [
            ['name' => 'per_page', 'type' => 'integer', 'description' => 'Results per page (' . config('api.pagination.per_page_min') . '-' . config('api.pagination.per_page_max') . ', default ' . config('api.pagination.per_page_default') . ')'],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Country ID'],
            ['name' => 'code', 'type' => 'string', 'description' => 'Country code (e.g., DE, AT, CH)'],
            ['name' => 'recipes_count', 'type' => 'integer', 'description' => 'Number of recipes available'],
            ['name' => 'ingredients_count', 'type' => 'integer', 'description' => 'Number of ingredients available'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/countries" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
