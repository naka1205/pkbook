<?php
namespace Models;
class Posts extends Common
{

    public function __construct($_id)
    {
        $data = Posts::data($_id);
        $this->data = $data;
    }

    public static function select($page,$num=10){
        $data = Posts::data();
        $count = count($data);
        $num = $count < $num ? $count : $num;
        $end = $page * $num;

        $pagination = Posts::pagination($page,$num,$count);

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

        $md = "---\n";
        $md .= "title: " . $data['title'] . "\n";
        $md .= "date: " . $data['date'] . "\n";
        $md .= "tags: [" . $data['tags'] . "]\n";
        $md .= "categories: " . $data['categories'] . "\n";
        $md = "---\n";
        $md = $data['content'];

        $file = DS . "_posts" . DS . $data['filename'] . '.md';
        $source = $configs['dir']['source'] . $file;
        return file_put_contents ( $source ,  $md ) === false ? false : true ;
    }

    public static function update(){
        global $configs;
        $source = $configs['dir']['source'] . DS . "_posts" . DS;
        $files = glob ( $source . "*.md" );
        $data = [];
        foreach ($files as $key => $value) {
            $file = file_get_contents($value);
            if ( !$file ) {
                continue;
            }
            $_id = md5(str_replace($configs['dir']['source'],'',$value));
            $arr = explode('---',$file);
            $info = explode("\n",$arr[1]);
            $detail = [];
            foreach ($info as $k => $v) {
                $v = trim($v);
                if (!$v || empty($v) ) {
                    continue;
                }
                $temp = explode(': ',$v);
                $data[$_id][$temp[0]] = $temp[1];
            }
            $data[$_id]['_id'] = $_id;
            $data[$_id]['id'] = $key + 1;
            $data[$_id]['content'] = $arr[2];
        }
        file_put_contents ( DB_FILE ,  json_encode($data) );
        return $data;
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
            return Posts::update();
        }
        $db = Posts::update();
        return isset($db[$_id]) ? $db[$_id] : false;
    }

}