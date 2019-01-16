<?php
namespace Controllers;
use Naka507\Koa\Context;
class Posts
{

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/posts/add.html");
    }

    public static function edit(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/posts/edit.html");
    }

}