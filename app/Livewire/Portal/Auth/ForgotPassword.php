<?php

declare(strict_types=1);

namespace App\Livewire\Portal\Auth;

use App\Livewire\AbstractComponent;
use App\Models\User;
use App\Support\Facades\Flux;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('portal::components.layouts.guest')]
class ForgotPassword extends AbstractComponent
{
    public string $email = '';

    public bool $linkSent = false;

    /**
     * Send password reset link.
     *
     * @throws ValidationException
     */
    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ]);

        ResetPassword::createUrlUsing(fn (User $user, string $token): string => route('portal.password.reset', [
            'token' => $token,
        ]));

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->linkSent = true;
            Flux::toastSuccess(__($status));

            return;
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    public function render(): View
    {
        return view('portal::livewire.auth.forgot-password')
            ->title('Forgot Password');
    }
}
