<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    use ThrottlesLogins;

    /**
     * The user has been authenticated.
     */
    public function authenticated(Request $request, $user)
    {
        return new JsonResponse([], 204);
    }
}
