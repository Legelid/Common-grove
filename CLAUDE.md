# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Project

CommonGrove — a closed, judgment-free, strictly platonic friendship platform for introverts and gamers. Nothing inside is visible to unauthenticated visitors. No ads, no bots, no fake activity.

**Stack:** Laravel 11 · Livewire 3 · Alpine.js · Tailwind CSS v3 (TALL) · MySQL 8 · Redis · Laravel Reverb (WebSockets)

**Developers:** Andrew Collins (Coldev Enterprises) + Adam (design/colours/layout — co-founder).
**Do not confuse with Briefd**, Andrew's separate AI SaaS project in another repo.

---

## Commands

```bash
# Start full dev stack (server + queue + logs + vite hot-reload)
composer dev

# Individual processes
php artisan serve
php artisan reverb:start --debug
php artisan queue:listen --tries=1
npm run dev

# Build assets for production
npm run build

# Tests
php artisan test
php artisan test --filter=TestClassName

# Code style (PSR-12)
./vendor/bin/pint

# Migrations
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh          # wipes and re-runs all

# REPL
php artisan tinker
```

---

## Architecture

### Middleware
`SecurityHeaders` is appended globally in `bootstrap/app.php` via `$middleware->append(SecurityHeaders::class)`. Laravel 11 has no `Http/Kernel.php` — all middleware registration happens in `bootstrap/app.php`.

### Authentication & Passwords
**Never use `Hash::` directly.** Always use `App\Services\PasswordService` (singleton registered in `AppServiceProvider`). It appends `PASSWORD_PEPPER` (from `config('app.password_pepper')`, sourced from `.env`) before hashing with Argon2id. `HASH_DRIVER=argon2id` is set in `.env`.

After a successful login, call `PasswordService::needsRehash()` and silently re-hash if true, then call `Auth::login()` for session rotation.

### User Model
`app/Models/User.php` — UUID primary key via `HasUuids`. Implements `MustVerifyEmail`. Uses `SoftDeletes`. Primary identifier is `gamertag` (not `name` — the platform never collects last names).

Key columns: `gamertag` (unique, 3–20 chars), `display_name` (nullable), `identity_mode` (tinyint 1/2/3), `show_names_pref` (boolean), `password_reset_required` (boolean).

### Livewire Components
Components live in `app/Livewire/` with matching Blade views in `resources/views/livewire/`. The base layout is `resources/views/layouts/app.blade.php` (includes `@livewireStyles`, `@livewireScripts`, and `@vite`).

### Database Conventions
- UUID primary keys on all user-facing models (`$table->uuid('id')->primary()`)
- Soft deletes on User, Message, HangoutPost, Room
- Foreign key constraints on every relationship; index all FK columns
- Never edit existing migrations — always create new ones

### Routes & Access Control
Every internal route must be behind `['auth', 'verified']` middleware. Unauthenticated requests redirect to the landing page. Public routes only: `/`, `/register`, `/login`, `/forgot-password`, `/reset-password`.

### Build Order (Phases)
1. ✅ **Foundation** — Laravel install, env, DB, SecurityHeaders, PasswordService, TALL stack
2. 🔄 **Authentication** — Registration (gamertag + HIBP password check), email verification, login (rate-limited, rehash), logout, password reset
3. **User Profiles & Identity** — Profile page, identity mode selector, avatar upload (B2, EXIF stripped)
4. **Interest Tags** — Tags table, pivot, ~200 seed tags, tag selection UI
5. **Hangout Feed** — Posts with 6h expiry, interest-filtered feed, live aggregate counts
6. **Messaging** — DMs, group rooms, Reverb real-time, block/mute
7. **Safety Systems** — Reports, 3-strike system, crisis keyword detection
8. **Admin Dashboard** — Report queue, user management, platform stats

---

## Absolute Security Rules

These must never be violated:

1. **Eloquent only** — no raw SQL with string interpolation; bindings are acceptable
2. **`{{ }}` in Blade only** — never `{!! !!}` for any user-supplied variable
3. **Auth + Authorisation** — `auth` middleware handles authentication; Laravel Policies handle ownership checks. Both required on every controller that touches user-owned data.
4. **Secrets in `.env` only** — never in code, config files, or comments
5. **`APP_DEBUG=false` in production**
6. **Laravel Policies** for all resource authorisation (Message, HangoutPost, UserProfile, Room, Report)
7. **File uploads** — PNG/JPEG only, max 2 MB, EXIF stripped via Intervention Image, stored in Backblaze B2 (never `public/`)
8. **Rate limits** — Login: 5/15 min per IP · Signup: 10/hr per IP · Gamertag check: 60/min per IP · Reports: 10/hr per user
9. **Session config** — `secure=true`, `http_only=true`, `same_site=lax` (verify in `config/session.php`). Not `strict`: it withholds the session cookie on top-level cross-site GET navigations, which breaks auth-gated signed links (e.g. email verification) opened from an external mail client — the request arrives cookie-less and `auth` middleware sees a guest. `lax` still blocks the cookie on cross-site POSTs/forms/embeds, which is the actual CSRF threat model this setting exists for.
10. **`declare(strict_types=1)`** at the top of every PHP file

### Gamertag Rules (enforced by `App\Rules\ValidGamertag`)
- 3–20 characters, starts with a letter
- Pattern: `^[a-zA-Z][a-zA-Z0-9_-]+$`
- Must contain at least one vowel (`[aeiouAEIOU]`)
- No 6+ consecutive consonants
- No 3+ consecutive identical characters
- Passes profanity filter (including leet-speak variants)
- Case-insensitive unique in `users` table

---

## Code Style

PSR-12 · `declare(strict_types=1)` in all PHP files · type hints on all method parameters and return types · DocBlocks on all public methods · named routes (never hardcode URLs) · Form Requests for all validation · Service classes for business logic (thin controllers) · Enums for fixed value sets · DB transactions for multi-table writes.
