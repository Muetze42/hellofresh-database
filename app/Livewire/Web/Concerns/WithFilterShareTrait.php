<?php

declare(strict_types=1);

namespace App\Livewire\Web\Concerns;

use App\Enums\FilterSharePageEnum;
use App\Models\Country;
use App\Models\FilterShare;

/**
 * @property int $countryId
 * @property Country $country
 * @property int $activeFilterCount
 * @property string $search
 * @property string $sortBy
 * @property bool $filterHasPdf
 * @property bool $filterOnlyPublished
 * @property array<int> $excludedAllergenIds
 * @property array<int> $ingredientIds
 * @property string $ingredientMatchMode
 * @property array<int> $excludedIngredientIds
 * @property array<int> $tagIds
 * @property array<int> $excludedTagIds
 * @property array<int> $labelIds
 * @property array<int> $excludedLabelIds
 * @property array<int> $difficultyLevels
 * @property array{0: int, 1: int}|null $prepTimeRange
 * @property array{0: int, 1: int}|null $totalTimeRange
 */
trait WithFilterShareTrait
{
    public string $shareUrl = '';

    /**
     * Prepare the share URL (called when share button is clicked).
     */
    public function prepareShareUrl(): void
    {
        $this->shareUrl = $this->activeFilterCount > 0
            ? $this->createFilterShare()
            : $this->getDirectShareUrl();
    }

    /**
     * Get the current page URL (no filters, no DB).
     */
    public function getDirectShareUrl(): string
    {
        $params = $this->collectUrlParamsForShare();
        $url = localized_route($this->getFilterSharePage()->routeName());

        return $params !== [] ? $url . '?' . http_build_query($params) : $url;
    }

    /**
     * Generate filter share URL (only called when filters are active).
     */
    public function generateFilterShareUrl(): void
    {
        $this->shareUrl = $this->createFilterShare();
    }

    /**
     * Create a filter share and return the URL.
     */
    protected function createFilterShare(): string
    {
        $filters = $this->collectFiltersForShare();
        ksort($filters);
        $page = $this->getFilterSharePage()->value;

        // Check for existing identical filter share (JSONB = comparison)
        $filterShare = FilterShare::where('country_id', $this->countryId)
            ->where('page', $page)
            ->whereRaw('filters = ?::jsonb', [json_encode($filters, JSON_THROW_ON_ERROR)])
            ->first();

        if ($filterShare === null) {
            $filterShare = new FilterShare([
                'page' => $page,
                'filters' => $filters,
            ]);
            $filterShare->country()->associate($this->country);
            $filterShare->save();
        }

        $url = localized_route('localized.filter-share', ['id' => $filterShare->id]);

        $urlParams = $this->collectUrlParamsForShare();
        if ($urlParams !== []) {
            $url .= '?' . http_build_query($urlParams);
        }

        return $url;
    }

    /**
     * Collect URL parameters for sharing (search, page, sort).
     *
     * @return array<string, mixed>
     */
    protected function collectUrlParamsForShare(): array
    {
        $params = [];

        if ($this->search !== '') {
            $params['search'] = $this->search;
        }

        if ($this->getSharePage() > 1) {
            $params['page'] = $this->getSharePage();
        }

        if ($this->sortBy !== '' && $this->sortBy !== $this->getDefaultSort()) {
            $params['sort'] = $this->sortBy;
        }

        return $params;
    }

    /**
     * Get the default sort value.
     */
    protected function getDefaultSort(): string
    {
        return '';
    }

    /**
     * Get the current page number for sharing (override in paginated components).
     */
    protected function getSharePage(): int
    {
        return 1;
    }

    /**
     * Get the filter share page enum for this component.
     */
    abstract protected function getFilterSharePage(): FilterSharePageEnum;

    /**
     * Collect the current filters for sharing.
     *
     * @return array<string, mixed>
     */
    protected function collectFiltersForShare(): array
    {
        $filters = [];

        $this->collectBooleanFilters($filters);
        $this->collectArrayFilters($filters);
        $this->collectTimeRangeFilters($filters);

        return $filters;
    }

    /**
     * Collect boolean filters.
     *
     * @param  array<string, mixed>  $filters
     */
    protected function collectBooleanFilters(array &$filters): void
    {
        if ($this->filterHasPdf) {
            $filters['has_pdf'] = true;
        }

        if ($this->filterOnlyPublished) {
            $filters['only_published'] = true;
        }

        $filters['ingredient_match'] = $this->ingredientMatchMode;
    }

    /**
     * Collect array filters.
     *
     * @param  array<string, mixed>  $filters
     */
    protected function collectArrayFilters(array &$filters): void
    {
        $arrayMappings = [
            'excludedAllergenIds' => 'excluded_allergens',
            'ingredientIds' => 'ingredients',
            'excludedIngredientIds' => 'excluded_ingredients',
            'tagIds' => 'tags',
            'excludedTagIds' => 'excluded_tags',
            'labelIds' => 'labels',
            'excludedLabelIds' => 'excluded_labels',
            'difficultyLevels' => 'difficulty',
        ];

        foreach ($arrayMappings as $property => $filterKey) {
            if ($this->{$property} !== []) {
                $values = $this->{$property};
                sort($values);
                $filters[$filterKey] = $values;
            }
        }
    }

    /**
     * Collect time range filters.
     *
     * @param  array<string, mixed>  $filters
     */
    protected function collectTimeRangeFilters(array &$filters): void
    {
        if ($this->isPrepTimeFilterActive()) {
            $filters['prep_time'] = $this->prepTimeRange;
        }

        if ($this->isTotalTimeFilterActive()) {
            $filters['total_time'] = $this->totalTimeRange;
        }
    }

    /**
     * Check if prep time filter is active (not default values).
     */
    protected function isPrepTimeFilterActive(): bool
    {
        if ($this->prepTimeRange === null) {
            return false;
        }

        $default = $this->getDefaultPrepTimeRange();

        return $default === null || $this->prepTimeRange !== $default;
    }

    /**
     * Check if total time filter is active (not default values).
     */
    protected function isTotalTimeFilterActive(): bool
    {
        if ($this->totalTimeRange === null) {
            return false;
        }

        $default = $this->getDefaultTotalTimeRange();

        return $default === null || $this->totalTimeRange !== $default;
    }
}
