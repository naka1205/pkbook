<?php
namespace Controllers;
use Naka507\Koa\Context;
class Admin
{

    public static function base(Context $ctx, $next, $vars){
        $admin = $ctx->getSession('admin');
        if ( !$admin ) {
            $ctx->redirect("/login",301);
            return;
        }
        $token = $ctx->getCookie('token');
        $ctx->state["token"] = $token;
    }

    public static function index(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/index.html");
    }

    public static function posts(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/posts.html");
    }

}