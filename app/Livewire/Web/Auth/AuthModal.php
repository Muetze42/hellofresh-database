<?php

namespace App\Livewire\Web\Auth;

use App\Livewire\AbstractComponent;
use App\Livewire\Actions\LoginUserAction;
use App\Livewire\Actions\RegisterUserAction;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

class AuthModal extends AbstractComponent
{
    use WithLocalizedContextTrait;

    public string $mode = 'login';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:8')]
    public string $password = '';

    public string $password_confirmation = '';

    #[Validate('required|min:2')]
    public string $name = '';

    public ?string $country_code = null;

    public bool $acceptPrivacy = false;

    public bool $remember = false;

    public bool $resetLinkSent = false;

    /**
     * Switch to login mode.
     */
    public function switchToLogin(): void
    {
        $this->mode = 'login';
        $this->resetLinkSent = false;
        $this->resetValidation();
    }

    /**
     * Switch to register mode.
     */
    public function switchToRegister(): void
    {
        $this->mode = 'register';
        $this->resetValidation();
    }

    /**
     * Switch to forgot password mode.
     */
    public function switchToForgotPassword(): void
    {
        $this->mode = 'forgot-password';
        $this->resetLinkSent = false;
        $this->resetValidation();
    }

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

        $status = PasswordBroker::sendResetLink(['email' => $this->email]);

        if ($status === PasswordBroker::RESET_LINK_SENT) {
            $this->resetLinkSent = true;
            Flux::toastSuccess(__($status));

            return;
        }

        throw ValidationException::withMessages([
            'email' => __($status),
        ]);
    }

    /**
     * Handle user login.
     *
     * @throws ValidationException
     */
    public function login(LoginUserAction $loginUser): void
    {
        $this->validate(LoginUserAction::rules());

        $loginUser($this->email, $this->password, $this->remember);

        $this->js('window.location.reload()');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterUserAction $registerUser): void
    {
        $validated = $this->validate(
            RegisterUserAction::rules(),
            RegisterUserAction::messages(),
        );

        $user = $registerUser($validated);

        Auth::login($user, true);

        Session::regenerate();

        $this->js('window.location.reload()');
    }

    /**
     * Handle logout.
     */
    public function logout(): void
    {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        $this->dispatch('user-logged-out');

        Flux::toastSuccess(__('You have been logged out.'));
    }

    /**
     * Open the modal for a required action.
     */
    #[On('require-auth')]
    public function openForAuth(): void
    {
        $this->mode = 'login';
        $this->resetLinkSent = false;
        $this->reset(['email', 'password', 'password_confirmation', 'name', 'country_code', 'acceptPrivacy', 'remember']);
        $this->resetValidation();
        Flux::showModal('auth-modal');
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.auth.auth-modal');
    }
}
