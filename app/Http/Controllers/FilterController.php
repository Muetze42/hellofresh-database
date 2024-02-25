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
        $model = 'App\Models\\' . Str::studly(Str::singular($model));
        if (!class_exists($model)) {
            abort(404, 'Model not found');
        }
        $model = app($model);

        return $model::whereRaw('LOWER(name) like ?', ['%' . Str::lower($query) . '%'])
            ->orWhere('id', 'like', '%' . $query . '%')
            ->limit(100)
            ->get(['id', 'name'])->toArray();
    }
}
