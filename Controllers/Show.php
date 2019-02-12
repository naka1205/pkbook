<?php
namespace Controllers;
use Naka507\Koa\Context;

use Models\Post;
use Models\Single;
use Models\Category;
use Models\Tag;

use Extend\Config;
use Extend\Publish;

class Show
{
    public static $pagenum = 10;
    public static function base(Context $ctx, $next, $vars){

        $configs = Config::all();
        $ctx->state['site'] = $configs['site'];

        $link = [
            'domain'        =>  "/show",
            'suffix'        =>  ".html"
        ];
        $ctx->state['link'] = $link;

        $categories = Category::select([]);
        $ctx->state['categories'] = $categories;

        $singles = Single::select([]);
        $ctx->state['singles'] = $singles;

    }

    public static function index(Context $ctx, $next){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;
        $where = [];
        $link = '/show/index?page=:page';
        $posts = Post::select($where,$page,self::$pagenum,$link);
        $ctx->status = 200;
        $ctx->state["posts"] = $posts['data'];
        $ctx->state["pagination"] = Publish::pagination($posts['pagination']);
        yield $ctx->show("index");
    } 

    public static function category(Context $ctx, $next, $vars){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $category = new Category($vars[0]);
        $link = '/show/category/'.$vars[0].'?page=:page';
        $where['categories_value'] = $category['title'];
        $posts = Post::select($where,$page,self::$pagenum,$link);

        $ctx->status = 200;
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = Publish::pagination($posts['pagination']);

        yield $ctx->show("category");
    } 

    public static function tags(Context $ctx, $next, $vars){

        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $tag = new Tag($vars[0]);
        $link = '/show/tags/'.$vars[0].'?page=:page';
        $where['tags_value'] = $tag['title'];
        $posts = Post::select($where,$page,self::$pagenum,$link);

        $ctx->status = 200;
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = Publish::pagination($posts['pagination']);

        yield $ctx->show("tags");
    }

    public static function posts(Context $ctx, $next, $vars){
        $post = new Post($vars[1]);
        $ctx->status = 200;
        $ctx->state['post'] = $post;
        yield $ctx->show("posts");
    } 

    public static function single(Context $ctx, $next, $vars){
        $single = new Single($vars[0]);
        $ctx->status = 200;
        $ctx->state['single'] = $single;
        yield $ctx->show("single");
    } 
}