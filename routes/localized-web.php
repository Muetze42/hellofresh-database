<?php

use App\Http\Controllers\FilterController;
use App\Http\Controllers\RecipeMenuController;
use App\Support\Requests\FilterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/menus/{menu}', [RecipeMenuController::class, 'index'])
    ->where('menu', '[0-9]+')
    ->name('recipes.menus');
Route::get('/', [RecipeMenuController::class, 'index'])->name('recipes.index');
//Route::get('{recipe}', [RecipeController::class, 'show'])->name('recipes.show');

Route::post('filter', fn (Request $request) => FilterRequest::make($request))->name('filter');

Route::post('filters/{model}', [FilterController::class, 'index'])
    ->name('filter.index');
