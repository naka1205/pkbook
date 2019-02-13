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

        self::$pagenum = intval($configs['site']['pagenum']);
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
        $link = '/show/index.html?page=:page';
        $posts = Post::select($where,$page,self::$pagenum,$link);
        $ctx->status = 200;

        $ctx->state['title'] = '首页';
        $ctx->state["posts"] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];
        yield $ctx->show("index");
    } 

    public static function categories(Context $ctx, $next, $vars){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $ctx->status = 200;
        $ctx->state['title'] = '分类';
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];

        yield $ctx->show("categories");
    } 

    public static function category(Context $ctx, $next, $vars){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $category = new Category($vars[0]);
        $link = '/show/category/'.$vars[0].'.html?page=:page';
        $where['categories_value'] = $category['title'];
        $posts = Post::select($where,$page,self::$pagenum,$link);

        $ctx->status = 200;
        $ctx->state['title'] = '分类';
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];

        yield $ctx->show("category");
    } 

    public static function tags(Context $ctx, $next, $vars){

        $tags = Tag::select([]);

        $ctx->status = 200;
        $ctx->state['title'] = '标签';
        $ctx->state['tags'] = $tags;

        yield $ctx->show("tags");
    }


    public static function tag(Context $ctx, $next, $vars){

        $tag = new Tag($vars[0]);
        $link = '/show/tag/'.$vars[0].'/index.html?page=:page';
        $where['tag_value'] = $tag['title'];
        $posts = Post::select($where,$page,self::$pagenum,$link);

        $ctx->status = 200;
        $ctx->state['title'] = $tag['title'];
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];

        yield $ctx->show("tag");
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