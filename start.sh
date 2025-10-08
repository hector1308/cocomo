#!/bin/bash

# Instalar dependencias si no existen
if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
fi

# Permisos necesarios para Laravel
chmod -R 775 storage bootstrap/cache

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migraciones
php artisan migrate --force

# Iniciar Laravel en el puerto que Railway asigna
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
