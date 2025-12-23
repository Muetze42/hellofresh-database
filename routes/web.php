<?php

use App\Http\Controllers\OgImageController;
use App\Livewire\Actions\LogoutAction;
use App\Livewire\RegionSelect;
use Illuminate\Support\Facades\Route;

Route::get('/', RegionSelect::class)->name('region.select');

Route::post('logout', LogoutAction::class)
    ->middleware('auth')
    ->name('logout');

// OG Image routes
Route::get('/og/recipe/{recipe}', [OgImageController::class, 'recipe'])
    ->where('recipe', '[0-9]+')
    ->name('og.recipe');

Route::get('/og/menu/{menu:year_week}', [OgImageController::class, 'menu'])
    ->name('og.menu');

Route::get('/og/generic', [OgImageController::class, 'generic'])
    ->name('og.generic');
