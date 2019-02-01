<?php
namespace Models;
use Extend\Config;
class User
{
    public static function check($email,$password){
        if ( !$email || !$password) {
            return false;
        }
        $admin = Config::get('admin');
        if ($admin['email'] == $email && $admin['password'] == $password) {
            $admin['token'] = md5( $email . time() );
            Config::set('admin',$admin);
            return true;
        }
        return false;
    }

    public static function token(){
        $admin = Config::get('admin');
        return $admin['token'];
    }

}