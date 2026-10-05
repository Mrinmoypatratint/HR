#!/bin/bash
set -e

echo "Starting IntraEats HR & Attendance System on Render..."

# 1. Configure Apache Port based on Render's $PORT environment variable
PORT="${PORT:-80}"
echo "Configuring Apache to listen on port: $PORT"
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 2. Ensure storage and database directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/database

# 3. Check and initialize SQLite database if needed
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating SQLite database file..."
    touch /var/www/html/database/database.sqlite
fi

chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# 4. Generate app key if missing
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    php artisan key:generate --force
fi

# 5. Run database migrations and seeders for fresh deployments
echo "Running database migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force

# 6. Optimize Laravel performance
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "IntraEats HR is ready. Launching Apache..."
exec apache2-foreground
