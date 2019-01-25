<?php
require __DIR__ . '/vendor/autoload.php';

define('DS', DIRECTORY_SEPARATOR);
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

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

$public_path = __DIR__ . DS .  "public" ;
$app->υse(new StaticFiles( $public_path )); 

use Middlewares\Assets; 
$themes_static_path = __DIR__ . DS .  "themes";
$app->υse(new Assets( $themes_static_path )); 


$app->listen(8080);