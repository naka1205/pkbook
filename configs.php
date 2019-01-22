<?php
return [
    'admin' => [
        'email'         => "171453643@qq.com",
        'password'      => "123456",
        'token'         => ""
    ],
    'dir' => [
        'source'        =>  __DIR__ . DS . "source",
        'public'        =>  __DIR__ . DS . "public",
        'archives'      =>  __DIR__ . DS . "archives",
        'categories'    =>  __DIR__ . DS . "categories",
        'tags'          =>  __DIR__ . DS . "tags",
    ],
    'url' => [
        'permalink'     =>  ":category/:id.html"
    ],
    'site' => [
        'title'         => "pkbook",
        'subtitle'      => "pkbook",
        'description'   => "pkbook",
        'author'        => "pkbook",
        'theme'         => "default"
    ],
    'write' => [
        'new_post_name'  => ":title.md"
    ],

];