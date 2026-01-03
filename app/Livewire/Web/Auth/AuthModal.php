<?php

namespace App\Livewire\Web\Auth;

use App\Livewire\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Models\User;
use App\Rules\CountryCodeRule;
use App\Rules\DisposableEmailRule;
use App\Support\Facades\Flux;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered as RegisteredEvent;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
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
    public function login(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->js('window.location.reload()');
    }

    /**
     * Handle user registration.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc', 'unique:users,email', new DisposableEmailRule()],
            'password' => ['required', 'confirmed', Password::defaults()],
            'country_code' => ['nullable', 'string', 'size:2', new CountryCodeRule()],
            'acceptPrivacy' => ['accepted'],
        ], [
            'acceptPrivacy.accepted' => __('You must accept the privacy policy.'),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'country_code' => $validated['country_code'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new RegisteredEvent($user));

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
     * Ensure the authentication request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.auth.auth-modal');
    }
}
