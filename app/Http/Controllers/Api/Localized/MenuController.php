<?php

namespace App\Http\Controllers\Api\Localized;

use App\Http\Resources\Api\MenuCollection;
use App\Http\Resources\Api\MenuResource;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MenuController extends AbstractLocalizedController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): MenuCollection
    {
        return new MenuCollection(
            Menu::selectable()
                ->where('country_id', $this->country()->id)
                ->when($request->boolean('include_recipes'), function (Builder $query): void {
                    $query->with(['recipes.label', 'recipes.tags']);
                })
                ->when($request->filled('from'), function (Builder $query) use ($request): void {
                    $query->where('start', '>=', $request->date('from'));
                })
                ->when($request->filled('to'), function (Builder $query) use ($request): void {
                    $query->where('start', '<=', $request->date('to'));
                })
                ->orderByDesc('year_week')
                ->paginate(validated_per_page($request))
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(int $yearWeek): MenuResource
    {
        $country = $this->country();

        $menu = Menu::where('country_id', $country->id)
            ->where('year_week', $yearWeek)
            ->with(['recipes.label', 'recipes.tags'])
            ->firstOrFail();

        return new MenuResource($menu);
    }

    /**
     * Display the current week's menu.
     */
    // public function current(): MenuResource
    // {
    //     $country = $this->country();
    //
    //     $menu = Menu::where('country_id', $country->id)
    //         ->where('start', '<=', now())
    //         ->orderByDesc('year_week')
    //         ->with(['recipes.label', 'recipes.tags'])
    //         ->firstOrFail();
    //
    //     return new MenuResource($menu);
    // }
}
