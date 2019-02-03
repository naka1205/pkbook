<?php
namespace Models;
use Extend\Source;
class Category extends Common
{

    public function __construct($_id)
    {
        $this->data = !empty($_id) ? Source::categories($_id) : [];
    }

    public function posts(){
        if ($this->posts) {
            return Source::posts(['_id'=>$this->posts]);
        }
        return [];
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