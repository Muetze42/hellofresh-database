<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Requests\Api\ListIndexRequest;
use App\Http\Resources\Api\TagCollection;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

class TagController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListIndexRequest $request): TagCollection
    {
        return new TagCollection(
            Tag::where('country_id', $this->country()->id)
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->paginate(validated_per_page($request))
        );
    }
}
