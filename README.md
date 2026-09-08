<p align="center">
  <img src="public/images/logo-full.png" width="320" alt="CommonGrove">
</p>

<p align="center">
  A closed, judgment free, strictly platonic friendship platform for introverts and gamers.
</p>

<p align="center">
  <a href="https://common-grove.com">Website</a> &middot;
  <a href="https://common-grove.com/terms">Terms of Service</a> &middot;
  <a href="https://common-grove.com/privacy">Privacy Policy</a> &middot;
  <a href="https://common-grove.com/guidelines">Community Guidelines</a>
</p>

---

## About CommonGrove

CommonGrove is a private social platform built for people who find most social apps exhausting. There are no ads, no bots, and no fake activity anywhere on the platform. Nothing inside is visible to unauthenticated visitors, and the entire experience is designed around calm, low pressure connection rather than engagement metrics.

## Sponsorship

CommonGrove is sponsored by GlitchCast & Affiliates.

## About This Repository

This repository contains the proprietary source code for CommonGrove. It is not an open source project. No license is granted for reuse, redistribution, or modification of any part of this codebase outside of work explicitly authorized by Coldev Enterprises. Full terms are available in the [LICENSE](LICENSE) file.

Access to this repository is limited to authorized contributors. If you have been given access for a specific purpose, please keep its contents confidential.

## Technology Stack

- [Laravel 11](https://laravel.com)
- [Livewire 3](https://livewire.laravel.com)
- [Alpine.js](https://alpinejs.dev)
- [Tailwind CSS v3](https://tailwindcss.com) (TALL stack)
- MySQL 8
- Redis
- [Laravel Reverb](https://laravel.com/docs/reverb) for real time WebSockets

## Local Development

**Prerequisites:** PHP 8.2 or later, Composer, Node.js, MySQL, and Redis.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Start the full development stack (server, queue worker, log viewer, and Vite hot reload) in a single command:

```bash
composer dev
```

Or run each process individually:

```bash
php artisan serve
php artisan reverb:start --debug
php artisan queue:listen --tries=1
npm run dev
```

Build frontend assets for production:

```bash
npm run build
```

## Testing

```bash
php artisan test
php artisan test --filter=TestClassName
```

## Code Style

This project follows PSR-12 and is enforced with Laravel Pint.

```bash
./vendor/bin/pint
```

## Security

The safety and privacy of our users is a top priority. If you discover a security vulnerability anywhere in this codebase or on the platform, please do not open a public issue. Instead, report it privately to **security@common-grove.com** so it can be investigated and addressed before any public disclosure. We take every report seriously and will respond as promptly as possible.

## Legal

CommonGrove operates under the following policies, each of which governs use of the platform:

- [Terms of Service](https://common-grove.com/terms)
- [Privacy Policy](https://common-grove.com/privacy)
- [Community Guidelines](https://common-grove.com/guidelines)

## Ownership

CommonGrove is developed and maintained by Coldev Enterprises. All rights reserved. Unauthorized copying, distribution, or use of this software, in whole or in part, is strictly prohibited without prior written permission. See [LICENSE](LICENSE) for complete terms.

Copyright &copy; 2026 Coldev Enterprises. All rights reserved.
