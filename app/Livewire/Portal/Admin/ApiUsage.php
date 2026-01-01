<?php

namespace App\Livewire\Portal\Admin;

use App\Models\PersonalAccessToken;
use App\Models\PersonalAccessTokenUsage;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use stdClass;

#[Layout('portal::components.layouts.app')]
class ApiUsage extends Component
{
    use WithPagination;

    #[Url]
    public string $period = '7d';

    #[Url]
    public string $search = '';

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
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
     * Get the most active users.
     *
     * @return Collection<int, stdClass>
     */
    #[Computed]
    public function topUsers(): Collection
    {
        $startDate = $this->getStartDate();

        return DB::table('personal_access_token_usages')
            ->join('users', 'personal_access_token_usages.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', DB::raw('COUNT(*) as request_count'))
            ->where('personal_access_token_usages.created_at', '>=', $startDate)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('request_count')
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
     * Get the paginated usage logs.
     *
     * @return LengthAwarePaginator<int, PersonalAccessTokenUsage>
     */
    #[Computed]
    public function usageLogs(): LengthAwarePaginator
    {
        $startDate = $this->getStartDate();

        return PersonalAccessTokenUsage::with(['user', 'token'])
            ->where('created_at', '>=', $startDate)
            ->latest('created_at')
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->whereLike('path', sprintf('%%%s%%', $this->search))
                        ->orWhereLike('host', sprintf('%%%s%%', $this->search))
                        ->orWhereHas('user', function (Builder $query): void {
                            $query->whereLike('name', sprintf('%%%s%%', $this->search))
                                ->orWhereLike('email', sprintf('%%%s%%', $this->search));
                        });
                });
            })
            ->paginate(20);
    }

    public function render(): View
    {
        return view('portal::livewire.admin.api-usage')->title('API Usage');
    }
}
