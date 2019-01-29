<?php
namespace Models;
use Extend\Source;
class Tag extends Common
{

    public function __construct($_id)
    {
        $data = Source::tags();
        $this->data = isset($data[$_id]) ? $data[$_id] : [];
    }

    public static function select($where,$page,$num=10,$link =''){
        $data = Source::tags();
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