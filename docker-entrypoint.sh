#!/bin/bash
set -e

echo "=== IntraEats HR & Attendance System Booting on Render ==="

# 1. Configure Port for Render ($PORT)
PORT="${PORT:-80}"
echo "Configuring Apache to listen on port: $PORT"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true

# 2. Ensure directories exist with full permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/database
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# 3. Setup SQLite database from seed_template.db if needed
if [ ! -f /var/www/html/database/database.sqlite ]; then
    if [ -f /var/www/html/database/seed_template.db ]; then
        echo "Initializing SQLite database from pre-seeded template..."
        cp /var/www/html/database/seed_template.db /var/www/html/database/database.sqlite
    else
        echo "Creating blank SQLite database file..."
        touch /var/www/html/database/database.sqlite
    fi
fi
chmod 666 /var/www/html/database/database.sqlite 2>/dev/null || true

# 4. Validate or generate AES-256 base64 APP_KEY
if [ -z "$APP_KEY" ] || [[ "$APP_KEY" != base64:* ]] || [ ${#APP_KEY} -lt 40 ]; then
    echo "Configuring valid base64 production AES-256 key..."
    export APP_KEY="base64:vy3FjUlWgMnNrqcQI3tN0EtnCcdtaf7bpBn9F0sfG1M="
fi

# 5. Run migrations & seeders safely
echo "Ensuring database schema is up-to-date..."
php artisan migrate --force || true
php artisan db:seed --force || true

# 6. Optimize Laravel performance
echo "Optimizing Laravel configuration, routes, and views..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== IntraEats HR Boot Completed. Starting Apache on port $PORT ==="
exec apache2-foreground
