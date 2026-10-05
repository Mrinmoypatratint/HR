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

# 5. Configure Mail Credentials for Gmail SMTP
export MAIL_MAILER="${MAIL_MAILER:-smtp}"
export MAIL_HOST="${MAIL_HOST:-smtp.gmail.com}"
export MAIL_PORT="${MAIL_PORT:-587}"
export MAIL_USERNAME="${MAIL_USERNAME:-hr.intraeats@gmail.com}"
export MAIL_PASSWORD="${MAIL_PASSWORD:-iazbncolxbsrqorv}"
export MAIL_ENCRYPTION="${MAIL_ENCRYPTION:-tls}"
export MAIL_FROM_ADDRESS="${MAIL_FROM_ADDRESS:-hr.intraeats@gmail.com}"
export MAIL_FROM_NAME="${MAIL_FROM_NAME:-IntraEats & Talisha Software HR}"
export MAIL_HR="${MAIL_HR:-hr@intraeats.com}"

# Ensure .env file exists for Laravel Dotenv loader
if [ ! -f /var/www/html/.env ]; then
    echo "Generating /var/www/html/.env..."
    cat << EOF > /var/www/html/.env
APP_NAME="IntraEats HR"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_TIMEZONE=Asia/Kolkata
APP_URL=https://intraeats-hr-system.onrender.com

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=hr.intraeats@gmail.com
MAIL_PASSWORD=iazbncolxbsrqorv
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hr.intraeats@gmail.com
MAIL_FROM_NAME="IntraEats & Talisha Software HR"
MAIL_HR=hr@intraeats.com
EOF
    chmod 644 /var/www/html/.env
fi

# Export environment variables to Apache envvars so mod_php child workers inherit them
if [ -f /etc/apache2/envvars ]; then
    echo "export APP_KEY=\"$APP_KEY\"" >> /etc/apache2/envvars
    echo "export MAIL_MAILER=\"$MAIL_MAILER\"" >> /etc/apache2/envvars
    echo "export MAIL_HOST=\"$MAIL_HOST\"" >> /etc/apache2/envvars
    echo "export MAIL_PORT=\"$MAIL_PORT\"" >> /etc/apache2/envvars
    echo "export MAIL_USERNAME=\"$MAIL_USERNAME\"" >> /etc/apache2/envvars
    echo "export MAIL_PASSWORD=\"$MAIL_PASSWORD\"" >> /etc/apache2/envvars
    echo "export MAIL_ENCRYPTION=\"$MAIL_ENCRYPTION\"" >> /etc/apache2/envvars
    echo "export MAIL_FROM_ADDRESS=\"$MAIL_FROM_ADDRESS\"" >> /etc/apache2/envvars
    echo "export MAIL_FROM_NAME=\"$MAIL_FROM_NAME\"" >> /etc/apache2/envvars
    echo "export MAIL_HR=\"$MAIL_HR\"" >> /etc/apache2/envvars
fi

# 6. Run migrations & seeders safely
echo "Ensuring database schema is up-to-date..."
php artisan migrate --force || true
php artisan db:seed --force || true

# 7. Optimize Laravel performance
echo "Optimizing Laravel configuration, routes, and views..."
php artisan config:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "=== IntraEats HR Boot Completed. Starting Apache on port $PORT ==="
exec apache2-foreground
