#!/usr/bin/env bash
# Exit on error
set -o errexit

# Install Composer dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node.js dependencies
npm ci

# Build frontend assets
npm run build

# Generate application key if not set
php artisan key:generate --force --no-interaction

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force --no-interaction

echo "Build completed successfully!"
