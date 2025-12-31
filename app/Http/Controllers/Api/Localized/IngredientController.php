<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Resources\Api\IngredientCollection;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class IngredientController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): IngredientCollection
    {
        return new IngredientCollection(
            Ingredient::where('country_id', $this->country()->id)
                ->when($request->filled('search'), function (Builder $query) use ($request): void {
                    $query->whereLike('name', '%' . $request->string('search') . '%');
                })
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->paginate(validated_per_page($request))
        );
    }
}
