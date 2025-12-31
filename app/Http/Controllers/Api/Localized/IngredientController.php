<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Resources\Api\IngredientResource;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IngredientController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return IngredientResource::collection(
            Ingredient::where('country_id', $this->country()->id)
                ->when($request->filled('search'), function (Builder $query) use ($request): void {
                    $query->whereLike('name', '%' . $request->string('search') . '%');
                })
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->paginate(validated_per_page($request))
        );
    }
}
