#!/bin/bash

# Instalar dependencias si no existen
if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
fi

# Permisos necesarios
chmod -R 775 storage bootstrap/cache

# Cachear configuración, routes y views (opcional)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Servir Laravel con PHP Built-in server en el puerto que asigna Railway
exec php -S 0.0.0.0:$PORT -t public



