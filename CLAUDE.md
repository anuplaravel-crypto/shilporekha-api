# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.2. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.

- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>

# ==============================================================================
# ShilpoRekha Project Development Rules
# ==============================================================================
# These rules are project-specific and override default behavior whenever
# they are applicable. Where they conflict with the auto-generated Laravel
# Boost guidelines above, these project rules take precedence for this repo.
#
# Goal:
# Maintain clean architecture, consistency, scalability, and production-quality
# code throughout the ShilpoRekha project.
# ==============================================================================

# ==============================================================================
# 1. Project Architecture Rules
# ==============================================================================

## Layered Architecture

Always follow this architecture:

Controller
    ↓
Service
    ↓
Repository
    ↓
Model (Eloquent)
    ↓
Database

### Responsibilities

Controller
- Keep controllers thin.
- Receive Request.
- Call Service.
- Return View or JSON Response.
- Never contain business logic.
- Never write complex database queries.

Service
- All business logic belongs here.
- Services may call multiple repositories.
- Services should be reusable.
- Services should remain independent from HTTP requests whenever possible.

Repository
- All Eloquent queries belong here.
- Never duplicate database queries.
- Reuse repository methods whenever possible.

Model
- Only relationships.
- Accessors.
- Mutators.
- Casts.
- Query scopes.

Never place business logic inside Models.

# ==============================================================================
# 2. Feature Development Workflow
# ==============================================================================

Whenever implementing a new feature:

Step 1
Analyze existing implementation.

Step 2
Search for similar functionality.

Step 3
Reuse existing Services.

Step 4
Reuse existing Repositories.

Step 5
Create or update FormRequest.

Step 6
Update Repository.

Step 7
Update Service.

Step 8
Update Controller.

Step 9
Update Blade Views.

Step 10
Update Routes if necessary.

Step 11
Update Documentation if requested.

Step 12
Run Pint.

Step 13
Run related PHPUnit tests.

Never skip analysis before implementation.

# ==============================================================================
# 3. Code Reuse Policy
# ==============================================================================

Before writing any code:

Always search for:

- Similar Controller
- Similar Service
- Similar Repository
- Similar Blade
- Existing Helper
- Existing Validation

Never duplicate:

- Business logic
- Validation
- Queries
- Helper methods
- JavaScript functions

Prefer extending existing implementation instead of creating new code.

# ==============================================================================
# 4. API Response Standard
# ==============================================================================

Every JSON response should follow the same structure.

Success

{
    "status": true,
    "message": "Success",
    "data": {}
}

Validation Error

{
    "status": false,
    "message": "Validation Failed",
    "errors": {}
}

Server Error

{
    "status": false,
    "message": "Something went wrong."
}

Always use API Resources whenever applicable.

# ==============================================================================
# 5. Database Rules
# ==============================================================================

Always

- Use Eloquent Models.
- Use Repository pattern.
- Use eager loading.
- Use transactions for multiple writes.
- Add indexes where necessary.

Avoid

- Raw SQL
- Duplicate queries
- N+1 problems
- Business logic inside migrations

Never query the database directly inside Blade.

# ==============================================================================
# 6. Blade View Rules
# ==============================================================================

Blade is for presentation only.

Do

- Display data.
- Include Components.
- Include Partials.

Do NOT

- Query database.
- Write business logic.
- Write large PHP blocks.
- Duplicate layouts.

Move complex logic to Controller or Service.

Note: this repo has no Blade-rendered application UI (the public site and
admin panel are the separate `shilporekha-vue3` Vue 3 SPA) — but Blade still
applies here for Mailable views (order-confirmation emails, admin
notifications) and any other server-rendered output, so these rules stay in
force wherever Blade is actually used.

# ==============================================================================
# 7. JavaScript Rules
# ==============================================================================

Not applicable to this repo — see Section 6. This Laravel app ships no
frontend JavaScript of its own.

# ==============================================================================
# 8. Security Rules
# ==============================================================================

Always

Validate every request.

Authorize every admin API endpoint via middleware.

Verify the authenticated Sanctum session (`auth:sanctum` middleware) — this
project uses Sanctum's cookie-session SPA auth, not JWT tokens.

Escape output.

Protect routes using middleware.

Never

Trust client-side validation.

Bypass Sanctum's CSRF/cookie flow by inventing a custom token scheme.

Expose sensitive data.

Bypass authorization.

