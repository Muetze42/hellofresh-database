<?php

use App\Http\Controllers\Api\CountryController;
use Illuminate\Support\Facades\Route;

Route::get('/', static fn () => response()->json(['message' => 'It works!']));

Route::apiResource('countries', CountryController::class)
    ->only(['index']);
