<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecipeController;
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

Route::get('/', HomeController::class)
    ->name('home');

Route::prefix('{country_lang}')
    ->group(function () {
        Route::post('/login', [LoginController::class, 'login'])
            ->name('auth.login');
        Route::post('/logout', [LoginController::class, 'logout'])
            ->name('auth.logout');
        Route::post('/register', [RegisterController::class, 'register'])
            ->name('auth.register');

        Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
            ->name('password.email');

        Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
            ->middleware('guest')->name('show.reset.form');
        Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
            ->name('password.reset');
        Route::get('/password/reset', fn () => abort(404));

        Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
            ->middleware(['auth', 'throttle:6,1'])->name('verification.send');
        Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
            ->middleware(['auth', 'signed'])->name('verification.verify');

        Route::get('/menus', [RecipeController::class, 'findMenu'])
            ->name('menus.find');
        Route::get('/menus/{menu}', [RecipeController::class, 'index'])
            ->where('menu', '[0-9]+')
            ->name('recipes.menus');

        Route::get('/shopping-list', [ShoppingListController::class, 'index'])
            ->name('shopping-list.index');
        Route::post('/shopping-list', [ShoppingListController::class, 'data'])
            ->name('shopping-list.data');

        Route::post('/filter', fn (Request $request) => FilterRequest::make($request))->name('filter');

        Route::post('/filters/{model}', [FilterController::class, 'index'])
            ->name('filter.index');

        Route::get('/', [RecipeController::class, 'index'])->name('recipes.index');
    });
