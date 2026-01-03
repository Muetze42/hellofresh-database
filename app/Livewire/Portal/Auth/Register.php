<?php

namespace App\Livewire\Portal\Auth;

use App\Livewire\AbstractComponent;
use App\Models\User;
use App\Rules\CountryCodeRule;
use App\Rules\DisposableEmailRule;
use App\Support\Facades\Flux;
use Illuminate\Auth\Events\Registered as RegisteredEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;

#[Layout('portal::components.layouts.guest')]
class Register extends AbstractComponent
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

        Flux::toastSuccess('Welcome!');

        $this->redirect(route('portal.dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('portal::livewire.auth.register')
            ->title('Register');
    }
}
