<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

if (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/web_penjualan')) {
    if ($_SERVER['REQUEST_URI'] === '/web_penjualan' || str_starts_with($_SERVER['REQUEST_URI'], '/web_penjualan?')) {
        $_SERVER['REQUEST_URI'] = '/web_penjualan/' . substr($_SERVER['REQUEST_URI'], strlen('/web_penjualan'));
    }
    if (isset($_SERVER['SCRIPT_NAME']) && !str_starts_with($_SERVER['SCRIPT_NAME'], '/web_penjualan')) {
        $_SERVER['SCRIPT_NAME'] = '/web_penjualan' . (str_starts_with($_SERVER['SCRIPT_NAME'], '/') ? '' : '/') . $_SERVER['SCRIPT_NAME'];
    }
}

$app->handleRequest(Request::capture());
