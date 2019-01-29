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
        return Cache::has($arguments[0]) ? Cache::get($arguments[0]) : Source::find($method,$arguments[0]);
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



    public static function save($data){
        global $configs;
        $data['createtime'] = isset( $data['createtime'] ) ? $data['createtime'] : time();

        $md = "---\n";
        $md .= "title: " . $data['title'] . "\n";
        $md .= "date: " . $data['date'] . "\n";

        $source = '';
        if ( isset($data['categories']) ) {
            if ( isset($data['tags'] ) ) {
                $md .= "tags: [" . $data['tags'] . "]\n";
            }
            $md .= "categories: " . $data['categories'] . "\n";
            $md .= "createtime: " . $data['createtime'] . "\n";

            $filename = self::key($data['title']);
            $source = $configs['dir']['source'] . DS . "_posts" . DS . $filename . '.md';
        }

        $md .= "---\n";
        $md .= trim($data['content']);
        
        if ( empty($source) ) {
            $source = $configs['dir']['source'] . DS . $data['name'];
            if ( !is_dir($source) ) {
                mkdir($source, 0755, true);
            }
            $source = $source . DS . 'index.md';
        }
        
        if ( !file_put_contents ( $source ,  $md ) ) {
            return false;
        }
        
        return true;
    }


    public static function update($name = ''){
        global $configs;

        if ( $name == 'singles' ) {
            $sources  =  scandir ( $configs['dir']['source'] );
            $_singles = [];
            foreach ($sources as $key => $value) {
                if ( strpos($value,'.') !== false || strpos($value,'_') !== false ) {
                    continue;
                }
                $index = $configs['dir']['source'] . DS . $value . DS . 'index.md';
                $single = self::parsePage($index);
                Cache::set($single['_id'],$single);
                unset($single['content']);
                $_singles[$single['_id']] = $single;
            }

            Cache::set('singles',$_singles);
            return $_singles;
        }

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
            $post['prev_link'] = isset($data[$prev_id]) ? $data[$prev_id]['link'] : '';

            $post['next_id'] = isset($data[$next_id]) ? $data[$next_id]['_id'] : '';
            $post['next_title'] = isset($data[$next_id]) ? $data[$next_id]['title'] : '';
            $post['next_link'] = isset($data[$next_id]) ? $data[$next_id]['link'] : '';
            
            if ( isset($post['categories']) ) {
                self::parseCategory($post,$_categories);
            }
            $tags = '';
            if ( isset($post['tags']) ) {
                $tags = $post['tags'];
                $post['tags'] = self::parseTags($post,$_tags);
            }

            Cache::set($_id,$post);

            unset($post['content']);
            $post['tags'] = $tags;
            $_posts[$_id] = $post;
        }

        Cache::set('posts',$_posts);
        Cache::set('tags',$_tags);
        Cache::set('categories',$_categories);

        $name = empty($name) ?  "_posts" : '_' . $name;
        return $$name;
    }

    public static function find($name,$_id){
        global $configs;

        if ( $name == 'singles' ) {
            $sources  =  scandir ( $configs['dir']['source'] );
            $_singles = Cache::get('singles');
            $single = [];
            foreach ($sources as $key => $value) {
                if ( strpos($value,'.') !== false || strpos($value,'_') !== false ||  self::key($value) != $_id ) {
                    continue;
                }
                $index = $configs['dir']['source'] . DS . $value . DS . 'index.md';
                $single = self::parsePage($index);
                Cache::set($single['_id'],$single);
                unset($single['content']);
                $_singles[$single['_id']] = $single;
            }

            Cache::set('singles',$_singles);
            return $single;
        }


        $source = $configs['dir']['source'] . DS . "_posts" . DS . $_id . '.md';
        
        $post = self::parsePost($source);
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

        Cache::set('posts',$_posts);

        return $post;
    }

    private static function glob($source){
        
        $data = glob ( $source . "*.md" );

        usort($data, function(&$prev, &$next) {
            if ( !is_array($prev) ) {
                $prev = self::parsePost($prev);
            }
            if ( !is_array($next) ) {
                $next = self::parsePost($next);
            }

            $prevtime = isset($prev['createtime']) ? $prev['createtime'] : strtotime($prev['date']);
            $prev['createtime'] = $prevtime;
            $nexttime = isset($next['createtime']) ? $next['createtime'] : strtotime($next['date']);
            $next['createtime'] = $nexttime;
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


    private static function parsePost($source){
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

    private static function parseCategory($post,&$_categories){
        global $configs;

        $_id = $post['_id'];
        $title = $post['categories'];
        $_cid = self::key($title);

        if ( !isset($_categories[$_cid]) ) {
            $category = [
                '_id' => $_cid,
                'link' => str_replace (':_id',$_cid,$configs['link']['category']),
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


    private static function parseTags($post,&$_tags){

        global $configs;
        
        $_id = $post['_id'];
        $data = explode(',',$post['tags']);
        $tags = [];
        foreach ($data as $title) {
            $_tid = self::key($title);
            if ( !isset($_tags[$_tid]) ) {
                $tag = [
                    '_id' => $_tid,
                    'link' => str_replace (':_id',$_tid,$configs['link']['tags']),
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

    private static function parsePage($source){
        if ( !is_file($source) ) {
            return false;
        }
        $contents = file_get_contents($source);
        if ( !$contents ) {
            return false;
        }
        global $configs;
        $data = self::parse($contents);
        $name = basename(dirname($source));
        $data['id'] = 0;
        $data['_id'] = self::key($name);
        $data['name'] = $name;
        $data['link'] = str_replace (':_id',$name,$configs['link']['page']);
        $data['source'] = $source;
        return $data;
    }
    
    private static function parse($contents){
        $array = explode('---',$contents);
        $info = explode("\n",$array[1]);
        $data = [];
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
            $data[$temp[0]] = $temp[1];
        }
        $data['_id'] = self::key($data['title']);
        $data['content'] = $array[2];
        
        return $data;
    }
}