<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
class Posts
{

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render("posts/add");
    }

    public static function edit(Context $ctx, $next, $vars){
        $data = new Post($vars[0]);
        $ctx->status = 200;
        $ctx->state = $data;
        yield $ctx->render("posts/edit");
    }

}