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
    }

    public static function posts(Context $ctx, $next, $vars){
        $_id = isset($vars[0]) ? $vars[0] : '';
        $data = self::parse($ctx->post['data']);
        $data['date'] = isset($data['date']) ? $data['date'] : date('Y-m-d h:i:s');

        $post = new Post($_id);
        $bool = $post->save($data) ? true : false;

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
                yield Source::singles();
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
            case 'single':
                yield Publish::single();
                break;
            case 'assets':
                yield Publish::assets();
                break;
            case '404':
                yield Publish::notFound();
                break;
            case 'posts':
                yield Publish::posts();
                yield Publish::categories();
                yield Publish::tags();
                yield Publish::index();
                yield Publish::search();
                break;
            default:
                yield Publish::single();
                yield Publish::posts();
                yield Publish::index();
                yield Publish::tags();
                yield Publish::categories();
                yield Publish::notFound();
                yield Publish::search();
                yield Publish::assets();
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