<?php

namespace App\Livewire\Actions;

use App\Models\User;
use App\Rules\CountryCodeRule;
use App\Rules\DisposableEmailRule;
use Illuminate\Auth\Events\Registered as RegisteredEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterUserAction
{
    /**
     * Validation rules for user registration.
     *
     * @return array<string, array<int, mixed>>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email:rfc', 'unique:users,email', new DisposableEmailRule()],
            'password' => ['required', 'confirmed', Password::defaults()],
            'country_code' => ['nullable', 'string', 'size:2', new CountryCodeRule()],
            'acceptPrivacy' => ['accepted'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'acceptPrivacy.accepted' => __('You must accept the privacy policy.'),
        ];
    }

    /**
     * Register a new user.
     *
     * @param  array{name: string, email: string, password: string, country_code: ?string}  $validated
     */
    public function __invoke(array $validated): User
    {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'country_code' => $validated['country_code'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new RegisteredEvent($user));

        return $user;
    }
}
