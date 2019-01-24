<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
class Ajax
{
    public static function base(Context $ctx, $next, $vars){
        $admin = $ctx->getSession('admin');
        if ( !$admin ) {
            $ctx->status = 200;
            $ctx->body = false;
            return;
        }
    }

    public static function posts(Context $ctx, $next, $vars){
        $_id = isset($vars[0]) ? $vars[0] : '';
        $data = self::parse($ctx->post['data']);
        $data['filename'] = isset($data['filename']) ? $data['filename'] : '';
        $data['date'] = isset($data['date']) ? urldecode($data['date']) : date('Y-m-d h:i:s');

        $post = new Post($_id);
        $bool = $post->save($data);

        $ctx->status = 200;
        $ctx->body = $bool;
    }

    public static function update(Context $ctx, $next){

        $data = Post::update();

        $bool = false;
        if ($data) {
            $bool = '';
        }
        $ctx->status = 200;
        $ctx->body = $bool;
    }

    public static function parse($data){
        $arr = explode('&',$data);
        $res = [];
        foreach ($arr as $key => $value) {
            $temp = explode('=',$value);
            $res[$temp[0]] = $temp[1];
        }
        return $res;
    }

}