<?php
return [
    'admin' => [
        'email'         => "171453643@qq.com",
        'password'      => "123456",
        'token'         => ""
    ],
    'view' => [
        'view_suffix'   =>	'html',
        'view_path'	    =>	__DIR__ . DS . "views",
	    'cache_path'	=>	__DIR__ . DS . "runtime" . DS . 'views'
    ],
    'dir' => [
        'source'        =>  __DIR__ . DS . "source",
        'public'        =>  __DIR__ . DS . "public",
        'archives'      =>  __DIR__ . DS . "archives",
        'categories'    =>  __DIR__ . DS . "categories",
        'tags'          =>  __DIR__ . DS . "tags",
    ],
    'link' => [
        'page'         =>  "/:_id",
        'posts'         =>  "/posts/:_id",
        'category'      =>  "/category/:_id"
    ],
    'site' => [
        'title'         => "pkbook",
        'subtitle'      => "pkbook",
        'description'   => "pkbook",
        'author'        => "pkbook",
        'theme'         => "default"
    ],
    'write' => [
        'cache_suffix'   => 'php',
        'cache_path'     => __DIR__ . DS . "runtime" . DS . 'cache',
        'new_post_name'  => ":title.md"
    ],
    'publish' => [
        'suffix'        => 'html',
        'path'          => __DIR__ . DS . "public"
    ]

];