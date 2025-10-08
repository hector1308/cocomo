#!/bin/bash

# Instalar dependencias si no existen
if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
fi

# Permisos
chmod -R 775 storage bootstrap/cache

# Servir Laravel con FrankenPHP
exec frankenphp public/index.php --port=$PORT





