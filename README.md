# Onyx

A clean Laravel + Vue/Inertia base application with authentication and user infrastructure.

## Stack

- **Backend**: Laravel 13, PHP 8.3
- **Frontend**: Vue 3 + Inertia.js
- **Styling**: Tailwind CSS
- **Auth**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **i18n**: vue-i18n (en, fr, de, es)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
```
