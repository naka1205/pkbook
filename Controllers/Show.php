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
        $ctx->state['link'] = $configs['link'];
        $ctx->state['contact'] = $configs['contact'];

        $friend = [];
        foreach ($configs['friend'] as $key => $value) {
            $friend[] = [
                'link' => $key,
                'name' => $value
            ];
        }
        $ctx->state['friend'] = $friend;

        $sidebar = [];
        $sidebar['tags'] = [];
        $sidebar['posts'] = [];
        $configs['sidebar']['tags'] = explode(',',$configs['sidebar']['tags']);
        foreach ($configs['sidebar']['tags'] as $key => $value) {
            $tag = new Tag($value);
            if ( !$tag->data ) {
                continue;
            }
            $sidebar['tags'][] = $tag;
        }

        $configs['sidebar']['posts'] = explode(',',$configs['sidebar']['posts']);
        foreach ($configs['sidebar']['posts'] as $key => $value) {
            $post = new Post($value);
            if ( !$post->data ) {
                continue;
            }
            $sidebar['posts'][] = $post;
        }
        $ctx->state['sidebar'] = $sidebar;

        self::$pagenum = intval($configs['site']['pagenum']);
        $ctx->state['site'] = $configs['site'];
        $ctx->state['github'] = $configs['github'];

        $categories = ( yield Category::select([]) );
        $ctx->state['categories'] = $categories;

        $singles = ( yield Single::select([]) );
        $ctx->state['singles'] = $singles;
        $ctx->state['single'] = current($singles);
        
        $counts = [];
        $counts['tags'] = ( yield Tag::count([]) );
        $counts['posts'] = ( yield Post::count([]) );
        $counts['categories'] = ( yield Category::count([]) );

        $ctx->state['counts'] = $counts;
    }

    public static function index(Context $ctx, $next){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;
        $where = [];
        $link = '/show/index.html?page=:page';
        $posts = ( yield Post::select($where,$page,self::$pagenum,$link) );
        $ctx->status = 200;

        $ctx->state['title'] = '首页';
        $ctx->state['description'] = '首页';
        $ctx->state['page_id'] = 'index';
        $ctx->state["posts"] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];
        yield $ctx->show("index");
    } 

    public static function categories(Context $ctx, $next, $vars){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $ctx->status = 200;
        $ctx->state['title'] = '分类';
        $ctx->state['description'] = '分类';
        $ctx->state['page_id'] = 'categories';
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];

        yield $ctx->show("categories");
    } 

    public static function category(Context $ctx, $next, $vars){
        $page = isset($ctx->get["page"]) && intval($ctx->get["page"]) ? intval($ctx->get["page"]) : 1;

        $category = new Category($vars[0]);
        $link = '/show/category/'.$vars[0].'.html?page=:page';
        $where['categories_value'] = $category['title'];
        $posts = ( yield Post::select($where,$page,self::$pagenum,$link) );

        $ctx->status = 200;
        $ctx->state['title'] = '分类';
        $ctx->state['description'] = '分类';
        $ctx->state['page_id'] = '';
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];

        yield $ctx->show("category");
    } 

    public static function tags(Context $ctx, $next, $vars){

        $tags = ( yield Tag::select([]) );

        $ctx->status = 200;
        $ctx->state['title'] = '标签';
        $ctx->state['description'] = '标签';
        $ctx->state['page_id'] = '';
        $ctx->state['tags'] = $tags;

        yield $ctx->show("tags");
    }


    public static function tag(Context $ctx, $next, $vars){

        $tag = new Tag($vars[0]);
        $link = '/show/tag/'.$vars[0].'/index.html?page=:page';
        $where['tag_value'] = $tag['title'];
        $posts = ( yield Post::select($where,$page,self::$pagenum,$link) );

        $ctx->status = 200;
        $ctx->state['page_id'] = '';
        $ctx->state['title'] = $tag['title'];
        $ctx->state['description'] = '标签';
        $ctx->state['posts'] = $posts['data'];
        $ctx->state["pagination"] = $posts['pagination'];

        yield $ctx->show("tag");
    }

    public static function posts(Context $ctx, $next, $vars){
        $post = new Post($vars[1]);
        $ctx->status = 200;
        $ctx->state['post'] = $post;
        $ctx->state['title'] = $post['title'];
        $ctx->state['description'] = $post['description'];
        $ctx->state['page_id'] = $post['_id'];
        yield $ctx->show("posts");
    } 

    public static function single(Context $ctx, $next, $vars){
        $single = new Single($vars[0]);
        $ctx->status = 200;
        $ctx->state['title'] = $single['title'];
        $ctx->state['description'] = $single['description'];
        $ctx->state['single'] = $single;
        $ctx->state['page_id'] = '';
        yield $ctx->show("single");
    } 
}