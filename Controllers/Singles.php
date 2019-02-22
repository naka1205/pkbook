<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Single;
use Extend\Upload;
class Singles
{

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        $ctx->state['qiniu'] = Upload::qiniu();
        yield $ctx->render("singles/add");
    }

    public static function edit(Context $ctx, $next, $vars){
        $data = new Single($vars[0]);
        $ctx->status = 200;
        $ctx->state = $data;
        $ctx->state['qiniu'] = Upload::qiniu();
        yield $ctx->render("singles/edit");
    }


}