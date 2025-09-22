#!/bin/sh

# Exit on error
set -e

echo "Starting Laravel application initialization..."

# Set default environment variables if not provided
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}
export APP_URL=${APP_URL:-http://localhost}
export DB_CONNECTION=${DB_CONNECTION:-sqlite}
export SESSION_DRIVER=${SESSION_DRIVER:-file}
export SESSION_CONNECTION=${SESSION_CONNECTION:-}
export SESSION_STORE=${SESSION_STORE:-}
export SESSION_DOMAIN=${SESSION_DOMAIN:-}
export SESSION_SECURE_COOKIE=${SESSION_SECURE_COOKIE:-false}
export CACHE_DRIVER=${CACHE_DRIVER:-file}
export QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}

# Wait for database to be ready (if using external database)
if [ "$DB_CONNECTION" = "mysql" ] || [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Waiting for database connection..."
    until php -r "
        try {
            \$pdo = new PDO('$DB_CONNECTION:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE', '$DB_USERNAME', '$DB_PASSWORD');
            echo 'Database connected successfully' . PHP_EOL;
            exit(0);
        } catch (PDOException \$e) {
            echo 'Database connection failed: ' . \$e->getMessage() . PHP_EOL;
            exit(1);
        }
    "; do
        echo "Database not ready, waiting..."
        sleep 2
    done
fi

# Create storage directories if they don't exist
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/app/temp
mkdir -p bootstrap/cache
mkdir -p public/build

# Create supervisor log directory
mkdir -p /var/log/supervisor

# Ensure public directory structure
if [ ! -f public/index.php ]; then
    echo "ERROR: public/index.php not found! Laravel public directory structure is incorrect."
    exit 1
fi

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/public
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/public

# Create SQLite database if using SQLite
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi

# Run Laravel setup commands
echo "Running Laravel optimization commands..."

# Clear any existing cache
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed database if SEED_DATABASE is set
if [ "$SEED_DATABASE" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# Cache configuration for production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
fi

# Create admin user if ADMIN_EMAIL is provided
if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
    echo "Creating admin user..."
    php artisan tinker --execute="
        \$user = App\Models\User::firstOrCreate(
            ['email' => '$ADMIN_EMAIL'],
            [
                'name' => 'Admin',
                'password' => bcrypt('$ADMIN_PASSWORD'),
                'email_verified_at' => now(),
            ]
        );
        \$user->assignRole('super_admin');
        echo 'Admin user created: ' . \$user->email . PHP_EOL;
    " || echo "Admin user creation skipped (may already exist)"
fi

echo "Laravel application initialization completed!"

# Verify nginx configuration
echo "Testing nginx configuration..."
nginx -t

# Show Laravel app info
echo "Laravel application info:"
echo "- App URL: $APP_URL"
echo "- Environment: $APP_ENV"
echo "- Database: $DB_CONNECTION"
echo "- Public directory: /var/www/html/public"
echo "- Index file exists: $([ -f /var/www/html/public/index.php ] && echo 'YES' || echo 'NO')"

# Execute the main command
exec "$@"