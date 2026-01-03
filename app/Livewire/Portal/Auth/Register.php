<?php

namespace App\Livewire\Portal\Auth;

use App\Livewire\AbstractComponent;
use App\Livewire\Actions\RegisterUserAction;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
    public function register(RegisterUserAction $registerUser): void
    {
        $validated = $this->validate(
            RegisterUserAction::rules(),
            RegisterUserAction::messages(),
        );

        $user = $registerUser($validated);

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
