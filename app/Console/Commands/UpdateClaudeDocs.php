<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateClaudeDocs extends Command
{
    protected $signature = 'claude:update-docs';

    protected $description = 'Update CLAUDE.md documentation based on current codebase state';

    public function handle(): int
    {
        $this->info('Updating CLAUDE.md documentation...');

        $basePath = base_path();
        $claudeDocPath = $basePath.'/CLAUDE.md';

        // Build the documentation content
        $content = $this->generateDocumentation();

        File::put($claudeDocPath, $content);

        // Also update root .autoload if it exists
        $autoloadPath = dirname($basePath).'/.autoload';
        if (File::exists($autoloadPath) || is_dir(dirname($autoloadPath))) {
            File::put($autoloadPath, $content);
            $this->info('Updated .autoload file as well.');
        }

        $this->info('CLAUDE.md documentation updated successfully!');

        return 0;
    }

    private function generateDocumentation(): string
    {
        $now = now()->format('Y-m-d');

        // Detect features
        $hasApiRoutes = file_exists(base_path('routes/api.php'));
        $hasCustomMiddleware = count(glob(app_path('Http/Middleware/*.php'))) > 1; // more than TrustProxies
        $models = array_filter(glob(app_path('Models/*.php')), function ($file) {
            return basename($file) !== 'User.php';
        });
        $controllers = array_filter(glob(app_path('Http/Controllers/*.php')), function ($file) {
            // Actually we need to check non-base Controller files
            return basename($file) !== 'Controller.php';
        });
        $migrations = glob(database_path('migrations/*.php'));
        $customMigrations = array_filter($migrations, function ($file) {
            $filename = basename($file);

            return ! str_contains($filename, 'create_users_table') &&
                   ! str_contains($filename, 'create_cache_table') &&
                   ! str_contains($filename, 'create_jobs_table');
        });

        $doc = "# Qora Backend - Project Context for Claude\n\n";
        $doc .= "**Last Updated:** $now\n";
        $doc .= '**Status:** '.$this->getStatus($hasApiRoutes, $models, $customMigrations, $hasCustomMiddleware)."\n";
        $doc .= "**Next Exploration Check:** Run `php artisan claude:update-docs` after significant changes\n\n";
        $doc .= "---\n\n";

        $doc .= "## 1. Quick Summary\n\n";
        $doc .= '- **Framework:** Laravel '.$this->getLaravelVersion()."\n";
        $doc .= "- **PHP:** ^8.2+\n";
        $doc .= '- **Structure:** '.($hasApiRoutes ? 'Full-stack (Web + API)' : 'Web-only')." Laravel application\n";
        $doc .= '- **Custom Models:** '.count($models)."\n";
        $doc .= '- **Custom Migrations:** '.count($customMigrations)."\n";
        $doc .= '- **API Endpoints:** '.($hasApiRoutes ? 'Defined in routes/api.php' : 'None (web routes only)')."\n";
        $doc .= '- **Middleware:** '.($hasCustomMiddleware ? 'Custom middleware present' : 'Standard Laravel only')."\n\n";

        $doc .= "---\n\n";
        $doc .= $this->generateTechStackSection();
        $doc .= "\n---\n\n";
        $doc .= $this->generateDirectoryStructure();
        $doc .= "\n---\n\n";
        $doc .= $this->generateDatabaseSchema($customMigrations);
        $doc .= "\n---\n\n";
        $doc .= $this->generateModelsSection($models);
        $doc .= "\n---\n\n";
        $doc .= $this->generateRoutesSection($hasApiRoutes);
        $doc .= "\n---\n\n";
        $doc .= $this->generateConfigurationSection();
        $doc .= "\n---\n\n";
        $doc .= $this->generateDevelopmentCommands();
        $doc .= "\n---\n\n";
        $doc .= $this->generateCurrentState($hasApiRoutes, $models, $customMigrations, $hasCustomMiddleware);
        $doc .= "\n---\n\n";
        $doc .= $this->generateImportantNotes();

        return $doc;
    }

    private function getStatus($hasApiRoutes, $models, $customMigrations, $hasCustomMiddleware): string
    {
        if ($hasApiRoutes || count($models) > 0 || count($customMigrations) > 0 || $hasCustomMiddleware) {
            return 'Active development - custom features present';
        }

        return 'Fresh Laravel skeleton, minimal customization';
    }

    private function getLaravelVersion(): string
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $laravelVersion = $composer['require']['laravel/framework'] ?? '^12.x';

        return trim($laravelVersion, '^');
    }

    private function generateTechStackSection(): string
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $package = json_decode(file_get_contents(base_path('package.json')), true);

        $techStack = "## 2. Tech Stack\n\n";
        $techStack .= "### Backend\n";
        $techStack .= '- **Framework:** Laravel '.($this->getLaravelVersion())."\n";
        $techStack .= "- **Language:** PHP 8.2+\n";
        $techStack .= "- **Authentication:** Laravel Sanctum (implied by framework)\n";
        $techStack .= "- **Database:** SQLite (default) or configurable via `.env`\n";
        $techStack .= "- **Queue:** Database driver\n";
        $techStack .= "- **Cache:** Database driver\n";
        $techStack .= "- **Session:** Database driver\n";
        $techStack .= "- **Mail:** Log driver (development)\n\n";

        if (isset($package['devDependencies']['vite']) || isset($package['devDependencies']['@vitejs/plugin-laravel'])) {
            $techStack .= "### Frontend\n";
            $techStack .= "- **Build Tool:** Vite\n";
            if (isset($package['dependencies']['tailwindcss'])) {
                $techStack .= "- **CSS:** Tailwind CSS v4 (with `@tailwindcss/vite` plugin)\n";
            } else {
                $techStack .= "- **CSS:** Configurable (check package.json)\n";
            }
            $techStack .= "- **JS:** Vanilla JS + Axios (or as configured)\n";
            $techStack .= "- **Module System:** ES Modules\n\n";
        }

        $techStack .= "### Dev Tools\n";
        $techStack .= '- **Testing:** PHPUnit '.($composer['require-dev']['phpunit/phpunit'] ?? '^11.x')."\n";
        if (isset($composer['require-dev']['laravel/pint'])) {
            $techStack .= "- **Code Style:** Laravel Pint\n";
        }
        $techStack .= "- **Debugging:** Laravel Pail (log viewer), Whoops\n";
        $techStack .= "- **Development Server:** `php artisan serve`\n";
        if (isset($package['devDependencies']['vite'])) {
            $techStack .= "- **Hot Reloading:** Vite dev server + Laravel Vite plugin\n";
        }
        $techStack .= "- **Queue Worker:** `php artisan queue:listen`\n";
        $techStack .= "- **Concurrent Dev:** `composer dev` runs all 4 processes\n";

        return $techStack;
    }

    private function generateDirectoryStructure(): string
    {
        $structure = "## 3. Directory Structure\n\n";
        $structure .= "```\nbackend/\n";
        $structure .= "├── app/\n";
        $structure .= "│   ├── Http/\n";
        $structure .= "│   │   └── Controllers/\n";
        $structure .= '│   │       └── Controller.php (base, '.($this->fileContains(base_path('app/Http/Controllers/Controller.php'), 'class Controller') ? 'customizable' : 'default').")\n";
        $structure .= "│   ├── Models/\n";
        $models = glob(app_path('Models/*.php'));
        foreach ($models as $model) {
            $structure .= '│   │   └── '.basename($model)."\n";
        }
        $structure .= "│   └── Providers/\n";
        $providers = glob(app_path('Providers/*.php'));
        foreach ($providers as $provider) {
            $structure .= '│       └── '.basename($provider)."\n";
        }
        $structure .= "├── bootstrap/\n";
        $structure .= "│   └── app.php (framework bootstrap)\n";
        $structure .= "├── config/\n";
        $configs = glob(config_path('*.php'));
        foreach ($configs as $config) {
            $structure .= '│   ├── '.basename($config)."\n";
        }
        $structure .= "├── database/\n";
        $structure .= "│   ├── migrations/ (standard Laravel migrations + custom)\n";
        if (file_exists(database_path('factories'))) {
            $structure .= "│   ├── factories/\n";
            $factories = glob(database_path('factories/*.php'));
            foreach ($factories as $factory) {
                $structure .= '│   │   └── '.basename($factory)."\n";
            }
        }
        if (file_exists(database_path('seeders'))) {
            $structure .= "│   └── seeders/\n";
            $seeders = glob(database_path('seeders/*.php'));
            foreach ($seeders as $seeder) {
                $structure .= '│       └── '.basename($seeder)."\n";
            }
        }
        $structure .= "├── public/\n";
        $structure .= "│   └── index.php (entry point)\n";
        $structure .= "├── resources/\n";
        $structure .= "│   ├── js/\n";
        $jsFiles = glob(resource_path('js/*.js'));
        foreach ($jsFiles as $js) {
            $structure .= '│   │   └── '.basename($js)."\n";
        }
        if (file_exists(resource_path('views'))) {
            $structure .= "│   └── views/\n";
            $views = glob(resource_path('views/*.blade.php'));
            foreach (array_slice($views, 0, 5) as $view) {
                $structure .= '│       └── '.basename($view)."\n";
            }
            if (count($views) > 5) {
                $structure .= '│       ... +'.(count($views) - 5)." more views\n";
            }
        }
        $structure .= "├── routes/\n";
        $routeFiles = glob(base_path('routes/*.php'));
        foreach ($routeFiles as $route) {
            $structure .= '│   ├── '.basename($route)."\n";
        }
        $structure .= "├── storage/ (logs, cache, framework files)\n";
        $structure .= "├── tests/\n";
        $testFiles = glob(base_path('tests/*/*.php'));
        $structure .= "│   └── ... PHPUnit tests\n";
        $structure .= "├── vendor/\n";
        $structure .= "├── .env.example (SQLite default)\n";
        $structure .= "├── artisan (CLI helper)\n";
        $structure .= "├── composer.json\n";
        if (file_exists(base_path('package.json'))) {
            $structure .= "├── package.json\n";
        }
        if (file_exists(base_path('vite.config.js'))) {
            $structure .= "└── vite.config.js\n";
        }
        $structure .= "```\n";

        return $structure;
    }

    private function generateDatabaseSchema($customMigrations): string
    {
        $schema = "## 4. Database Schema\n\n";
        $schema .= "### Core Tables (from migrations)\n\n";

        // Check for users table
        if (file_exists(database_path('migrations/0001_01_01_000000_create_users_table.php'))) {
            $schema .= "**users**\n";
            $schema .= "- `id` (bigIncrements)\n";
            $schema .= "- `name` (string)\n";
            $schema .= "- `email` (string, unique)\n";
            $schema .= "- `email_verified_at` (timestamp, nullable)\n";
            $schema .= "- `password` (string)\n";
            $schema .= "- `remember_token` (string)\n";
            $schema .= "- `timestamps()` (created_at, updated_at)\n\n";
        }

        if (file_exists(database_path('migrations/0001_01_01_000001_create_cache_table.php'))) {
            $schema .= "**cache**\n";
            $schema .= "- `key` (string, primary)\n";
            $schema .= "- `value` (mediumText)\n";
            $schema .= "- `expiration` (integer, indexed)\n\n";
        }

        if (file_exists(database_path('migrations/0001_01_01_000002_create_jobs_table.php'))) {
            $schema .= "**jobs** (queue)\n";
            $schema .= "- `id` (bigIncrements)\n";
            $schema .= "- `queue` (string, indexed)\n";
            $schema .= "- `payload` (longText)\n";
            $schema .= "- `attempts` (unsignedTinyInteger)\n";
            $schema .= "- `reserved_at` (unsignedInteger, nullable)\n";
            $schema .= "- `available_at` (unsignedInteger)\n";
            $schema .= "- `created_at` (unsignedInteger)\n\n";
        }

        if (count($customMigrations) > 0) {
            $schema .= "### Custom Tables\n\n";
            foreach ($customMigrations as $migration) {
                $filename = basename($migration, '.php');
                // Try to extract table name from migration
                $schema .= "**custom** - See migration: `$filename`\n\n";
            }
        }

        return $schema;
    }

    private function generateModelsSection($models): string
    {
        $section = "## 5. Models\n\n";

        if (file_exists(app_path('Models/User.php'))) {
            $section .= "### User (App\\Models\\User)\n";
            $section .= "- **Extends:** `Illuminate\\Foundation\\Auth\\User as Authenticatable`\n";
            $section .= "- **Traits:** `HasFactory`, `Notifiable`\n";
            $section .= "- **Fillable:** `name`, `email`, `password`\n";
            $section .= "- **Hidden:** `password`, `remember_token`\n";
            $section .= "- **Casts:** `email_verified_at` → `datetime`, `password` → `hashed`\n\n";
        }

        if (empty($models)) {
            $section .= "*No other models exist yet.*\n";
        } else {
            $section .= "### Custom Models\n\n";
            foreach ($models as $model) {
                $basename = basename($model, '.php');
                $section .= "**$basename** - `App\\Models\\$basename`\n";
                if (class_exists("App\\Models\\$basename")) {
                    try {
                        $reflection = new \ReflectionClass("App\\Models\\$basename");
                        $defaults = $reflection->getDefaultProperties();
                        $fillable = $defaults['fillable'] ?? null;
                        if (is_array($fillable) && ! empty($fillable)) {
                            $section .= '- Fillable: `'.implode(', ', $fillable)."`\n";
                        }
                    } catch (\ReflectionException $e) {
                        // Class might not be loaded yet
                    }
                }
                $section .= "\n";
            }
        }

        return $section;
    }

    private function generateRoutesSection($hasApiRoutes): string
    {
        $section = "## 6. Routes & Endpoints\n\n";

        $section .= "### Web Routes (`routes/web.php`)\n";
        if (file_exists(base_path('routes/web.php'))) {
            $webContent = file_get_contents(base_path('routes/web.php'));
            // Extract route definitions (simplified)
            $lines = explode("\n", $webContent);
            $routeLines = [];
            foreach ($lines as $line) {
                if (str_contains($line, 'Route::') && str_contains($line, '->')) {
                    $routeLines[] = trim($line);
                }
            }
            if (empty($routeLines)) {
                $section .= "```\n// Standard Laravel welcome route or custom routes\n```\n";
            } else {
                $section .= "```\n".implode("\n", array_slice($routeLines, 0, 10))."\n```\n";
                if (count($routeLines) > 10) {
                    $section .= '... plus '.(count($routeLines) - 10)." more routes\n";
                }
            }
        }

        if (file_exists(base_path('routes/api.php'))) {
            $section .= "\n### API Routes (`routes/api.php`)\n";
            $apiContent = file_get_contents(base_path('routes/api.php'));
            $lines = explode("\n", $apiContent);
            $routeLines = [];
            foreach ($lines as $line) {
                if (str_contains($line, 'Route::') && str_contains($line, '->')) {
                    $routeLines[] = trim($line);
                }
            }
            $section .= "```\n".implode("\n", array_slice($routeLines, 0, 15))."\n```\n";
            if (count($routeLines) > 15) {
                $section .= '... plus '.(count($routeLines) - 15)." more routes\n";
            }
        } else {
            $section .= "\n*No API routes exist yet (routes/api.php is not present).*\n";
        }

        $section .= "\n**Console Routes** (`routes/console.php`)\n";
        if (file_exists(base_path('routes/console.php'))) {
            $section .= "- Artisan commands defined\n";
        }

        return $section;
    }

    private function generateConfigurationSection(): string
    {
        $section = "## 7. Configuration Highlights\n\n";
        $section .= "### Database (`config/database.php`)\n";
        $envDb = env('DB_CONNECTION', 'sqlite');
        $section .= "- Default connection: `$envDb` (from .env DB_CONNECTION)\n";
        $section .= "- Migrations table: `migrations`\n";
        if (env('REDIS_HOST')) {
            $section .= "- Redis configured for caching/queues\n";
        }

        $section .= "\n### Session (`config/session.php`)\n";
        $section .= '- Driver: `'.config('session.driver', 'file')."`\n";
        $section .= '- Lifetime: '.config('session.lifetime', 120)." minutes\n";
        $section .= '- Encrypt: '.(config('session.encrypt') ? 'true' : 'false')."\n";

        $section .= "\n### Cache (`config/cache.php`)\n";
        $section .= '- Default store: `'.config('cache.default', 'file')."`\n";
        $section .= "- Prefix: configurable via `CACHE_PREFIX`\n";

        $section .= "\n### Queue (`config/queue.php`)\n";
        $section .= '- Default connection: `'.config('queue.default', 'sync')."`\n";
        $section .= '- `after_commit`: '.(config('queue.after_commit') ? 'true' : 'false')."\n";

        $section .= "\n### Mail (`config/mail.php`)\n";
        $section .= '- Mailer: `'.config('mail.default', 'log')."`\n";
        $section .= '- From address: `'.config('mail.from.address', 'hello@example.com')."`\n";

        $section .= "\n### Logging (`config/logging.php`)\n";
        $section .= '- Default channel: `'.config('logging.default', 'stack')."`\n";
        $section .= "- Stack channel configured (daily & single file)\n";

        return $section;
    }

    private function generateDevelopmentCommands(): string
    {
        $commands = "## 8. Development Commands\n\n";
        $commands .= "### Setup (First Time)\n```bash\n";
        $commands .= "composer install\n";
        $commands .= "cp .env.example .env\n";
        $commands .= "php artisan key:generate\n";
        $commands .= "npm install\n";
        $commands .= "npm run build\n";
        $commands .= "php artisan migrate\n";
        $commands .= "```\n\n";
        $commands .= "### Development (All-in-One)\n```bash\ncomposer dev\n```\n";
        $commands .= "Runs concurrently:\n";
        $commands .= "- PHP artisan serve (default: http://localhost:8000)\n";
        $commands .= "- Queue listener\n";
        $commands .= "- Laravel Pail (logs)\n";
        $commands .= "- Vite dev server\n\n";
        $commands .= "### Individual\n```bash\n";
        $commands .= "php artisan serve          # Start Laravel server\n";
        $commands .= "npm run dev               # Start Vite\n";
        $commands .= "php artisan queue:listen  # Queue worker\n";
        $commands .= "php artisan pail          # Log viewer\n";
        $commands .= "```\n\n";
        $commands .= "### Testing\n```bash\n";
        $commands .= "composer test            # Runs php artisan test\n";
        $commands .= "php artisan test         # Direct PHPUnit\n";
        $commands .= "```\n\n";
        $commands .= "### Code Quality\n```bash\n./vendor/bin/pint        # Laravel Pint (code style)\n```\n\n";
        $commands .= "### Documentation Update\n```bash\n";
        $commands .= "php artisan claude:update-docs  # Update this CLAUDE.md\n";
        $commands .= "```\n";

        return $commands;
    }

    private function generateCurrentState($hasApiRoutes, $models, $customMigrations, $hasCustomMiddleware): string
    {
        $section = '## 12. Current State Summary ('.now()->format('Y-m-d').")\n\n";

        $customModelCount = count($models);
        $customMigrationCount = count($customMigrations);

        if ($hasApiRoutes || $customModelCount > 0 || $customMigrationCount > 0 || $hasCustomMiddleware) {
            $section .= "**This Laravel application has custom features implemented.**\n\n";
        } else {
            $section .= "**This is a clean Laravel skeleton with no custom features yet.**\n\n";
        }

        $section .= "### Implemented\n";
        $section .= "- ✅ Framework fully installed\n";
        $section .= "- ✅ Database migrations created (standard Laravel tables)\n";
        $section .= "- ✅ Basic User model ready for authentication\n";
        if (file_exists(base_path('vite.config.js'))) {
            $section .= "- ✅ Frontend build system configured (Vite + Tailwind)\n";
        }
        $section .= "- ✅ Development workflow defined (composer dev)\n";

        if ($customModelCount > 0) {
            $section .= "- ✅ $customModelCount custom model(s)\n";
        }
        if ($customMigrationCount > 0) {
            $section .= "- ✅ $customMigrationCount custom migration(s)\n";
        }
        if ($hasApiRoutes) {
            $section .= "- ✅ API routes defined\n";
        }
        if ($hasCustomMiddleware) {
            $section .= "- ✅ Custom middleware\n";
        }

        $section .= "\n### Not Yet Implemented\n";
        if ($customModelCount == 0) {
            $section .= "- ❌ Custom models beyond User\n";
        }
        if ($customMigrationCount == 0) {
            $section .= "- ❌ Custom database tables\n";
        }
        if (! $hasApiRoutes) {
            $section .= "- ❌ API routes\n";
        }
        if (! $hasCustomMiddleware) {
            $section .= "- ❌ Custom middleware\n";
        }

        // Check for controllers (excluding base)
        $controllers = glob(app_path('Http/Controllers/*.php'));
        $customControllers = array_filter($controllers, function ($c) {
            return basename($c) !== 'Controller.php';
        });
        if (empty($customControllers)) {
            $section .= "- ❌ Custom controllers\n";
        } else {
            $section .= '- ✅ '.count($customControllers)." custom controller(s)\n";
        }

        // Check for views
        $views = glob(resource_path('views/*.blade.php'));
        if (count($views) <= 1) { // Only welcome.blade.php
            $section .= "- ❌ Custom views\n";
        } else {
            $section .= '- ✅ '.count($views)." view(s)\n";
        }

        $section .= "- ❌ Tests beyond examples\n";
        $section .= "- ❌ Service providers with custom logic\n";

        return $section;
    }

    private function generateImportantNotes(): string
    {
        $notes = "## 13. Important Conventions\n\n";
        $notes .= "### Laravel 12 Specifics\n";
        $notes .= "- **PSR-4 Autoload:** `App\\` → `app/`, `Database\\Factories\\` → `database/factories/`, `Database\\Seeders\\` → `database/seeders/`\n";
        $notes .= "- **Typed Properties & Return Types:** Heavily used (PHP 8.2+)\n";
        $notes .= "- **Route List:** `php artisan route:list`\n";
        $notes .= "- **Config Cache:** `php artisan config:cache`\n";
        $notes .= "- **Route Cache:** `php artisan route:cache`\n";
        $notes .= "- **View Cache:** `php artisan view:cache`\n\n";
        $notes .= "### Standard Practices\n";
        $notes .= "- Controllers extend base `App\\Http\\Controllers\\Controller`\n";
        $notes .= "- Models extend `Illuminate\\Database\\Eloquent\\Model` (or `Authenticatable` for auth)\n";
        $notes .= "- Migrations use `Illuminate\\Database\\Migrations\\Migration`\n";
        $notes .= "- Configuration stored in `config/` with env() for environment variables\n";
        $notes .= "- Environment variables prefixed with `APP_`, `DB_`, `CACHE_`, `QUEUE_`, `MAIL_`, etc.\n\n";
        $notes .= "## 14. What Triggers Re-Exploration\n\n";
        $notes .= "New session should **NOT** re-explore unless:\n\n";
        $notes .= "1. **New files added** outside `vendor/` that weren't previously documented\n";
        $notes .= "2. **Existing files modified** beyond their known default state\n";
        $notes .= "3. **Configuration changes** (`.env` or `config/*.php`)\n";
        $notes .= "4. **New dependencies** added to `composer.json` or `package.json`\n";
        $notes .= "5. **Database migrations** added beyond the standard three\n";
        $notes .= "6. **New models, controllers, or service providers** detected\n";
        $notes .= "7. **Custom routes** (`web.php`, `api.php`) added\n";
        $notes .= "8. **Any structural change** to `app/`, `routes/`, `database/` contents\n\n";
        $notes .= "**If no changes since last documentation update:** Skip exploration.\n\n";
        $notes .= "## 15. For Claude: Quick Start Guide\n\n";
        $notes .= "When working on this codebase:\n\n";
        $notes .= "1. **Check this file first** - it has all the context needed\n";
        $notes .= "2. **Default environment:** Local development, SQLite, debugging on\n";
        $notes .= "3. **Database:** Run `php artisan migrate` after setting up `.env`\n";
        $notes .= "4. **Frontend:** `npm run dev` runs Vite on port 5173. Ensure `vite` dev dependency is installed.\n";
        $notes .= "5. **Authentication:** User model exists; add auth scaffolding as needed.\n";
        $notes .= "6. **API Building:** Create new routes in `routes/api.php` (if missing) or `routes/web.php` with appropriate middleware\n";
        $notes .= "7. **Models:** Create in `app/Models/` using `php artisan make:model ModelName`\n";
        $notes .= "8. **Controllers:** Create in `app/Http/Controllers/` using `php artisan make:controller ControllerName`\n";
        $notes .= "9. **Migrations:** Use `php artisan make:migration create_xxx_table`\n";
        $notes .= "10. **Factories & Seeders:** `php artisan make:factory`, `php artisan make:seeder`\n\n";
        $notes .= "---\n\n";
        $notes .= '*Generated by Claude Code via `php artisan claude:update-docs`. Keep this updated as the project evolves.*';

        return $notes;
    }

    private function fileContains($path, $needle): bool
    {
        if (! file_exists($path)) {
            return false;
        }

        return strpos(file_get_contents($path), $needle) !== false;
    }
}
