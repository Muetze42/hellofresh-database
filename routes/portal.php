<?php

use App\Livewire\Portal\Auth\Login;
use App\Livewire\Portal\Auth\Register;
use App\Livewire\Portal\Auth\VerifyEmail;
use App\Livewire\Portal\Dashboard;
use App\Livewire\Portal\Docs\AllergensDoc;
use App\Livewire\Portal\Docs\IngredientsDoc;
use App\Livewire\Portal\Docs\LabelsDoc;
use App\Livewire\Portal\Docs\MenusIndexDoc;
use App\Livewire\Portal\Docs\MenusShowDoc;
use App\Livewire\Portal\Docs\RecipesIndexDoc;
use App\Livewire\Portal\Docs\RecipesShowDoc;
use App\Livewire\Portal\Docs\TagsDoc;
use App\Livewire\Portal\Profile;
use App\Livewire\Portal\Tokens\TokenIndex;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
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
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Public routes (accessible to everyone)
Route::get('/', Dashboard::class)->name('dashboard');

// API Documentation (public, no authentication required)
Route::prefix('docs')->name('docs.')->group(function (): void {
    Route::get('/recipes', RecipesIndexDoc::class)->name('recipes');
    Route::get('/recipes-show', RecipesShowDoc::class)->name('recipes-show');
    Route::get('/menus', MenusIndexDoc::class)->name('menus');
    Route::get('/menus-show', MenusShowDoc::class)->name('menus-show');
    Route::get('/tags', TagsDoc::class)->name('tags');
    Route::get('/labels', LabelsDoc::class)->name('labels');
    Route::get('/allergens', AllergensDoc::class)->name('allergens');
    Route::get('/ingredients', IngredientsDoc::class)->name('ingredients');
});

// Authenticated routes (require login)
Route::middleware('auth')->group(function (): void {
    // Profile
    Route::get('/profile', Profile::class)->name('profile');

    // Email verification
    Route::get('/email/verify', VerifyEmail::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request): RedirectResponse {
        $request->fulfill();

        return to_route('portal.dashboard')
            ->with('success', 'Your email has been verified successfully.');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    // Logout
    Route::post('/logout', function (): Redirector|RedirectResponse {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return to_route('portal.login');
    })->name('logout');

    // API Tokens
    Route::prefix('tokens')->name('tokens.')->group(function (): void {
        Route::get('/', TokenIndex::class)->name('index');
    });
});
