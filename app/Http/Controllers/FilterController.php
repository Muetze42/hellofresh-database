<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

        $value = $model instanceof Label ? 'text' : 'name';

        $data = $model::whereRaw('LOWER(' . $value . ') like ?', ['%' . Str::lower($query) . '%'])
            ->orWhere('id', 'like', '%' . $query . '%')
            ->limit(100)
            ->get(['id', $value])->toArray();

        if ($model instanceof Label) {
            return Arr::map($data, fn (array $label) => [
                'id' => $label['id'],
                'name' => $label['text'],
            ]);
        }

        return $data;
    }
}
