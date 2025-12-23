# HelloFresh Database

HelloFresh recipe database for 16 countries with features like filtering by ingredients, allergens, tags, shopping lists, and more.

## Tech Stack

* [Laravel 12](https://laravel.com/)
* [Livewire 3](https://livewire.laravel.com/)
* [Flux UI](https://fluxui.dev/)
* [Tailwind CSS 4](https://tailwindcss.com/)
* [Sentry](https://sentry.io/) for error tracking

## Features

* Browse recipes from 16 HelloFresh countries
* Filter by ingredients, allergens, tags, labels, difficulty, prep time
* Shopping list with ingredient aggregation
* Save and load shopping lists
* Create custom recipe lists
* Dark mode support
* Multi-language support (DE, EN, NL, FR, DA, ES, IT, NO, SE)

## Links

* [Reddit Post (Active community)](https://www.reddit.com/r/hellofresh/comments/1b37z4x/hellofresh_recipes_database_relaunched/)
* [Ko-Fi Post](https://ko-fi.com/post/HelloFresh-Recipes-Database-Relaunched-D1D8V2OF7)
* [LinkedIn Post](https://www.linkedin.com/posts/normanhuth_hellofresh-database-activity-7169039554446901248-CrKH)

For wishes, suggestions and ideas, simply write a comment on Reddit, Ko-Fi, LinkedIn or create an
[issue](https://github.com/Muetze42/hellofresh-database/issues) or use the
[discussions area](https://github.com/Muetze42/hellofresh-database/discussions).

## Feature Ideas

Ideas for future development:

### User Experience
* **Week Planner** - Drag & drop recipes onto weekdays for meal planning
* **Cooking Mode** - Step-by-step view with large text for cooking
* **Random Recipe** - "What should I cook today?" button
* **Personal Notes** - Save your own notes on recipes

### Shopping List
* **Export** - Download as PDF, send via email, or share
* **Categories** - Group ingredients by supermarket aisle
* **Pantry Management** - Track what you already have at home

### Discovery
* **Similar Recipes** - Recommendations based on ingredients/tags
* **Recently Viewed** - Quick access to recently visited recipes
* **Seasonal Recipes** - Highlight what's currently in season

### Technical
* **PWA / Offline Mode** - Access recipes without internet
* **Push Notifications** - Get notified about new menu recipes

## Development

Translations can be found in the `/lang` directory.

### Deployment

Staging server update when merging into the main branch and production server update with every release.
