<?php

namespace App\Livewire\Portal;

use Illuminate\Contracts\View\View;
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

        return view('portal::livewire.dashboard', [
            'isAuthenticated' => $isAuthenticated,
            'tokens' => $tokens,
            'tokenCount' => $tokenCount,
        ])->title('API Portal');
    }
}
