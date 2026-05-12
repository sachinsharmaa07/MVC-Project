# Smart Waste Segregation & Collection System

Local-first Laravel 11 + MongoDB 7 system for citizen pickup requests, driver routes, and admin analytics.

## Prerequisites

- PHP 8.2+ with `ext-mongodb`
- Composer
- MongoDB 7 Community Edition (local)
- Node.js + npm

## Environment (.env)

```dotenv
APP_NAME="Waste System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1

DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=waste_system
DB_USERNAME=
DB_PASSWORD=
DB_QUEUE_CONNECTION=mongodb

MAIL_MAILER=log
QUEUE_CONNECTION=database
```

## Local Startup

1. Start MongoDB

```bash
mongod --dbpath /data/db
```

2. Install PHP dependencies

```bash
composer install
```

3. Install frontend dependencies

```bash
npm install
```

4. Build frontend assets

```bash
npm run dev
```

5. Run the app

```bash
php artisan serve
```

6. Run the queue worker

```bash
php artisan queue:work
```

7. Seed demo data

```bash
php artisan db:seed --class=DemoDataSeeder
```

8. Link storage (for pickup photos)

```bash
php artisan storage:link
```

## Default Login Credentials

- Admin: `admin1@waste.local` / `password`
- Citizen: `citizen@waste.local` / `password`
- Driver: `driver@waste.local` / `password`

## Notes

- MongoDB is the only data store. The ODM uses a 2dsphere index on `pickup_requests.location` for geo queries.
- If `ext-mongodb` is missing, install it and re-run `composer install`.
- Email notifications are logged to `storage/logs/laravel.log` via `MAIL_MAILER=log`.
