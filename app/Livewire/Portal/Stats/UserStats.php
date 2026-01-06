<?php

namespace App\Livewire\Portal\Stats;

use App\Livewire\AbstractComponent;
use App\Services\Portal\StatisticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use stdClass;

#[Layout('portal::components.layouts.app')]
class UserStats extends AbstractComponent
{
    protected function statistics(): StatisticsService
    {
        return resolve(StatisticsService::class);
    }

    /**
     * Get user engagement statistics.
     *
     * @return array{total_users: int, users_with_lists: int, total_lists: int, total_recipes_in_lists: int}
     */
    #[Computed]
    public function userEngagement(): array
    {
        return $this->statistics()->userEngagement();
    }

    /**
     * Get user counts grouped by country.
     *
     * @return Collection<int, stdClass>
     */
    #[Computed]
    public function usersByCountry(): Collection
    {
        return $this->statistics()->usersByCountry();
    }

    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.stats.user-stats')
            ->title(page_title('User', 'Statistics'));

        return $view;
    }
}
