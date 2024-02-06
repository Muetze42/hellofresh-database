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
        app()->setLocale('de');

        $recipe = \App\Models\Recipe::find('65801b0f16590f43e9698d65');

        /* @var \App\Models\Recipe $recipe */

        return $recipe->yields->toArray();

        return Inertia::render('Home/Index', [
            'ip' => $request->ip(),
        ]);
    }
}
