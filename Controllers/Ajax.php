<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\Post;
use Extend\Cache;
use Extend\Source;
use Extend\Template;
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
        $bool = $post->save($data);

        $ctx->status = 200;
        $ctx->body = $bool;
    }

    public static function update(Context $ctx, $next){

        $bool = ( yield Source::update() ? '' : false );
        $ctx->status = 200;
        $ctx->body = $bool;
    }

    public function publish(Context $ctx, $next){
        // $bool = ( yield Source::publish() );

        global $configs;
        $data = ( yield Source::update() );
        $source = $configs['publish']['path'] . str_replace ('/',DS,$configs['link']['posts']) . "." . $configs['publish']['suffix'];
        $template = THEME_PATH . DS . $configs['site']['theme'] . "/posts.html";
        $opt = $configs['view'];
        $opt['tpl_cache'] = false;
        $opt['view_path'] = THEME_PATH . DS . $configs['site']['theme'];

        $savepath = str_replace (basename( $source ),'', $source);
        if (!is_dir($savepath)) {
            mkdir($savepath, 0755, true);
        }
        $view = new Template($opt);
        foreach ($data as $key => $post) {
            $_id = $post['_id'];
            $post = new Post($_id);
            $view->assign('posts',$post);
            $html = ( yield $view->fetch('posts') );
            $file =  str_replace (':_id',$_id,$source);
            yield file_put_contents ( $file ,  $html );
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