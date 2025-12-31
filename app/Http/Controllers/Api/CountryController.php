<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CountryCollection;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): CountryCollection
    {
        return new CountryCollection(
            Country::active()->paginate(validated_per_page($request))
        );
    }
}
