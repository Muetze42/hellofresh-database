<?php

namespace App\Livewire\Web\Auth;

use App\Livewire\AbstractComponent;
use App\Rules\CountryCodeRule;
use App\Rules\DisposableEmailRule;
use App\Support\Facades\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

#[Layout('web::components.layouts.localized')]
class AccountSetting extends AbstractComponent
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public ?string $country_code = null;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?TemporaryUploadedFile $avatar = null;

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

        $emailWasVerified = $user->hasVerifiedEmail();
        $emailChanged = $user->email !== $validated['email'];

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'country_code' => $validated['country_code'],
        ]);

        if ($emailChanged && $emailWasVerified && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            Flux::toast(__('Profile updated. Please verify your new email address.'));

            $this->afterEmailChanged();

            return;
        }

        Flux::toastSuccess(__('Profile updated successfully.'));

        if ($emailChanged) {
            $this->afterEmailChanged();
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
                'current_password' => __('The provided password does not match your current password.'),
            ]);
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        Flux::toastSuccess(__('Password updated successfully.'));
    }

    /**
     * Update the user's avatar.
     *
     * @throws ValidationException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function updateAvatar(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $this->validate([
            'avatar' => [
                'required',
                'mimes:jpg,jpeg',
                'max:' . (2 * 1024),
                Rule::dimensions()
                    ->minWidth(200)
                    ->minHeight(200)
                    ->maxWidth(1000)
                    ->maxHeight(1000)
                    ->ratio(1),
            ],
        ]);

        /** @var TemporaryUploadedFile $avatar */
        $avatar = $this->avatar;

        $user->addMedia($avatar->getRealPath())
            ->usingFileName(Str::slug($user->name) . '.jpg')
            ->toMediaCollection('avatar');

        $this->reset('avatar');

        Flux::toastSuccess(__('Avatar updated successfully.'));
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $user->clearMediaCollection('avatar');

        Flux::toastSuccess(__('Avatar removed successfully.'));
    }

    /**
     * Cancel the pending avatar upload.
     */
    public function cancelAvatarUpload(): void
    {
        $this->reset('avatar');
    }

    /**
     * Get the current avatar URL.
     */
    public function getAvatarUrlProperty(): ?string
    {
        return auth()->user()?->getFirstMediaUrl('avatar', 'md') ?: null;
    }

    protected function afterEmailChanged(): void
    {
        // Override in child class if needed
    }

    public function render(): View
    {
        return view('livewire.settings');
    }
}
