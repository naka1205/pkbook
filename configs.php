<?php
return [
    'admin' => [
        'email'     => "171453643@qq.com",
        'password'  => "123456",
        'token'     => ""
    ],
    'dir' => [
        'source'      =>  __DIR__ . DS . "source",
        'public'      =>  __DIR__ . DS . "public",
        'archives'      =>  __DIR__ . DS . "archives",
        'categories'      =>  __DIR__ . DS . "categories",
        'tags'      =>  __DIR__ . DS . "tags",
    ],
    'url' => [
        'permalink' =>  ":category/:id.html"
    ],
    'site' => [
        'title'     => "source",
        'subtitle'     => "source",
        'description'     => "source",
        'author'     => "source",
        'language'     => "source",
        'timezone'     => "source",
        'theme' =>  "next"
    ],
    'write' => [
        'new_post_name'      => ":title.md",
        'default_layout'     => "post",
        'highlight'         =>  false
    ],

];