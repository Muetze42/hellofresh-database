<?php

namespace App\Livewire\Web\Auth;

use App\Livewire\Web\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Rules\DisposableEmailRule;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordChange extends AbstractComponent
{
    use WithLocalizedContextTrait;

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->email = auth()->user()->email ?? '';
    }

    /**
     * Update the user's account settings.
     *
     * @throws ValidationException
     */
    public function updateAccount(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $rules = [
            'current_password' => ['required'],
            'email' => ['required', 'email:rfc', 'unique:users,email,' . $user->id, new DisposableEmailRule()],
        ];

        if ($this->password !== '') {
            $rules['password'] = ['confirmed', Password::defaults()];
        }

        $this->validate($rules);

        if (! Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The provided password does not match your current password.'),
            ]);
        }

        $data = ['email' => $this->email];

        if ($this->password !== '') {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        Flux::toastSuccess(__('Account updated successfully.'));
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): ViewInterface
    {
        return view('web::livewire.auth.password-change');
    }
}
