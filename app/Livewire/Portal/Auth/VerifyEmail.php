<?php

namespace App\Livewire\Portal\Auth;

use App\Livewire\AbstractComponent;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;

#[Layout('portal::components.layouts.guest')]
class VerifyEmail extends AbstractComponent
{
    /**
     * Resend the email verification notification.
     */
    public function resend(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('portal.dashboard'), navigate: true);

            return;
        }

        $key = 'verify-email:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            Flux::toastDanger(sprintf('Please wait %d seconds before requesting another email.', $seconds));

            return;
        }

        RateLimiter::hit($key, 60);

        $user->sendEmailVerificationNotification();

        Flux::toastSuccess('A new verification link has been sent to your email address.');
    }

    public function render(): View
    {
        return view('portal::livewire.auth.verify-email')
            ->title('Verify Email');
    }
}
