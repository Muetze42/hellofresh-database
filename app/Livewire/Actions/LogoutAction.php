<?php

namespace App\Livewire\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutAction
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke(): JsonResponse
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return response()->json(['success' => true]);
    }
}
