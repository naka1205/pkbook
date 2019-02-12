<?php
namespace Extend;
use Exception;

use Models\Tag;
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
                if ( intval($pagination['content']['current']) > 5 ) {
                    $html .= '<li><a href="'.$pagination['content']['firstLink'].'">1</a></li>';
                    $html .= '<li><a>...</a></li>';
                }
            }
            $html .= '<li class="'.$value['className'].'"><a href="'.$value['link'].'">'.$value['title'].'</a></li>';

            if ( ( $key + 1 ) == $count ) {
                if ( ( intval($pagination['content']['current']) + 5 ) < $pagination['content']['pages'] ) {
                    $html .= '<li><a>...</a></li>';
                    $html .= '<li><a href="'.$pagination['content']['lastLink'].'">'.$pagination['content']['pages'].'</a></li>';
                }
                $html .= '<li class="'.$pagination['content']['lastClassName'].'"><a href="'.$pagination['content']['lastLink'].'">&raquo;</a></li>';
            }
        }
        return $html;
    }

    public static function index()
    {
        self::clear(PUBLIC_PATH . DS . 'page');

        $configs = Config::all();

        $posts = Post::select([]);
        
        $num = intval($configs['site']['pagenum']);
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

        $single = new Single('about');

        for ( $page = 1 ; $page <= $pages; $page++) { 
            $where = [];
            $link = '/page/:page.html';
            $posts = Post::select($where,$page,$num,$link);

            $state = [];
            $state['link'] = $configs['link'];
            $state['site'] = $configs['site'];
            $state['title'] = '首页';
            $state['posts'] = $posts['data'];
            $state['pagination'] = self::pagination($posts['pagination']);
            $state['single'] = $single;
            $state['singles'] = $singles;
            $state['categories'] = $categories;
            $view->assign('state',$state);

            if ( $page == 1 ) {
                yield $view->publish($configs['site']['theme'] . '/index', PUBLIC_PATH . DS . "index.html");
            }
            if ( $num == 1 ) {
                return;
            }
            $file =  str_replace (':_id',$page,$source);   
            yield $view->publish($configs['site']['theme'] . '/index',$file);
        }
    }

    public static function single()
    {
        self::clear( PUBLIC_PATH , ['assets','category','page','posts','tags','tags.html','index.html','404.html','search.json'] );

        $configs = Config::all();
        $singles = Single::select([]);
        $categories = Category::select([]);
        
        $opt = [
            'view_suffix'   =>	'html',
            'tpl_cache'     =>	false,
            'view_path'	    =>  THEME_PATH,
            'cache_path'	=>	CACHE_PATH . DS . 'themes'
        ];
        $view = new Template($opt);

        foreach ($singles as $key => $value) {
            $single = new Post($value['_id']);

            $state = [];
            $state['link'] = $configs['link'];
            $state['site'] = $configs['site'];
            $state['title'] = $value['title'];
            $state['singles'] = $singles;
            $state['categories'] = $categories;
            $state['single'] = $single;
            $view->assign('state',$state);
            yield $view->publish($configs['site']['theme'] . '/single', PUBLIC_PATH . DS . $single['name'] . ".html");
        }
    }

    public static function tags()
    {
        self::clear(PUBLIC_PATH . DS . 'tags');
        $configs = Config::all();
        $num = intval($configs['site']['pagenum']);

        $categories = Category::select([]);
        $singles = Single::select([]);
        $tags = Tag::select([]);

        $opt = [
            'view_suffix'   =>	'html',
            'tpl_cache'     =>	false,
            'view_path'	    =>  THEME_PATH,
            'cache_path'	=>	CACHE_PATH . DS . 'themes'
        ];
        $view = new Template($opt);
        $state = [];
        $state['link'] = $configs['link'];
        $state['site'] = $configs['site'];
        $state['title'] = '标签';
        $state['singles'] = $singles;
        $state['categories'] = $categories;
        $state['tags'] = $tags;
        $view->assign('state',$state);
        $bool = ( yield $view->publish($configs['site']['theme'] . '/tags', PUBLIC_PATH . DS . "tags.html") );
        if ( !$bool ) {
            return;
        }
        foreach ($tags as $key => $value) {
            $where = ['_id'=>$value['posts']];
            $posts = Post::select($where);
            
            $pages = ceil( count($posts) / $num );

            $source = PUBLIC_PATH . DS . 'tags' . DS . $value['_id'] . DS . 'page' . DS . ':_id.html';

            for ( $page = 1 ; $page <= $pages; $page++) { 
                $link = '/tags/'.$value['_id'].'/page/:page'. $configs['link']['suffix'];
                $posts = Post::select($where,$page,$num,$link);

                $state = [];
                $state['link'] = $configs['link'];
                $state['site'] = $configs['site'];
                $state['title'] = $value['title'];
                $state['posts'] = $posts['data'];
                $state['pagination'] = self::pagination($posts['pagination']);
                $state['singles'] = $singles;
                $state['categories'] = $categories;
                $state['tag'] = $value;
                
                $view->assign('state',$state);

                if ( $page == 1 ) {
                    yield $view->publish($configs['site']['theme'] . '/tag', PUBLIC_PATH. DS . 'tags' . DS . $value['_id'] . DS . "index.html");
                }
                $file =  str_replace (':_id',$page,$source);   
                yield $view->publish($configs['site']['theme'] . '/tag',$file);
            }

        }
        
        return;

    }

    public static function categories()
    {
        self::clear(PUBLIC_PATH . DS . 'category');

        $configs = Config::all();
        $num = intval($configs['site']['pagenum']);

        $categories = Category::select([]);
        $singles = Single::select([]);

        $opt = [
            'view_suffix'   =>	'html',
            'tpl_cache'     =>	false,
            'view_path'	    =>  THEME_PATH,
            'cache_path'	=>	CACHE_PATH . DS . 'themes'
        ];
        $view = new Template($opt);
        $state = [];
        $state['link'] = $configs['link'];
        $state['site'] = $configs['site'];
        $state['title'] = '分类';
        $state['singles'] = $singles;
        $state['categories'] = $categories;
        $view->assign('state',$state);
        yield $view->publish($configs['site']['theme'] . '/categories', PUBLIC_PATH . DS . "categories.html");
        
        foreach ($categories as $key => $value) {
            $where = ['_id'=>$value['posts']];
            $posts = Post::select($where);
            
            $pages = ceil( count($posts) / $num );

            $source = PUBLIC_PATH . DS . 'category' . DS . $value['_id'] . DS . 'page' . DS . ':_id.html';

            for ( $page = 1 ; $page <= $pages; $page++) { 
                $link = '/category/'.$value['_id'].'/page/:page'. $configs['link']['suffix'];
                $posts = Post::select($where,$page,$num,$link);

                $state = [];
                $state['link'] = $configs['link'];
                $state['site'] = $configs['site'];
                $state['title'] = $value['title'];
                $state['posts'] = $posts['data'];
                $state['pagination'] = self::pagination($posts['pagination']);
                $state['singles'] = $singles;
                $state['categories'] = $categories;
                $state['category'] = $value;
                
                $view->assign('state',$state);

                if ( $page == 1 ) {
                    yield $view->publish($configs['site']['theme'] . '/category', PUBLIC_PATH. DS . 'category' . DS . $value['_id'] . DS . "index.html");
                }
                $file =  str_replace (':_id',$page,$source);   
                yield $view->publish($configs['site']['theme'] . '/category',$file);
            }

        }
        
        return;
    }

    public static function posts()
    {
        self::assets();
        self::clear(PUBLIC_PATH . DS . 'posts');

        $configs = Config::all();

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
            $state['link'] = $configs['link'];
            $state['site'] = $configs['site'];
            $state['title'] = $post['title'];
            $state['post'] = $post;
            $state['singles'] = $singles;
            $state['categories'] = $categories;
            $view->assign('state',$state);

            $_date = date('Ymd',strtotime($post['date']));

            $file =  str_replace (':_id',$_id,$source);
            $file =  str_replace (':_date',$_date,$file);
            yield $view->publish($configs['site']['theme'] . '/posts',$file);
        }
    }


    public static function notFound()
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
        $state = [];
        $state['link'] = $configs['link'];
        $state['site'] = $configs['site'];
        $state['title'] = '404';
        $state['singles'] = $singles;
        $state['categories'] = $categories;
        $view->assign('state',$state);
        yield $view->publish($configs['site']['theme'] . '/404', PUBLIC_PATH . DS . "404.html");
    }

    public static function search()
    {
        $posts = Post::select([]);

        $search = [];
        foreach($posts as $key=>$value ) {
            $post = new Post($value['_id']);

            $data['title'] = $post['title'];
            $data['url'] = $post['link'];
            $data['content'] = strip_tags($post['html']);
            $data['categories'] = $post['categories_value'];
            $data['tags'] = [];
            foreach ($post['tags'] as $k => $v) {
                $data['tags'][] = $v['title'];
            }

            $search[] = $data;
        }
        return file_put_contents(PUBLIC_PATH . DS . "search.json",json_encode($search));
    }

    public static function assets()
    {
        $configs = Config::all();

        $assets = PUBLIC_PATH . DS . 'assets';
        self::clear($assets);

        $themes = THEME_PATH . DS . $configs['site']['theme'] . DS . 'assets';
        self::copys($themes,$assets);

        return ;
    }

    public static function copys($source, $dest){

        $sources = scandir($source);
        foreach($sources as $val){
            if($val =="." || $val ==".."){
                continue;
            }

            $source_file = $source . DS . $val;
            $dest_file = $dest . DS . $val;

            if(is_dir($source_file)){
                mkdir($dest_file, 0755, true);
                self::copys($source_file, $dest_file);
            }else{
                copy($source_file, $dest_file);
            }

        }

    }

    public static function clear($path,$filter = [])
    {
        if(!is_dir($path)){
           return false;
        }
        $filter[] = ".";
        $filter[] = "..";
        $sources = scandir($path);
        foreach($sources as $val){
            if( in_array($val,$filter) ){
                continue;
            }
            $source = $path . DS . $val;
            if(is_dir($source)){
                $source .= DS;
                self::clear($source);
                @rmdir($source);
            }else{
                unlink($source);
            }
        }
        return true;
    }

}