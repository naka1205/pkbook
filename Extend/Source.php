<?php
namespace Extend;
use Exception;
class Source
{

	public static function __callstatic($method, $arguments) {
        if (empty($arguments)) {
            return Cache::has($method) ? Cache::get($method) : Source::update($method);
        }
        if ( is_array($arguments[0]) ) {
            $data = Cache::has($method) ? Cache::get($method) : Source::update($method);
            if ( !empty($arguments[0]) ) {
                $where = $arguments[0];
                foreach ($data as $key => $value) {
                    foreach ($where as $k => $v ) {
                        if ( !isset($value[$k]) || self::key($value[$k]) != $v ) {
                            unset($data[$key]);
                        }
                    }
                }
            }
            return $data;
        }
        return Cache::has($arguments[0]) ? Cache::get($arguments[0]) : Source::find($arguments[0]);
    }

    public static function key($name){
        return md5( base64_encode($name) );
    }

    public static function del($_id){
        global $configs;
        $source = $configs['dir']['source'] . DS . "_posts" . DS . $_id . '.md';
        if ( is_file($source) ) {
            unlink($source);
        }
        if ( Cache::has($_id) ) {
            Cache::remove($_id);
        }
        return true;
    }

    public static function find($_id){
        global $configs;
        $source = $configs['dir']['source'] . DS . "_posts" . DS . $_id . '.md';
        
        $post = self::addPost($source);
        if ( !$post ) {
            return false;
        }
        $_id = $post['_id'];
        $_posts = Cache::get('_posts');

        if( isset($data['_id']) && $data['_id'] != $_id ){
            self::del($data['_id']);
            unset($_posts[$data['_id']]);
        }
        
        if ( Cache::has($_id) ) {
            $_post = Cache::get($_id);
            $post['id'] = $_post['id'];
            $post['createtime'] = $_post['createtime'];
        }else{
            $prev  =  end ( $_posts ); 
            $prev_id = $prev['_id'];
    
            $post['id'] = intval($prev['id']) + 1;
            $post['prev'] = $prev['link'];
            $post['prev_id'] = $prev_id;
            $post['prev_title'] = $prev['title'];

            $prev['next'] = $post['link'];
            $prev['next_id'] = $_id;
            $prev['next_title'] = $post['title'];
        }

        Cache::set($_id,$post);

        unset($post['content']);
        $_posts[$_id] = $post;

        Cache::set('_posts',$_posts);

        return $post;
    }

    public static function save($data){
        global $configs;
        $data['createtime'] = isset( $data['createtime'] ) ? $data['createtime'] : time();

        $md = "---\n";
        $md .= "title: " . $data['title'] . "\n";
        $md .= "date: " . $data['date'] . "\n";
        $md .= "tags: [" . $data['tags'] . "]\n";
        $md .= "categories: " . $data['categories'] . "\n";
        $md .= "createtime: " . $data['createtime'] . "\n";
        $md .= "---\n";
        $md .= trim($data['content']);
        $filename = self::key($data['title']);
        $source = $configs['dir']['source'] . DS . "_posts" . DS . $filename . '.md';;

        if ( !file_put_contents ( $source ,  $md ) ) {
            return false;
        }
        
        return true;
    }


    public static function update($name = ''){
        global $configs;
        $source = $configs['dir']['source'] . DS . "_posts" . DS;
        $data = self::glob($source);
        $_tags = [];
        $_posts = [];
        $_categories = [];
        foreach ( $data as $key => $post) {
            $_id = $post['_id'];
            $post['id'] = $key + 1;
            $prev_id = $key - 1 >= 0 ? $key - 1 : '';
            $next_id = $key + 1;
            $post['prev_id'] = isset($data[$prev_id]) ? $data[$prev_id]['_id'] : '';
            $post['prev_title'] = isset($data[$prev_id]) ? $data[$prev_id]['title'] : '';
            $post['prev'] = isset($data[$prev_id]) ? $data[$prev_id]['link'] : '';

            $post['next_id'] = isset($data[$next_id]) ? $data[$next_id]['_id'] : '';
            $post['next_title'] = isset($data[$next_id]) ? $data[$next_id]['title'] : '';
            $post['next'] = isset($data[$next_id]) ? $data[$next_id]['link'] : '';

            $filename = str_replace ('.md','', basename( $post['source'] ));
            if ( $post['_id'] != self::key( $filename ) ) {
                if ( is_file( $post['source'] ) ) {
                    unlink( $post['source'] );
                }
                self::save($post);
            }
            
            Cache::set($_id,$post);
            unset($post['content']);

            if ( isset($post['categories']) ) {
                self::addCategory($post,$_categories);
            }

            if ( isset($post['tags']) ) {
                self::addTags($post,$_tags);
            }

            $_posts[$_id] = $post;
        }

        Cache::set('_posts',$_posts);
        Cache::set('_tags',$_tags);
        Cache::set('_categories',$_categories);

        if ( !empty($name) ) {
            $name = "_" . $name;
            return isset($$name) ? $$name : [];
        }
        return $_posts;
    }


