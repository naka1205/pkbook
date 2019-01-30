<?php
namespace Extend;
use Exception;
use Models\Post;
use Models\Single;
use Models\Category;
class Publish
{

    public static function pagination($pagination){
        $count = count($pagination['content']['page']);
        if ( $count == 1 ) {
            return '';
        }
        $html = '';
        foreach ($pagination['content']['page'] as $key => $value) {
            if ( $key == 0 ) {
                $html .= '<li class="'.$pagination['content']['prevClassName'].'"><a href="'.$pagination['content']['prevLink'].'">&laquo;</a></li>';
                if ( intval($pagination['content']['current']) > 4 ) {
                    $html .= '<li><a href="'.$pagination['content']['firstLink'].'">1</a></li>';
                    $html .= '<li><a>...</a></li>';
                }
            }
            $html .= '<li class="'.$value['className'].'"><a href="'.$value['link'].'">'.$value['title'].'</a></li>';

            if ( ( $key + 1 ) == $count ) {
                if ( ( intval($pagination['content']['current']) + 4 ) < $pagination['content']['pages'] ) {
                    $html .= '<li><a>...</a></li>';
                    $html .= '<li><a href="'.$pagination['content']['lastLink'].'">'.$pagination['content']['pages'].'</a></li>';
                }
                $html .= '<li class="'.$pagination['content']['lastClassName'].'"><a href="'.$pagination['content']['lastLink'].'">&raquo;</a></li>';
            }
        }
        return $html;
    }

    public function index()
    {
        global $configs;
        $posts = Post::select([]);
        
        $num = 1;
        $pages = ceil( count($posts) / $num );

        $source = $configs['publish']['path'] . DS . 'page' . DS . ':_id.' . $configs['publish']['suffix'];

        $opt = $configs['view'];
        $opt['tpl_cache'] = false;
        $opt['view_path'] = THEME_PATH . DS;
        $view = new Template($opt);

        $categories = Category::select([]);
        $singles = Single::select([]);

        for ( $page = 1 ; $page <= $pages; $page++) { 
            $where = [];
            $link = '/page/:page.html';
            $posts = Post::select($where,$page,$num,$link);

            $state = [];
            $state['link'] = '';
            $state['posts'] = $posts['data'];
            $state['pagination'] = self::pagination($posts['pagination']);
            $state['singles'] = $singles;
            $state['categories'] = $categories;
            $view->assign('state',$state);

            if ( $page == 1 ) {
                yield $view->publish($configs['site']['theme'] . '/index',$configs['publish']['path'] . DS . "index.html");
            }
            $file =  str_replace (':_id',$page,$source);   
            yield $view->publish($configs['site']['theme'] . '/index',$file);
        }
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

        $posts = Source::update();
        $categories = Category::select([]);
        $singles = Single::select([]);
        
        $source = $configs['publish']['path'] . str_replace ('/',DS,$configs['link']['posts']) . "." . $configs['publish']['suffix'];
        $opt = $configs['view'];
        $opt['tpl_cache'] = false;
        $opt['view_path'] = THEME_PATH . DS;
        $view = new Template($opt);

        foreach ($posts as $key => $post) {
            $_id = $post['_id'];

            $file =  str_replace (':_id',$_id,$source);

            $post = new Post($_id);
            $state = [];
            $state['link'] = '';
            $state['post'] = $post;
            $state['singles'] = $singles;
            $state['categories'] = $categories;
            $view->assign('state',$state);

            yield $view->publish($configs['site']['theme'] . '/posts',$file);
        }
    }

}
