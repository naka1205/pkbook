<?php
namespace Models;
class Common
{
    public static function pagination($page,$num,$count){
        $paginationData = [
            'className' => '',
            'theme' => 'default',
            'options' => [
                'select' => ''
            ],
            'content' => [
                'prevTitle' => '上一页',
                'prevLink' => '#',
                'firstTitle' => '第一页',
                'firstLink' => '#',
                'nextTitle' => '下一页',
                'nextLink' => '#',
                'lastTitle' => '最末页',
                'lastLink' => '#',
                'total' => '15',
                'page' => [
                    ['title'=> '1',"link"=>  "#","className"=> ""],
                    ['title'=> '2',"link"=>  "#","className"=> ""],
                    ['title'=> '3',"link"=>  "#","className"=> ""]
                ]
            ],
        ];
        return $paginationData;
    }

}