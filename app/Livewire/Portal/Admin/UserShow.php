<?php

namespace App\Livewire\Portal\Admin;

use App\Livewire\AbstractComponent;
use App\Models\PersonalAccessTokenUsage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use stdClass;

#[Layout('portal::components.layouts.app')]
class UserShow extends AbstractComponent
{
    use WithPagination;

    public User $user;

    #[Url]
    public string $period = '30d';

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    /**
     * Get the start date based on the selected period.
     */
    protected function getStartDate(): Carbon
    {
        return match ($this->period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            'all' => now()->subYears(10),
            default => now()->subDays(30),
        };
    }

    /**
     * Get user statistics.
     *
     * @return array{total_requests: int, tokens_count: int, active_tokens: int, favorites_count: int, recipe_lists_count: int}
     */
    #[Computed]
    public function stats(): array
    {
        $startDate = $this->getStartDate();

        return [
            'total_requests' => PersonalAccessTokenUsage::where('user_id', $this->user->id)
                ->where('created_at', '>=', $startDate)
                ->count(),
            'tokens_count' => $this->user->tokens()->count(),
            'active_tokens' => $this->user->tokens()
                ->where(function (Builder $query): void {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })->count(),
            'favorites_count' => $this->user->favorites()->count(),
            'recipe_lists_count' => $this->user->recipeLists()->count(),
        ];
    }

    /**
     * Get the user's tokens.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, PersonalAccessToken>
     */
    #[Computed]
    public function tokens(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->user->tokens()
            ->withCount('usages')
            ->latest()
            ->get();
    }

    /**
     * Get the most used endpoints by this user.
     *
     * @return Collection<int, stdClass>
     */
    #[Computed]
    public function topEndpoints(): Collection
    {
        $startDate = $this->getStartDate();

        return DB::table('personal_access_token_usages')
            ->select('path', DB::raw('COUNT(*) as count'))
            ->where('user_id', $this->user->id)
            ->where('created_at', '>=', $startDate)
            ->groupBy('path')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    /**
     * Get daily usage for this user.
     *
     * @return Collection<int, stdClass>
     */
    #[Computed]
    public function dailyUsage(): Collection
    {
        $startDate = $this->getStartDate();

        return DB::table('personal_access_token_usages')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('user_id', $this->user->id)
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Get the paginated usage logs for this user.
     *
     * @return LengthAwarePaginator<int, PersonalAccessTokenUsage>
     */
    #[Computed]
    public function usageLogs(): LengthAwarePaginator
    {
        $startDate = $this->getStartDate();

        return PersonalAccessTokenUsage::with('token')
            ->where('user_id', $this->user->id)
            ->where('created_at', '>=', $startDate)
            ->latest('created_at')
            ->paginate(15);
    }

    public function render(): View
    {
        return view('portal::livewire.admin.user-show')
            ->title($this->user->name);
    }
}
