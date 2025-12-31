<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Resources\Api\AllergenResource;
use App\Models\Allergen;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AllergenController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return AllergenResource::collection(
            Allergen::where('country_id', $this->country()->id)
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->get()
        );
    }
}
