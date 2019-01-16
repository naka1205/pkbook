<?php
require __DIR__ . '/vendor/autoload.php';

define('DS', DIRECTORY_SEPARATOR);
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);
define('VIEW_PATH', __DIR__ . DS . 'views');
define('STATIC_PATH', __DIR__ . DS . 'static');
define('DB_FILE', __DIR__ . DS . 'db.json');

if ( !IS_CLI ) {
    die('Please use cli');
}

$configs = require __DIR__ . DS . "configs.php";

use Naka507\Koa\Application;
use Naka507\Koa\Context;
use Naka507\Koa\Error;
use Naka507\Koa\Timeout;
use Naka507\Koa\NotFound;

use Naka507\Koa\StaticFiles; 

$app = new Application();
$app->υse(new Error());
$app->υse(new Timeout(10));
$app->υse(new NotFound()); 

$static_path = __DIR__ . DS .  "static" ;
$app->υse(new StaticFiles( $static_path )); 

use Middlewares\BodyJson; 
$app->υse(new BodyJson()); 

$routes = require __DIR__ . DS . "routes.php";
$app->υse($routes->routes());

$app->listen(3000);