# ==============================================================================
# 9. Git Workflow Rules
# ==============================================================================

Before every commit:

Run

vendor/bin/pint --dirty --format agent

Run related tests.

Review git diff.

Use Conventional Commit messages.

Examples

feat:

fix:

refactor:

docs:

style:

test:

chore:

Never commit broken code.

# ==============================================================================
# 10. Claude Code Working Rules
# ==============================================================================

Before modifying code:

Read related files.

Understand existing implementation.

Explain impact if requested.

Reuse existing code.

Prefer minimal changes.

Never rewrite an entire file unless necessary.

Never rename methods without reason.

Never change route names unless requested.

Preserve existing functionality.

# ==============================================================================
# 11. Large Feature Analysis Rules
# ==============================================================================

Before implementing a major feature, always analyze:

Routes

Controllers

Services

Repositories

Models

Migrations

Middleware

Notifications

Views (in `shilporekha-vue3`, where applicable)

JavaScript (in `shilporekha-vue3`, where applicable)

Validation

Tests

Documentation

After analysis, explain implementation plan before writing code if requested.

# ==============================================================================
# 12. Refactoring Rules
# ==============================================================================

Refactoring must NEVER change functionality.

Goals

Improve readability.

Improve architecture.

Improve performance.

Reduce duplication.

Increase maintainability.

Never change

Routes

Database schema

API response

Frontend behavior

unless explicitly requested.

# ==============================================================================
# 13. Documentation Rules
# ==============================================================================

When documentation is requested:

Update relevant documentation.

Maintain consistency.

Keep examples accurate.

Use existing project terminology.

Do not create unnecessary documentation files.

# ==============================================================================
# 14. Performance Rules
# ==============================================================================

Prefer

Eager Loading

Pagination

Lazy Collections

Caching where appropriate

Queue for long-running jobs

Avoid

Duplicate queries

Repeated notifications

Loading unnecessary data

Large loops with database queries

Always consider scalability.

# ==============================================================================
# 15. Code Quality Rules
# ==============================================================================

Follow

SOLID

DRY

KISS

YAGNI

Single Responsibility Principle

Dependency Injection

Typed Parameters

Return Types

PHPDoc where appropriate

Write readable code over clever code.

Methods should remain small and focused.

Classes should have one responsibility.

Prioritize maintainability over shortcuts.

# ==============================================================================
# Final Development Principle
# ==============================================================================

Whenever implementing any change:

1. Analyze existing implementation first.
2. Reuse existing architecture.
3. Follow Controller → Service → Repository pattern.
4. Keep code clean and maintainable.
5. Preserve existing functionality.
6. Write production-ready code.
7. Run formatting and relevant tests.
8. Follow Laravel and ShilpoRekha coding conventions.
9. Minimize code duplication.
10. Think long-term maintainability before writing code.

# ShilpoRekha — Project Guide

## What this project is

`shilporekha-api` is the Laravel backend for **ShilpoRekha**, a freelance graphic-design service business site (t-shirt design live now; logo, packaging, and branding design planned as future services). This repo is the API/data layer only — the actual public site and admin panel are a separate Vue 3 SPA in the sibling directory `../shilporekha-vue3`, which consumes this API. Auth is set up for that SPA via Laravel Sanctum's stateful (cookie-based) SPA authentication, not token auth for third parties.

The project is early-stage: the database schema and Eloquent models (with relationships) are built, but no controllers, API routes, form requests, or seeders beyond the Laravel default exist yet. Expect to be building the HTTP layer (controllers, routes, validation, resources) on top of the domain model described below — following the layered Controller → Service → Repository → Model architecture in Section 1 above.

## Environment

