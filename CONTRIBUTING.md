# Contributing to School Management System

Thank you for considering a contribution!

## Getting started

1. Fork the repository and clone your fork.
2. Install dependencies: `composer install && npm install`.
3. Copy `.env.example` to `.env`, generate a key, and run the migrations.
4. Run the test suite: `php artisan test`. Everything must pass before you start.

## How to contribute

1. Create a feature branch: `git checkout -b feature/your-feature`.
2. Make your changes with tests. New features require new tests.
3. Ensure the full suite passes: `php artisan test`.
4. Run the linter: `vendor/bin/pint`.
5. Open a pull request with a clear description of the change and why it is needed.

## Code standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/); Pint enforces the style.
- Keep controllers thin — business logic belongs in `app/Services`.
- Validate all input with form requests or explicit validation.
- Database changes must be migrations; never edit existing migrations.
- Every new permission boundary needs an authorization test.
- No hard-coded credentials, demo data in production code paths, or fake statistics.

## Reporting bugs

Open an issue with:
- Steps to reproduce
- Expected vs actual behaviour
- Environment (PHP, database, deployment method)

## Feature requests

Open an issue describing the use case first. This project values focused,
maintainable features over configurability for its own sake.
