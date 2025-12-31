<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Resources\Api\AllergenCollection;
use App\Models\Allergen;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AllergenController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AllergenCollection
    {
        return new AllergenCollection(
            Allergen::where('country_id', $this->country()->id)
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->paginate(validated_per_page($request))
        );
    }
}
