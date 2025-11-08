#!/bin/bash
set -e

# Install Composer dependencies if vendor directory doesn't exist
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Create .env file from .env.example if .env doesn't exist
if [ ! -f /var/www/html/.env ]; then
    if [ -f /var/www/html/.env.example ]; then
        echo "Creating .env file from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
    fi
fi

# Fix permissions for storage and bootstrap/cache directories
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear routes and optimizations
php artisan route:clear || true
php artisan optimize:clear || true

# Execute the original command
exec "$@"
