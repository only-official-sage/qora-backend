# Qora Backend - Project Context for Claude

**Last Updated:** 2026-06-12
**Status:** Active development - custom features present
**Next Exploration Check:** Run `php artisan claude:update-docs` after significant changes

---

## 1. Quick Summary

- **Framework:** Laravel 12.0
- **PHP:** ^8.2+
- **Structure:** Full-stack (Web + API) Laravel application
- **Custom Models:** 10
- **Custom Migrations:** 11
- **API Endpoints:** Defined in routes/api.php
- **Middleware:** Custom middleware present

---

## 2. Tech Stack

### Backend
- **Framework:** Laravel 12.0
- **Language:** PHP 8.2+
- **Authentication:** Laravel Sanctum (implied by framework)
- **Database:** SQLite (default) or configurable via `.env`
- **Queue:** Database driver
- **Cache:** Database driver
- **Session:** Database driver
- **Mail:** Log driver (development)

### Frontend
- **Build Tool:** Vite
- **CSS:** Configurable (check package.json)
- **JS:** Vanilla JS + Axios (or as configured)
- **Module System:** ES Modules

### Dev Tools
- **Testing:** PHPUnit ^11.5.50
- **Code Style:** Laravel Pint
- **Debugging:** Laravel Pail (log viewer), Whoops
- **Development Server:** `php artisan serve`
- **Hot Reloading:** Vite dev server + Laravel Vite plugin
- **Queue Worker:** `php artisan queue:listen`
- **Concurrent Dev:** `composer dev` runs all 4 processes

---

## 3. Directory Structure

```
backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Controller.php (base, customizable)
│   ├── Models/
│   │   └── Admin.php
│   │   └── AdminAddress.php
│   │   └── AdminPayment.php
│   │   └── Beverage.php
│   │   └── BeverageCategory.php
│   │   └── Campaign.php
│   │   └── Dish.php
│   │   └── DishCategory.php
│   │   └── Section.php
│   │   └── Staff.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   └── app.php (framework bootstrap)
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── cors.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   ├── session.php
├── database/
│   ├── migrations/ (standard Laravel migrations + custom)
│   ├── factories/
│   │   └── UserFactory.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   └── index.php (entry point)
├── resources/
│   ├── js/
│   │   └── app.js
│   │   └── bootstrap.js
│   └── views/
│       └── welcome.blade.php
├── routes/
│   ├── api.php
│   ├── console.php
│   ├── web.php
├── storage/ (logs, cache, framework files)
├── tests/
│   └── ... PHPUnit tests
├── vendor/
├── .env.example (SQLite default)
├── artisan (CLI helper)
├── composer.json
├── package.json
└── vite.config.js
```

---

## 4. Database Schema

### Core Tables (from migrations)

**users**
- `id` (bigIncrements)
- `name` (string)
- `email` (string, unique)
- `email_verified_at` (timestamp, nullable)
- `password` (string)
- `remember_token` (string)
- `timestamps()` (created_at, updated_at)

**cache**
- `key` (string, primary)
- `value` (mediumText)
- `expiration` (integer, indexed)

**jobs** (queue)
- `id` (bigIncrements)
- `queue` (string, indexed)
- `payload` (longText)
- `attempts` (unsignedTinyInteger)
- `reserved_at` (unsignedInteger, nullable)
- `available_at` (unsignedInteger)
- `created_at` (unsignedInteger)

### Custom Tables

**custom** - See migration: `2025_04_30_000001_create_admins_table`

**custom** - See migration: `2025_04_30_000002_create_admin_addresses_table`

**custom** - See migration: `2025_04_30_000003_create_admin_payments_table`

**custom** - See migration: `2026_05_06_000004_create_campaigns_table`

**custom** - See migration: `2026_05_06_103528_create_sections_table`

**custom** - See migration: `2026_05_06_145724_create_staffs_table`

**custom** - See migration: `2026_05_09_114953_create_dish_categories_table`

**custom** - See migration: `2026_05_09_223141_create_personal_access_tokens_table`

**custom** - See migration: `2026_05_10_120000_create_dishes_table`

**custom** - See migration: `2026_05_10_232021_create_beverage_categories_table`

**custom** - See migration: `2026_05_11_000000_create_beverages_table`


---

## 5. Models

### User (App\Models\User)
- **Extends:** `Illuminate\Foundation\Auth\User as Authenticatable`
- **Traits:** `HasFactory`, `Notifiable`
- **Fillable:** `name`, `email`, `password`
- **Hidden:** `password`, `remember_token`
- **Casts:** `email_verified_at` → `datetime`, `password` → `hashed`

### Custom Models

**Admin** - `App\Models\Admin`
- Fillable: `id, email, password, business_name, business_type, logo`

**AdminAddress** - `App\Models\AdminAddress`
- Fillable: `id, admin_id, address, country, state, city, country_code, phone_number`

**AdminPayment** - `App\Models\AdminPayment`
- Fillable: `id, admin_id, bank_name, account_number, account_holder_name`

**Beverage** - `App\Models\Beverage`
- Fillable: `admin_id, category_id, name, price, description, ingredients, image`

**BeverageCategory** - `App\Models\BeverageCategory`
- Fillable: `admin_id, name`

**Campaign** - `App\Models\Campaign`
- Fillable: `id, admin_id, name, audience, description, image`

