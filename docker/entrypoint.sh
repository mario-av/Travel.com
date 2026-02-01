#!/bin/bash
set -e

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed database only if admin user doesn't exist (prevents duplicates)
echo "Checking if seeding is needed..."
php artisan tinker --execute="exit(App\Models\User::where('email', 'admin@travel.com')->exists() ? 0 : 1);" 2>/dev/null
if [ $? -eq 1 ]; then
    echo "Seeding database..."
    php artisan db:seed --force
else
    echo "Database already seeded, skipping..."
fi

# Cache configuration
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions (crucial for Render)
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start Apache in foreground
echo "Starting Apache..."
exec apache2-foreground
