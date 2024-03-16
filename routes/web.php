<?php

use App\Http\Controllers\FilterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecipeMenuController;
use App\Http\Controllers\ShoppingListController;
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

Route::get('/', HomeController::class)->name('home');

Route::prefix('{country_lang}')
    ->group(function () {
        Route::get('/menus', [RecipeMenuController::class, 'findMenu'])
            ->name('menus.find');
        Route::get('/menus/{menu}', [RecipeMenuController::class, 'index'])
            ->where('menu', '[0-9]+')
            ->name('recipes.menus');

        Route::get('shopping-list', [ShoppingListController::class, 'index'])
            ->name('shopping-list.index');
        Route::post('shopping-list', [ShoppingListController::class, 'data'])
            ->name('shopping-list.data');

        Route::post('filter', fn (Request $request) => FilterRequest::make($request))->name('filter');

        Route::post('filters/{model}', [FilterController::class, 'index'])
            ->name('filter.index');

        Route::get('/', [RecipeMenuController::class, 'index'])->name('recipes.index');
    });
