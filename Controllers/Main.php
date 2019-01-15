<?php
namespace Controllers;
use Naka507\Koa\Context;
class Main
{

    public static function login(Context $ctx, $next){
        $admin = $ctx->getSession('admin');
        if ( $admin ) {
            $ctx->redirect("/admin/index",301);
            return;
        }
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/login.html");
    }
}