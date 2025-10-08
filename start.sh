#!/bin/bash

# Instalar dependencias si no existen
if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
fi

# Permisos necesarios
chmod -R 775 storage bootstrap/cache

# Servir Laravel con PHP Built-in server
exec php -S 0.0.0.0:$PORT -t public




