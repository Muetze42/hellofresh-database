<?php

use App\Http\Controllers\HomeController;
use App\Http\Middleware\CountryMiddleware;
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

Route::get('/', HomeController::class)->withoutMiddleware(CountryMiddleware::class)->name('home');

Route::prefix('{country_lang}')->group(function () {
    Route::get('debug', fn () => dd((new \App\Http\Clients\HelloFreshClient())->menu(-100)));
});
