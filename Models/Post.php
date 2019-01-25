<?php
namespace Models;
use Extend\Source;
use Extend\Parsedown;
class Post extends Common
{

    public function __construct($_id='')
    {
        if ( !empty($_id) ) {
            $data = Source::posts($_id);
            $this->data = $data;
        }
    }

    public static function add($data){
        return Source::save($data);
    }

    public function save($data = []){
        if ( empty($data) ) {
            $data = $this->data;
        }
        if ( !isset($data['createtime']) ) {
            $data['createtime'] = time();
        }
        return Source::save($data);
    }

    public function getHtml()
    {
        $parsedown = new Parsedown();
        return $parsedown->text($this->data['content']);
    }

    public function setHtml($content)
    {
        $parsedown = new Parsedown();
        $this->data['html'] = $parsedown->text($content);
    }

    public function offsetGet($offset)
    {
        if ( $offset == 'html' ) {
            $parsedown = new Parsedown();
            return $parsedown->text($this->data['content']);
        }
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if ( $offset == 'html' ) {
            $parsedown = new Parsedown();
            $this->data[$offset] = $parsedown->text($content);
        }else{
            $this->data[$offset] = $value;
        }
        
    }

    public static function select($where,$page=0,$num=10,$link =''){
        $data = Source::posts($where);
        if ( $page <= 0 ) {
            return $data;
        }
        $count = count($data);
        $num = $count < $num ? $count : $num;
        $end = $page * $num;

        $pagination = [];
        if ( $link ) {
            $pagination = self::pagination($link,$page,$num,$count);
        }

        return ['data' => array_slice ( $data , $end - $num , $num ) , 'count' => $count ,'pagination' => $pagination];
    }


    public static function publish(){
        
    }

}