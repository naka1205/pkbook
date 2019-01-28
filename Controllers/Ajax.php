<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
use Models\Single;
use Extend\Publish;
class Ajax
{
    public static function base(Context $ctx, $next, $vars){
        $admin = $ctx->getSession('admin');
        if ( !$admin ) {
            $ctx->status = 200;
            $ctx->body = false;
            return;
        }

        if ( isset($ctx->post['data']) ) {
            $ctx->post['data'] = self::parse($ctx->post['data']);
        }
    }

    public static function posts(Context $ctx, $next, $vars){
        $_id = isset($vars[0]) ? $vars[0] : '';
        $data = self::parse($ctx->post['data']);
        $data['date'] = isset($data['date']) ? $data['date'] : date('Y-m-d h:i:s');

        $post = new Post($_id);
        $bool = $post->save($data);

        $ctx->status = 200;
        $ctx->body = $bool;
    }

    public static function update(Context $ctx, $next){

        $type = isset($ctx->post['type']) ? $ctx->post['type'] : '';
        switch ($type) {
            case 'posts':
                yield Source::update();
                break;
            case 'singles':
                yield Publish::singles();
                break;
            default:
                # code...
                break;
        }

        $ctx->status = 200;
        $ctx->body = '';
    }

    public function publish(Context $ctx, $next){

        $type = isset($ctx->post['type']) ? $ctx->post['type'] : '';
        switch ($type) {
            case 'index':
                yield Publish::index();
                break;
            case 'posts':
                yield Publish::posts();
                break;
            case 'tags':
                yield Publish::tags();
                break;
            case 'categories':
                yield Publish::categories();
                break;
            default:
                # code...
                break;
        }
        
        $ctx->status = 200;
        $ctx->body = '';
    }

    public static function parse($data){
        $arr = explode('&',$data);
        $res = [];
        foreach ($arr as $key => $value) {
            $temp = explode('=',$value);
            $res[$temp[0]] = urldecode($temp[1]);
        }
        return $res;
    }



}