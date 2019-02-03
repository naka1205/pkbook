<?php
namespace Extend;
use Exception;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Upload
{
    public static function qiniu(){
        $qiniu = Config::get('qiniu');
        $auth = new Auth($qiniu['access'] ,$qiniu['secret']);
        $qiniu['token'] = $auth->uploadToken( $qiniu['bucket'] );
        return $qiniu;
    }

    public static function upload($filePath){
        $token = self::token();
        $manager = new UploadManager();
        list($ret, $err) = $manager->putFile($token, null, $filePath);
        if ($err !== null) {
            // var_dump($err);
            return $err;
        } else {
            // var_dump($ret);
            return $ret;
        }
    }
}