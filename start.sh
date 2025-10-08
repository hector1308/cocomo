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

# Migraciones en producción
php artisan migrate --force

# Servir la app con FrankenPHP
frankenphp public/index.php


