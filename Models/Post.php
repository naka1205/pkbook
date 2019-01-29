<?php
namespace Models;
use Extend\Source;

class Post extends Common
{

    public function __construct($_id='')
    {
        $this->data = !empty($_id) ? Source::posts($_id) : [];
    }

    public function save($data = []){
        if ( empty($data) ) {
            $data = $this->data;
        }
        return Source::save($data);
    }


    public static function add($data){
        if ( empty($data) ) {
            return false;
        }
        return Source::save($data);
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

}