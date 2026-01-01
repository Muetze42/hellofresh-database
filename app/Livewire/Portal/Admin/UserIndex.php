<?php

namespace App\Livewire\Portal\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('portal::components.layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filter = 'all';

    #[Url]
    public string $sortBy = 'id';

    #[Url]
    public string $sortDirection = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
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
     * Get the users statistics.
     *
     * @return array{total: int, verified: int, unverified: int, admins: int, with_tokens: int}
     */
    #[Computed]
    public function stats(): array
    {
        return [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'admins' => User::where('admin', true)->count(),
            'with_tokens' => User::has('tokens')->count(),
        ];
    }

    /**
     * Get the paginated users.
     *
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::withCount('tokens')
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->whereLike('name', sprintf('%%%s%%', $this->search))
                        ->orWhereLike('email', sprintf('%%%s%%', $this->search));
                });
            })
            ->when($this->filter === 'verified', fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
            ->when($this->filter === 'unverified', fn (Builder $query): Builder => $query->whereNull('email_verified_at'))
            ->when($this->filter === 'admins', fn (Builder $query): Builder => $query->where('admin', true))
            ->when($this->filter === 'with_tokens', fn (Builder $query): Builder => $query->has('tokens'))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    public function render(): View
    {
        return view('portal::livewire.admin.user-index')->title('Users');
    }
}
