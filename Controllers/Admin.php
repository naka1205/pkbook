<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
use Models\Category;
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
        yield $ctx->render("index");
    }

    public static function info(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/info.html");
    }

    public static function pages(Context $ctx, $next){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $ctegoryData = Category::select($page,2,'/admin/pages');
        $ctx->state["data"] = $ctegoryData['data'];
        $ctx->state["pagination"] = json_encode ($ctegoryData['pagination']);

        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/pages.html");
    }

    public static function posts(Context $ctx, $next, $vars){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;
        $cate = isset($vars[0]) ? $vars[0] : '';

        $categories = Category::select([]);

        $where = [];
        $link = '/admin/posts';
        if ( !empty($cate) && $cate != 'all' ) {
            $where['categories'] = $cate;
            $link .= "/" . $cate;
        }

        $postsData = Post::select($where,$page,2,$link);
        
        $ctx->state["data"] = $postsData['data'];
        $ctx->state["pagination"] = json_encode ($postsData['pagination']);
        $ctx->state["cate"] = $cate;
        $ctx->state["categories"] = $categories;

        $ctx->status = 200;
        yield $ctx->render("posts");
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