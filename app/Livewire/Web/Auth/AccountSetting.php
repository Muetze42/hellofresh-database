<?php

namespace App\Livewire\Web\Auth;

use App\Livewire\AbstractComponent;
use App\Livewire\Web\Concerns\WithLocalizedContextTrait;
use App\Rules\CountryCodeRule;
use App\Rules\DisposableEmailRule;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AccountSetting extends AbstractComponent
{
    use WithLocalizedContextTrait;

    public string $name = '';

    public string $email = '';

    public ?string $country_code = null;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();

        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->country_code = $user?->country_code;
    }

    /**
     * Update the user's profile information.
     *
     * @throws ValidationException
     */
    public function updateProfile(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255', 'unique:users,name,' . $user->id],
            'email' => ['required', 'email:rfc,dns', 'unique:users,email,' . $user->id, new DisposableEmailRule()],
            'country_code' => ['nullable', 'string', 'size:2', new CountryCodeRule()],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'country_code' => $validated['country_code'],
        ]);

        Flux::toastSuccess(__('Profile updated successfully.'));
    }

    /**
     * Update the user's password.
     *
     * @throws ValidationException
     */
    public function updatePassword(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The provided password does not match your current password.'),
            ]);
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        Flux::toastSuccess(__('Password updated successfully.'));
    }

    public function render(): ViewInterface
    {
        return view('web::livewire.auth.settings');
    }
}
