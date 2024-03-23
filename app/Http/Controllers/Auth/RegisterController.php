<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\DisposableEmailRule;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * The user has been registered.
     */
    protected function registered(Request $request, $user)
    {
        return new JsonResponse([], 201);
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'unique:users', 'max:' . config('application.users.name.max_length')],
            'email' =>
                [
                    'required', 'string', 'email:rfc,dns', 'max:255', 'unique:users', 'confirmed',
                    new DisposableEmailRule(),
                ],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            'privacy' => ['accepted'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }
}
