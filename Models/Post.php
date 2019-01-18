<?php
namespace Models;
class Post extends Common
{

    public function __construct($_id)
    {
        $data = self::data($_id);
        $this->data = $data;
    }

    public static function select($page,$num=10){
        $data = self::data();
        $count = count($data);
        $num = $count < $num ? $count : $num;
        $end = $page * $num;

        $pagination = self::pagination($page,$num,$count);

        return ['data' => array_slice ( $data , $end - $num , $num ) , 'count' => $count ,'pagination' => $pagination];
    }


    public static function pagination($page,$num,$count){

        $pages = ceil( $count / $num );

        $content = [];
        $content['prevTitle'] = '上一页';
        $content['prevLink'] = '/admin/posts?page=' . $page + 1;

        $content['nextTitle'] = '下一页';
        $content['nextLink'] = '/admin/posts?page='. $page - 1;

        $content['firstTitle'] = '第一页';
        $content['firstLink'] = '/admin/posts?page=1';

        $content['lastTitle'] = '最末页';
        $content['lastLink'] = '/admin/posts?page=' . $pages;

        if ( $page <= 1 ) {
            $content['prevClassName'] = 'am-disabled';
            $content['prevLink'] = '';

            $content['firstClassName'] = 'am-disabled';
            $content['firstLink'] = '';
        }

        if ( $page == $pages ) {
            $content['nextClassName'] = 'am-disabled';
            $content['nextLink'] = '';

            $content['lastClassName'] = 'am-disabled';
            $content['lastLink'] = '';

        }

        for ( $i=2; $i >= 0; $i-- ) { 
            $title = $page - $i;
            if ( $title >= 1) {
                $className = '';
                if ( $title == $page ) {
                    $className = 'am-active';
                }
                $content['page'][] = ['title'=> $title ,"link"=> '/admin/posts?page=' . $title ,"className"=> $className];
            }
        }

        for ( $i=1; $i < 4; $i++) { 
            $title = $page + $i;
            if ( $title <= $pages) {
                $className = '';
                if ( $title == $page ) {
                    $className = 'am-active';
                }
                $content['page'][] = ['title'=> $title ,"link"=> '/admin/posts?page=' . $title ,"className"=> $className];
            }
        }

        $paginationData = [
            'className' => '',
            'theme' => 'default',
            'options' => [
                'select' => ''
            ],
            'content' => $content
        ];
        return $paginationData;
    }

    public static function save($data,$_id = ''){

        if ( !isset( $data['filename'] ) || empty($data['filename']) ) {
            return false;
        }

        global $configs;
        $md = "---\n";
        $md .= "title: " . $data['title'] . "\n";
        $md .= "date: " . $data['date'] . "\n";
        $md .= "tags: [" . $data['tags'] . "]\n";
        $md .= "categories: " . $data['categories'] . "\n";
        $md .= "---\n";
        $md .= $data['content'];

        $file = DS . "_posts" . DS . $data['filename'] . '.md';
        $source = $configs['dir']['source'] . $file;

        if ( is_file(DB_FILE) ) {
            $db_file = file_get_contents(DB_FILE);
            $db = json_decode($db_file,true);
            $md5 = md5($data['filename']);
            if ( !empty($_id) && isset($db[$_id]) && $_id !== $md5 ) {
                $data['id'] = $db[$_id]['id'];
                self::del($db[$_id]);
                unset($db[$_id]);
            }
            $data['_id'] = $md5;
            $db[$md5] = $data;    
            array_multisort(array_column($db,'id'),SORT_DESC,$db);
            file_put_contents ( DB_FILE ,  json_encode($db) );
        }

        return file_put_contents ( $source ,  $md ) === false ? false : true ;
    }

    public function del($data){
        global $configs;
        $file = DS . "_posts" . DS . $data['filename'] . '.md';
        $source = $configs['dir']['source'] . $file;
        if ( is_file($source) ) {
            unlink($source);
        }
    }

    public static function update($_id=''){
        global $configs;
        $source = $configs['dir']['source'] . DS . "_posts" . DS;
        $files = glob ( $source . "*.md" );
        $db = [];
        foreach ($files as $key => $value) {
            $file = file_get_contents($value);
            if ( !$file ) {
                continue;
            }
            $filename = str_replace($source,'',$value);
            $filename = str_replace('.md','',$filename);

            $_id = md5($filename);
            $arr = explode('---',$file);
            $info = explode("\n",$arr[1]);
            $detail = [];
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
                $db[$_id][$temp[0]] = $temp[1];
            }
            $db[$_id]['_id'] = $_id;
            $db[$_id]['id'] = $key + 1;
            $db[$_id]['content'] = $arr[2];
            $db[$_id]['filename'] = $filename;
        }
        array_multisort(array_column($db,'id'),SORT_DESC,$db);
        file_put_contents ( DB_FILE ,  json_encode($db) );
        return $db;
    }

    public static function data($_id = ''){
        if ( is_file(DB_FILE) ) {
            $db_file = file_get_contents(DB_FILE);
            if ( $db_file ) {
                if ( empty($_id) ) {
                    return json_decode($db_file,true);
                }
                $db = json_decode($db_file,true);
                return isset($db[$_id]) ? $db[$_id] : false;
            }
        }
        if ( empty($_id) ) {
            return self::update();
        }
        $db = self::update();
        return isset($db[$_id]) ? $db[$_id] : false;
    }

}