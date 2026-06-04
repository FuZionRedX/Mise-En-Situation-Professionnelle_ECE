#!/bin/sh
set -e

# Install dependencies if vendor is missing (first clone on a new machine)
if [ ! -d vendor ]; then
    composer install --optimize-autoloader --no-interaction
fi

# Create .env from docker template if none exists
if [ ! -f .env ]; then
    cp .env.docker .env
fi

# Generate app key if not already set
php artisan key:generate --force

# Ensure SQLite database file exists
touch database/database.sqlite

# Fix permissions on writable directories
chown -R www-data:www-data storage bootstrap/cache database 2>/dev/null || true
chmod -R 775 storage bootstrap/cache database

# Run pending migrations
php artisan migrate --force

# Create public/storage symlink (safe to re-run)
php artisan storage:link --force 2>/dev/null || true

# Clear compiled files so config is fresh
php artisan config:clear
php artisan view:clear

exec "$@"
