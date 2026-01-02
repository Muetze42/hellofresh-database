<?php

namespace App\Livewire\Portal\Auth;

use App\Models\User;
use App\Rules\DisposableEmailRule;
use App\Support\Facades\Flux;
use Illuminate\Auth\Events\Registered as RegisteredEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.guest')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?string $country_code = null;

    public bool $acceptPrivacy = false;

    /**
     * Handle user registration.
     */
    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc', 'unique:users,email', new DisposableEmailRule()],
            'password' => ['required', 'confirmed', Password::defaults()],
            'country_code' => ['nullable', 'string', 'size:2'],
            'acceptPrivacy' => ['accepted'],
        ], [
            'acceptPrivacy.accepted' => __('You must accept the privacy policy.'),
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'password' => Hash::make($this->password),
        ]);

        event(new RegisteredEvent($user));

        Auth::login($user, true);

        Session::regenerate();

        Flux::toastSuccess('Welcome! Please verify your email address.');

        $this->redirect(route('portal.dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('portal::livewire.auth.register')
            ->title('Register');
    }
}
