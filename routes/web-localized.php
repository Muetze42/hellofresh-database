<?php

use App\Http\Controllers\MenuRedirectController;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\PrivacyPolicy\PrivacyPolicyShow;
use App\Livewire\Recipes\RecipeIndex;
use App\Livewire\Recipes\RecipeShow;
use App\Livewire\ShoppingList\ShoppingListIndex;
use App\Livewire\User\UserAccount;
use App\Livewire\User\UserRecipeLists;
use App\Livewire\User\UserShoppingLists;
use Illuminate\Support\Facades\Route;

Route::get('/', RecipeIndex::class)->name('recipes.index');
Route::get('recipes/{recipe}', RecipeShow::class)
    ->where('recipe', '[0-9]+')
    ->name('recipes.show');
Route::get('shopping-list', ShoppingListIndex::class)->name('shopping-list.index');
Route::get('shopping-list/print', ShoppingListIndex::class)->name('shopping-list.print');

Route::get('menus', MenuRedirectController::class)
    ->name('menus.index');
Route::get('menus/{menu:year_week}', RecipeIndex::class)
    ->where('menu', '[0-9]+')
    ->name('menus.show');

Route::get('privacy-policy', PrivacyPolicyShow::class)
    ->name('privacy-policy');

Route::get('password/reset/{token}', ResetPassword::class)
    ->middleware('guest')
    ->name('password.reset');

Route::middleware('auth')->group(function (): void {
    Route::get('settings', UserAccount::class)->name('settings');
    Route::get('lists', UserRecipeLists::class)->name('lists');
    Route::get('saved-shopping-lists', UserShoppingLists::class)->name('saved-shopping-lists');
});
