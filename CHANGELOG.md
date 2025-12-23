# Changelog

All notable changes to this project will be documented in this file.

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

