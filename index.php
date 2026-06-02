<?php

/*
|--------------------------------------------------------------------------
| Root front controller
|--------------------------------------------------------------------------
| This lets the application run from a sub-directory such as
| http://localhost/Projects/PharmacyPOS/ with clean URLs, without the
| "/public" segment and without showing a directory listing. Static assets
| are mapped into public/ by the accompanying .htaccess file.
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
