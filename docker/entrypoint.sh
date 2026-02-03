#!/bin/bash
set -e

# 1. Run migrations ALWAYS / Ejecutar migraciones SIEMPRE
echo "Running migrations..."
php artisan migrate --force

# 2. Run seeders ALWAYS / Ejecutar seeders SIEMPRE
# Removed 'if' check. Will execute on every deploy. / Eliminamos el 'if' y el conteo de usuarios. Se ejecutará en cada despliegue.
echo "Seeding database..."
php artisan db:seed --force

# 3. Cache configuration / Cachear configuración
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Fix permissions (Critical for Render) / Arreglar permisos (Crítico para Render)
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Start Apache / Iniciar Apache
echo "Starting Apache..."
exec apache2-foreground
