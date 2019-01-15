<?php
namespace Controllers;
use Naka507\Koa\Context;
use Models\User;
class Api
{

    public static function login(Context $ctx, $next){
        $post = $ctx->request->post;
        $email = isset( $post['email'] ) ? $post['email']  : '';
        $password = isset( $post['password'] ) ? $post['password']  : '';

        $result = ( yield User::check($email,$password) );

        $token = '';
        if ( $result ) {
            $token = ( yield User::token() );
            $ctx->setSession('admin',$token);
            $ctx->setCookie('token',$token);
        }

        $ctx->status = 200;
        $ctx->body = $token;
    }    


}