    public static function glob($source){
        
        $data = glob ( $source . "*.md" );

        usort($data, function(&$prev, &$next) {
            if ( !is_array($prev) ) {
                $prev = self::addPost($prev);
            }
            if ( !is_array($next) ) {
                $next = self::addPost($next);
            }

            $prevtime = isset($prev['createtime']) ? $prev['createtime'] : strtotime($prev['date']);
            $prev['createtime'] = $prevtime;
            $nexttime = isset($next['createtime']) ? $next['createtime'] : strtotime($next['date']);
            $prev['createtime'] = $nexttime;
            if ( $prevtime < $nexttime ) {
                return -1;
            }
            if ( $prevtime > $nexttime ) {
                return 1;
            }
            return 0;
        });
        return $data;
    }


    public static function addPost($source){
        if ( !is_file($source) ) {
            return false;
        }
        $contents = file_get_contents($source);
        if ( !$contents ) {
            return false;
        }
        global $configs;
        $post = self::parse($contents);

        $post['id'] = 0;
        $post['prev'] = '';
        $post['prev_id'] = '';
        $post['prev_title'] = '';
        
        $post['next'] = '';
        $post['next_id'] = '';
        $post['next_title'] = '';
        $post['link'] = str_replace (':_id',$post['_id'],$configs['link']['posts']);
        $post['source'] = $source;
        
        return $post;
    }

    public static function addCategory($post,&$_categories){
        $_id = $post['_id'];
        $title = $post['categories'];
        $_cid = self::key($title);

        if ( !isset($_categories[$_cid]) ) {
            $category = [
                '_id' => $_cid,
                'title' => $title,
                'posts' => [$_id]
            ];
            $_categories[$_cid] = $category;

            return $category;
        }

        $category = $_categories[$_cid];
        if( !in_array($_id,$category['posts']) ){
            $category['posts'][] = $_id;
            $_categories[$_cid] = $category;
        }

        return $category;
    }


    public static function addTags($post,&$_tags){
        $_id = $post['_id'];
        $data = explode(',',$post['tags']);
        $tags = [];
        foreach ($data as $title) {
            $_tid = self::key($title);
            if ( !isset($_tags[$_tid]) ) {
                $tag = [
                    '_id' => $_tid,
                    'title' => $title,
                    'posts' => [$_id]
                ];
                $_tags[$_tid] = $tag;

                $tags[] =  $tag;
                continue;
            }

            $tag = $_tags[$_tid];
            if( !in_array($_id,$tag['posts']) ){
                $tag['posts'][] = $_id;
                $_tags[$_tid] = $tag;
            }

            $tags[] =  $tag;
        }

        return $tags;
    }

    public static function parse($contents){
        $array = explode('---',$contents);
        $info = explode("\n",$array[1]);
        $post = [];
        foreach ($info as $val) {
            $val = trim($val);
            if (!$val || empty($val) ) {
                continue;
            }
            $temp = explode(': ',$val);
            if ( $temp[0] == 'tags' ) {
                $temp[1] = str_replace ('[','',$temp[1]);
                $temp[1] = str_replace (']','',$temp[1]);
            }
            $post[$temp[0]] = $temp[1];
        }
        $post['_id'] = self::key($post['title']);
        $post['content'] = $array[2];
        
        return $post;
    }

}