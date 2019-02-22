<?php
namespace Extend;

class Github
{
    protected static $host = 'https://api.github.com';
    protected static $handler = null;

    public static function init($token = ''){
        $config = Config::get('github');

        if ( self::$handler == null ) {
            self::$handler = new GCurl;
            self::$handler->url = self::$host . "/repos/" . $config['owner'] . "/" . $config['repo'] . "/issues";
        }
        self::$handler->token = $token;
        return self::$handler;
    }

    public static function create(array $body = [] )
    {

        $issues = self::find($body['title']);
        if ( $issues ) {
            return $issues;
        }

        $config = Config::get('github');
        $content = self::init($config['token'])->post($body);
        $value = json_decode($content,true);

        $createtime = strtotime($value['created_at']) + ( 8 * 3600 );
        $updatetime = strtotime($value['updated_at']) + ( 8 * 3600 );
        $issues = [
            'id' => $value['id'],
            'node_id' => $value['node_id'],
            'number' => $value['number'],
            'title' => $value['title'],
            'comments' => $value['comments'],
            'date' => date('Y-m-d H:i:s',$createtime),
            'state' => $value['state'],
            'locked' => $value['locked'],
            'createtime' => $createtime,
            'updatetime' => $updatetime
        ];

        $data = [];
        if ( Cache::has('issues') ){
            $data = Cache::get('issues');
        }
        $data[$issues['title']] = $issues;
        Cache::set('issues',$data);

        return $issues;
    }

    public static function find( $title = '' )
    {

        if ( !Cache::has('issues') ) {
            $content = self::init()->get() ;

            $data = [];
            if ( $content ) {
                $data = json_decode($content,true);
            }
            $issues = [];
            foreach ($data as $key => $value) {
                $createtime = strtotime($value['created_at']) + ( 8 * 3600 );
                $updatetime = strtotime($value['updated_at']) + ( 8 * 3600 );

                $issues[$value['title']] = [
                    'id' => $value['id'],
                    'node_id' => $value['node_id'],
                    'number' => $value['number'],
                    'title' => $value['title'],
                    'comments' => $value['comments'],
                    'date' => date('Y-m-d H:i:s',$createtime),
                    'state' => $value['state'],
                    'locked' => $value['locked'],
                    'createtime' => $createtime,
                    'updatetime' => $updatetime
                ];
                
            }
            Cache::set('issues',$issues);  
        }else{
            $issues = Cache::get('issues');
        }

        if ( !empty( $title ) ) {
            return isset($issues[$title]) ? $issues[$title] : false;
        }
        return $issues;
    }

    // public static function update( int $number , array $body = []  )
    // {
    //     //https://api.github.com/repos/用户名/仓库名/issues/序号
    //     // {
    //     // "title": "Creating issue from API ---updated",
    //     //  body": "Posting a issue from Insomnia \n\n Updated from insomnia.",
    //     //  "state": "open"
    //     // }
    //     //注意：如果JSON中加入空白的labels或assignees，如"labels": []，作用就是清空所有的标签和相关人。
        
    //     self::$handler->url .= "/" . $number;
    //     $config = Config::get('github');
    //     $content = self::init($config['token'])->post($body);
    //     $value = json_decode($content,true);

    //     $api = self::$host . "/repos/用户名/仓库名/issues";
    //     return self::post($api,$options);
    // }

    // public static function lock( $title  )
    // {
    //     //https://api.github.com/repos/用户名/仓库名/issues/序号/lock
    //     // {
    //     // "locked": true,
    //     // "active_lock_reason": "too heated"
    //     // }
    //     $api = self::$host . "/repos/用户名/仓库名/issues";
    //     return self::post($api,$options);
    // }

}
