<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class LengthAwarePaginator extends Paginator
{
    /**
     * Get the paginator links as a collection (for JSON responses).
     *
     * @return \Illuminate\Support\Collection
     * @noinspection DuplicatedCode
     */
    public function linkCollection()
    {
        return collect($this->elements())->flatMap(function ($item) {
            if (! is_array($item)) {
                return [['url' => null, 'label' => '...', 'active' => false]];
            }

            return collect($item)->map(function ($url, $page) {
                return [
                    'url' => $url,
                    'label' => (string) $page,
                    'active' => $this->currentPage() === $page,
                ];
            });
        })->prepend([
            'url' => $this->previousPageUrl(),
            'label' => function_exists('__') ? __('previous') : 'Previous',
            'active' => false,
        ])->push([
            'url' => $this->nextPageUrl(),
            'label' => function_exists('__') ? __('next') : 'Next',
            'active' => false,
        ]);
    }
}
