<?php
namespace Models;
use Extend\Source;
use Extend\Parsedown;
class Post extends Common
{

    public function __construct($_id)
    {
        $data = Source::posts($_id);
        $this->data = $data;
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

    public static function select($where,$page,$num=10,$link =''){
        $data = Source::posts($where);
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