<?php
namespace Models;
use Extend\Source;
class Single extends Common
{

    public function __construct($_id)
    {
        $data = Source::singles();
        $this->data = isset($data[$_id]) ? $data[$_id] : [];
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

    public static function select($page=0,$num=10,$link =''){
        $data = Source::singles();
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