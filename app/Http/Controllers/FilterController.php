<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function ingredients(Request $request)
    {
        if (!$query = $request->input('query')) {
            return [];
        }

        return Ingredient::where('name', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'name'])->toArray();
    }
}
