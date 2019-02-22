<?php
namespace Models;
use Extend\Source;
class Tag extends Common
{

    public function __construct($_id)
    {
        $this->data = !empty($_id) ? Source::tags($_id) : false;
    }

    public static function count($where=[]){
        $data = Source::tags($where);
        return $data ? count($data) : 0;
    }

    public static function select($where,$page=0,$num=10,$link =''){
        $data = Source::tags($where);
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