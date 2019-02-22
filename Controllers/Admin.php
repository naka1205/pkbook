<?php
namespace Controllers;
use Naka507\Koa\Context;

use Models\Post;
use Models\Category;
use Models\Single;
use Extend\Config;

class Admin
{
    public static $pagenum = 10;
    public static function base(Context $ctx, $next, $vars){
        $admin = $ctx->getSession('admin');
        if ( !$admin ) {
            $ctx->redirect("/login",301);
            return;
        }
        $token = $ctx->getCookie('token');
        $ctx->state["token"] = $token;

        $admin = ( yield Config::get('admin') );
        self::$pagenum = intval($admin["pagenum"]);
    }

    public static function index(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render("index");
    }

    public static function info(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render("info");
    }

    public static function singles(Context $ctx, $next){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $singlesData = ( yield Single::select([],$page,self::$pagenum,'/admin/singles?page=:page') );
        $ctx->state["data"] = $singlesData['data'];
        $ctx->state["pagination"] = json_encode ($singlesData['pagination']);
        
        $ctx->status = 200;
        yield $ctx->render("singles");
    }

    public static function posts(Context $ctx, $next, $vars){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;
        $cate = isset($ctx->get["cate"]) ? $ctx->get["cate"] : '';

        $categories = ( yield Category::select([]) );
        $where = [];
        $link = '/admin/posts?page=:page';
        if ( !empty($cate) && $cate != 'all' ) {
            $where['categories_value'] = $cate;
            $link = '/admin/posts?cate='.$cate.'&page=:page';
        }
        $postsData = ( yield Post::select($where,$page,self::$pagenum,$link) );
        
        $ctx->state["data"] = $postsData['data'];
        $ctx->state["pagination"] = json_encode ($postsData['pagination']);
        $ctx->state["cate"] = $cate;
        $ctx->state["categories"] = $categories;

        $ctx->status = 200;
        yield $ctx->render("posts");
    }

    public static function gallery(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render("gallery");
    }

    public static function logs(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render("logs");
    }
    
}