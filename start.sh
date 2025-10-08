#!/bin/bash

# Instalar dependencias si no existen
if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
fi

# Permisos necesarios
chmod -R 775 storage bootstrap/cache

# Servir Laravel con FrankenPHP en el puerto que asigna Railway
exec frankenphp public/index.php --port=$PORT





