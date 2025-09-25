# 3. Stack Tecnológico

## Backend Framework

### Laravel 11.x
```json
{
  "framework": "Laravel",
  "version": "^11.9",
  "php_version": "^8.2",
  "architecture": "MVC + Inertia.js",
  "features": [
    "Eloquent ORM",
    "Artisan CLI",
    "Blade Templates",
    "Queue System",
    "Event Broadcasting",
    "Authentication",
    "Authorization (Policies)",
    "Validation",
    "File Storage"
  ]
}
```

### Principais Pacotes Backend
```json
{
  "core_packages": {
    "inertiajs/inertia-laravel": "^1.0",
    "laravel/sanctum": "^4.0",
    "laravel/tinker": "^2.9",
    "tightenco/ziggy": "^2.0"
  },
  "development_packages": {
    "laravel/breeze": "^2.0",
    "laravel/pint": "^1.13",
    "laravel/sail": "^1.26",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.0",
    "phpunit/phpunit": "^11.0.1"
  }
}
```

### Configurações de Ambiente
```bash
# .env Configuration
APP_NAME="Sistema de Votação"
APP_ENV=local|production
APP_KEY=base64:...
APP_DEBUG=true|false
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

# Configuração alternativa para MySQL/PostgreSQL
# DB_CONNECTION=mysql|pgsql
# DB_HOST=127.0.0.1
# DB_PORT=3306|5432
# DB_DATABASE=vote_system
# DB_USERNAME=root
# DB_PASSWORD=

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# File Storage
FILESYSTEM_DISK=local
```

## Frontend Framework

### Vue.js 3 + Composition API
```json
{
  "framework": "Vue.js",
  "version": "^3.4.0",
  "api_style": "Composition API",
  "features": [
    "Reactive Data",
    "Component System",
    "Single File Components",
    "TypeScript Support",
    "Teleport",
    "Suspense"
  ]
}
```

### Inertia.js Stack
```json
{
  "adapter": "@inertiajs/vue3",
  "version": "^1.0.0",
  "benefits": [
    "SPA-like experience",
    "Server-side routing",
    "No API endpoints needed",
    "Shared data between requests",
    "Progressive enhancement"
  ]
}
```

### Frontend Dependencies
```json
{
  "ui_framework": {
    "tailwindcss": "^3.2.1",
    "@tailwindcss/forms": "^0.5.2"
  },
  "build_tools": {
    "vite": "^5.0.0",
    "@vitejs/plugin-vue": "^5.0.0",
    "laravel-vite-plugin": "^1.0"
  },
  "utilities": {
    "axios": "^1.6.4",
    "ziggy-js": "^2.0.0"
  },
  "development": {
    "autoprefixer": "^10.4.12",
    "postcss": "^8.4.31"
  }
}
```

## Banco de Dados

### MySQL/PostgreSQL
```sql
-- Estrutura de Tabelas
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    saram VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'voter') NOT NULL DEFAULT 'voter',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE projects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE photos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE votes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    photo_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (photo_id) REFERENCES photos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_photo (user_id, photo_id)
);
```

### Índices e Performance
```sql
-- Índices para Performance
CREATE INDEX idx_projects_active ON projects(is_active);
CREATE INDEX idx_photos_project ON photos(project_id);
CREATE INDEX idx_votes_user ON votes(user_id);
CREATE INDEX idx_votes_photo ON votes(photo_id);
CREATE INDEX idx_votes_created ON votes(created_at);

-- Índices Compostos
CREATE INDEX idx_user_votes_count ON votes(user_id, created_at);
CREATE INDEX idx_photo_votes_count ON votes(photo_id, created_at);
```

## CSS Framework

### Tailwind CSS 3.x
```javascript
// tailwind.config.js
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#eff6ff',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                },
                success: {
                    50: '#f0fdf4',
                    500: '#22c55e',
                    600: '#16a34a',
                },
                danger: {
                    50: '#fef2f2',
                    500: '#ef4444',
                    600: '#dc2626',
                }
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
```

