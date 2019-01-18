<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
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

    public static function info(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/info.html");
    }

    public static function posts(Context $ctx, $next){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $postsData = Post::select($page,2);
        foreach ($postsData['data'] as $key => &$value) {
            unset($value['filename']);
            unset($value['content']);
        }
        $ctx->state["data"] = $postsData['data'];
        $ctx->state["pagination"] = json_encode ($postsData['pagination']);
        
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/posts.html");
    }

    public static function gallery(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/gallery.html");
    }

    public static function logs(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/logs.html");
    }

}