<?php
use Naka507\Koa\Router;

$router = new Router();

$router->get('/login',['Controllers\Main', 'login']); 

$router->before('GET|POST', '/admin/.*',['Controllers\Admin', 'base']);

$router->mount('/admin', function() use ($router) {

    $router->get('/index', ['Controllers\Admin', 'index']);
    $router->get('/posts', ['Controllers\Admin', 'posts']);

});
$router->mount('/api', function() use ($router) {

    $router->post('/login', ['Controllers\Api', 'login']);

});

return $router;
