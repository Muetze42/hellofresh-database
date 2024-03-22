<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    use ThrottlesLogins;

    /**
     * The user has been authenticated.
     */
    public function authenticated(Request $request, $user)
    {
        return new JsonResponse([
            'successful' => true,
        ]);
    }
}
