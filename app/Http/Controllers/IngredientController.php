<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        dd(__CLASS__);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        dd($ingredient);
    }
}
