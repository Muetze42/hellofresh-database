## General code instructions

- Don't generate code comments above the methods or code blocks if they are obvious. Generate comments only for something that needs extra explanation for the reasons why that code was written

---

## Core Principles

- Write concise, technical responses with accurate PHP/Laravel examples
- Prioritize SOLID principles for object-oriented programming and clean architecture
- Follow PHP and Laravel best practices, ensuring consistency and readability
- Design for scalability and maintainability, ensuring the system can grow with ease
- Prefer iteration and modularization over duplication to promote code reuse
- Use consistent and descriptive names for variables, methods, and classes to improve readability
- Always prefer **explicit over implicit** behavior in code to improve maintainability and predictability
- Keep **imports sorted alphabetically** and group framework imports, third-party packages, and local code separately
- Maintain a **single responsibility** for each file/class. If a class grows beyond one clear responsibility, refactor it
- Avoid “magic numbers” and “magic strings” — use enums

## PHP instructions

- Leverage PHP 8.3+ features when appropriate (e.g., typed properties, match expressions)
- Adhere to PSR-12 coding standards for consistent code style
- In PHP, use `match` operator over `switch` whenever possible
- Use PHP 8 constructor property promotion. Don't create an empty Constructor method if it doesn't have any parameters.
- Using Services in Controllers: if Service class is used only in ONE method of Controller, inject it directly into that method with type-hinting. If Service class is used in MULTIPLE methods of Controller, initialize it in Constructor.
- Use return types in functions whenever possible, adding the full path to classname to the top in `use` section
- **Static Analysis:** All PHP code must pass **PHPStan** at `--level=max` without errors or warnings.
- Use precise type hints everywhere. Avoid `mixed` or untyped values unless absolutely necessary (document such cases with explicit PHPDoc).
- Use generics for collections and Eloquent relations where applicable.
- Remove unused imports, variables, and dead code.
- Never suppress PHPStan/Larastan errors with `@phpstan-ignore-next-line` unless there is no other possible fix and document the reason
- Avoid using `isset()` for null checks; prefer strict comparisons where possible

---

## Laravel instructions

- For DB pivot tables, use correct alphabetical order, like "project_role" instead of "role_project"
- I am using Laravel Herd locally, so always assume that the main URL of the project is `http://[folder_name].test`
- **Eloquent Observers** should be registered in Eloquent Models with PHP Attributes, and not in AppServiceProvider. Example: `#[ObservedBy([UserObserver::class])]` with `use Illuminate\Database\Eloquent\Attributes\ObservedBy;` on top
- When generating Controllers, put validation in Form Request classes
- Aim for "slim" Controllers and put larger logic pieces in Service classes
- Use Laravel helpers instead of `use` section classes whenever possible. Examples: use `auth()->id()` instead of `Auth::id()` and adding `Auth` in the `use` section. Another example: use `redirect()->route()` instead of `Redirect::route()`.
- Utilize Laravel's built-in features and helpers to maximize efficiency
- **Static Analysis:** All Laravel code must pass **PHPStan** with **Larastan** integration at `--level=max` without errors or warnings.
- Use Larastan's Laravel-specific type improvements for models, relationships, factories, and query scopes.
- Always type Eloquent relationships with generics (e.g., `BelongsTo<User, Post>`) and include `@method` / `@property` annotations for IDE/static analysis support.
- Avoid dynamic properties and magic calls when a strongly typed alternative exists.
- Keep **database migrations atomic**; don’t mix schema changes and data changes in the same migration
- Always use `foreignIdFor()` instead of manually writing foreign keys, to keep migrations consistent
- Keep route definitions consistent: **grouped routes** for related features, **controller single-action routes** where applicable

---

## Use Laravel 11+ skeleton structure

- **Service Providers**: there are no other service providers except AppServiceProvider. Don't create new service providers unless absolutely necessary. Use Laravel 11+ new features, instead. Or, if you really need to create a new service provider, register it in `bootstrap/providers.php` and not `config/app.php` like it used to be before Laravel 11.
- **Event Listeners**: since Laravel 11, Listeners auto-listen for the events if they are type-hinted correctly.
- **Console Scheduler**: scheduled commands should be in `routes/console.php` and not `app/Console/Kernel.php` which doesn't exist since Laravel 11.
- **Middleware**: whenever possible, use Middleware by class name in the routes. But if you do need to register Middleware alias, it should be registered in `bootstrap/app.php` and not `app/Http/Kernel.php` which doesn't exist since Laravel 11.
- **Tailwind**: in new Blade pages, use Tailwind and not Bootstrap, unless instructed otherwise in the prompt. Tailwind is already pre-configured since Laravel 11, with Vite.
- **Faker**: in Factories, use `fake()` helper instead of `$this->faker`.
- **Policies**: Laravel automatically auto-discovers Policies, no need to register them in the Service Providers.

---

## Testing instructions

Use PHPUnit and not Pest. Run tests with `php artisan test`.

Every test method should be structured with Arrange-Act-Assert.

In the Arrange phase, use Laravel factories but add meaningful column values and variable names if they help to understand failed tests better.
Bad example: `$user1 = User::factory()->create();`
Better example: `$adminUser = User::factory()->create(['email' => 'admin@admin.com'])`;

In the Assert phase, perform these assertions when applicable:
- HTTP status code returned from Act: `assertStatus()`
- Structure/data returned from Act (Blade or JSON): functions like `assertViewHas()`, `assertSee()`, `assertDontSee()` or `assertJsonContains()`
- Or, redirect assertions like `assertRedirect()` and `assertSessionHas()` in case of Flash session values passed
- DB changes if any create/update/delete operation was performed: functions like `assertDatabaseHas()`, `assertDatabaseMissing()`, `expect($variable)->toBe()` and similar.
- Always test **happy path** and **failure path** for each feature
- Avoid relying on global state in tests — reset database and cache before each test class
- For Laravel feature tests, prefer `assertExactJson()` where possible to avoid false positives


## MCP

- Context7 should use for Next.js, Shadcn UI, Tailwind CSS, React, Vue.js, Laravel, Filament, Flux UI, Laravel Livewire,
  Laravel Pulse, Inertia.js, Laravel Medialibrary
