<?php

use Illuminate\Foundation\Application;

define('LARAVEL_START', microtime(true));

// Modo mantenimiento
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload de Composer
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Manejar la request y enviar la respuesta
$request = Illuminate\Http\Request::capture();
$response = $app->handle($request);
$response->send();

$app->terminate($request, $response);

