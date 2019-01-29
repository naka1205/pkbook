<?php
namespace Controllers;
use Naka507\Koa\Context;

use Models\Post;
use Models\Single;
use Models\Category;
use Models\Tag;

class Show
{

    public static $theme;

    public static function base(Context $ctx, $next, $vars){
        global $configs;
        self::$theme = $configs['site']['theme'];

        $categories = Category::select([]);
        $ctx->state['categories'] = $categories;

        $singles = Single::select([]);
        $ctx->state['singles'] = $singles;

        $ctx->state['link'] = '/show';
    }

    public static function index(Context $ctx, $next){
        $ctx->status = 200;
        yield $ctx->show("index");
    } 

    public static function posts(Context $ctx, $next, $vars){
        $post = new Post($vars[0]);
        $ctx->status = 200;
        $ctx->state['post'] = $post;
        yield $ctx->show("posts");
    } 

    public static function category(Context $ctx, $next, $vars){
        $category = new Category($vars[0]);
        $ctx->status = 200;
        $ctx->state['category'] = $category;
        yield $ctx->show("category");
    } 

    public static function tags(Context $ctx, $next, $vars){
        $tag = new Tag($vars[0]);
        $ctx->status = 200;
        $ctx->state['tag'] = $tag;
        yield $ctx->show("tags");
    }

    public static function single(Context $ctx, $next, $vars){
        $single = new Single('1a1e2cad476532005aa34efb433eaa97');
        $ctx->status = 200;
        $ctx->state['single'] = $single;
        yield $ctx->show("single");
    } 
}