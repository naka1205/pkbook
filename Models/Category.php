<?php
namespace Models;
use Extend\Source;
class Category extends Common
{

    public function __construct($_id)
    {
        $data = Source::categories();
        $this->data = isset($data[$_id]) ? $data[$_id] : [];
    }

    public static function find($_id = []){
        $_categories = Source::categories();
        if ( empty( $_id )) {
            return $_categories;
        }
        $data = [];
        foreach ($_id as $key => $value) {
            if ( isset( $_categories[$value] )) {
                $data[] = $_categories[$value];
            }
        }
        return $data;
    }

    public static function select($where,$page=0,$num=10,$link =''){
        $data = Source::categories($where);
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