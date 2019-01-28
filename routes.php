<?php
use Naka507\Koa\Router;

$router = new Router();

$router->get('/login',['Controllers\Main', 'login']); 

$router->before('GET|POST', '/admin/.*',['Controllers\Admin', 'base']);
$router->before('GET|POST', '/ajax/.*',['Controllers\Ajax', 'base']);

$router->mount('/admin', function() use ($router) {

    $router->get('/index', ['Controllers\Admin', 'index']);
    $router->get('/info', ['Controllers\Admin', 'info']);

    $router->get('/singles/(\w+)', ['Controllers\Admin', 'singles']);
    $router->get('/singles', ['Controllers\Admin', 'singles']);
    
    $router->get('/posts/(\w+)', ['Controllers\Admin', 'posts']);
    $router->get('/posts', ['Controllers\Admin', 'posts']);
    

    $router->get('/gallery', ['Controllers\Admin', 'gallery']);
    $router->get('/logs', ['Controllers\Admin', 'logs']);

});


$router->mount('/posts', function() use ($router) {

    $router->get('/add', ['Controllers\Posts', 'add']);
    $router->get('/edit/(\w+)', ['Controllers\Posts', 'edit']);

});

$router->mount('/singles', function() use ($router) {

    $router->get('/add', ['Controllers\Singles', 'add']);
    $router->get('/edit/(\w+)', ['Controllers\Singles', 'edit']);

});

$router->mount('/show', function() use ($router) {

    $router->get('/index', ['Controllers\Show', 'index']);
    $router->get('/posts/(\w+)', ['Controllers\Show', 'posts']);
    $router->get('/singles/(\w+)', ['Controllers\Show', 'singles']);

});

$router->mount('/api', function() use ($router) {

    $router->post('/login', ['Controllers\Api', 'login']);

});

$router->mount('/ajax', function() use ($router) {

    $router->post('/publish', ['Controllers\Ajax', 'publish']);
    $router->post('/update', ['Controllers\Ajax', 'update']);
    $router->post('/posts', ['Controllers\Ajax', 'posts']);
    $router->post('/posts/(\w+)', ['Controllers\Ajax', 'posts']);
    $router->post('/singles', ['Controllers\Ajax', 'singles']);
    $router->post('/singles/(\w+)', ['Controllers\Ajax', 'singles']);

});

return $router;
