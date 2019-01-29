<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Single;

class Singles
{

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render("singles/add");
    }

    public static function edit(Context $ctx, $next, $vars){
        $data = new Single($vars[0]);
        $ctx->status = 200;
        $ctx->state = $data;
        yield $ctx->render("singles/edit");
    }


}