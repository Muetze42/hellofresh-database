<?php

use App\Http\Controllers\Api\Localized\AllergenController;
use App\Http\Controllers\Api\Localized\IngredientController;
use App\Http\Controllers\Api\Localized\LabelController;
use App\Http\Controllers\Api\Localized\MenuController;
use App\Http\Controllers\Api\Localized\RecipeController;
use App\Http\Controllers\Api\Localized\TagController;
use Illuminate\Support\Facades\Route;

Route::apiResource('recipes', RecipeController::class)
    ->only(['index', 'show'])
    ->where(['recipe' => '[0-9]+']);

Route::apiResource('menus', MenuController::class)
    ->only(['index', 'show'])
    ->where(['menu' => '[0-9]+']);

// Route::get('menus/current', [MenuController::class, 'current'])
//     ->name('menus.current');

Route::apiResource('tags', TagController::class)
    ->only(['index']);

Route::apiResource('labels', LabelController::class)
    ->only(['index']);

Route::apiResource('allergens', AllergenController::class)
    ->only(['index']);

Route::apiResource('ingredients', IngredientController::class)
    ->only(['index']);
