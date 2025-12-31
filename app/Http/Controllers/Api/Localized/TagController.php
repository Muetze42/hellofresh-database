<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Resources\Api\TagResource;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return TagResource::collection(
            Tag::where('country_id', $this->country()->id)
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->get()
        );
    }
}
