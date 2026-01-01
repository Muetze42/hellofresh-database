<?php

namespace App\Livewire\Portal;

use App\Models\PersonalAccessTokenUsage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class Dashboard extends Component
{
    public function render(): View
    {
        $user = auth()->user();
        $isAuthenticated = $user !== null;
        $tokens = $isAuthenticated ? $user->tokens()->latest()->take(5)->get() : collect();
        $tokenCount = $isAuthenticated ? $user->tokens()->count() : 0;

        $usageStats = $isAuthenticated ? $this->getUsageStats($user->id) : null;

        return view('portal::livewire.dashboard', [
            'isAuthenticated' => $isAuthenticated,
            'tokens' => $tokens,
            'tokenCount' => $tokenCount,
            'usageStats' => $usageStats,
        ])->title('API Portal');
    }

    /**
     * Get usage statistics for the user.
     *
     * @return array{today: int, week: int, total: int, chartData: array<int, array{date: string, requests: int}>, topEndpoints: array<int, array{path: string, count: int}>}
     */
    protected function getUsageStats(int $userId): array
    {
        $baseQuery = PersonalAccessTokenUsage::where('user_id', $userId);

        $today = (clone $baseQuery)->whereDate('created_at', Date::today())->count();
        $week = (clone $baseQuery)->where('created_at', '>=', Date::now()->subDays(7))->count();
        $total = (clone $baseQuery)->count();

        // Last 14 days for chart
        $chartData = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Date::now()->subDays($i);
            $count = (clone $baseQuery)
                ->whereDate('created_at', $date)
                ->count();

            $chartData[] = [
                'date' => $date->format('M j'),
                'requests' => $count,
            ];
        }

        // Top 5 endpoints
        /** @var array<int, object{path: string, count: int}> $endpointRows */
        $endpointRows = (clone $baseQuery)
            ->selectRaw('path, COUNT(*) as count')
            ->groupBy('path')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->all();

        $topEndpoints = array_map(
            fn (object $row): array => [
                'path' => $row->path,
                'count' => (int) $row->count,
            ],
            $endpointRows
        );

        return [
            'today' => $today,
            'week' => $week,
            'total' => $total,
            'chartData' => $chartData,
            'topEndpoints' => $topEndpoints,
        ];
    }
}
