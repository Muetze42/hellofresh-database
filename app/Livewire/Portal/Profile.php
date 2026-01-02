<?php

namespace App\Livewire\Portal;

use App\Rules\DisposableEmailRule;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('portal::components.layouts.app')]
class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    public ?string $country_code = null;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $delete_confirmation = '';

    /**
     * Mount the component.
     */
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

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc', 'unique:users,email,' . $user->id, new DisposableEmailRule()],
            'country_code' => ['nullable', 'string', 'size:2'],
        ]);

        $emailWasVerified = $user->hasVerifiedEmail();
        $emailChanged = $user->email !== $this->email;

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'country_code' => $this->country_code,
        ]);

        if ($emailChanged && $emailWasVerified && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            Flux::toast('Profile updated. Please verify your new email address.');

            $this->redirect(route('portal.profile'), navigate: true);

            return;
        }

        Flux::toastSuccess('Profile updated successfully.');

        if ($emailChanged) {
            $this->redirect(route('portal.profile'), navigate: true);
        }
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
                'current_password' => 'The provided password does not match your current password.',
            ]);
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        Flux::toastSuccess('Password updated successfully.');
    }

    /**
     * Delete the user's account.
     *
     * @throws ValidationException
     */
    public function deleteAccount(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->validate([
            'delete_confirmation' => ['required', 'in:DELETE'],
        ], [
            'delete_confirmation.in' => 'Please type DELETE to confirm.',
        ]);

        // Delete all tokens
        $user->tokens()->delete();

        // Logout
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        // Delete the user
        $user->delete();

        $this->redirect(route('portal.login'), navigate: true);
    }

    public function render(): View
    {
        return view('portal::livewire.profile')
            ->title('Profile');
    }
}
