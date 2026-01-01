<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecipeRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $slug, string $uuid): RedirectResponse
    {
        $country = current_country();

        $recipe = Recipe::where('country_id', $country->id)
            ->where('hellofresh_id', $uuid)
            ->firstOrFail();

        return redirect(localized_route('localized.recipes.show', [
            'slug' => slugify($recipe->name),
            'recipe' => $recipe->id,
        ]));
    }
}
