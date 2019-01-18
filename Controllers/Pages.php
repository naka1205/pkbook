<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Page;

class Pages
{

    public static function add(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/pages/add.html");
    }

    public static function edit(Context $ctx, $next, $vars){
        $data = new Page($vars[0]);
        $ctx->status = 200;
        $ctx->state["page"] = $data;
        yield $ctx->render(VIEW_PATH . "/pages/edit.html");
    }




}