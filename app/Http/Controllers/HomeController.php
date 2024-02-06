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
        $recipe = \App\Models\Recipe::find('6571a15002bcfeab6556727f');

        /* @var \App\Models\Recipe $recipe */

        return $recipe->steps->toArray();

        return Inertia::render('Home/Index', [
            'ip' => $request->ip(),
        ]);
    }
}
