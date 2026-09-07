# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

`shilporekha-api` is the Laravel backend for **ShilpoRekha**, a freelance graphic-design service business site (t-shirt design live now; logo, packaging, and branding design planned as future services). This repo is the API/data layer only — the actual public site and admin panel are a separate Vue 3 SPA in the sibling directory `../shilporekha-vue3`, which consumes this API. Auth is set up for that SPA via Laravel Sanctum's stateful (cookie-based) SPA authentication, not token auth for third parties.

The project is early-stage: the database schema and Eloquent models (with relationships) are built, but no controllers, API routes, form requests, or seeders beyond the Laravel default exist yet. Expect to be building the HTTP layer (controllers, routes, validation, resources) on top of the domain model described below.

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

The core business taxonomy is a two-level tree — **Service → Subcategory** — not a flat category list, because each service's subcategories mean something different:

```
Service (T-Shirt, Packaging, Logo, Branding)
└── Subcategory (belongs to one Service)
    T-Shirt:    Outdoor Adventure, Fishing, Camping, Hiking, Hunting, Motorsports, Western, Fitness, Typography, Vintage/Retro
    Packaging:  Box Packaging, Pouch Packaging, Label Design, Bottle Packaging, Food Packaging
    Logo:       Wordmark, Lettermark, Monogram, Symbol/Icon, Combination Mark
    Branding:   Brand Identity, Brand Guidelines, Business Card, Social Media Branding, Marketing Collateral
    └── PortfolioItem (belongs to one Subcategory)
```

Everything else hangs off `Service` directly, not off `Subcategory`:

- **`Service.status`** is `active` or `coming_soon` — currently only T-Shirt is meant to be `active`; the other three are placeholders until built out.
- **`Package`** (pricing tiers, e.g. Basic/Standard/Premium) belongs to a `Service`, not a `Subcategory` — pricing doesn't vary by niche within a service. Each `Package` has many `PackageFeature` rows (the bullet list shown per tier).
- **`Order`** (a client's "start a project" brief submission) belongs to a `Service` via a nullable, `nullOnDelete` foreign key — deliberately so that deleting/retiring a `Service` later doesn't destroy past order history. Each `Order` has many `OrderAttachment` (uploaded style-reference files).
- **`Testimonial`** and **`SiteSetting`** are standalone — no foreign keys. `SiteSetting` is a generic key-value store (`SiteSetting::get($key, $default)` / `SiteSetting::set($key, $value)`) for editable site config (site name, tagline, contact email, social links) rather than fixed columns.

**`PortfolioItem` has no direct link to `Service`** — only to `Subcategory`. Reach the parent service via `$item->subcategory->service`, and eager-load both together (`PortfolioItem::with('subcategory.service')`) to avoid N+1 queries when listing portfolio items with their service.

`Package.price` is cast as `decimal:2`, so it's always a 2-decimal-place string, not a raw float.
