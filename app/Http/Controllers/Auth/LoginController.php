<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    use ThrottlesLogins;

    /**
     * The maximum number of attempts to allow.
     */
    protected int $maxAttempts = 5;

    /**
     * The number of minutes to throttle for.
     */
    protected int $decayMinutes = 2;
}
