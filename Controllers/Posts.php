<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Posts as Post;

class Posts
{

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/posts/add.html");
    }

    public static function edit(Context $ctx, $next, $vars){
        $data = new Post($vars[0]);
        $ctx->status = 200;
        $ctx->state["posts"] = $data;
        yield $ctx->render(VIEW_PATH . "/posts/edit.html");
    }

}