<?php
namespace Models;
class User
{
    public static function check($email,$password){
        global $configs;
        if ( !$email || !$password) {
            return false;
        }
        if ($configs['admin']['email'] == $email && $configs['admin']['password'] == $password) {
            $configs['admin']['token'] = md5( $email . time() );
            return true;
        }
        return false;
    }

    public static function token(){
        global $configs;
        return $configs['admin']['token'];
    }

}