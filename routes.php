<?php
use Naka507\Koa\Router;

$router = new Router();

$router->get('/login',['Controllers\Main', 'login']); 

$router->before('GET|POST', '/admin/.*',['Controllers\Admin', 'base']);

$router->mount('/admin', function() use ($router) {

    $router->get('/index', ['Controllers\Admin', 'index']);
    $router->get('/info', ['Controllers\Admin', 'info']);
    $router->get('/posts', ['Controllers\Admin', 'posts']);
    $router->get('/gallery', ['Controllers\Admin', 'gallery']);
    $router->get('/logs', ['Controllers\Admin', 'logs']);

});


$router->mount('/posts', function() use ($router) {

    $router->get('/add', ['Controllers\Posts', 'add']);
    $router->get('/edit', ['Controllers\Posts', 'edit']);

});

$router->mount('/api', function() use ($router) {

    $router->post('/login', ['Controllers\Api', 'login']);

});

return $router;
