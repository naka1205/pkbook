<?php
namespace Extend;
use Exception;
use Models\Post;
class Publish
{

    public function index()
    {
        global $configs;

    }

    public function single()
    {
        global $configs;

    }

    public function tags()
    {
        global $configs;

    }

    public function categories()
    {
        global $configs;

    }

    public function posts()
    {
        global $configs;
        $data = ( yield Source::update() );
        $source = $configs['publish']['path'] . str_replace ('/',DS,$configs['link']['posts']) . "." . $configs['publish']['suffix'];
        $opt = $configs['view'];
        $opt['tpl_cache'] = false;
        $opt['view_path'] = THEME_PATH . DS . $configs['site']['theme'];
        $view = new Template($opt);

        foreach ($data as $key => $post) {
            $_id = $post['_id'];

            $file =  str_replace (':_id',$_id,$source);

            $post = new Post($_id);
            $view->assign('posts',$post);

            yield $view->publish('posts',$file);
        }
    }

}