- **Database**: MySQL (via XAMPP), database name `shilporekha_api` — snake_case by convention, distinct from the hyphenated project folder name `shilporekha-api` (MySQL identifiers with hyphens require backtick-quoting everywhere, so the DB name deliberately doesn't match the folder name literally).
- **Timezone**: `config('app.timezone')` is `Asia/Dhaka` (not the Laravel default `UTC`) — affects `now()`, Carbon, and all `created_at`/`updated_at` display, but not MySQL's own internal `NOW()`.
- **Laravel Boost** is installed (`laravel/boost`, dev dependency) and exposes an MCP server for AI coding agents via `.mcp.json` (`php artisan boost:mcp`) — configured for `claude_code` in `boost.json`.

## Commands

```bash
# Install
composer install
npm install                     # present from the Laravel default skeleton; see note below

# Local dev (serves app + queue worker + log tailer + vite, concurrently)
composer run dev

# Run all tests
composer test
# or directly:
php artisan test

# Run a single test file / method
php artisan test tests/Feature/ExampleTest.php
php artisan test --filter=test_method_name

# Code style (Laravel Pint)
vendor/bin/pint

# Migrations
php artisan migrate
php artisan migrate:status
php artisan migrate:fresh       # drops all tables and re-runs migrations — destructive

# Inspect a model's columns, casts, and relationships (useful given the schema is still evolving)
php artisan model:show Service
```

Tests run against an in-memory SQLite database (configured in `phpunit.xml`), independent of the app's own MySQL connection in `.env` — no separate test DB setup is needed.

**Note on `npm`/Vite/Tailwind in this repo**: this is unused Laravel-default scaffolding (no Blade views actually render app UI beyond the stock `welcome` page). The real frontend build lives in the sibling `shilporekha-vue3` project, not here — don't wire up Blade/Vite pages in this repo for the public site or admin panel.

## Domain model

The core business taxonomy is a three-level tree — **Service → Category (niche) → Subcategory (micro-niche)** — because each service's niches break down further (e.g. T-Shirt's "Outdoor Adventure" category covers the Fishing/Camping/Hiking/Hunting micro-niches). This used to be a flat two-level Service→Subcategory list; it was restructured to this three-level tree so `Product` could hang off the right granularity:

```
Service (T-Shirt, Packaging, Logo, Branding)
└── Category (belongs to one Service) — e.g. T-Shirt's Outdoor Adventure, Motorsports, Western, Fitness, Typography, Vintage & Retro
    └── Subcategory (belongs to one Category) — e.g. Outdoor Adventure's Fishing, Camping, Hiking, Hunting
        ├── PortfolioItem (belongs to one Subcategory)
        └── Product (belongs to one Service + Category + Subcategory, denormalized — see below)
```

Everything else hangs off `Service` directly, not off `Category`/`Subcategory`:

- **`Service.status`** is `active` or `coming_soon` — currently only T-Shirt is meant to be `active`; the other three are placeholders until built out.
- **`Package`** (pricing tiers, e.g. Basic/Standard/Premium) belongs to a `Service`, not a `Category` — pricing doesn't vary by niche within a service. Each `Package` has many `PackageFeature` rows (the bullet list shown per tier).
- **`Order`** (a client's "start a project" brief submission) belongs to a `Service` via a nullable, `nullOnDelete` foreign key — deliberately so that deleting/retiring a `Service` later doesn't destroy past order history. Each `Order` has many `OrderAttachment` (uploaded style-reference files).
- **`Testimonial`** and **`SiteSetting`** are standalone — no foreign keys. `SiteSetting` is a generic key-value store (`SiteSetting::get($key, $default)` / `SiteSetting::set($key, $value)`) for editable site config (site name, tagline, contact email, social links) rather than fixed columns.

**`PortfolioItem` has no direct link to `Service`** — only to `Subcategory`. Reach the parent service via `$item->subcategory->category->service`, and eager-load all three together (`PortfolioItem::with('subcategory.category.service')`) to avoid N+1 queries when listing portfolio items with their service.

**`Product`** stores `service_id`, `category_id`, AND `subcategory_id` directly (rather than only `subcategory_id` and reaching the rest through the chain) so product listings/filters don't need to join three levels deep. `ProductService::assertTaxonomyIsConsistent()` enforces that these three IDs actually chain together (category belongs to that service, subcategory belongs to that category) on every create/update, since nothing at the database level stops a client from submitting a valid-but-unrelated combination. `Product.image_path` is normally a `storage/app/public` path from a real upload (`ProductResource` resolves it via `Storage::disk('public')->url()`), but a seeder/test may put a ready-made external URL there instead — `ProductResource` passes those through unchanged rather than mangling them.

**`Style`** (Minimalist, Vintage, Bold Typography, ...) is a global lookup, not scoped to a `Service` — the same style vocabulary applies to every service's products. `Product.style_id` is nullable with `nullOnDelete`, since it's an optional tag, not a structural part of the taxonomy.

`Package.price` and `Product.price` are cast as `decimal:2`, so they're always a 2-decimal-place string, not a raw float.
