<?php

namespace App\Livewire\Actions\Portal;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class LogoutAction
{
    public function __invoke(): RedirectResponse
    {
        auth()->logout();

        Session::invalidate();
        Session::regenerateToken();

        return to_route('portal.dashboard');
    }
}
