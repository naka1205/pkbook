<?php
return [
    'admin' => [
        'email'         => "171453643@qq.com",
        'password'      => "123456",
        'token'         => "",
        'pagenum'       => 10
    ],
    'link' => [
        // 'domain'        =>  "http://www.kmei.org",
        'domain'        =>  "",
        'suffix'        =>  ".html",
        'page'          =>  "/:_id",
        'posts'         =>  "/posts/:_id",
        'tags'          =>  "/tag/:_id",
        'category'      =>  "/category/:_id"
    ],
    'site' => [
        'title'         => "PKBOOK",
        'subtitle'      => "个人博客",
        'description'   => "开源WEB开发框架",
        'keywords'      => "开源WEB开发框架",
        'author'        => "naka1205",
        'year'          => "2019",
        'holder'        => "Naka1205",
        'icp'           => "",
        'logo'          => "",
        'theme'         => "next",
        'pagenum'       => 10
    ],
    'qiniu' => [
        'domain'        =>  "",
        'upload'        =>  "",
        'token'         =>  "",
        'bucket'        =>  "",
        'access'        =>  "",
        'secret'        =>  ""
    ],
];