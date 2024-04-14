<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use NormanHuth\Library\Rules\DisposableEmailRule;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * The user has been registered.
     */
    protected function registered(Request $request, $user)
    {
        $message = trans('Congratulations, your account has been successfully created');
        $request->session()->flash('message', $message);

        return new JsonResponse([], 204);
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'unique:users', 'max:' . Setting::get('users.name.max_length', 20)],
            'email' => [
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
