<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
class Show
{
    public static function index(Context $ctx, $next){
        global $configs;
        $ctx->status = 200;
        yield $ctx->render(THEME_PATH . DS . $configs['site']['theme'] . "/index.html");
    } 

    public static function posts(Context $ctx, $next, $vars){
        global $configs;
        $post = new Post($vars[0]);
        $ctx->status = 200;
        $ctx->state["posts"] = $post;
        yield $ctx->render(THEME_PATH . DS . $configs['site']['theme'] . "/posts.html");
    } 

}