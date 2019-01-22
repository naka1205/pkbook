<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
class Show
{
    public static function index(Context $ctx, $next){
        global $configs;
        $ctx->status = 200;
        yield $ctx->render(THEME_PATH . DS . $configs['site']['theme'] . "/index.tpl");
    } 

    public static function posts(Context $ctx, $next, $vars){
        global $configs;
        $data = new Post($vars[0]);
        $ctx->status = 200;
        $ctx->state["posts"] = $data;
        yield $ctx->render(THEME_PATH . DS . $configs['site']['theme'] . "/posts.tpl");
    } 

}