<?php
require __DIR__ . '/vendor/autoload.php';

define('DS', DIRECTORY_SEPARATOR);
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);
define('ROOT_PATH', __DIR__ );
define('VIEW_PATH', ROOT_PATH . DS . 'views');
define('THEME_PATH', ROOT_PATH . DS . 'themes');
define('STATIC_PATH', ROOT_PATH . DS . 'static');
define('SOURCE_PATH', ROOT_PATH . DS . 'source');
define('PUBLIC_PATH', ROOT_PATH . DS . 'public');
define('TEMP_PATH', ROOT_PATH . DS . 'runtime');

if ( !IS_CLI ) {
    die('Please use cli');
}

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

$app->υse(new StaticFiles( STATIC_PATH )); 

use Middlewares\Assets; 
$app->υse(new Assets()); 

use Middlewares\BodyJson; 
$app->υse(new BodyJson()); 

use Middlewares\Render; 
$app->υse(new Render()); 

use Middlewares\Show; 
$app->υse(new Show()); 

require __DIR__ . DS . "helper.php";

$routes = require __DIR__ . DS . "routes.php";
$app->υse($routes->routes());


$app->listen(3000);