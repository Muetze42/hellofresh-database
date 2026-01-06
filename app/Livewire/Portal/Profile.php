<?php

namespace App\Livewire\Portal;

use App\Livewire\AbstractComponent;
use App\Livewire\Concerns\ManagesUserProfileTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('portal::components.layouts.app')]
class Profile extends AbstractComponent
{
    use ManagesUserProfileTrait;

    public string $delete_confirmation = '';

    public function mount(): void
    {
        $this->mountUserProfile();
    }

    /**
     * Redirect to profile page after email change.
     */
    protected function afterEmailChanged(): void
    {
        $this->redirect(route('portal.profile'), navigate: true);
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

        $user->tokens()->delete();

        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        $user->delete();

        $this->redirect(route('portal.login'), navigate: true);
    }

    public function render(): View
    {
        return view('portal::livewire.profile')
            ->title('Profile');
    }
}
