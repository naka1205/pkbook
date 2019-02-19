<?php
namespace Middlewares;
use Naka507\Koa\Middleware;
use Naka507\Koa\Context;
use Extend\Config;
use Extend\Template;
class Render implements Middleware
{
    public $view;
    public function __construct()
    {
        $config = [
            'view_suffix'   =>	'html',
            'view_path'	    =>  VIEW_PATH,
            'cache_path'	=>	TEMP_PATH . DS . 'views'
        ];
        $this->view = new Template($config);
    }

    public function __invoke(Context $ctx, $next)
    {   
        $ctx->add('render',function($template) use ($ctx) {
            $this->view->assign('state',$ctx->state);
            $ctx->body = $this->view->fetch($template);
        });
        yield $next;
    }

}
