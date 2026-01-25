<?php

use App\Http\Controllers\BringExportController;
use App\Http\Controllers\FilterShareController;
use App\Http\Controllers\MenuRedirectController;
use App\Http\Controllers\RecipeRedirectController;
use App\Livewire\Web\Auth\AccountSetting;
use App\Livewire\Web\Auth\ResetPassword;
use App\Livewire\Web\Legal\PrivacyPolicyShow;
use App\Livewire\Web\Legal\TermsOfUseShow;
use App\Livewire\Web\Recipes\RecipeIndex;
use App\Livewire\Web\Recipes\RecipeRandom;
use App\Livewire\Web\Recipes\RecipeShow;
use App\Livewire\Web\ShoppingList\ShoppingListIndex;
use App\Livewire\Web\User\UserRecipeLists;
use App\Livewire\Web\User\UserShoppingLists;
use Illuminate\Support\Facades\Route;

Route::get('/', RecipeIndex::class)->name('recipes.index');
Route::get('random', RecipeRandom::class)->name('recipes.random');

// Legacy redirects
Route::get('recipes/{slug}-{uuid}', RecipeRedirectController::class)
    ->where(['slug' => '.*', 'uuid' => '[a-f0-9]{24}'])
    ->name('recipes.redirect');

Route::get('recipes/{slug}-{recipe}', RecipeShow::class)
    ->where(['slug' => '.*', 'recipe' => '[0-9]+'])
    ->name('recipes.show');

Route::get('s/{id}', [FilterShareController::class, '__invoke'])->name('filter-share');

Route::get('shopping-list', ShoppingListIndex::class)->name('shopping-list.index');
Route::get('shopping-list/print', ShoppingListIndex::class)->name('shopping-list.print');
Route::get('shopping-list/bring', BringExportController::class)->name('shopping-list.bring');

Route::get('menus', MenuRedirectController::class)
    ->name('menus.index');
Route::get('menus/{menu:year_week}', RecipeIndex::class)
    ->where('menu', '[0-9]+')
    ->name('menus.show');

Route::get('privacy-policy', PrivacyPolicyShow::class)
    ->name('privacy-policy');

Route::get('terms-of-use', TermsOfUseShow::class)
    ->name('terms-of-use');

Route::get('password/reset/{token}', ResetPassword::class)
    ->middleware('guest')
    ->name('password.reset');

Route::middleware('auth.or.message')->group(function (): void {
    Route::get('settings', AccountSetting::class)->name('settings');
    Route::get('lists', UserRecipeLists::class)->name('lists');
    Route::get('saved-shopping-lists', UserShoppingLists::class)->name('saved-shopping-lists');
});
