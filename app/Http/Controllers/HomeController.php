<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        \App\Models\Country::createFromHfApi();

        return Inertia::render('Home/Index', [
            'ip' => $request->ip(),
        ]);
    }
}
