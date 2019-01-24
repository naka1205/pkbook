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

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public static function pagination($link,$page,$num,$count){

        $pages = ceil( $count / $num );
        $content = [];
        $content['prevTitle'] = '上一页';
        $content['prevLink'] = $link . '?page=' . $page + 1;

        $content['nextTitle'] = '下一页';
        $content['nextLink'] = $link . '?page='. $page - 1;

        $content['firstTitle'] = '第一页';
        $content['firstLink'] = $link . '?page=1';

        $content['lastTitle'] = '最末页';
        $content['lastLink'] = $link . '?page=' . $pages;

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
                $content['page'][] = ['title'=> $title ,"link"=> $link . '?page=' . $title ,"className"=> $className];
            }
        }

        for ( $i=1; $i < 4; $i++) { 
            $title = $page + $i;
            if ( $title <= $pages) {
                $className = '';
                if ( $title == $page ) {
                    $className = 'am-active';
                }
                $content['page'][] = ['title'=> $title ,"link"=> $link . '?page=' . $title ,"className"=> $className];
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

}