**Dish** - `App\Models\Dish`
- Fillable: `admin_id, category_id, name, price, description, ingredients, image`

**DishCategory** - `App\Models\DishCategory`
- Fillable: `admin_id, name`

**Section** - `App\Models\Section`
- Fillable: `id, admin_id, name, type, icon`

**Staff** - `App\Models\Staff`
- Fillable: `id, admin_id, fname, lname, email, role, schedule`


---

## 6. Routes & Endpoints

### Web Routes (`routes/web.php`)
```
// Standard Laravel welcome route or custom routes
```

### API Routes (`routes/api.php`)
```
Route::prefix('admin')->group(function () {
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:60,1');
```

**Console Routes** (`routes/console.php`)
- Artisan commands defined

---

## 7. Configuration Highlights

### Database (`config/database.php`)
- Default connection: `mysql` (from .env DB_CONNECTION)
- Migrations table: `migrations`
- Redis configured for caching/queues

### Session (`config/session.php`)
- Driver: `database`
- Lifetime: 120 minutes
- Encrypt: false

### Cache (`config/cache.php`)
- Default store: `database`
- Prefix: configurable via `CACHE_PREFIX`

### Queue (`config/queue.php`)
- Default connection: `database`
- `after_commit`: false

### Mail (`config/mail.php`)
- Mailer: `log`
- From address: `hello@example.com`

### Logging (`config/logging.php`)
- Default channel: `stack`
- Stack channel configured (daily & single file)

---

## 8. Development Commands

### Setup (First Time)
```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate
```

### Development (All-in-One)
```bash
composer dev
```
Runs concurrently:
- PHP artisan serve (default: http://localhost:8000)
- Queue listener
- Laravel Pail (logs)
- Vite dev server

### Individual
```bash
php artisan serve          # Start Laravel server
npm run dev               # Start Vite
php artisan queue:listen  # Queue worker
php artisan pail          # Log viewer
```

### Testing
```bash
composer test            # Runs php artisan test
php artisan test         # Direct PHPUnit
```

### Code Quality
```bash
./vendor/bin/pint        # Laravel Pint (code style)
```

### Documentation Update
```bash
php artisan claude:update-docs  # Update this CLAUDE.md
```

---

## 12. Current State Summary (2026-06-12)

**This Laravel application has custom features implemented.**

### Implemented
- ✅ Framework fully installed
- ✅ Database migrations created (standard Laravel tables)
- ✅ Basic User model ready for authentication
- ✅ Frontend build system configured (Vite + Tailwind)
- ✅ Development workflow defined (composer dev)
- ✅ 10 custom model(s)
- ✅ 11 custom migration(s)
- ✅ API routes defined
- ✅ Custom middleware

### Not Yet Implemented
- ✅ 6 custom controller(s)
- ❌ Custom views
- ❌ Tests beyond examples
- ❌ Service providers with custom logic

---

## 13. Important Conventions

### Laravel 12 Specifics
- **PSR-4 Autoload:** `App\` → `app/`, `Database\Factories\` → `database/factories/`, `Database\Seeders\` → `database/seeders/`
- **Typed Properties & Return Types:** Heavily used (PHP 8.2+)
- **Route List:** `php artisan route:list`
- **Config Cache:** `php artisan config:cache`
- **Route Cache:** `php artisan route:cache`
- **View Cache:** `php artisan view:cache`

### Standard Practices
- Controllers extend base `App\Http\Controllers\Controller`
- Models extend `Illuminate\Database\Eloquent\Model` (or `Authenticatable` for auth)
- Migrations use `Illuminate\Database\Migrations\Migration`
- Configuration stored in `config/` with env() for environment variables
- Environment variables prefixed with `APP_`, `DB_`, `CACHE_`, `QUEUE_`, `MAIL_`, etc.

## 14. What Triggers Re-Exploration

New session should **NOT** re-explore unless:

1. **New files added** outside `vendor/` that weren't previously documented
2. **Existing files modified** beyond their known default state
3. **Configuration changes** (`.env` or `config/*.php`)
4. **New dependencies** added to `composer.json` or `package.json`
5. **Database migrations** added beyond the standard three
6. **New models, controllers, or service providers** detected
7. **Custom routes** (`web.php`, `api.php`) added
8. **Any structural change** to `app/`, `routes/`, `database/` contents

**If no changes since last documentation update:** Skip exploration.

## 15. For Claude: Quick Start Guide

When working on this codebase:

1. **Check this file first** - it has all the context needed
2. **Default environment:** Local development, SQLite, debugging on
3. **Database:** Run `php artisan migrate` after setting up `.env`
4. **Frontend:** `npm run dev` runs Vite on port 5173. Ensure `vite` dev dependency is installed.
5. **Authentication:** User model exists; add auth scaffolding as needed.
6. **API Building:** Create new routes in `routes/api.php` (if missing) or `routes/web.php` with appropriate middleware
7. **Models:** Create in `app/Models/` using `php artisan make:model ModelName`
8. **Controllers:** Create in `app/Http/Controllers/` using `php artisan make:controller ControllerName`
9. **Migrations:** Use `php artisan make:migration create_xxx_table`
10. **Factories & Seeders:** `php artisan make:factory`, `php artisan make:seeder`

---

*Generated by Claude Code via `php artisan claude:update-docs`. Keep this updated as the project evolves.*