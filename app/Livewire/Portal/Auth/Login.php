<?php

namespace App\Livewire\Portal\Auth;

use App\Livewire\Actions\LoginUserAction;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.guest')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * Handle user login.
     *
     * @throws ValidationException
     */
    public function login(LoginUserAction $loginUser): void
    {
        $this->validate(LoginUserAction::rules());

        $loginUser($this->email, $this->password, $this->remember);

        Flux::toastSuccess('Welcome back!');

        $this->redirect(route('portal.dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('portal::livewire.auth.login')
            ->title('Login');
    }
}
