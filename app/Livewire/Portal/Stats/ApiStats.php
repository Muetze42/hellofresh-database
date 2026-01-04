<?php

namespace App\Livewire\Portal\Stats;

use App\Models\PersonalAccessToken;
use App\Models\PersonalAccessTokenUsage;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use stdClass;

/**
 * @property-read array{total_requests: int, unique_tokens: int, unique_users: int, total_tokens: int, active_tokens: int} $stats
 * @property-read Collection<int, stdClass> $topEndpoints
 * @property-read Collection<int, stdClass> $dailyUsage
 * @property-read array<int, array{date: string, requests: int}> $chartData
 */
#[Layout('portal::components.layouts.app')]
class ApiStats extends Component
{
    #[Url]
    public string $period = '7d';

    public function updatedPeriod(): void
    {
        unset($this->stats, $this->topEndpoints, $this->dailyUsage, $this->chartData);
    }

    /**
     * Get the start date based on the selected period.
     */
    protected function getStartDate(): Carbon
    {
        return match ($this->period) {
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(7),
        };
    }

    /**
     * Get the API usage statistics.
     *
     * @return array{total_requests: int, unique_tokens: int, unique_users: int, total_tokens: int, active_tokens: int}
     */
    #[Computed]
    public function stats(): array
    {
        $startDate = $this->getStartDate();

        return [
            'total_requests' => PersonalAccessTokenUsage::where('created_at', '>=', $startDate)->count(),
            'unique_tokens' => PersonalAccessTokenUsage::where('created_at', '>=', $startDate)->distinct('token_id')->count('token_id'),
            'unique_users' => PersonalAccessTokenUsage::where('created_at', '>=', $startDate)->distinct('user_id')->count('user_id'),
            'total_tokens' => PersonalAccessToken::count(),
            'active_tokens' => PersonalAccessToken::where(function (Builder $query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })->whereHas('usages', function (Builder $query) use ($startDate): void {
                $query->where('created_at', '>=', $startDate);
            })->count(),
        ];
    }

    /**
     * Get the most used endpoints.
     *
     * @return Collection<int, stdClass>
     */
    #[Computed]
    public function topEndpoints(): Collection
    {
        $startDate = $this->getStartDate();

        return DB::table('personal_access_token_usages')
            ->select('path', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('path')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    /**
     * Get the usage per day.
     *
     * @return Collection<int, stdClass>
     */
    #[Computed]
    public function dailyUsage(): Collection
    {
        $startDate = $this->getStartDate();

        return DB::table('personal_access_token_usages')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get chart data for the daily usage line chart.
     *
     * @return array<int, array{date: string, requests: int}>
     */
    #[Computed]
    public function chartData(): array
    {
        return $this->dailyUsage->map(fn (stdClass $day): array => [
            'date' => $day->date,
            'requests' => (int) $day->count,
        ])->all();
    }

    public function render(): View
    {
        /** @var View $view */
        $view = view('portal::livewire.stats.api-stats')
            ->title(page_title('API', 'Statistics'));

        return $view;
    }
}
