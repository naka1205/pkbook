<?php
require __DIR__ . '/vendor/autoload.php';

define('DS', DIRECTORY_SEPARATOR);
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);
define('ROOT_PATH', __DIR__ );
define('THEME_PATH', ROOT_PATH . DS . 'themes');
define('PUBLIC_PATH', ROOT_PATH . DS . 'public');
if ( !IS_CLI ) {
    die('Please use cli');
}

use Naka507\Koa\Application;
use Naka507\Koa\Context;
use Naka507\Koa\Error;
use Naka507\Koa\Timeout;
use Naka507\Koa\NotFound;

$app = new Application();
$app->υse(new Error());
$app->υse(new Timeout(5));
$app->υse(new NotFound()); 

use Middlewares\Html; 
$app->υse(new Html( PUBLIC_PATH )); 

use Middlewares\Assets; 
$app->υse(new Assets()); 


$app->listen(88);