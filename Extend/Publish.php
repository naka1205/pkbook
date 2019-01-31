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
        
        $posts = Post::select([]);
        
        $num = 1;
        $pages = ceil( count($posts) / $num );

        $source = PUBLIC_PATH . DS . 'page' . DS . ':_id.html';

        $opt = [
            'view_suffix'   =>	'html',
            'tpl_cache'     =>	false,
            'view_path'	    =>  THEME_PATH,
            'cache_path'	=>	CACHE_PATH . DS . 'themes'
        ];

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

            $site = Config::all('site');
            if ( $page == 1 ) {
                yield $view->publish($site['theme'] . '/index', PUBLIC_PATH . DS . "index.html");
            }
            $file =  str_replace (':_id',$page,$source);   
            yield $view->publish($site['theme'] . '/index',$file);
        }
    }

    public function single()
    {

    }

    public function tags()
    {

    }

    public function categories()
    {
        $configs = Config::all();

        $categories = Category::select([]);
        $singles = Single::select([]);

        $opt = [
            'view_suffix'   =>	'html',
            'tpl_cache'     =>	false,
            'view_path'	    =>  THEME_PATH,
            'cache_path'	=>	CACHE_PATH . DS . 'themes'
        ];
        $view = new Template($opt);

        foreach ($categories as $key => $value) {
            $where = ['_id'=>$value['posts']];
            $posts = Post::select($where);
            $num = 1;
            $pages = ceil( count($posts) / $num );

            $source = PUBLIC_PATH . DS . 'category' . DS . $value['_id'] . DS . 'page' . DS . ':_id.html';

            for ( $page = 1 ; $page <= $pages; $page++) { 
                $link = '/category/'.$value['_id'].'/page/:page'. $configs['link']['suffix'];
                $posts = Post::select($where,$page,$num,$link);

                $state = [];
                $state['link'] = '';
                $state['posts'] = $posts['data'];
                $state['pagination'] = self::pagination($posts['pagination']);
                $state['singles'] = $singles;
                $state['categories'] = $categories;
                $view->assign('state',$state);

                if ( $page == 1 ) {
                    yield $view->publish($configs['site']['theme'] . '/category', PUBLIC_PATH. DS . 'category' . DS . $value['_id'] . DS . "index.html");
                }
                $file =  str_replace (':_id',$page,$source);   
                yield $view->publish($configs['site']['theme'] . '/index',$file);
            }

        }
        
        return;

        
    }

    public function posts()
    {
        $configs = Config::all();
        
        $configs['link']['domain'] = '';
        $configs['link']['suffix'] = '.html';

        $posts = Source::update();
        $categories = Category::select([]);
        $singles = Single::select([]);
        
        $source = PUBLIC_PATH . str_replace ('/',DS, $configs['link']['posts']) . ".html";
        
        $opt = [
            'view_suffix'   =>	'html',
            'tpl_cache'     =>	false,
            'view_path'	    =>  THEME_PATH,
            'cache_path'	=>	CACHE_PATH . DS . 'themes'
        ];
        $view = new Template($opt);

        foreach ($posts as $key => $post) {
            $_id = $post['_id'];

            $post = new Post($_id);
            $state = [];
            $state['post'] = $post;
            $state['singles'] = $singles;
            $state['categories'] = $categories;
            $view->assign('state',$state);

            $file =  str_replace (':_id',$_id,$source);
            yield $view->publish($configs['site']['theme'] . '/posts',$file);
        }
    }

}