### Componentes CSS Customizados
```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
    .btn-primary {
        @apply bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200;
    }
    
    .btn-secondary {
        @apply bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors duration-200;
    }
    
    .card {
        @apply bg-white rounded-lg shadow-md p-6 border border-gray-200;
    }
    
    .photo-card {
        @apply relative overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300;
    }
    
    .vote-button {
        @apply absolute bottom-4 right-4 bg-primary-600 hover:bg-primary-700 text-white p-2 rounded-full shadow-lg transition-all duration-200;
    }
}
```

## Build Tools & DevOps

### Vite Configuration
```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
```

### Scripts de Desenvolvimento
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  }
}
```

### Laravel Artisan Commands
```bash
# Desenvolvimento
php artisan serve
php artisan migrate
php artisan db:seed
php artisan queue:work
php artisan storage:link

# Produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## Autenticação e Segurança

### Laravel Breeze
```php
// Authentication Features
- Login/Logout
- Registration
- Password Reset
- Email Verification
- Profile Management
- CSRF Protection
- Rate Limiting
```

### Middleware Stack
```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \App\Http\Middleware\HandleInertiaRequests::class,
        \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
    ],
];

protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
];
```

## File Storage

### Storage Configuration
```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
        'throw' => false,
    ],
    
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
        'throw' => false,
    ],
    
    'photos' => [
        'driver' => 'local',
        'root' => storage_path('app/public/photos'),
        'url' => env('APP_URL').'/storage/photos',
        'visibility' => 'public',
    ],
];
```

### Upload Handling
```php
// Photo Upload Service
class PhotoUploadService
{
    public function store(UploadedFile $file, Project $project): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs(
            "projects/{$project->id}", 
            $filename, 
            'photos'
        );
        
        return $path;
    }
}
```

## Testing Framework

### PHPUnit Configuration
```xml
<!-- phpunit.xml -->
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </source>
</phpunit>
```

### Testing Stack
```php
// Testing Tools
- PHPUnit (Unit & Feature Tests)
- Laravel Dusk (Browser Tests)
- Mockery (Mocking)
- Pest PHP (Alternative Syntax)
- Database Factories
- Database Seeders
```

## Performance & Optimization

### Caching Strategy
```php
// Cache Configuration
'stores' => [
    'database' => [
        'driver' => 'database',
        'table' => 'cache',
        'connection' => null,
        'lock_connection' => null,
    ],
    
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
];
```

### Query Optimization
```php
// Eager Loading
Project::with(['photos.votes', 'votes.user'])->get();

// Query Scopes
Project::active()->withCount('votes')->get();

// Database Indexes
Schema::table('votes', function (Blueprint $table) {
    $table->index(['user_id', 'created_at']);
    $table->index(['photo_id', 'created_at']);
});
```

## Deployment & Production

### Production Requirements
```bash
# Server Requirements
- PHP 8.2+
- MySQL 8.0+ / PostgreSQL 13+
- Nginx / Apache
- Node.js 18+ (for build)
- Composer 2.x
- Redis (optional, for caching)

# PHP Extensions
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD / ImageMagick
```

### Production Optimization
```bash
# Laravel Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize

# Asset Compilation
npm run build

# File Permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Containerização e Deploy

### Docker Stack
```yaml
# docker-compose.yml
services:
  app:
    build: .
    ports:
      - "8011:80"
    volumes:
      - ./database:/var/www/html/database
    environment:
      - APP_ENV=production
      - DB_CONNECTION=sqlite
```

### Dockerfile
```dockerfile
FROM php:8.2-fpm-alpine
RUN apk add --no-cache nginx supervisor sqlite
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/init.sh /usr/local/bin/init.sh
EXPOSE 80
CMD ["/usr/local/bin/init.sh"]
```

### Configurações Críticas
- **Permissões SQLite**: database.sqlite deve pertencer a www-data:www-data
- **Diretório Database**: Permissões 775 para escrita
- **Init Script**: Automatiza correção de permissões no startup
- **Supervisor**: Gerencia PHP-FPM + Nginx
- **Volume Mount**: Database persistente fora do container

### Environment Variables (Production)
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8011

# Database SQLite (Recomendado)
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

# Cache & Session
CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Configuração alternativa MySQL (se necessário)
# DB_CONNECTION=mysql
# DB_HOST=localhost
# DB_PORT=3306
# DB_DATABASE=vote_production
# DB_USERNAME=vote_user
# DB_PASSWORD=secure_password
```