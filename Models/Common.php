<?php
namespace Models;
use ArrayAccess;
use Extend\Cache;
use Extend\Storage;
class Common implements ArrayAccess
{
    public $data;
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : '';
    }

    public function __set($name,$value)
    {
        $this->data[$name] = $value;
    }
    
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public static function pagination($link,$page,$num,$count){

        $pages = ceil( $count / $num );
        $content = [];
        $content['pages'] = $pages;
        $content['page'] = [];
        $content['current'] = 0;
        
        $content['prevTitle'] = '上一页';
        $content['prevLink'] = str_replace (':page',$page - 1,$link);

        $content['nextTitle'] = '下一页';
        $content['nextLink'] = str_replace (':page',$page + 1,$link);

        $content['firstTitle'] = '第一页';
        $content['firstLink'] = str_replace (':page',1,$link);

        $content['lastTitle'] = '最末页';
        $content['lastLink'] = str_replace (':page',$pages,$link);

        $content['prevClassName'] = '';
        $content['nextClassName'] = '';
        $content['lastClassName'] = '';
        
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

        $btnNum = 8;
        $nowPage = ceil($btnNum/2);

        for($i = 1; $i <= $btnNum; $i++){
            if(($page - $nowPage) <= 0 ){
                    $title = $i;
            }elseif(($page + $nowPage - 1) >= $pages){
                    $title = $pages - $btnNum + $i;
            }else{
                    $title = $page - $nowPage + $i;
            }
            if($title > 0 && $title != $page){
                if($title <= $pages){
                    $content['page'][] = ['title'=> $title ,"link"=> str_replace (':page',$title,$link) ,"className"=> ''];
                }else{
                    break;
                }
            }else{
                if($title > 0 && $pages != 1){
                    $content['current'] = $title;
                    $content['page'][] = ['title'=> $title ,"link"=> str_replace (':page',$title,$link) ,"className"=> 'am-active'];
                }
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

    public static function toc($data){
        $tree = [];
        $level = 0;
        $id = '';
        $child_id = '';
        $num1 = 1;
        $num2 = 1;
        $num3 = 1;
        foreach ($data as $key => $value) {
            
            if ( $level != 0 && $level < $value['level'] ) {
                if ( ( intval($value['level']) - 1 ) == $level ) {
                    $child_id = $value['id'];
                    $value['num'] = $num2;
                    $tree[$id]['child'][$child_id] = $value;
                    $num2++;
                    $num3 = 1;
                }elseif ( ( intval($value['level']) - 2 ) == $level ) {
                    $value['num'] = $num3;
                    $tree[$id]['child'][$child_id]['child'][] = $value;
                    $num3++;
                }
                
            }else{
                $id = $value['id'];
                $level = intval($value['level']);
                $value['num'] = $num1;
                $tree[$id] = $value;
                $tree[$id]['child'] = [];
                $num1++;
                $num2 = 1;
                $num3 = 1;
            }
        }
        return $tree;
    }

    public static function count(){
        
    }

}