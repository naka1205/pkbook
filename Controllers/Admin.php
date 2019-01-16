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

    public static function info(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->render(VIEW_PATH . "/info.html");
    }

    public static function posts(Context $ctx, $next){

        $paginationData = [
            'className' => '',
            'theme' => 'default',
            'options' => [
                'select' => ''
            ],
            'content' => [
                'prevTitle' => '上一页',
                'prevLink' => '#',
                'firstTitle' => '第一页',
                'firstLink' => '#',
                'nextTitle' => '下一页',
                'nextLink' => '#',
                'lastTitle' => '最末页',
                'lastLink' => '#',
                'total' => '15',
                'page' => [
                    ['title'=> '1',"link"=>  "#","className"=> ""],
                    ['title'=> '2',"link"=>  "#","className"=> ""],
                    ['title'=> '3',"link"=>  "#","className"=> ""]
                ]
            ],
        ];
        $ctx->state["paginationData"] = json_encode($paginationData);
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