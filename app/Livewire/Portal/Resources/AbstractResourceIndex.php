<?php

namespace App\Livewire\Portal\Resources;

use App\Livewire\AbstractComponent;
use App\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

/**
 * Abstract base component for resource index pages.
 */
#[Layout('portal::components.layouts.app')]
abstract class AbstractResourceIndex extends AbstractComponent
{
    use WithPagination;

    #[Url]
    public int|string|null $countryId = null;

    #[Url]
    public string $search = '';

    #[Url]
    public string $sortBy = 'id';

    #[Url]
    public string $sortDirection = 'desc';

    public function updatedCountryId(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Sort by the given column.
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
            $this->resetPage();

            return;
        }

        $this->sortBy = $column;
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    /**
     * Get the list of countries for the select.
     *
     * @return Collection<int, Country>
     */
    #[Computed]
    public function countries(): Collection
    {
        return Country::orderBy('code')->get();
    }

    /**
     * Get the paginated resources.
     *
     * @return LengthAwarePaginator<int, Model>
     */
    #[Computed]
    public function resources(): LengthAwarePaginator
    {
        $modelClass = $this->getModelClass();

        return $modelClass::with('country')
            ->when($this->countryId !== null && $this->countryId !== '', fn (Builder $query): Builder => $query->where('country_id', $this->countryId))
            ->when($this->search !== '', fn (Builder $query): Builder => $this->applySearchFilter($query))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(25);
    }

    /**
     * Get the model class for this resource.
     *
     * @return class-string<Model>
     */
    abstract protected function getModelClass(): string;

    /**
     * Apply search filter to the query.
     *
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    protected function applySearchFilter(Builder $query): Builder
    {
        return $query->whereLike('name', sprintf('%%%s%%', $this->search));
    }

    /**
     * Get the resource title (e.g., "Cuisines", "Ingredients").
     */
    abstract protected function getResourceTitle(): string;

    /**
     * Get the resource title for the view.
     */
    #[Computed]
    public function resourceTitle(): string
    {
        return $this->getResourceTitle();
    }

    /**
     * Get the search placeholder text.
     */
    #[Computed]
    public function searchPlaceholder(): string
    {
        return sprintf('Search %s...', strtolower($this->getResourceTitle()));
    }

    /**
     * Get the empty state message.
     */
    #[Computed]
    public function emptyMessage(): string
    {
        return sprintf('No %s found.', strtolower($this->getResourceTitle()));
    }

    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.resources.resource-index')->title($this->getResourceTitle());

        return $view;
    }
}
