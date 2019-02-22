<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
use Extend\Upload;
class Posts
{

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        $ctx->state['qiniu'] = Upload::qiniu();
        yield $ctx->render("posts/add");
    }

    public static function edit(Context $ctx, $next, $vars){
        $data = new Post($vars[0]);
        $ctx->status = 200;
        $ctx->state = $data;
        $ctx->state['qiniu'] = Upload::qiniu();
        yield $ctx->render("posts/edit");
    }

}