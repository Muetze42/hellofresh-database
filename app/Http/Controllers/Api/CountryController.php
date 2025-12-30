<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator<array-key, Country>
     */
    public function index(Request $request): LengthAwarePaginator
    {
        return Country::active()->paginate(validated_per_page($request));
    }
}
