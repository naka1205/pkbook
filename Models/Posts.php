<?php
namespace Models;
class Posts extends Common
{

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

    public static function get($id){

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
            $_id = md5($value);
            $arr = explode('---',$file);
            $info = explode("\n",$arr[1]);
            foreach ($info as $k => $v) {
                $v = trim($v);
                if (!$v || empty($v) ) {
                    continue;
                }
                $temp = explode(': ',$v);
                $data[$_id][$temp[0]] = $temp[1];
            }
            $data[$_id]['content'] = $arr[2];
        }
        file_put_contents ( DB_FILE ,  json_encode($data) );
        return $data;
    }

    public static function data(){
        if ( is_file(DB_FILE) ) {
            $db_file = file_get_contents(DB_FILE);
            if ( $db_file ) {
                return json_decode($db_file,true);
            }
        }
        return Posts::update();
    }

}