# Changelog

All notable changes to this project will be documented in this file.

## [3.10.0] - 2026-01-03

### Added

- **Clickable Tags** - Tags on recipe cards can now be clicked to filter recipes
    - Toggle setting in header menu under "Display Settings"
    - Active tags are highlighted in green with a checkmark
    - Works on Recipes, Random Recipes, and Menu pages
    - Setting is stored in browser localStorage

## [3.9.0] - 2026-01-03

### Added

- **Portal Statistics** - Comprehensive database statistics dashboard
    - Global stats: Total recipes, ingredients, menus, and countries
    - Recipe quality metrics: PDFs available, missing images, missing nutrition info
    - User engagement: Registered users, recipe lists, saved recipes
    - Top 10 lists: Most used ingredients, tags, and cuisines
    - Monthly recipe trend: Recipes added per month over the last 12 months
    - Average prep times by country
    - Difficulty distribution with visual progress bars
    - Recently added recipes overview
- **Enhanced Country Statistics** - Sortable table with country flags, localized names, supported locales, and PDF recipe counts

## [3.8.0] - 2026-01-02

### Added

- **Mixed-Country Recipe Lists** - Recipe lists can now contain recipes from different countries ([#144](https://github.com/Muetze42/hellofresh-database/issues/144))
    - Portal shows all recipes grouped by country with flag indicators
    - Main site shows only recipes from the current country with a count of recipes from other countries

## [3.7.0] - 2026-01-02

### Added

- **Country Selection** - Users can select their country in profile settings and during registration

## [3.6.0] - 2026-01-01

### Added

- **Random Recipes** - Discover random recipes with a shuffle button; supports all existing filters (allergens, ingredients, tags, difficulty, cooking time)

## [3.5.0] - 2026-01-01

### Added

- **Public REST API** - Full-featured API for accessing recipe data programmatically
    - Bearer token authentication via Laravel Sanctum
    - Configurable rate limiting (default: 60 requests/minute)
    - Localized endpoints (`/{locale}-{country}/...`)
    - Unified pagination on all list endpoints
- **API Portal** - Developer portal for API access
    - Token management (create, view, revoke API tokens)
    - Interactive API documentation
    - Get Started guide with authentication, localization, and pagination info
- **API Endpoints**
    - Recipes (list, show with saved lists info for authenticated users)
    - Menus (list, show with recipes)
    - Ingredients, Countries, Tags, Labels, Allergens
- **Response Enhancements**
    - Website URLs in Recipe and Menu responses
    - `saved_in_lists` field for authenticated users on Recipe show

## [3.4.0] - 2025-12-30

### Added

- **Global Search** - Command palette accessible via ⌘K/Ctrl+K or search button in navbar; searches recipes and displays associated menu weeks
- **Recipe Page Search** - Search field on recipes page that filters by name, headline, tags, ingredients, and labels

## [3.2.0] - 2025-12-25

### Added

- **Recipe List Sharing** - Share recipe lists with other users via email
- **Activity Tracking** - Track additions, removals, and sharing actions on recipe lists

## [3.1.1] - 2025-12-23

- **Remember Me** - "Stay logged in" option on login form

## [3.1.0] - 2025-12-23

### Added

- **Bring Shopping List Export** - Export shopping list directly to Bring! app ([#54](https://github.com/Muetze42/hellofresh-database/pull/54), [Discussion #41](https://github.com/Muetze42/hellofresh-database/discussions/41))
- **Mini-Cart Component** - Floating shopping list preview button ([#55](https://github.com/Muetze42/hellofresh-database/pull/55), [Discussion #34](https://github.com/Muetze42/hellofresh-database/discussions/34))
- **Legacy URL Redirects** - Automatic 301 redirects from old lowercase URLs (e.g., `/de-de`) to new format (`/de-DE`)

## [3.0.0] - 2025-12-23

### Complete Rewrite

This version represents a complete rewrite of the application, moving from Vue.js/Inertia.js to Livewire/Flux UI.

### Changed

- **Tech Stack Migration**
    - Replaced Vue.js 3 with Livewire 3
    - Replaced Inertia.js with native Livewire navigation
    - Replaced custom components with Flux UI Pro components
    - Upgraded to Laravel 12
    - Upgraded to Tailwind CSS 4
    - Switched from Font Awesome to Lucide icons

### Added

- **User Accounts**
    - User registration and login
    - Password management
    - Privacy policy acceptance
    - Rate limiting and security enhancements

- **Recipe Lists**
    - Create custom recipe lists
    - Add/remove recipes from lists
    - Edit list name and description
    - Pillbox UI for quick list selection

- **Shopping List**
    - Print functionality (with/without checkboxes, combined/by recipe)
    - Save shopping lists for later
    - Load saved shopping lists

- **Recipe Details Page**
    - Full recipe view with ingredients, steps, nutrition
    - Dynamic serving size selection
    - Allergens and utensils display
    - Similar recipes suggestions
    - Links to HelloFresh and PDF

- **UI/UX Improvements**
    - Dark mode support with system preference detection
    - Responsive design for mobile and desktop
    - Grid and list view modes for recipes
    - Advanced filtering (ingredients, tags, labels, allergens, difficulty, prep time)
    - Sorting options
    - Localized routes for all supported regions

- **Internationalization**
    - Full translation support for 9 languages (DE, EN, NL, FR, DA, ES, IT, NO, SE)
    - Localized routing per country

- **Backend Improvements**
    - Horizon queue management
    - Reverb real-time broadcasting support

### Removed

- Vue.js components
- Inertia.js middleware and adapters

