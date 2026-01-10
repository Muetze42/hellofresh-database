# Contributing

Thank you for your interest in contributing to this project!

## Requirements

Unfortunately, this project requires a [Flux UI Pro](https://fluxui.dev/) license to run locally. Flux UI Pro is a paid component library for Livewire.

*Note: I have no affiliation with Flux UI, receive no referral benefits, and gain nothing from using it in this project.*

## Getting Started

1. Fork the repository
2. Clone your fork
3. Install dependencies: `composer install && pnpm install`
4. Copy `.env.example` to `.env` and configure your environment
5. Run migrations: `php artisan migrate`
6. Seed countries: `php artisan db:seed --class=CountrySeeder`
7. Build assets: `pnpm run build`

## Background Jobs

Many tasks are handled by background jobs: importing data from the HelloFresh API, updating statistics, caching recipe counts, and more.

```bash
php artisan app:launcher
```

The launcher provides an interactive menu to select and dispatch available jobs.

## Commits

Use [Conventional Commits](https://www.conventionalcommits.org/): `feat:`, `fix:`, `docs:`, `chore:`, etc.

## Pull Requests

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- New features require tests
- Ensure all checks pass before committing:
  - `composer test`
  - `vendor/bin/pint`
  - `vendor/bin/rector`
  - `vendor/bin/phpstan`
