<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * The user has been verified.
     */
    protected function verified(Request $request)
    {
        $message = trans('Your email address has been confirmed');
        $request->session()->flash('message', $message);

        return new JsonResponse([], 204);
    }
}
