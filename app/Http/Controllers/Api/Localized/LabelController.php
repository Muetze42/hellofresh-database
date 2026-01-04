<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Requests\Api\ListIndexRequest;
use App\Http\Resources\Api\LabelCollection;
use App\Models\Label;
use Illuminate\Database\Eloquent\Builder;

class LabelController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListIndexRequest $request): LabelCollection
    {
        return new LabelCollection(
            Label::where('country_id', $this->country()->id)
                ->tap(fn (Builder $query): Builder => $this->applySorting($query, $request))
                ->paginate(validated_per_page($request))
        );
    }
}
