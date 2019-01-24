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
        return Cache::has($arguments[0]) ? Cache::get($arguments[0]) : false;
    }

    public function key($name){
        return md5($name);
    }

    public function del($data){
        global $configs;
        $filename = $data['filename'] . '.md';
        $source = $configs['dir']['source'] . DS . "_posts" . DS . $filename;
        if ( is_file($source) ) {
            unlink($source);
        }
        $_id = self::key($filename);
        if ( Cache::has($_id) ) {
            Cache::remove($_id);
        }

        $_posts = Cache::get('_posts');
        $_cid = '';
        $_tids = [];
        if ( isset($_posts[$_id]) ) {
            $_cid = self::key($_posts[$_id]['categories']);
            $tags = explode(',',$_posts[$_id]['tags']);
            foreach ($tags as $key => $value) {
                $_tids[] = self::key($value);
            }
            unset($_posts[$_id]);
            Cache::set('_posts',$_posts);
        }

        $_categories = Cache::get('_categories');
        if ( !empty($_cid) && isset($_categories[$_cid]) && in_array($_id,$_categories[$_cid]['posts']) ) {
            $key = array_search($_id,$_categories[$_cid]['posts']);
            unset($_categories[$_cid]['posts'][$key]);
            Cache::set('_categories',$_categories);
        }

        $_tags = Cache::get('_tags');
        if ( !empty($_tids) && !empty($_tags) ) {
            foreach ($_tids as $k => $_tid) {
                if ( isset($_tags[$_tid]) && in_array($_id,$_tags[$_tid]['posts']) ) {
                    $key = array_search($_id,$_tags[$_tid]['posts']);
                    unset($_tags[$_tid]['posts'][$key]);
                }
            }
            Cache::set('_tags',$_tags);
        }

    }

    public function save($data){
        if ( !isset( $data['filename'] ) || empty($data['filename']) ) {
            return false;
        }
        global $configs;
        $data['content'] = urldecode($data['content']);
        $md = "---\n";
        $md .= "title: " . $data['title'] . "\n";
        $md .= "date: " . $data['date'] . "\n";
        $md .= "tags: [" . $data['tags'] . "]\n";
        $md .= "categories: " . $data['categories'] . "\n";
        $md .= "createtime: " . $data['createtime'] . "\n";
        $md .= "---\n";
        $md .= $data['content'];

        $filename = $data['filename'] . '.md';
        $source = $configs['dir']['source'] . DS . "_posts" . DS . $filename;

        return file_put_contents ( $source ,  $md ) === false ? false : true ;
    }

    public function add($source,$id = 0){
        global $configs;
        $contents = file_get_contents($source);
        if ( !$contents ) {
            return false;
        }
        $filename = basename($source);

        $_id = self::key($filename);

        $arr = explode('---',$contents);
        $info = explode("\n",$arr[1]);
        $post = [];
        $post['_id'] = $_id;
        $post['filename'] = str_replace ('.md','',$filename);
        $post['id'] = $id;
        foreach ($info as $k => $v) {
            $v = trim($v);
            if (!$v || empty($v) ) {
                continue;
            }
            $temp = explode(': ',$v);
            if ( $temp[0] == 'tags' ) {
                $temp[1] = str_replace ('[','',$temp[1]);
                $temp[1] = str_replace (']','',$temp[1]);
            }
            $post[$temp[0]] = $temp[1];
        }
        $post['content'] = $arr[2];
        $post['prev'] = '';
        $post['next'] = '';
        $post['link'] = str_replace (':_id',$_id,$configs['link']['posts']);
        if ( !isset($post['createtime'])) {
            $post['createtime'] = time();
        }
        return $post;
    }

    public static function prev($_id=''){
        if ( $_id == '') {
            return false;
        }

        $data = Cache::has('_posts') ? Cache::get('_posts') : Source::update();

        return $files;
    }    


    public static function globs(){
        global $configs;
        $source = $configs['dir']['source'] . DS . "_posts" . DS;
        $files = glob ( $source . "*.md" );

        usort($files, function($prev, $next) {
            $prevtime = $prev['createtime'];
            $nexttime = $next['createtime'];
            if ( $prevtime < $nexttime ) {
                return -1;
            }
            if ( $prevtime > $nexttime ) {
                return 1;
            }
            return 0;
        });

        return $files;
    }
    
    
    public static function update($name = ''){
        $files = slef::globs();

        $_tags = [];
        $_posts = [];
        $_categories = [];
        $prev = '';    
        $next = '';    
        foreach ($files as $key => $value) {
            
            $id = $key + 1; 
            $post = self::add($value,$id);

            Cache::set($_id,$post);
            unset($post['content']);

            $_id = $post['_id'];
            $_posts[$_id] = $post;

            if ( isset($post['categories']) ) {
                $_cid = self::key($post['categories']);
                if ( !isset($_categories[$_cid]) ) {
                    $cate = [
                        '_id' => $_cid,
                        'title' => $post['categories'],
                        'posts' => [$_id]
                    ];
                    $_categories[$_cid] = $cate;
                }elseif( !in_array($_id,$_categories[$_cid]['posts']) ){
                    $_categories[$_cid]['posts'][] = $_id;
                }
            }

            if ( isset($post['tags']) ) {
                $tag = explode(',',$post['tags']);
                foreach ($tag as $vo) {
                    $_tid = self::key($vo);
                    if ( !isset($_tags[$_tid]) ) {
                        $t = [
                            '_id' => $_tid,
                            'title' => $vo,
                            'posts' => [$_id]
                        ];
                        $_tags[$_tid] = $t;
                    }elseif( !in_array($_id,$_tags[$_tid]['posts']) ){
                        $_tags[$_tid]['posts'][] = $_id;
                    }
                }
            }
        }
        array_multisort(array_column($_posts,'id'),SORT_DESC,$_posts);
        Cache::set('_posts',$_posts);
        Cache::set('_categories',$_categories);
        Cache::set('_tags',$_tags);

        if ( !empty($name) ) {
            $name = "_" . $name;
            return $$name;
        }
        return $_posts;
    }



}
