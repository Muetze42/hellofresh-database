<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilterController extends Controller
{
    public function index(Request $request, string $model)
    {
        if (!$query = $request->input('query')) {
            return [];
        }

        /* @var \App\Models\Ingredient|\App\Models\Allergen $model */
        $model = app('App\Models\\' . Str::ucfirst(Str::singular($model)));

        return $model::where('name', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name'])->toArray();
    }
}
