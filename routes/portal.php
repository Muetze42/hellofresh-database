<?php

use App\Http\Controllers\Portal\ApiSpecDownloadController;
use App\Http\Controllers\Portal\VerifyEmailController;
use App\Livewire\Actions\Portal\LogoutAction;
use App\Livewire\Portal\Admin\ApiUsage;
use App\Livewire\Portal\Admin\UserIndex;
use App\Livewire\Portal\Admin\UserShow;
use App\Livewire\Portal\Auth\ForgotPassword;
use App\Livewire\Portal\Auth\Login;
use App\Livewire\Portal\Auth\Register;
use App\Livewire\Portal\Auth\ResetPassword;
use App\Livewire\Portal\Auth\VerifyEmail;
use App\Livewire\Portal\Changelog;
use App\Livewire\Portal\Dashboard;
use App\Livewire\Portal\Docs\AllergensDoc;
use App\Livewire\Portal\Docs\CountriesDoc;
use App\Livewire\Portal\Docs\DocsIndex;
use App\Livewire\Portal\Docs\GetStartedDoc;
use App\Livewire\Portal\Docs\IngredientsDoc;
use App\Livewire\Portal\Docs\LabelsDoc;
use App\Livewire\Portal\Docs\MenusIndexDoc;
use App\Livewire\Portal\Docs\MenusShowDoc;
use App\Livewire\Portal\Docs\RecipesIndexDoc;
use App\Livewire\Portal\Docs\RecipesShowDoc;
use App\Livewire\Portal\Docs\TagsDoc;
use App\Livewire\Portal\Legal\PrivacyPolicy;
use App\Livewire\Portal\Legal\TermsOfUse;
use App\Livewire\Portal\Profile;
use App\Livewire\Portal\Recipe\RecipeLists;
use App\Livewire\Portal\Recipe\RecipeListShow;
use App\Livewire\Portal\Resources\AllergensIndex;
use App\Livewire\Portal\Resources\CuisinesIndex;
use App\Livewire\Portal\Resources\IngredientsIndex;
use App\Livewire\Portal\Resources\LabelsIndex;
use App\Livewire\Portal\Resources\ResourcesIndex;
use App\Livewire\Portal\Resources\TagsIndex;
use App\Livewire\Portal\Resources\UtensilsIndex;
use App\Livewire\Portal\Stats\ApiStats;
use App\Livewire\Portal\Stats\RecipeStats;
use App\Livewire\Portal\Stats\StatsIndex;
use App\Livewire\Portal\Stats\UserStats;
use App\Livewire\Portal\Tokens\TokenIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal Routes
|--------------------------------------------------------------------------
|
| Routes for the API Portal subdomain. These routes provide API documentation,
| token management, and authentication for API consumers.
|
*/

// Guest routes (only for unauthenticated users)
Route::middleware('guest')->group(function (): void {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// Public routes (accessible to everyone)
Route::get('/', Dashboard::class)->name('dashboard');
Route::get('changelog', Changelog::class)->name('changelog');
Route::get('privacy', PrivacyPolicy::class)->name('privacy');
Route::get('terms', TermsOfUse::class)->name('terms');

// Statistics routes
Route::prefix('stats')->name('stats.')->group(function (): void {
    Route::get('/', StatsIndex::class)->name('index');
    Route::get('users', UserStats::class)->name('users');
    Route::get('recipes', RecipeStats::class)->name('recipes');
    Route::get('api', ApiStats::class)->name('api');
});

// Resources routes
Route::prefix('resources')->name('resources.')->group(function (): void {
    Route::get('/', ResourcesIndex::class)->name('index');
    Route::get('ingredients', IngredientsIndex::class)->name('ingredients');
    Route::get('allergens', AllergensIndex::class)->name('allergens');
    Route::get('tags', TagsIndex::class)->name('tags');
    Route::get('labels', LabelsIndex::class)->name('labels');
    Route::get('cuisines', CuisinesIndex::class)->name('cuisines');
    Route::get('utensils', UtensilsIndex::class)->name('utensils');
});

// API Documentation (public, no authentication required)
Route::prefix('docs')->name('docs.')->group(function (): void {
    Route::get('/', DocsIndex::class)->name('index');
    Route::get('get-started', GetStartedDoc::class)->name('get-started');
    Route::get('countries', CountriesDoc::class)->name('countries');
    Route::get('recipes', RecipesIndexDoc::class)->name('recipes');
    Route::get('recipes-show', RecipesShowDoc::class)->name('recipes-show');
    Route::get('menus', MenusIndexDoc::class)->name('menus');
    Route::get('menus-show', MenusShowDoc::class)->name('menus-show');
    Route::get('tags', TagsDoc::class)->name('tags');
    Route::get('labels', LabelsDoc::class)->name('labels');
    Route::get('allergens', AllergensDoc::class)->name('allergens');
    Route::get('ingredients', IngredientsDoc::class)->name('ingredients');

    // API Specs Downloads
    Route::get('download/openapi', [ApiSpecDownloadController::class, 'openapi'])->name('download.openapi');
    Route::get('download/postman', [ApiSpecDownloadController::class, 'postman'])->name('download.postman');
});

// Authenticated routes (require login)
Route::middleware('auth')->group(function (): void {
    Route::get('profile', Profile::class)->name('profile');
    Route::get('recipe-lists', RecipeLists::class)->name('recipe-lists.index');
    Route::get('recipe-lists/{recipeList}', RecipeListShow::class)->name('recipe-lists.show')
        ->where('recipeList', '[0-9]+');
    Route::post('logout', LogoutAction::class)->name('logout');

    // Email verification
    Route::get('email/verify', VerifyEmail::class)->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // API Tokens
    Route::get('tokens', TokenIndex::class)->name('tokens.index');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('users', UserIndex::class)->name('users');
        Route::get('users/{user}', UserShow::class)->name('users.show')->where('user', '[0-9]+');
        Route::get('api-usage', ApiUsage::class)->name('api-usage');
    });
});
