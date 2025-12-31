<?php

namespace App\Livewire\Portal\Docs;

use Override;

class TagsDoc extends AbstractEndpointDoc
{
    protected function title(): string
    {
        return 'Tags API';
    }

    protected function description(): string
    {
        return 'Retrieve available recipe tags for filtering.';
    }

    protected function endpoints(): array
    {
        return [
            [
                'method' => 'GET',
                'path' => '/{locale}-{country}/tags',
                'description' => 'List all available tags.',
            ],
        ];
    }

    #[Override]
    protected function queryParams(): array
    {
        return [
            ['name' => 'per_page', 'type' => 'integer', 'description' => 'Results per page (' . config('api.pagination.per_page_min') . '-' . config('api.pagination.per_page_max') . ', default ' . config('api.pagination.per_page_default') . ')'],
            ['name' => 'sort', 'type' => 'string', 'description' => 'Sort by: created_at (default) or updated_at, always descending'],
        ];
    }

    protected function responseFields(): array
    {
        return [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Tag ID'],
            ['name' => 'name', 'type' => 'string', 'description' => 'Tag name (localized)'],
            ['name' => 'created_at', 'type' => 'datetime', 'description' => 'Creation timestamp'],
            ['name' => 'updated_at', 'type' => 'datetime', 'description' => 'Last update timestamp'],
        ];
    }

    protected function exampleRequest(): string
    {
        return 'curl -X GET "https://' . config('api.domain_name') . '/de-DE/tags" \\
  -H "Authorization: Bearer YOUR_API_TOKEN" \\
  -H "Accept: application/json"';
    }
}
